<?php namespace DreamFactory\Enterprise\Common\Commands;

use DreamFactory\Enterprise\Common\Traits\ArtisanHelper;
use DreamFactory\Enterprise\Common\Traits\ArtisanOptionHelper;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Composer;

/**
 * Adds some additional functionality to the Command class
 */
abstract class ConsoleCommand extends Command
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use DispatchesJobs, ArtisanHelper, ArtisanOptionHelper;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type \Illuminate\Foundation\Composer The Composer class instance.
     */
    protected $composer;
    /**
     * @type \Illuminate\Filesystem\Filesystem The filesystem instance.
     */
    protected $filesystem;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param Composer   $composer
     * @param Filesystem $filesystem
     */
    public function __construct(Composer $composer, Filesystem $filesystem)
    {
        parent::__construct();

        $this->composer = $composer;
        $this->filesystem = $filesystem;
    }

    /**
     * Handle the command
     */
    public function fire()
    {
//        if (null === $this->getOutputPrefix()) {
//            $this->setOutputPrefix($this->name);
//        }

        $this->writeHeader();
    }


}
