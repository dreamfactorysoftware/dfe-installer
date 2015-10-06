<?php namespace DreamFactory\Enterprise\Common\Traits;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A trait for things interact with the console
 *
 * @implements \Symfony\Component\Console\Input\InputAwareInterface
 */
trait InteractsWithConsole
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type InputInterface
     */
    protected $input;
    /**
     * @type OutputInterface
     */
    protected $output;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param InputInterface $input
     *
     * @return $this
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     *
     * @return $this
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }
}