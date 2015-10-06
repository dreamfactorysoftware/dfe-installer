<?php namespace DreamFactory\Enterprise\Common\Auth;

/**
 * Provides users for dashboard users
 */
class DashboardUserProvider extends BaseUserProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string Our user class
     */
    protected $userClass = 'DreamFactory\\Enterprise\\Database\\Models\\User';
}
