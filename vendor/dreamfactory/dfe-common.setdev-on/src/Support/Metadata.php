<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Common\Enums\ManifestTypes;
use DreamFactory\Enterprise\Common\Traits\MicroLogger;
use League\Flysystem\Filesystem;

/**
 * Instance metadata
 */
class Metadata extends Manifest
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string Operational metadata
     */
    const OPERATIONS_LOG_KEY = '_operations';

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use MicroLogger;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type array Basic metadata template
     */
    protected $allowedKeys = [
        'db',
        'env',
        'paths',
        'storage-map',
        'audit',
        'limits',
        self::CUSTODY_LOG_KEY,
        self::OPERATIONS_LOG_KEY,
    ];

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct($contents = [], $filename = null, Filesystem $filesystem = null, array $template = [])
    {
        parent::__construct(ManifestTypes::METADATA, $contents, $filename, $filesystem, $template);
    }
}