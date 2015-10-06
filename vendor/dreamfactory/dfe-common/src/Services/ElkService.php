<?php namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Enterprise\Common\Enums\ElkIntervals;
use DreamFactory\Enterprise\Common\Providers\ElkServiceProvider;
use Elastica\Client;
use Elastica\Exception\PartialShardFailureException;
use Elastica\Filter\Bool;
use Elastica\Filter\Prefix;
use Elastica\Query;
use Elastica\QueryBuilder\DSL\Aggregation;
use Elastica\ResultSet;
use Elastica\Search;

/**
 * Gets data from the ELK stack
 */
class ElkService extends BaseService
{
    //*************************************************************************
    //* Members
    //*************************************************************************

    /**
     * @var Client
     */
    protected $_client = null;
    /**
     * @type array Array of elastic search shards
     */
    protected static $_indices = null;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param array                                        $settings
     *
     * @throws \Exception
     */
    public function __construct($app = null, array $settings = [])
    {
        parent::__construct($app);

        $_config = ElkServiceProvider::getServiceConfig();

        if (empty($_config)) {
            throw new \RuntimeException('No ELK configuration found (config/elk.php).');
        }

        $this->_client = new Client($_config);

        $this->_getIndices();
    }

    /**
     * Retrieves the indices available from ElasticSearch
     */
    protected function _getIndices()
    {
        if (null === static::$_indices) {
            $_indices = [];

            try {
                $_response = $this->_client->request('_aliases?pretty=1');

                foreach ($_response->getData() as $_index => $_aliases) {
                    //  No recent index
                    if (false === stripos($_index, '_recent') && '.kibana' !== $_index) {
                        $_indices[] = $_index;
                    }
                }

                if (!empty($_indices)) {
                    static::$_indices = $_indices;
                }
            } catch (\Exception $_ex) {
                \Log::error($_ex);

                throw $_ex;
            }
        }

        return static::$_indices;
    }

    /**
     * @param string       $facility
     * @param string       $interval
     * @param int          $size
     * @param int          $from
     * @param array|string $term
     *
     * @return \Elastica\ResultSet
     */
    public function callOverTime($facility, $interval = ElkIntervals::DAY, $size = 30, $from = 0, $term = null)
    {
        if (!ElkIntervals::contains($interval)) {
            throw new \InvalidArgumentException('Interval "' . $interval . '" is not valid.');
        }

        $_query = $this->_buildQuery($facility, $interval, $size, $from, $term);

        \Log::debug(json_encode($_query));

        $_results = null;

        try {
            $_results = $this->_doSearch($_query);
        } catch (\Exception $_ex) {
            \Log::error('Exception retrieving logs: ' . $_ex->getMessage());

            throw new \RuntimeException(500, $_ex->getMessage());
        }

        return $_results;
    }

    /**
     * @param string       $facility
     * @param string       $interval
     * @param int          $size
     * @param int          $from
     *
     * @param string|array $term
     *
     * @return array
     */
    protected function _buildQuery($facility, $interval = 'day', $size = 30, $from = 0, $term = null)
    {
        $facility = str_replace('/', '?', $facility);

        $_query = [
            'size' => $size,
            'from' => $from,
            'aggs' => [
                'facilities'   => [
                    'terms' => [
                        'field' => 'fabric.facility.raw',
                        'size'  => 10,
                    ],
                ],
                'published_on' => [
                    'date_histogram' => [
                        'field'    => '@timestamp',
                        'interval' => $interval,
                    ],
                ],
            ],
        ];

        if (empty($term)) {
            $_query['aggs']['paths'] = [
                'terms' => [
                    'field' => 'fabric.path.raw',
                    'size'  => 10,
                ],
            ];

            if (!empty($facility)) {
                $_query['query'] = [
                    'bool' => [
                        'must' => [
                            'wildcard' => [
                                'fabric.facility.raw' => $facility,
                            ],
                        ],
                    ],
                ];
            }
        } else {
            $_query['query'] = ['term' => []];

            if (is_array($term)) {
                foreach ($term as $_field => $_value) {
                    $_query['query']['term'][$_field] = $_value;
                }
            } else {
                $_query['query']['term']['fabric.path.raw'] = $term;
            }
        }

        return $_query;
    }

