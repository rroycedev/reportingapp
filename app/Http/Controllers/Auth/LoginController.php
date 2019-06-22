<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Gatekeeper\GatekeeperAuthConnectException;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller {
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
	public function __construct() {
		$this->middleware('guest')->except('logout');
	}

	public function authenticate() {
		throw new Exception("In authenticate");

		if (Auth::attempt(['email' => $email, 'password' => $password])) {
			// Authentication passed...
			if (Auth::user()->suspended) {
				return redirect('account-suspended');
			}
			return redirect()->intended('dashboard');
		}
	}

	public function login(Request $request) {
		Log::emergency('Inside login');

		$this->validate($request, ['email' => 'required|email', 'password' => 'required']);

		$credentials = array('email' => $request->email, 'password' => $request->password);

		try {
			if (Auth::attempt($credentials, $request->has('remember'))) {
				Log::emergency('Attempt returned success');
				return redirect()->intended($this->redirectPath());
			}

			Log::emergency('Login failed');

			return redirect('/login')
				->withInput($request->only('email', 'remember'))
				->withErrors(array('exception' => 'Invalid credentials'));
		} catch (GatekeeperAuthConnectException $gex) {
			return redirect('/login')
				->withInput($request->only('email', 'remember'))
				->withErrors(array('exception' => "Error connecting to gatekeeper authorization server"));
		} catch (\Exception $ex) {
			return redirect('/login')
				->withInput($request->only('email', 'remember'))
				->withErrors(array('exception' => $ex->getMessage()));
		}

		return redirect('/validate');
	}

}
