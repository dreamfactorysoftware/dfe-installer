<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The types of manifests
 */
class ManifestTypes extends FactoryEnum
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var string Generic manifest (manifest.json)
     */
    const GENERIC = 'manifest';
    /**
     * @var string Archive metadata (metadata.json)
     */
    const METADATA = 'metadata';
    /**
     * @var string Instance manifest, stored in owner's private directory
     */
    const INSTANCE = 'instance';
    /**
     * @var string Package (library/app) manifest (package.json)
     */
    const PACKAGE = 'package';
    /**
     * @var string The cluster manifest for instance root (.dfe.cluster.json)
     */
    const CLUSTER = '.dfe.cluster';
    /**
     * @var string A snapshot manifest
     */
    const SNAPSHOT = 'snapshot';
}