    /**
     * @param string|Query $query
     * @param string|array $indices
     *
     * @return \Elastica\ResultSet
     */
    protected function _doSearch($query, $indices = null)
    {
        $_results = false;

        $_query = !($query instanceof Query) ? new Query($query) : $query;
        $_search = new Search($this->_client);

        $indices = $indices ?: static::$_indices;

        if (null !== $indices) {
            if (!is_array($indices)) {
                $indices = [$indices];
            }

            $_search->addIndices($indices);
        }

        try {
            $_results = $_search->search($_query);
        } catch (PartialShardFailureException $_ex) {
            //Log::info( 'Partial shard failure. ' . $_ex->getMessage() . ' failed shard(s).' );

            return new ResultSet($_ex->getResponse(), $_query);
        } catch (\Exception $_ex) {
            \Log::error($_ex->getMessage());
        }

        return $_results;
    }

    /**
     * @param int $from
     * @param int $size
     *
     * @return bool
     */
    public function globalStats($from = 0, $size = 1)
    {
        $_query = [
            'query' => [
                'term' => ['fabric.facility.raw' => 'cloud/cli/global/metrics'],
            ],
            'size'  => $size,
            'from'  => $from,
            'sort'  => [
                '@timestamp' => [
                    'order' => 'desc',
                ],
            ],
        ];

        $_query = new Query($_query);
        $_search = new Search($this->_client);

        try {
            $_result = $_search->search($_query)->current()->getHit();
        } catch (PartialShardFailureException $_ex) {
            \Log::info('Partial shard failure: ' . $_ex->getMessage() . ' failed shard(s).');
            $_result = $_ex->getResponse()->getData();

            if (array_key_exists('hits', $_result)) {
                if (isset($_result['total']) && 0 != $_result['total']) {
                    return $_result['hits']['hits'][0]['_source'];
                }
            }

            \Log::warning('No global stats found.');

            return false;
        }

        return $_result['_source'];
    }

    /**
     * @param int $from
     * @param int $size
     *
     * @return array
     */
    public function allStats($from = null, $size = null)
    {
        $_query = [
            'query' => [
                'term' => ['fabric.facility.raw' => 'cloud/cli/metrics'],
            ],
            'size'  => $size ?: 99999999,
            'from'  => $from ?: 0,
            'sort'  => [
                '@timestamp' => [
                    'order' => 'desc',
                ],
            ],
        ];

        $_query = new Query($_query);
        $_search = new Search($this->_client);
        $_result = $_search->search($_query)->getResults();

        $_data = [];

        foreach ($_result as $_hit) {
            $_data[] = $_hit->getSource();
        }

        return $_data;
    }

    /**
     * @param string $term
     * @param string $value
     * @param int    $size
     *
     * @return \Elastica\ResultSet
     */
    public function termQuery($term, $value, $size = 30)
    {
        $_agg = new Aggregation();
        $_facet = $_agg->date_histogram('occurred_on', '@timestamp', ElkIntervals::DAY);

//        $_facet = new DateHistogram('occurred_on');
//        $_facet->setField('@timestamp');
//        $_facet->setInterval('day');

        $_query = new Query();
        $_query->setSize($size);
        $_query->setSort(['@timestamp']);

        //	Filter for term
        $_filter = new Prefix($term, $value);
        $_and = new Bool();
        $_and->addMust($_filter);
        $_query->setPostFilter($_and);
        $_query->addAggregation($_facet);

        $_results = $this->_doSearch($_query);

        return $_results;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->_client;
    }

}
