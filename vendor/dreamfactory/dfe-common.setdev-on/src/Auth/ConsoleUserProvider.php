<?php namespace DreamFactory\Enterprise\Common\Auth;

/**
 * Provides users for the console logins
 */
class ConsoleUserProvider extends BaseUserProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    protected $userClass = 'DreamFactory\\Enterprise\\Database\\Models\\ServiceUser';
}
