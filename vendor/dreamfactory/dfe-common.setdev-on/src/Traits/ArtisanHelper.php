<?php namespace DreamFactory\Enterprise\Common\Traits;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * A trait that adds shortcuts for artisan commands
 *
 * @property OutputInterface $output
 */
trait ArtisanHelper
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string An optional prefix, such as the command name, which will be prepended to output
     */
    private $outputPrefix;
    /**
     * @type string The currently buffered output
     */
    private $lineBuffer;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Displays the command's name and info
     *
     * @param bool $newline If true, a blank line is added to the end of the header
     *
     * @return $this
     */
    protected function writeHeader($newline = true)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        if ($this->output) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->output->writeln($this->context(config('commands.display-name'), 'info') .
                ' (' .
                $this->context(config('commands.display-version', 'Alpha'), 'comment') .
                ')');

            if (null !== ($_copyright = config('commands.display-copyright'))) {
                /** @noinspection PhpUndefinedFieldInspection */
                $this->output->writeln($this->context($_copyright, 'info') . ($newline ? PHP_EOL : null));
            }
        }

        return $this;
    }

    /**
     * @param string|array $messages
     * @param string       $context The message context (info, comment, error, or question)
     * @param int          $type
     */
    protected function writeln($messages, $context = null, $type = OutputInterface::OUTPUT_NORMAL)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->output && $this->output->writeln($this->formatMessages($messages, $context), $type);
    }

    /**
     * @param string|array $messages
     * @param bool         $newline
     * @param string       $context The message context (info, comment, error, or question)
     * @param int          $type
     */
    protected function write($messages, $newline = false, $context = null, $type = OutputInterface::OUTPUT_NORMAL)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->output && $this->output->write($this->formatMessages($messages, $context), $newline, $type);
    }

    /**
     * @param string $content The content to wrap
     * @param string $tag     The tag to wrap content
     *
     * @return string
     */
    protected function context($content, $tag)
    {
        return '<' . $tag . '>' . $content . '</' . $tag . '>';
    }

    /**
     * Buffers a string (optionally contextual) to write when flush() is called
     *
     * @param string      $text
     * @param string|null $context
     *
     * @return $this
     */
    protected function concat($text, $context = null)
    {
        $this->lineBuffer .= ($context ? $this->context($text, $context) : $text);

        return $this;
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string $text
     *
     * @return $this
     */
    protected function asInfo($text)
    {
        return $this->concat($text, 'info');
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string $text
     *
     * @return $this
     */
    protected function asComment($text)
    {
        return $this->concat($text, 'comment');
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string $text
     *
     * @return $this
     */
    protected function asQuestion($text)
    {
        return $this->concat($text, 'question');
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string $text
     *
     * @return $this
     */
    protected function asError($text)
    {
        return $this->concat($text, 'error');
    }

    /**
     * Writes any buffered text and clears the buffer
     *
     * @param string|null $message Any text to add to the buffer before flushing
     * @param string|null $context The context of $message
     */
    protected function flush($message = null, $context = null)
    {
        if (null !== $message) {
            $this->concat($message, $context);
        }

        if (!empty($this->lineBuffer)) {
            $this->writeln($this->lineBuffer);
        }

        $this->lineBuffer = null;
    }

    /**
     * @param string|array $messages
     * @param string       $context The message context (info, comment, error, or question)
     * @param bool         $prefix  If false, text will not be prefixed
     *
     * @return array|string
     */
    protected function formatMessages($messages, $context = null, $prefix = true)
    {
        $_scrubbed = [];
        $_data = !is_array($messages) ? [$messages] : $messages;

        if (!empty($this->outputPrefix) && ': ' != substr($this->outputPrefix, -2)) {
            $this->outputPrefix = trim($this->outputPrefix, ':') . ': ';
        }

        foreach ($_data as $_message) {
            $context && ($_message = $this->context(trim($_message), $context));
            $_scrubbed[] = ($prefix && $this->outputPrefix ? $this->outputPrefix : null) . $_message;
        }

        return is_array($messages) ? $_scrubbed : $_scrubbed[0];
    }

    /**
     * Retrieve any configuration settings for a command.
     *
     * @param string|null $command The command in question. If not specified, derived from $this->name minus 'dfe:'
     *                             prefix
     *
     * @return array
     */
    protected function getCommandConfig($command = null)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return config('commands.' . $command ?: str_replace('dfe:', null, $this->name), []);
    }

    /**
     * @return string
     */
    public function getOutputPrefix()
    {
        return $this->outputPrefix;
    }

    /**
     * @param string $outputPrefix
     *
     * @return ArtisanHelper
     */
    public function setOutputPrefix($outputPrefix)
    {
        $this->outputPrefix = $outputPrefix;

        return $this;
    }
}
