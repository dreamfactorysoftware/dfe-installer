<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Provisioners\ProvisionServiceOffering;

/**
 * A trait that adds offerings capabilities to provisioners
 *
 * @implements \DreamFactory\Enterprise\Common\Contracts\OfferingsAware
 */
trait HasOfferings
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array My offerings
     */
    protected $offerings = null;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Returns the list of offerings for this provider
     *
     * @param string|null $provisionerId
     *
     * @return array|null Array of offerings or null if none
     */
    public function getOfferings($provisionerId = null)
    {
        if (null === $this->offerings) {
            $this->offerings = [];

            /** @noinspection PhpUndefinedMethodInspection */
            $_list = config('provisioners.hosts.' . ($provisionerId ?: $this->getProvisionerId()) . '.offerings', []);

            if (is_array($_list) && !empty($_list)) {
                foreach ($_list as $_key => $_value) {
                    if (!empty($_key)) {
                        $_offer = new ProvisionServiceOffering($_key, $_value);
                        $this->offerings[$_key] = $_offer->toArray();
                    }
                }
            }
        }

        return $this->offerings;
    }
}
