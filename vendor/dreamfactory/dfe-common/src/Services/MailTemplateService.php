<?php
namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Enterprise\Common\Enums\MailTemplates;
use DreamFactory\Library\Utility\IfSet;

/**
 * A simple mail template service
 */
class MailTemplateService extends BaseService
{
    //*************************************************************************
    //* Constants
    //*************************************************************************

    /**
     * @var bool
     */
    const USE_AMAZON_MAIL_SERVICE = false;

    //**************************************************************************
    //* Methods
    //**************************************************************************

    /**
     * Sends an email
     *
     * @param int   $template The template to use
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function send($template, $data = [])
    {
        //	If we came in through a service call, make sure type is specified...
        if (!MailTemplates::contains($template)) {
            throw new \InvalidArgumentException('Invalid template "' . $template . '" specified.');
        }

        $_settings = config('services.smtp-mail', []);
        $_service = array_get($_settings, 'smtp-service', 'localhost');

        if (null === ($_mailTemplate = array_getDeep($_settings, 'templates', $template))) {
            throw new \InvalidArgumentException('There was no associated template for type #' . $template);
        }

        if ('localhost' != $_service) {
            if (!isset($_settings['access-key']) || !isset($_settings['secret-key'])) {
                throw new \InvalidArgumentException('You must set both the "access-key" and "secret-key" in order to use this service.');
            }
        }

        //	Use template subject first, then local
        if (!isset($data['subject'])) {
            $data['subject'] = array_get($_mailTemplate, 'subject');
        }

        $_templateFile = base_path() . '/config/templates/' . $_mailTemplate['template'];

        if (!file_exists($_templateFile)) {
            throw new \InvalidArgumentException('Template "' . $_templateFile . '" cannot be found.');
        }

        //	Build the message
        $data['__smtp-mail.settings__'] = $_settings;
        $_message = $this->_createMessage($_templateFile, $data);

        try {
            switch ($_service) {
                case 'localhost':
                    $_transport = new \Swift_MailTransport();
                    break;

                default:
                    if (null === ($_settings = array_getDeep($_settings, 'services', $_service))) {
                        throw new \RuntimeException('Service "' . $_service . '" is not properly configured.');
                    }

                    //	Create the transport
                    $_transport = \Swift_SmtpTransport::newInstance($_settings['server-name'],
                        $_settings['server-port'])
                        ->setUsername($_settings['access-key'])
                        ->setPassword($_settings['secret-key']);
                    break;
            }

            //	And the mailer...
            $_mailer = new \Swift_Mailer($_transport);
            $_recipients = $_mailer->send($_message, $_bogus);

            if (!empty($_bogus)) {
                $this->logger->error('Failed recipients: ' . implode(', ', $_bogus));
            }

            if (empty($_recipients)) {
                $this->logger->error('Sending email to "' . $_message->getTo() . '" failed.');
            }

            return $_recipients;
        } catch (\Exception $_ex) {
            //	Something went awry
            $this->logger->error('Mail delivery exception: ' . $_ex->getMessage());
            throw $_ex;
        }
    }

    //**************************************************************************
    //* Private Methods
    //**************************************************************************

    /**
     * @param string $template
     * @param array  $data
     *
     * @throws \InvalidArgumentException
     * @return \Swift_Mime_Message
     */
    protected function _createMessage($template, &$data)
    {
        //	Pull out all the message data
        $_to = array_get($data, 'to', null, true);
        $_from = array_get($data, 'from', null, true);
        $_replyTo = array_get($data, 'reply_to', null, true);
        $_cc = array_get($data, 'cc', null, true);
        $_bcc = array_get($data, 'bcc', null, true);
        $_subject = array_get($data, 'subject', null, true);

        //	Get body template...
        if (false === ($_html = @file_get_contents($template))) {
            //	Something went awry
            throw new \InvalidArgumentException('Error reading contents of template "' . $template . '".');
        }

        //	And the message...
        $_message = new \Swift_Message();

        if (!empty($_subject)) {
            $_message->setSubject($_subject);
        }

        if (!empty($_to)) {
            $_message->setTo($_to);
        }

        if (!empty($_from)) {
            $_message->setFrom($_from);
        }

        if (!empty($_cc)) {
            $_message->setCc($_cc);
        }

        if (!empty($_bcc)) {
            $_message->setBcc($_bcc);
        }

        if (!empty($_replyTo)) {
            $_message->setReplyTo($_replyTo);
        }

        //	process generic macros.
        $_message->setBody(
            $this->replaceMacros($data, $_html),
            'text/html'
        );

        return $_message;
    }

    /**
     * Given an array of macro data, the source string is augmented with said data.
     *
     * @param array  $replacements
     * @param string $source
     * @param string $prefix    Defaults to 'private_'
     * @param string $delimiter Defaults to '%%'
     *
     * @return string
     */
    protected function replaceMacros($replacements, $source, $prefix = 'private_', $delimiter = '%%')
    {
        $_data = [];
        $_settings = array_get($replacements, '__smtp-mail.settings__', []);

        //	Replace private macros...
        if (false !== stripos($source, $delimiter . $prefix)) {
            foreach ($replacements as $_key => $_value) {
                //	No passwords allowed
                if (false !== stripos($_key, 'password') && '__smtp-mail.settings__' != $_key) {
                    continue;
                }

                $_data[strtoupper($delimiter . $prefix . $_key . $delimiter)] = $_value;
            }
        }

        //	With a sprinkle of settings...
        foreach ($_settings as $_key => $_value) {
            if (!is_scalar($_value)) {
                continue;
            }

            //	No passwords/keys/secret allowed
            if (false !== stripos($_key, 'password') || false !== stripos($_key, 'secret_') || false !== stripos($_key,
                    '_key')
            ) {
                continue;
            }

            //	No prefix on these...
            $_data[strtoupper($delimiter . $_key . $delimiter)] = $_value;
        }

        //	Do all replacements at once
        $_result = str_ireplace(
            array_keys($_data),
            array_values($_data),
            $source
        );

        //	Return re-worked source
        return $_result;
    }
}
