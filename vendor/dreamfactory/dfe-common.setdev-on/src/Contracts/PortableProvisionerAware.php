<?php
namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Services\Jobs\ExportJob;
use DreamFactory\Enterprise\Services\Jobs\ImportJob;

/**
 * Something that is aware of provisioners
 */
interface PortableProvisionerAware extends PortabilityAware
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Export portability data
     *
     * @param ExportJob $job
     *
     * @return mixed
     */
    public function export(ExportJob $job);

    /**
     * Import portability data
     *
     * @param ImportJob $job
     *
     * @return mixed
     */
    public function import(ImportJob $job);
}