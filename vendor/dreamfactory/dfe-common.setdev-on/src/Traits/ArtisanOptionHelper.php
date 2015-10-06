<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Library\Utility\JsonFile;

/**
 * A trait that adds artisan option helpers for DFE
 */
trait ArtisanOptionHelper
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param array $array
     * @param bool  $required
     *
     * @return bool
     */
    protected function optionOwner(array &$array = null, $required = false)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        if (null === ($_ownerId = $this->option('owner-id'))) {
            if ($required) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->writeln('"owner-id" is required for this operation.', 'error');

                return false;
            }

            //  No owner, we're gone
            return true;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $_ownerType = $this->option('owner-type');

        if (empty($_ownerType)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->writeln('"owner-type" required when "owner-id" specified.', 'error');

            return false;
        }

        try {
            /** @noinspection PhpUndefinedMethodInspection */
            $_owner = $this->_locateOwner($_ownerId, $_ownerType);

            $array['owner_id'] = $_owner->id;
            $array['owner_type_nbr'] = $_owner->owner_type_nbr;

            return true;
        } catch (\Exception $_ex) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->writeln('owner-id "' . $_ownerId . '" is not valid.');

            return false;
        }
    }

    /**
     * @param string|null $optionKey
     * @param string|null $arrayKey
     * @param array       $array
     * @param bool        $required
     *
     * @return bool
     */
    protected function optionString($optionKey = null, $arrayKey = null, array &$array = null, $required = false)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $_string = $this->option($optionKey);

        if ($required && empty($_string)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->writeln('"' . $optionKey . '" is a required option for this operation.');

            return false;
        }

        !empty($_string) && ($array[$arrayKey] = $_string);

        return true;
    }

    /**
     * Retrieves an input argument and checks for valid JSON.
     *
     * @param string|null $optionKey The option name to retrieve
     * @param string|null $arrayKey  If specified, decoded array will be placed into $array[$arrayKey]
     * @param array|null  $array     The $array in which to place the result
     * @param bool        $required  If this is required
     *
     * @return bool|array
     */
    protected function optionArray($optionKey = null, $arrayKey = null, array &$array = null, $required = false)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $_data = $this->option($optionKey);

        if (null === $arrayKey) {
            return $_data;
        }

        if (empty($_data)) {
            if ($required) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->writeln('"' . $optionKey . '" is a required option for this operation.');

                return false;
            }

            $array[$arrayKey] = $_data = [];

            return true;
        }

        try {
            $_data = JsonFile::decode($_data);
        } catch (\Exception $_ex) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->writeln('the "' . $optionKey . '" provided does not contain valid JSON.');

            return false;
        }

        $array[$arrayKey] = $_data;

        return true;
    }
}
