<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Database\Models\Instance;
use DreamFactory\Library\Utility\Json;
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
        try {
            if (!empty($this->subjectPrefix)) {
                $subject = $this->subjectPrefix . ' ' . trim(str_replace($this->subjectPrefix, null, $subject));
            }

            $_result = \Mail::send('emails.generic',
                $data,
                function ($message/** @var Message $message */) use ($instance, $subject){
                    $message->to($instance->user->email_addr_text,
                        $instance->user->first_name_text . ' ' . $instance->user->last_name_text)->subject($subject);
                });

            ($this instanceof Lumberjack) &&
            $this->debug('notification sent to "' . $instance->user->email_addr_text . '"');

            return $_result;
        } catch (\Exception $_ex) {
            \Log::error('Error sending notification: ' . $_ex->getMessage());

            $_mailPath = storage_path('logs/unsent-mail');

            if (!is_dir($_mailPath)) {
                mkdir($_mailPath, 0777, true);
            }

            @file_put_contents(date('YmdHis') . '-' . $instance->user->email_addr_text . '.json',
                Json::encode(array_merge($data,
                    ['subject' => $subject, 'template' => 'emails.generic', 'instance' => $instance->toArray()])));
        }
    }

}
