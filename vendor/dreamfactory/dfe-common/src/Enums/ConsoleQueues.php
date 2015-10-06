<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * The queues used by the DFE console back-end
 */
class ConsoleQueues extends FactoryEnum
{
    //*************************************************************************
    //* Defaults
    //*************************************************************************

    /**
     * @var string The email provisioning queue
     */
    const PROVISION = 'provision';
    /**
     * @var string The email deprovisioning queue
     */
    const DEPROVISION = 'deprovision';
    /**
     * @var string The email notification queue
     */
    const EMAIL_NOTIFICATION = 'email';
    /**
     * @var string The push notification queue
     */
    const PUSH_NOTIFICATION = 'push';
    /**
     * @var string The deployment queue
     */
    const DEPLOY = 'deploy';
}
