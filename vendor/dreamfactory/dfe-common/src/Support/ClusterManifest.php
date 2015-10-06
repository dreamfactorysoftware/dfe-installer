<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Common\Enums\ManifestTypes;
use League\Flysystem\Filesystem;

/**
 * Retrieves, validates, and makes available the DFE cluster manifest, if one exists.
 */
class ClusterManifest extends Manifest
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array Basic metadata template
     */
    protected $allowedKeys = [
        'cluster-id',
        'default-domain',
        'signature-method',
        'storage-root',
        'console-api-url',
        'console-api-key',
        'console-api-client-id',
        'console-api-client-secret',
        'dashboard-client-id',
        'dashboard-client-secret',
        'client-id',
        'client-secret',
        self::CUSTODY_LOG_KEY,
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct($contents = [], $filename = null, Filesystem $filesystem = null, array $template = [])
    {
        parent::__construct(ManifestTypes::CLUSTER, $contents, $filename, $filesystem, $template);
    }
}