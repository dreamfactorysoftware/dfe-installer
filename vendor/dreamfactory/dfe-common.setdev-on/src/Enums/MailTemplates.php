<?php namespace DreamFactory\Enterprise\Common\Enums;

use DreamFactory\Library\Utility\Enums\FactoryEnum;

/**
 * Templates for email
 */
class MailTemplates extends FactoryEnum
{
    //*************************************************************************
    //* Templates
    //*************************************************************************

    /**
     * @var int
     */
    const SYSTEM_NOTIFICATION = -1;
    /**
     * @var int
     */
    const WELCOME = 0;
    /**
     * @var int
     */
    const PASSWORD_RESET = 1;
    /**
     * @var int
     */
    const NOTIFICATION = 2;
    /**
     * @var int
     */
    const RESEND_CONFIRMATION = 3;
    /**
     * @var int
     */
    const STATUS = 4;
    /**
     * @var int
     */
    const PASSWORD_CHANGED = 5;
    /**
     * @var int
     */
    const PROVISION_COMPLETE = 6;
    /**
     * @var int
     */
    const DEPROVISION_COMPLETE = 6;
    /**
     * @var int
     */
    const FREE_FORM = 100;

    //*************************************************************************
    //* Mail Template Text
    //*************************************************************************

    /**
     * @var string
     */
    const LAUNCH_SUCCESS_TEXT = 'Your instance has been created. You can reach it by going to <a href="http://%%DSP_NAME%%.cloud.dreamfactory.com" target="_blank">http://%%DSP_NAME%%.cloud.dreamfactory.com</a> from any browser.';
    /**
     * @var string
     */
    const LAUNCH_FAILURE_TEXT = 'Your instance launch did not succeed. Our engineers will examine the issue and, if necessary, notify you when it has been resolved.';
    /**
     * @var string
     */
    const DEPROVISION_SUCCESS_TEXT = 'Your instance retirement was successful. If you would like to launch another one, just visit your dashboard.';
    /**
     * @var string
     */
    const DEPROVISION_FAILURE_TEXT = 'Your instance retirement did not succeed. Our engineers will examine the issue and, if necessary, notify you when it has been resolved.';

}
