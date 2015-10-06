<?php namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Library\Utility\DataShaper;
use DreamFactory\Library\Utility\Enums\DataShapes;
use Elastica\Query;

/**
 * Data shaping services
 */
class DataShaperService extends BaseService
{
    //*************************************************************************
    //* Members
    //*************************************************************************

    /**
     * @var DataShaper
     */
    protected $shaper;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    public function boot()
    {
        parent::boot();

        $this->shaper = new DataShaper();
    }

    /**
     * Transforms $data into the desired shape
     *
     * @param array $data    The data to shape
     * @param int   $shape   The desired shape. Defaults to JSON
     * @param array $options Any options to pass to the shaper
     *
     * @return mixed
     */
    public function transform($data = [], $shape = DataShapes::JSON, $options = [])
    {
        return $this->shaper->reshape($data, $shape, $options);
    }

    /**
     * Returns the file extension, without a dot, for the shape. Returns null if unknown or no extension
     *
     * @param int $shape The desired shape
     *
     * @return string|null
     */
    public function getShapeExtension($shape)
    {
        return $this->shaper->getShapeExtension($shape);
    }
}
