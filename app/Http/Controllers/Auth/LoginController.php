<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use PhpXmlRpc\Value;
use PhpXmlRpc\Request as XmlRpcRequest;
use PhpXmlRpc\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticate()
    {
    	throw new Exception("In authenticate");

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Authentication passed...
            if(Auth::user()->suspended){
                return redirect('account-suspended');
            }
            return redirect()->intended('dashboard');
        }
    }

    public function login(Request $request)
    {
    	Log::emergency('Inside login');

	$this->validate($request, [ 'email' => 'required|email', 'password' => 'required']);
/*
	if ($this->auth->validate(['email' => $request->email, 'password' => $request->password, 'status' => 0])) {
            return redirect($this->loginPath())
                ->withInput($request->only('email', 'remember'))
                ->withErrors('Your account is Inactive or not verified');
        }
*/
        $credentials  = array('email' => $request->email, 'password' => $request->password);
        if (Auth::attempt($credentials, $request->has('remember'))){
		Log::emergency('Attempt returned success');
                return redirect()->intended($this->redirectPath());
        }

	Log::emergency('Attempt returned failure');

        return redirect('/validate');
    }

}
