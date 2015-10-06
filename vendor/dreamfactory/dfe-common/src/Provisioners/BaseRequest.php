<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Common\Traits\HasCollectionResults;
use DreamFactory\Enterprise\Common\Traits\StaticComponentLookup;
use Illuminate\Support\Collection;

/**
 * A dumb container for job requests that holds a single key: "result"
 */
class BaseRequest extends Collection
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use HasCollectionResults, EntityLookup;
}
