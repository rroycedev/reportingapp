<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use PhpXmlRpc\Value;
use PhpXmlRpc\Request as XmlRpcRequest;
use PhpXmlRpc\Client;


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
    protected $redirectTo = '/fsf';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

/*
    public function login(Request $request)
    {

    	$method = 'authenticatorlogin';
    	$parameters = array("rroyce@transunion.com", "mcdoodle22@", $_SERVER['REMOTE_ADDR']);

	$client = new Client('https://auth.tlo.com/AuthService.php');
	$response = $client->send(new XmlRpcRequest($method, $parameters));

	print_r($response);

	exit(1);

    	throw new \Exception('Login failure');

        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password, 'is_activated' => 1])) {
            // return redirect()->intended('dashboard');
        }  else {
            $this->incrementLoginAttempts($request);
            return response()->json([
                'error' => 'This account is not activated.'
            ], 401);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }
*/
}
