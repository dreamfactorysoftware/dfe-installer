<?php namespace DreamFactory\Enterprise\Common\Traits;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

/**
 * A trait for working with Guzzle
 */
trait Guzzler
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use VerifiesSignatures;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Client
     */
    protected $guzzleClient;
    /**
     * @type array The Guzzle configuration
     */
    protected $guzzleConfig;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Initialize and set up the guzzle client
     *
     * @param string $baseUrl The optional base url to use
     * @param array  $config  Optional guzzle configuration options
     *
     * @return $this
     */
    public function createClient($baseUrl = null, $config = [])
    {
        //  Check the endpoint...
        if ($baseUrl && false === parse_url($baseUrl)) {
            throw new \InvalidArgumentException('The specified url "' . $baseUrl . '" is not valid.');
        }

        $_options = ['debug' => env('APP_DEBUG', false)];
        $baseUrl && $_options['base_url'] = $baseUrl;

        $this->guzzleConfig = array_merge($config, $_options);
        $this->guzzleClient = new Client($this->guzzleConfig);

        return $this;
    }

    /**
     * Initialize and set up the guzzle client with signing credentials
     *
     * @param string $url         The url of the app server to use
     * @param array  $credentials Any credentials needed for the request [:client-id, :client-secret]
     * @param array  $config      Optional guzzle configuration options
     *
     * @return $this
     */
    public function createRequest($url, $credentials = [], $config = [])
    {
        $this->createClient($url, $config);

        //  Set credentials if provided
        if (isset($credentials, $credentials['client-id'], $credentials['client-secret'])) {
            $this->setSigningCredentials($credentials['client-id'], $credentials['client-secret']);
            unset($credentials['client-id'], $credentials['client-secret']);
        }

        return $this;
    }

    /**
     * Perform a GET
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $object If true, the result is returned as an object instead of an array
     *
     * @return array|bool
     */
    public function guzzleGet($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzleAny($uri, $payload, $options, Request::METHOD_GET, $object);
    }

    /**
     * Perform a POST
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $object If true, the result is returned as an object instead of an array
     *
     * @return \stdClass|array|bool
     */
    public function guzzlePost($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzleAny($uri, $payload, $options, Request::METHOD_POST, $object);
    }

    /**
     * Perform a DELETE
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $object If true, the result is returned as an object instead of an array
     *
     * @return \stdClass|array|bool
     */
    public function guzzleDelete($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzleAny($uri, $payload, $options, Request::METHOD_DELETE, $object);
    }

    /**
     * Perform a PUT
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options
     * @param bool   $object If true, the result is returned as an object instead of an array
     *
     * @return \stdClass|array|bool
     */
    public function guzzlePut($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzleAny($uri, $payload, $options, Request::METHOD_PUT, $object);
    }

    /**
     * Performs a generic HTTP request
     *
     * @param string $url
     * @param array  $payload
     * @param array  $options
     * @param string $method
     * @param bool   $object If true, the result is returned as an object instead of an array
     *
     * @return array|bool|\stdClass
     */
    public function guzzleAny($url, $payload = [], $options = [], $method = Request::METHOD_POST, $object = true)
    {
        $_response = null;

        try {
            is_array($payload) && $payload = $this->signRequest($payload);

            if (!empty($payload)) {
                $options = array_merge($options, is_scalar($payload) ? ['body' => $payload,] : ['json' => $payload]);
            }

            /** @type \Response $_response */
            $_response = $this->getGuzzleClient()->{$method}($url, $options);

            return $_response->json(['object' => $object]);
        } catch (\Exception $_ex) {
            if (is_object($_response) && method_exists($_response, 'getBody')) {
                return trim((string)$_response->getBody());
            }

            return $_response;
        }
    }

    /**
     * Returns the guzzle client
     *
     * @param array $config
     *
     * @return \GuzzleHttp\Client
     */
    public function getGuzzleClient($config = [])
    {
        if ($this->guzzleClient) {
            return $this->guzzleClient;
        }

        $this->guzzleConfig = $config;

        return $this->guzzleClient = new Client($this->guzzleConfig);
    }

    /**
     * @return array
     */
    public function getGuzzleConfig()
    {
        return $this->guzzleConfig;
    }
}
