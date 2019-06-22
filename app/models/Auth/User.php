<?php
// app/Models/Auth/User.php
namespace App\Models\Auth;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use App\Application_Model_Constants;
use \Zend\XmlRpc\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Helpers\GatekeeperHelper;

class User implements AuthenticatableContract {
	private $conn;

	public $email;
	public $password;
	public $name;
	protected $rememberTokenName = 'email';

	public function __construct() {
		// $this->conn = $conn;
	}

	/**
	 * Fetch user by Credentials
	 *
	 * @param array $credentials
	 * @return Illuminate\Contracts\Auth\Authenticatable
	 */
	public function fetchUserByCredentials(Array $credentials) {
		$this->email = '';
		$this->password = '';

                $email = $credentials['email'];
                $password = $credentials['password'];

		if ($user = $this->login($email, $password))
		{
			Log::emergency('User: ' . json_encode($user, JSON_PRETTY_PRINT));

			$this->email = $credentials['email'];
			$this->password = $credentials['password'];
			$this->name = "Ron Royce";

			return $this;
		}

		return null;
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::getAuthIdentifierName()
	 */
	public function getAuthIdentifierName() {
		return "email";
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::getAuthIdentifier()
	 */
	public function getAuthIdentifier() {
		return $this->{$this->getAuthIdentifierName()};
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::getAuthPassword()
	 */
	public function getAuthPassword() {
		return $this->password;
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::getRememberToken()
	 */
	public function getRememberToken() {
		if (!empty($this->getRememberTokenName())) {
			return $this->{$this->getRememberTokenName()};
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::setRememberToken()
	 */
	public function setRememberToken($value) {
		if (!empty($this->getRememberTokenName())) {
			$this->{$this->getRememberTokenName()} = $value;
		}
	}

	/**
	 * {@inheritDoc}
	 * @see \Illuminate\Contracts\Auth\Authenticatable::getRememberTokenName()
	 */
	public function getRememberTokenName() {
		return $this->rememberTokenName;
	}

	protected function login($email, $password)
    	{
		$auth_session_xml = GatekeeperHelper::sendRequest('authenticatorlogin',array($email, $password, $_SERVER['REMOTE_ADDR']));

        	$auth_session_object = simplexml_load_string($auth_session_xml);
	        $auth_session_data = get_object_vars($auth_session_object);
        	$auth_status_data = get_object_vars($auth_session_data['status']);
	        $message = $auth_status_data['message'];

	        if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_VALID || $auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CODE_SENT || 
				$auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_GTA_ENABLED || $auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CHANGEPASSWORD) {
			Session::put('user_id',(string)$auth_session_object->user_id);
                        Session::put('user', $email);
                        Session::put('session_id', (string) $auth_session_object->session_id);
                        Session::put('token', (string) $auth_session_object->token);

                        if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_VALID) {
	                        $auth_permissions_data = get_object_vars($auth_session_data['permissions']);
                        	$auth_permissions = GatekeeperHelper::getPermissions($auth_permissions_data);

                            	if ((in_array(env('BUSINESS_REPORTING_PERMISSION', 0), $auth_permissions) === true)) {
                                	GatekeeperHelper::checkPermissionsOfUser($auth_permissions);
	                                Session::put('authenticated', true);
        	                        return redirect('/home');
                	        } 
				else {
                        	        throw new Exception("Invalid permissions to access this application.");
	                        }
       	                } 
			else if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CODE_SENT) {
                       	    	Session::put('isCodeSent', true);
		    		return null;
                        } 
			else if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_GTA_ENABLED) {
                            	Session::put('isAuthenticatorEnabled', true);
                            	$this->_redirect('index/googleauthenticator');
                        } 
			else if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CHANGEPASSWORD) {
                            	Session::put('isChangePassword', true);
                            	$this->_redirect('index/changepassword');
                        }
			else {
				return null;
			}
		} 
		else 
		{
			return null;
   		}
	}
	
}
