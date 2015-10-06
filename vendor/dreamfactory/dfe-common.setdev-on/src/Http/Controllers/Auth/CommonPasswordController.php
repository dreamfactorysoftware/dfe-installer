<?php namespace DreamFactory\Enterprise\Common\Http\Controllers\Auth;

use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;
use DreamFactory\Enterprise\Database\Models\ServiceUser;
use DreamFactory\Enterprise\Database\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommonPasswordController extends BaseController
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use ResetsPasswords;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Create a new password controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /** @inheritdoc */
    public function getEmail()
    {
        return view('dfe-common::auth.password');
    }

    /** @inheritdoc */
    public function getReset($token = null)
    {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }

        return view('dfe-common::auth.reset')->with('token', $token);
    }

    /**
     * @param ServiceUser|User $user
     * @param string           $password
     */
    protected function resetPassword($user, $password)
    {
        $user->password_text = bcrypt($password);
        $user->save();
        \Auth::login($user);
    }

}
