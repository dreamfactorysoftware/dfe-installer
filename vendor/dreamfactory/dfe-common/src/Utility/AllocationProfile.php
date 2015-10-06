<?php namespace DreamFactory\Enterprise\Common\Utility;

use DreamFactory\Enterprise\Common\Enums\AllocationFeatures;
use DreamFactory\Library\Utility\JsonFile;

/**
 * The types of allocation profiles
 */
class AllocationProfile
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array The allocations of this profile
     */
    protected $_allocations = [];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param array $allocations An array of single value, or array of [:min, :max] allocations. These represent the minimum/maximum/size of
     *                           "$feature" allocated to this profile. A value of null or zero represents "no limit" on the min/max allocation. If a
     *                           single value is specified, both the min and max are set to this value. Examples:
     */
    public function __construct($allocations = [])
    {
        foreach ($allocations as $_feature => $_minMax) {
            $this->allocate($_feature, $_minMax);
        }
    }

    /**
     * @param int       $feature The feature that has allocation capabilities {@see AllocationFeatures}.
     * @param int|array $minMax  A single value, or an array of [:min, :max]. These represent the minimum/maximum/size of "$feature"
     *                           allocated to this profile. A value of null or zero represents "no limit" on the min/max allocation. If a single
     *                           value is specified, both the min and max are set to this value. Examples:
     *
     *                           $minMax = 0;           //  No limits
     *                           $minMax = [0, 0];      //  No limits
     *                           $minMax = [0, 10];     //  No minimum, maximum of 10
     *                           $minMax = [100, 0];    //  Minimum of 100, no maximum
     *                           $minMax = 100;         //  BOTH Minimum AND maximum of 100
     *
     * @return $this
     */
    public function allocate($feature, $minMax = [0, 0])
    {
        if (!AllocationFeatures::contains($feature)) {
            throw new \InvalidArgumentException('The feature "' . $feature . '" is not valid.');
        }

        //  Convert single-value input to array
        if (!is_array($minMax) && is_numeric($minMax)) {
            $minMax = [$minMax, $minMax];
        }

        $this->_allocations[$feature] = $minMax;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_allocations;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return JsonFile::encode($this->toArray());
    }
}
