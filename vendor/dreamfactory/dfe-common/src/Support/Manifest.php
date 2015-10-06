<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Common\Enums\ManifestTypes;
use League\Flysystem\Filesystem;

/**
 * Retrieves, validates, and makes available a JSON manifest file, if one exists.
 */
abstract class Manifest extends FileCanister
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string                       $manifestType The type of manifest @see ManifestTypes
     * @param array                        $contents     Optional contents to fill
     * @param string                       $filename     If you do not want the default manifest name for the type,
     *                                                   override it with this
     * @param \League\Flysystem\Filesystem $filesystem   The filesystem where the manifest lives
     * @param array                        $template     The optional structure, or template, for an empty canister
     */
    public function __construct($manifestType, $contents = [], $filename = null, Filesystem $filesystem = null, array $template = [])
    {
        parent::__construct(
            $contents,
            static::buildManifestFilename($manifestType, $filename),
            $filesystem,
            $template
        );
    }

    /**
     * Creates a file name based on the manifest type, or $filename if not null
     *
     * @param string      $manifestType
     * @param string|null $filename
     *
     * @return null|string
     */
    protected static function buildManifestFilename($manifestType, $filename = null)
    {
        if (!ManifestTypes::contains($manifestType)) {
            throw new \InvalidArgumentException('The $manifestType "' . $manifestType . '" is invalid.');
        }

        !$filename && ($filename = strtolower($manifestType));
        false === strpos($filename, '.json') && $filename .= '.json';

        return $filename;
    }
}