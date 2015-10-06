<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Database\Models\Instance;
use Illuminate\Mail\Message;

/**
 * A trait that aids with notifying
 */
trait Notifier
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string|null The prefix for outbound subject lines
     */
    protected $subjectPrefix;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param Instance $instance
     * @param string   $subject
     * @param array    $data
     *
     * @return int The number of recipients mailed
     */
    protected function notifyInstanceOwner($instance, $subject, array $data)
    {
        if (!empty($this->subjectPrefix)) {
            $subject = $this->subjectPrefix . ' ' . trim(str_replace($this->subjectPrefix, null, $subject));
        }

        $_result =
            \Mail::send(
                'emails.generic',
                $data,
                function ($message/** @var Message $message */) use ($instance, $subject) {
                    $message
                        ->to($instance->user->email_addr_text,
                            $instance->user->first_name_text . ' ' . $instance->user->last_name_text)
                        ->subject($subject);
                }
            );

        ($this instanceof Lumberjack) && $this->debug('notification sent to "' . $instance->user->email_addr_text . '"');

        return $_result;
    }

}
