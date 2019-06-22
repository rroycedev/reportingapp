<?php


namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\models\Tsop_report;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Application_Model_Constants;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\GatekeeperHelper;
use Illuminate\Support\Facades\Session;

class ValidateController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
	public function index(Request $request)
    	{
		$errors = $request->input('errors');

		if (!isset($errors))
		{
			$errors = "";
		} 

		return view('validate.index', [ "errors" => $errors ]);
    	}

    	public function validatelogin(Request $request) 
    	{
		$all = $request->all();

		Log::emergency('post: ' . json_encode($all, JSON_PRETTY_PRINT));

		$authenticated = $request->session()->get('authenticated');
		$sessionId = $request->session()->get('session_id');
		$verifyCode = isset($all['verify_code']) ? $all['verify_code'] : null;
		$resendCode = isset($all['resend_code']) ? $all['resend_code'] : null;

		$code = isset($all['code']) ? $all['code'] : null;

		if (!$code) {
			return Redirect::route('validate', array('errors' => 'You must specify a code'));
		}

        	if (isset($authenticated)) {
                        return Redirect::route('home');
        	}

        	if (!isset($sessionId)) {
	    		Log::emergency('Session Id not set');
            		return Redirect::route('login', array('errors' => 'Session Id is not set'));
        	}

        	if (isset($verifyCode)) {
                	$auth_session_xml = $this->checkCode($sessionId, $code);
                	if ($auth_session_xml !== false && is_null($auth_session_xml) === false) {
                    		$auth_session_object = simplexml_load_string($auth_session_xml);
                    		$auth_session_data = get_object_vars($auth_session_object);
                    		$auth_status_data = get_object_vars($auth_session_data['status']);
                    		if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_INVALID_CODE) {
					return Redirect::route('validate', array('errors' => "Invalid PIN"));
                 	   	} 
				else {
					try
					{
                        			$auth_permissions = $this->getAuthPermissions($auth_session_xml);
					}
					catch (\Exception $ex)
					{
						return Redirect::route('login', array('errors' => $ex->getMessage()));
					}

					if ((in_array(env('BUSINESS_REPORTING_PERMISSION', 0), $auth_permissions) === true)) {
        	                    		//$this->_session->redirect_to = $this->checkPermissionsOfUser($auth_permissions);
                	            		$request->session()->put('authenticated', true);
	                            		return Redirect::route('home');
        	                	} 
					else {
                        	    		return Redirect::route('login', array('errors' => 'You do not have permissions to access this application'));
                        		}
                    		}
                	} 
			else {
                       		return Redirect::route('login', array('errors' => 'Invalid PIN'));
                	}
       		}
       		else if (isset($resendCode)) {
/*
                $auth_session_xml = $this->_helper->AuthHelper->SendLoginPin($this->_session->session_id);
                if ($auth_session_xml !== false && is_null($auth_session_xml) === false) {
                    $auth_session_object = simplexml_load_string($auth_session_xml);
                    $auth_session_data = get_object_vars($auth_session_object);
                    $auth_status_data = get_object_vars($auth_session_data['status']);
                    if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CODE_SENT) {
                        $this->_session->isCodeSent = true;
                        $this->_redirect("index/validate");
                    } elseif ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_INVALID_SESSION) {
                        $this->_redirect('index/logout/redirect/1');
                    } else {
                        $this->view->error = $auth_status_data['message'];
                    }
                }
*/
            }
	else {
               return response()->json(['errors'=> "Invalid post"]);
	}

   }

	protected function checkCode($session_id, $pin)
        {
		return GatekeeperHelper::sendRequest('checkCode', array(isset($session_id) ? $session_id : '', $pin, $_SERVER['REMOTE_ADDR']));
        }

	public function getAuthPermissions($auth_session_xml = false)
    	{
		$sessionId = Session::get('session_id');

	        $auth_permissions = array();
        
		if ($auth_session_xml === false)
        	{
            		$auth_session_xml = $this->getUserPermission($sessionId,$_SERVER['REMOTE_ADDR']);
        	}
        	if ($auth_session_xml !== false && empty($auth_session_xml) === false)
        	{
            		$auth_session_object = simplexml_load_string($auth_session_xml);

            		$auth_session_data = get_object_vars($auth_session_object);

            		$auth_status_data = get_object_vars($auth_session_data['status']);

            		if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_VALID)
            		{
                		$auth_permissions_data = get_object_vars($auth_session_data['permissions']);

                		if (is_array($auth_permissions_data) === true && array_key_exists('permission', $auth_permissions_data) === true)
                		{
                    			if (is_array($auth_permissions_data['permission']) === true)
                    			{
                        			$auth_permissions = $auth_permissions_data['permission'];
                    			}
                    			else
                    			{
                        			$auth_permissions = array($auth_permissions_data['permission']);
                    			}
                		}
            		}
            		elseif (in_array($auth_status_data['value'], array(Application_Model_Constants::GATEKEEPER_STATUS_CODE_SENT, Application_Model_Constants::GATEKEEPER_STATUS_INVALID_CODE)) === true)
            		{
				throw new \Exception("Invalid PIN code");
            		}
            		elseif (in_array($auth_status_data['value'], array(Application_Model_Constants::GATEKEEPER_STATUS_INVALIDIP, Application_Model_Constants::GATEKEEPER_STATUS_INVALIDCREDS, 
									Application_Model_Constants::GATEKEEPER_STATUS_INVALID_SESSION, Application_Model_Constants::GATEKEEPER_STATUS_LOCKED)) === true)
            		{
				throw new \Exception("Invalid credentials");
            		}
        	}

        	return $auth_permissions;
    	}

	public function getUserPermission($sessionId, $ip_address)
    	{
		return GatekeeperHelper::sendRequest('getSession', array($sessionId,$ip_address));
	}
}


