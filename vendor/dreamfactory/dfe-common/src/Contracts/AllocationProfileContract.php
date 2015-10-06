<?php
namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Describes an object that manages other things
 */
interface AllocationProfileContract
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param int              $allocationProfile The profile type as defined in {@see Enums\AllocationProfiles}
     * @param int|float|double $amount            The value to set for this
     *
     * @return mixed
     */
    public function setValue($allocationProfile, $amount);

    /**
     * @param int   $allocationProfile The profile type as defined in {@see Enums\AllocationProfiles}
     * @param mixed $default           Default value if nothing is set.
     *
     * @return mixed
     */
    public function getValue($allocationProfile, $default = null);
}