<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Library\Console\Enums\AnsiCodes;
use DreamFactory\Library\Console\Utility\Cursor;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A trait that adds console cursor control
 */
trait CursorControl
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages The message as an array of lines or a single string
     * @param bool         $newline  Whether to add a newline
     * @param int          $type     The type of output (one of the OUTPUT constants)
     *
     * @return $this
     * @throws \InvalidArgumentException When unknown output type is given
     */
    public function write($messages, $newline = false, $type = OutputInterface::OUTPUT_NORMAL)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->clearArea('line')->output->write($messages, $newline, $type);

        return $this;
    }

    /**
     * Writes an escape sequence to the output.
     *
     * @param string $code   The code to output
     * @param int    $value1 Optional row #
     * @param int    $value2 Optional column #
     *
     * @return $this
     * @throws \InvalidArgumentException When unknown output type is given
     */
    public function writeCode($code, $value1 = 1, $value2 = 1)
    {
        isset($this->output) && $this->output->write(AnsiCodes::render($code, $value1, $value2));

        return $this;
    }

    /**
     * Writes an escape sequence to the output.
     *
     * @param string $moves A string of cursor movements
     * @param int    $count How many times to repeat the sequence
     *
     * @return $this
     */
    public function moveCursor($moves, $count = 1)
    {
        isset($this->output) && $this->output->write(Cursor::move($moves, $count));

        return $this;
    }

    /**
     * Writes an escape sequence to the output.
     *
     * @param string $areas A string of areas to clear
     *
     * @return $this
     */
    public function clearArea($areas)
    {
        isset($this->output) && $this->output->write(Cursor::clear($areas));

        return $this;
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array $messages The message as an array of lines of a single string
     * @param int          $type     The type of output (one of the OUTPUT constants)
     *
     * @return $this
     * @throws \InvalidArgumentException When unknown output type is given
     */
    public function writeln($messages, $type = OutputInterface::OUTPUT_NORMAL)
    {
        return
            $this->clearArea('line_end')->write($messages, true, $type);
    }

    /**
     * Writes a string to the output then shifts the cursor back to the beginning of the line
     *
     * @param string $message
     *
     * @return $this
     */
    public function writeInPlace($message)
    {
        return $this
            ->writeCode(AnsiCodes::SCP)
            ->write($message)
            ->writeCode(AnsiCodes::RCP);
    }

}
