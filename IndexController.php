<?php
include "CSVFormat.php";
class IndexController extends Base_Controller_Action
{

    const SESSION_NAMESPACE = 'reporting';

    public function indexAction()
    {
        if (($this->getRequest()->isGet()) && $this->_session->authenticated) {
            $this->_redirect('index/reportmanagement');
        } else if ($this->getRequest()->isPost()) {
            $email = trim($this->_getParam('username'));
            $password = $this->_getParam('password');
            if ($email == '' || $password == '') {
                $this->view->error = 'Please provide username and password.';
            } else {
                unset($this->_session->isCodeSent);
                unset($this->_session->isAuthenticatorEnabled);
                $auth_session_xml = $this->_helper->AuthHelper->checkCredentials($email, $password);
                if ($auth_session_xml !== false && is_null($auth_session_xml) === false) {
                    $auth_session_object = simplexml_load_string($auth_session_xml);
                    $auth_session_data = get_object_vars($auth_session_object);
                    $auth_status_data = get_object_vars($auth_session_data['status']);
                    $message = $auth_status_data['message'];
                    if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_VALID || $auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CODE_SENT || $auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_GTA_ENABLED || $auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CHANGEPASSWORD) {
                        $auth_permissions_data = get_object_vars($auth_session_data['permissions']);
                        $auth_permissions = $this->_helper->AuthHelper->getPermissions($auth_permissions_data);
                        $this->_session->user_id = (string) $auth_session_object->user_id;
                        $this->_session->user = $email;
                        $this->_session->session_id = (string) $auth_session_object->session_id;
                        $this->_session->token = (string) $auth_session_object->token;
                        if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_VALID) {
                            if ((in_array($this->_config->businessreporting->permission, $auth_permissions) === true)) {
                                $this->_session->redirect_to = $this->checkPermissionsOfUser($auth_permissions);
                                $this->_session->authenticated = true;
                                $accountId = $this->_getParam('account_id');
                                if ($accountId) {
                                    $this->_session->redirect_to = "anomaly/anomalyexception?account_id=$accountId";
                                }
                                $this->_redirect($this->_session->redirect_to);
                            } else {
                                Zend_Session::namespaceUnset(self::SESSION_NAMESPACE);
                                $this->view->error = "Invalid permissions to access this application.";
                            }
                        } else if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CODE_SENT) {
                            $this->_session->isCodeSent = true;
                            $this->_redirect('index/validate');
                        } else if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_GTA_ENABLED) {
                            $this->_session->isAuthenticatorEnabled = true;
                            $this->_redirect('index/googleauthenticator');
                        } else if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CHANGEPASSWORD) {
                            $this->_session->isChangePassword = true;
                            $this->_redirect('index/changepassword');
                        }
                    } else {
                        $this->view->error = $message;
                    }
                } else {
                    $this->view->error = 'Invalid credentials, please check your entries.';
                }
            }
        }
        $redirect = $this->_getParam('redirect');
        if ($redirect == '1') {
            $this->view->error = "Session has expired. Please login again";
        }
        if ($redirect == '2') {
            $this->view->error = "Invalid permissions to access this application.";
        }
    }
    public function changepasswordAction(){
        try {
            if (isset($this->_session->authenticated)) {
                $this->_redirect('index/reportmanagement');
            }
            if (! isset($this->_session->session_id)) {
                $this->_redirect('index/logout');
            }
            if (isset($_POST['changepassword'])) {
                $current_password = $this->_getParam('current_password');
                $new_password = $this->_getParam('new_password');
                $retype_new_password = $this->_getParam('retype_new_password');
                if($new_password === $retype_new_password){
                    $auth_session_xml = $this->_helper->AuthHelper->changePassword($this->_session->session_id, $current_password, $new_password);
                    if ($auth_session_xml !== false && is_null ( $auth_session_xml ) === false) {
                        $auth_session_object = simplexml_load_string ( $auth_session_xml );
                        $auth_session_data = get_object_vars ( $auth_session_object );
                        $auth_status_data = get_object_vars ( $auth_session_data ['status'] );
                        if ($auth_status_data ['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CHANGEPASSWORDINVALIDCREDS) {
                            $this->view->error = 'Current password was incorrect';
                        }else if($auth_status_data ['value'] == Application_Model_Constants::GATEKEEPER_STATUS_CHANGEPASSWORDRECENT) {
                            $this->view->error = 'Please pick another password since you have used this one recently.';
                        }else if($auth_status_data ['value'] == Application_Model_Constants::GATEKEEPER_STATUS_VALID){
                            $auth_permissions = $this->_helper->AuthHelper->getAuthPermissions ( $auth_session_xml );
                            if ((in_array($this->_config->businessreporting->permission, $auth_permissions) === true)) {
                                $this->_session->redirect_to = $this->checkPermissionsOfUser($auth_permissions);
                                $this->_session->authenticated = true;
                                $this->_redirect($this->_session->redirect_to);
                            } else {
                                Zend_Session::namespaceUnset(self::SESSION_NAMESPACE);
                                $this->view->error = "Invalid permissions to access this application.";
                            }
                        }else{
                            switch ($auth_status_data['value']){
                                case Application_Model_Constants::GATEKEEPER_STATUS_CHANGEPASSWORDSTRENGTH:
                                    $this->view->error = 'Please increase the strength of your password.';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_CHANGEPASSWORDINVALIDCREDS:
                                    $this->view->error = 'Current password was incorrect.';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_CHANGEPASSWORDRECENT:
                                    $this->view->error = 'Please pick another password since you have used this one recently.';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_SHORT_PASSWORD:
                                    $this->view->error = 'Must be at least 8 characters.';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_NOALPHA_PASSWORD:
                                    $this->view->error = 'Did not contain a letter.';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_NODIGIT_PASSWORD:
                                    $this->view->error = 'Did not contain a number.';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_NOSPECIAL_PASSWORD:
                                    $this->view->error = 'Did not contain a Special Character (anything that\'s not a number or a letter).';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_SHORT_NOALPHA_PASSWORD:
                                    $this->view->error = 'Must be at least 8 characters and did not contain a letter.';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_SHORT_NODIGIT_PASSWORD:
                                    $this->view->error = 'Must be at least 8 characters and did not contain a number.';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_SHORT_NOSPECIAL_PASSWORD:
                                    $this->view->error = 'Must be at least 8 characters and did not contain a Special Character (anything that\'s not a number or a letter).';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_NOALPHA_NODIGIT_PASSWORD:
                                    $this->view->error = 'Did not contain a letter and did not contain a number.';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_NOALPHA_NOSPECIAL_PASSWORD:
                                    $this->view->error = 'Did not contain a letter and did not contain a Special Character (anything that\'s not a number or a letter).';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_NODIGIT_NOSPECIAL_PASSWORD:
                                    $this->view->error = 'Did not contain a number and did not contain a Special Character (anything that\'s not a number or a letter).';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_SHORT_NOALPHA_NODIGIT_PASSWORD:
                                    $this->view->error = 'Must be at least 8 characters and did not contain a letter and did not contain a number.';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_SHORT_NOALPHA_NOSPECIAL_PASSWORD:
                                    $this->view->error = 'Must be at least 8 characters and did not contain a letter and did not contain a Special Character (anything that\'s not a number or a letter).';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_SHORT_NODIGIT_NOSPECIAL_PASSWORD:
                                    $this->view->error = 'Must be at least 8 characters and did not contain a number and did not contain a Special Character (anything that\'s not a number or a letter).';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_NOALPHA_NODIGIT_NOSEPCIAL_PASSWORD:
                                    $this->view->error = 'Did not contain a letter and did not contain a number and did not contain a Special Character (anything that\'s not a number or a letter).';
                                    break;
                                case Application_Model_Constants::GATEKEEPER_STATUS_SHORT_NOALPHA_NODIGIT_NOSEPCIAL_PASSWORD:
                                    $this->view->error = 'Must be at least 8 characters and did not contain a letter and did not contain a number and did not contain a Special Character (anything that\'s not a number or a letter).';
                                    break;
                                default:
                                    $this->view->error = 'Unkown error';
                                    $this->_redirect('index/logout');
                                    break;
                            }
                        }
                    }
                }else{
                    $this->view->error = "Please enter your new password twice, making sure they match.";
                }
            }
        } catch ( Exception $e ) {
            Zend_Registry::get ( 'logger' )->log ( 'Exception : ' . $e->getMessage () . $e->getTraceAsString (), LOG_ERR );
        }

    }
	public function googleauthenticatorAction() {
        try {
            if (isset($this->_session->authenticated)) {
                $this->_redirect('index/reportmanagement');
            }
            if (! isset($this->_session->session_id)) {
                $this->_redirect('index/logout');
            }
            if (isset($_POST['verify_code'])) {
                $token = $this->_getParam('code');
                if ($token == '') {
                    $this->view->error = 'Please provide Authenticator Code to verify.';
                } else {
                    $auth_session_xml = $this->_helper->AuthHelper->verifyAuthSecret($this->_session->session_id, $token);
                    if ($auth_session_xml !== false && is_null($auth_session_xml) === false) {
                        $auth_session_object = simplexml_load_string($auth_session_xml);
                        $auth_session_data = get_object_vars($auth_session_object);
                        $auth_status_data = get_object_vars($auth_session_data['status']);
                        if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_GTA_INVALID_CODE) {
                            $this->view->error = 'Invalid Authenticator Code.';
                        } else if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_GTA_NOT_REGISTERED) {
                            $this->_redirect('index/logout');
                        } else if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_VALID) {
                            $auth_permissions = $this->_helper->AuthHelper->getAuthPermissions($auth_session_xml);
                            if ((in_array($this->_config->businessreporting->permission, $auth_permissions) === true)) {
                                $this->_session->redirect_to = $this->checkPermissionsOfUser($auth_permissions);
                                $this->_session->authenticated = true;
                                $this->_redirect($this->_session->redirect_to);
                            } else {
                                Zend_Session::namespaceUnset(self::SESSION_NAMESPACE);
                                $this->view->error = "Invalid permissions to access this application.";
                            }
                        }
                    } else {
                        $this->view->error = 'Invalid Authenticator Code.';
                    }
                }
            } else if (isset($_POST['use_sms_code'])) {
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
            }
        } catch (Exception $e) {
            Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString(), LOG_ERR);
        }
    }
	public function validateAction() {
        if (isset($this->_session->authenticated)) {
            $this->_redirect('index/reportmanagement');
        }

        if (! isset($this->_session->session_id)) {
            $this->_redirect('index/logout');
        }
        if (isset($_POST['verify_code'])) {
            $code = $this->_getParam('code');
            if ($code == '') {
                $this->view->error = 'Please provide PIN CODE to verify.';
            } else {
                $auth_session_xml = $this->_helper->AuthHelper->checkCode($this->_session->session_id, $code);
                if ($auth_session_xml !== false && is_null($auth_session_xml) === false) {
                    $auth_session_object = simplexml_load_string($auth_session_xml);
                    $auth_session_data = get_object_vars($auth_session_object);
                    $auth_status_data = get_object_vars($auth_session_data['status']);
                    if ($auth_status_data['value'] == Application_Model_Constants::GATEKEEPER_STATUS_INVALID_CODE) {
                        $this->view->error = 'Invalid PIN.';
                    } else {
                        $auth_permissions = $this->_helper->AuthHelper->getAuthPermissions($auth_session_xml);
                        if ((in_array($this->_config->businessreporting->permission, $auth_permissions) === true)) {
                            $this->_session->redirect_to = $this->checkPermissionsOfUser($auth_permissions);
                            $this->_session->authenticated = true;
                            $this->_redirect($this->_session->redirect_to);
                        } else {
                            Zend_Session::namespaceUnset(self::SESSION_NAMESPACE);
                            $this->view->error = "Invalid permissions to access this application.";
                        }
                    }
                } else {
                    $this->view->error = 'Invalid PIN';
                }
            }
        }

        if (isset($_POST['resend_code'])) {
            $auth_session_xml = $this->_helper->AuthHelper->resendCode($this->_session->session_id);
            $this->view->error = 'PIN Code Resent';
        }
        if (isset($_POST['google_auth'])) {
            $this->_redirect("index/googleauthenticator");
        }
    }

    public function checkPermissionsOfUser($auth_permissions)
    {
        if ((in_array($this->_config->businessreporting->reportserver->access, $auth_permissions) === true)) {
            $this->_session->is_reportserver_access = true;
            if ((in_array($this->_config->businessreporting->reportmanagement->edit, $auth_permissions) === true)) {
                $this->_session->is_reportmanagement_editaccess = true;
            } else if ((in_array($this->_config->businessreporting->reportmanagement->view, $auth_permissions) === true)) {
                $this->_session->is_reportmanagement_viewaccess = true;
            }
            if ((in_array($this->_config->businessreporting->advanced->edit, $auth_permissions) === true)) {
                $this->_session->is_reportadvanced_edit = true;
            }
            $this->_session->redirect_to = 'index/reportmanagement';
        }
        if ((in_array($this->_config->businessreporting->groupserver->access, $auth_permissions) === true)) {
            $this->_session->is_groupserver_access = true;
            if ((in_array($this->_config->businessreporting->groupmanagement->edit, $auth_permissions) === true)) {
                $this->_session->is_groupmanagement_editaccess = true;
            } else if ((in_array($this->_config->businessreporting->groupmanagement->view, $auth_permissions) === true)) {
                $this->_session->is_groupmanagement_viewaccess = true;
            }
            if (empty($this->_session->redirect_to)) {
                $this->_session->redirect_to = 'index/groupmanagement';
            }
        }
        if ((in_array($this->_config->businessreporting->tsopstatus->access, $auth_permissions) === true)) {
            $this->_session->is_tsopstatus_access = true;
            if ((in_array($this->_config->businessreporting->tsopstatus->view, $auth_permissions) === true)) {
                $this->_session->is_tsopstatus_viewaccess = true;
            }
        }
        if ((in_array($this->_config->businessreporting->ledgerreport->access, $auth_permissions) === true)) {
            $this->_session->is_ledgerreport_access = true;
            if ((in_array($this->_config->businessreporting->ledgerreport->view, $auth_permissions) === true)) {
                $this->_session->is_ledgerreport_viewaccess = true;
            }
        }
        if ((in_array($this->_config->businessreporting->employeemanagement->access, $auth_permissions) === true)) {
            $this->_session->is_employeemanagement_access = true;
            if ((in_array($this->_config->businessreporting->employeemanagement->edit, $auth_permissions) === true)) {
                $this->_session->is_employeemanagement_editaccess = true;
            } else if ((in_array($this->_config->businessreporting->employeemanagement->view, $auth_permissions) === true)) {
                $this->_session->is_employeemanagement_viewaccess = true;
            }
            if (empty($this->_session->redirect_to)) {
                $this->_session->redirect_to = 'bizops/employeemanagement';
            }
        }
        if ((in_array($this->_config->businessreporting->territorymanagement->access, $auth_permissions) === true)) {
            $this->_session->is_territorymanagement_access = true;
            if ((in_array($this->_config->businessreporting->territorymanagement->edit, $auth_permissions) === true)) {
                $this->_session->is_territorymanagement_editaccess = true;
            } else if ((in_array($this->_config->businessreporting->territorymanagement->view, $auth_permissions) === true)) {
                $this->_session->is_territorymanagement_viewaccess = true;
            }
            if ((in_array($this->_config->businessreporting->territorymanagement->export, $auth_permissions) === true)) {
                $this->_session->is_territorymanagement_export = true;
            }
            if (empty($this->_session->redirect_to)) {
                $this->_session->redirect_to = 'bizops/territorymanagement';
            }
        }
        if ((in_array($this->_config->businessreporting->anoamlysetup->access, $auth_permissions) === true)) {
            $this->_session->is_anoamlysetup_access = true;
            if ((in_array($this->_config->businessreporting->anoamlysetup->edit, $auth_permissions) === true)) {
                $this->_session->is_anoamlysetup_editaccess = true;
            }
            else if ((in_array($this->_config->businessreporting->anoamlysetup->view, $auth_permissions) === true)) {
                $this->_session->is_anoamlysetup_viewaccess = true;
            }
            if ((in_array($this->_config->businessreporting->anoamlysetup->sqledit, $auth_permissions) === true)) {
                $this->_session->is_anoamlysetup_sqleditaccess = true;
            } 
            if (empty($this->_session->redirect_to)) {
                $this->_session->redirect_to = 'anomaly/anomalysetup';
            }
        }
        if ((in_array($this->_config->businessreporting->anomalyexception->access, $auth_permissions) === true)) {
            $this->_session->is_anomalyexception_access = true;
            if ((in_array($this->_config->businessreporting->anomalyexception->edit, $auth_permissions) === true)) {
                $this->_session->is_anomalyexception_editaccess = true;
            } else if ((in_array($this->_config->businessreporting->anomalyexception->view, $auth_permissions) === true)) {
                $this->_session->is_anomalyexception_viewaccess = true;
            }
            if (empty($this->_session->redirect_to)) {
                $this->_session->redirect_to = 'anomaly/anomalyexception';
            }
        }
        if ((in_array($this->_config->businessreporting->transactionhistory->access, $auth_permissions) === true)) {
            $this->_session->is_transactiontraceback_access = true;
            if ((in_array($this->_config->businessreporting->transactionhistory->edit, $auth_permissions) === true)) {
                $this->_session->is_transactiontraceback_editaccess = true;
            }
            if (empty($this->_session->redirect_to)) {
                $this->_session->redirect_to = 'anomaly/transactiontraceback';
            }
        }
        if ((in_array($this->_config->businessreporting->anomalyreport->access, $auth_permissions) === true)) {
            $this->_session->is_anomalyreport_access = true;
            if ((in_array($this->_config->businessreporting->anomalyreport->edit, $auth_permissions) === true)) {
                $this->_session->is_anomalyreport_editaccess = true;
            }
            if (empty($this->_session->redirect_to)) {
                $this->_session->redirect_to = 'anomaly/anomalyreport';
            }
        }
        if ((in_array($this->_config->businessreporting->iptracking->access, $auth_permissions) === true)) {
            $this->_session->is_iptracking_access = true;
            if ((in_array($this->_config->businessreporting->iptracking->edit, $auth_permissions) === true)) {
                $this->_session->is_iptracking_editaccess = true;
            }
            if (empty($this->_session->redirect_to)) {
                $this->_session->redirect_to = 'anomaly/iptracking';
            }
        }
        if ((in_array($this->_config->businessreporting->ledgerreportapproval->access, $auth_permissions) === true)) {
            $this->_session->is_ledgerreportapproval_access = true;
            if ((in_array($this->_config->businessreporting->ledgerreportapproval->edit, $auth_permissions) === true)) {
                $this->_session->is_ledgerreportapproval_editaccess = true;
            }else if ((in_array($this->_config->businessreporting->ledgerreportapproval->view, $auth_permissions) === true)) {
                $this->_session->is_ledgerreportapproval_viewaccess = true;
            }
            if (empty($this->_session->redirect_to)) {
                $this->_session->redirect_to = 'finance/ledgerreportapproval';
            }
        }
        return $this->_session->redirect_to;
    }

    public function logoutAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            Zend_Controller_Action_HelperBroker::getStaticHelper('AuthHelper')->getUserLogout($this->_session->session_id, $_SERVER['REMOTE_ADDR']);
            Zend_Session::namespaceUnset(self::SESSION_NAMESPACE);
            Zend_Session::destroy(true);
            $redirect = $this->_getParam('redirect');
            if ($redirect == 1 || $redirect == 2) {
                $this->_redirect('index/index/redirect/' . $redirect);
            } else {
                $this->_redirect('index/index');
            }
        } catch (Exception $e) {
            Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString(), LOG_ERR);
        }
    }

    public function groupmanagementAction()
    {
        try {
            $tsopgroup = $this->_modelFactory->TsopGroup();
            $group_data = $tsopgroup->getTableDataTsopGroup();
            $this->view->groupdata = $group_data;
        } catch (Exception $e) {
            Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString(), LOG_ERR);
        }
    }

    public function groupmanagementactivitiesAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            $result = array(
                'status' => 'error',
                'message' => ' ',
                'data' => array()
            );
            $action = $_REQUEST['type'];
            $tsopgroup = $this->_modelFactory->TsopGroup();
            switch ($action) {
                case 'addGroup':
                    if (isset($_REQUEST['groupName']) && isset($_REQUEST['groupEmails'])) {
                        if (! $tsopgroup->checkgroupexistence($_REQUEST['groupName'], null)) {
                            $groupEmails = implode(",", json_decode($_REQUEST['groupEmails']));
                            $groupId = $tsopgroup->SaveGroupDetails($_REQUEST['groupName'], $groupEmails);
                            if (! empty($groupId)) {
                                $result['status'] = 'success';
                                $result['message'] = 'Successfully added new group.';
                                $result['data'] = array(
                                    'groupId' => $groupId
                                );
                            } else {
                                $result['message'] = 'Error while added the group.';
                            }
                        } else {
                            $result['status'] = 'warning';
                            $result['message'] = 'Given group name already exist. Please provide another group name.';
                        }
                    } else {
                        $result['message'] = 'Details are invaild. Please provide valid group name and email addresses.';
                    }
                    break;
                case 'updateGroup':
                    if (isset($_REQUEST['groupId']) && isset($_REQUEST['groupName']) && isset($_REQUEST['groupEmails'])) {
                        if (! $tsopgroup->checkgroupexistence($_REQUEST['groupName'], $_REQUEST['groupId'])) {
                            $groupEmails = implode(",", json_decode($_REQUEST['groupEmails']));
                            $groupDetails = $tsopgroup->updategroupdetails($_REQUEST['groupId'], $_REQUEST['groupName'], $groupEmails);
                            if (! empty($groupDetails)) {
                                $result['status'] = 'success';
                                $result['message'] = 'Group details are updated successfully.';
                                $result['data'] = array(
                                    'groupId' => $_REQUEST['groupId'],
                                    'groupName' => $_REQUEST['groupName'],
                                    'groupEmails' => $_REQUEST['groupEmails']
                                );
                            } else {
                                $result['message'] = 'Error while updating the group details.';
                            }
                        } else {
                            $result['status'] = 'warning';
                            $result['message'] = 'Given group name already exist. Please provide another group name.';
                        }
                    } else {
                        $result['message'] = 'Details are invaild. Please provide valid group name and email addresses.';
                    }
                    break;
                case 'deleteGroup':
                    break;
                case 'getGroupDetails':
                    if (isset($_REQUEST['groupId'])) {
                        $groupDetails = $tsopgroup->getGroupDetails($_REQUEST['groupId']);
                        if (count($groupDetails) > 0) {
                            $result['status'] = 'success';
                            $result['message'] = 'Group details';
                            $result['data'] = array(
                                'groupId' => $groupDetails['Id'],
                                'groupName' => $groupDetails['name'],
                                'groupEmails' => $groupDetails['email']
                            );
                        } else {
                            $result['message'] = 'No records are found with the given group Id.';
                        }
                    } else {
                        $result['message'] = 'Details are invaild. Please provide valid group Id.';
                    }
                    break;
                default:
                    $result['status'] = 'warning';
                    $result['message'] = 'Invalid operation call.';
                    $result['data'] = array();
            }
            print_r(json_encode($result));
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['message'] = 'Error while handling request.';
            $result['data'] = array();
            print_r(json_encode($result));
            Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString(), LOG_ERR);
        }
    }

    public function reportmanagementAction()
    {
        try {
            $tsopreport = $this->_modelFactory->TsopReport();
            $tsopscheduler = $this->_modelFactory->TsopScheduler();
            $tsopreportrecipients = $this->_modelFactory->TsopReportRecipients();
            $manage_reports = $this->_modelFactory->ManageReports();
            $this->view->reportsList = $tsopreport->getTableDataTsopReport(null);
            $selected_group_data = $manage_reports->getTableGroupDetails();
            $this->view->showGroupDetails = $selected_group_data;
            $selected_file_type_data = $manage_reports->getTableFileType();
            $this->view->showFileType = $selected_file_type_data;
            $selected_parameter_value_data = $manage_reports->getTableParameterValue();
            $this->view->showParameterValue = $selected_parameter_value_data;
            $selected_schedule_frequency_data = $manage_reports->getTableScheduleFrequency();
            $this->view->showScheduleFrequency = $selected_schedule_frequency_data;
            $selected_spid_data = $manage_reports->getTableTsopSPID();
            $this->view->ShowSPID = $selected_spid_data;
            $this->view->domainName = $this->_config->confluence->documentation->domain->name;
	    $executionTypes = $tsopreport->getExecutionTypes();
	    $this->view->executionTypes = $executionTypes;
        } catch (Exception $e) {
            Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString(), LOG_ERR);
        }
    }

    public function reportmanagementactivitiesAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            $result = array(
                'status' => 'error',
                'message' => ' ',
                'data' => array()
            );
            $tsopreport = $this->_modelFactory->TsopReport();
            $tsopscheduler = $this->_modelFactory->TsopScheduler();
            $tsopreportrecipients = $this->_modelFactory->TsopReportRecipients();
            $tsopcontroler = $this->_modelFactory->TsopControl();
            $action = $_REQUEST['type'];
            switch ($action) {
                case 'addReport':
                case 'updateReport':
                    $time = isset($_REQUEST['time']) ? $_REQUEST['time'] : '';
                    $priority = isset($_REQUEST['priority']) ? $_REQUEST['priority'] : '';
                    $reportId = isset($_REQUEST['reportId']) ? $_REQUEST['reportId'] : null;
                    $start_date = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
                    $report_name = isset($_REQUEST['reportName']) ? $_REQUEST['reportName'] : '';
                    $confluenceUrl = isset($_REQUEST['confluenceUrl']) ? $_REQUEST['confluenceUrl'] : '';
                    $day_in_title = isset($_REQUEST['dayInTitle']) ? $_REQUEST['dayInTitle'] : null;
                    $schedule_frequency = isset($_REQUEST['frequency']) ? $_REQUEST['frequency'] : '';
                    $scheduled_day = isset($_REQUEST['scheduledDay']) ? $_REQUEST['scheduledDay'] : '';
                    $ignore_replication_lag = isset($_REQUEST['replication']) ? $_REQUEST['replication'] : 0;
                    $multiselect_values = (is_array($_REQUEST['reportGroups']) && count($_REQUEST['reportGroups']) > 0) ? array_filter($_REQUEST['reportGroups']) : array();
                    if ($report_name != '' && $schedule_frequency != '') {
                        if (($tsopreport->checkReportExistence($report_name, $reportId)) === false) {
                            if ($action == 'addReport') {
                                $reportId = $tsopreport->saveReport($report_name, $confluenceUrl, $day_in_title, $ignore_replication_lag);
                                if (! empty($reportId)) {
                                    $saveschedulerdetails = $tsopscheduler->saveTsopScheduler($reportId, $schedule_frequency, $scheduled_day, $start_date, $priority, $time);
                                    if (count($multiselect_values) > 0) {
                                        $savereportrecipientdetails = $tsopreportrecipients->saveTsopReportRecipients($reportId, $multiselect_values);
                                    }
                                    $result['status'] = 'success';
                                    $result['message'] = 'Successfully added new report.';
                                    $result['data'] = array(
                                        'reportId' => $reportId,
                                        'domainName' => $this->_config->confluence->documentation->domain->name
                                    );
                                } else {
                                    $result['status'] = 'warning';
                                    $result['message'] = 'Error while adding the report.';
                                }
                            } elseif ($action == 'updateReport') {
                                $reportDetails = $tsopreport->updateReport($reportId, $report_name, $confluenceUrl, $day_in_title, $ignore_replication_lag);
                                if ($reportDetails) {
                                    if ($tsopscheduler->checkschedulerexistence($reportId)['count'] == 0) {
                                        $saveschedulerdetails = $tsopscheduler->saveTsopScheduler($reportId, $schedule_frequency, $scheduled_day, $start_date, $priority, $time);
                                    } else {
                                        $saveschedulerdetails = $tsopscheduler->updateTsopScheduler($reportId, $schedule_frequency, $scheduled_day, $start_date, $priority, $time);
                                    }
                                    $totalgroupidtsopreportrecipients = $tsopreportrecipients->getTsoReportrecipientsData($reportId);
                                    $groupid_recipients_array = is_array($totalgroupidtsopreportrecipients) ? array_column($totalgroupidtsopreportrecipients, 'group_name') : array();
                                    if (count($groupid_recipients_array) > 0) {
                                        $removereportrecipients = array_diff($groupid_recipients_array, $multiselect_values);
                                        $insertreportrecipients = array_diff($multiselect_values, $groupid_recipients_array);
                                        if (count($removereportrecipients) > 0)
                                            $deltereportrecipients = $tsopreportrecipients->deleteTsopReportRecipients($reportId, $removereportrecipients);
                                        if (count($insertreportrecipients) > 0)
                                            $savereportrecipientdetails = $tsopreportrecipients->saveTsopReportRecipients($reportId, $insertreportrecipients);
                                    } else {
                                        if (count($multiselect_values) > 0)
                                            $savereportrecipientdetails = $tsopreportrecipients->saveTsopReportRecipients($reportId, $multiselect_values);
                                    }
                                    $result['status'] = 'success';
                                    $result['message'] = 'Report details are updated successfully.';
                                    $result['data'] = array(
                                        'domainName' => $this->_config->confluence->documentation->domain->name
                                    );
                                } else {
                                    $result['status'] = 'warning';
                                    $result['message'] = 'Error while updating the report details.';
                                }
                            }
                        } else {
                            $result['status'] = 'warning';
                            $result['message'] = 'Given report name already exist. Please provide another report name.';
                        }
                    } else {
                        $result['message'] = 'Details are invaild. Please provide valid report name and schedule frequency.';
                    }
                    break;
                case 'deactivateReport':
                    if (isset($_REQUEST['reportId'])) {
                        $delete_report = $tsopreport->changeReportStatus($_REQUEST['reportId'], 0);
                        if ($delete_report == true) {
                            $result['status'] = 'success';
                            $result['message'] = 'Report details are deactivated successfully.';
                        } else {
                            $result['message'] = 'Error while deactivating the report.';
                        }
                    } else {
                        $result['message'] = 'Details are invaild. Please provide valid report id.';
                    }
                    break;
                case 'activateReport':
                    if (isset($_REQUEST['reportId'])) {
                        $delete_report = $tsopreport->changeReportStatus($_REQUEST['reportId'], 1);
                        if ($delete_report == true) {
                            $result['status'] = 'success';
                            $result['message'] = 'Report details are activated successfully.';
                        } else {
                            $result['message'] = 'Error while activating the report.';
                        }
                    } else {
                        $result['message'] = 'Details are invaild. Please provide valid report id.';
                    }
                    break;
                case 'getReportDetails':
                    if (isset($_REQUEST['reportId'])) {
                        $reportDetails = $tsopreport->getTsoReportData($_REQUEST['reportId']);
                        if (count($reportDetails) > 0) {
                            $result['status'] = 'success';
                            $result['message'] = 'Report details';
                            $result['data'] = array(
                                'reportId' => $reportDetails["Id"],
                                'reportName' => $reportDetails["name"],
                                'confluenceUrl' => $reportDetails["help_url"],
                                'frequency' => $reportDetails["frequency"],
                                'dayInTitle' => $reportDetails["day_in_title"],
                                'time' => $reportDetails["time"],
                                'priority' => $reportDetails["priority"],
                                'scheduledDay' => $reportDetails["scheduled_day"],
                                'startDate' => $reportDetails["start_date"],
                                'replication' => $reportDetails["ignore_replication_lag"],
                                'reportGroups' => $reportDetails["group_name"]
                            );
                        } else {
                            $result['message'] = 'No records are found with the given report id.';
                        }
                    } else {
                        $result['message'] = 'Details are invaild. Please provide valid report id.';
                    }
                    break;
                case 'runReport':
                    if (isset($_REQUEST['reportId']) && count($_REQUEST['reportId']) > 0) {
                        $run_reports = $tsopscheduler->updateRunNow($_REQUEST['reportId']);
                        if ($run_reports == true) {
                            $response = $tsopcontroler->deleteTsopControlDetails($_REQUEST['reportId']);
                            if ($response == true) {
                                $result['status'] = 'success';
                                $result['message'] = 'Run report executed successfully.';
                            } else {
                                $result['status'] = 'warning';
                                $result['message'] = 'Run report not executed successfully.';
                            }
                        } else {
                            $result['message'] = 'Error while executing the run report.';
                        }
                    } else {
                        $result['message'] = 'Details are invaild. Please provide valid report id.';
                    }
                    break;
            }
            print_r(json_encode($result));
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['message'] = 'Error while handling request.';
            $result['data'] = array();
            print_r(json_encode($result));
            Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString(), LOG_ERR);
        }
    }

    private function validReportFileRequest($action) {
	switch ($action)
	{
	case "reportfiles":
		return isset($_REQUEST['reportId']);
        case 'addReportFile':
        case 'updateReportFile':
		if ($_REQUEST['executionTypeName'] == 'Tableau')
		{
			return isset($_REQUEST['reportFileId']) && isset($_REQUEST['reportFileName']) && isset($_REQUEST['reportFileURL']) && isset($_REQUEST['reportFileType'])
                                && isset($_REQUEST['sequence']) && isset($_REQUEST['executiontype']);
		}
		else if ($_REQUEST['executionTypeName'] == 'PHP')
		{
			return isset($_REQUEST['scriptpath']);
		}
		else
		{
			return true;
		}
        case 'getReportFileDetails':
        case 'deactivateReportFile':
        case 'activateReportFile':
		return isset($_REQUEST['reportFileId']);
	default:
		return true;
	}

    }

    public function reportfileactivitiesAction()
    {
        try {
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();
            $result = array(
                'status' => 'error',
                'message' => ' ',
                'data' => array()
            );
            $action = $_REQUEST['type'];
            $file_details = $this->_modelFactory->ReportFiles();
            $tsop_parameter = $this->_modelFactory->TsopParameter();

	    if (!$this->validReportFileRequest($action))
	    {
                $result['message'] = 'Missing request parameters';
		print_r(json_encode($result));
		return;
	    }

            $specialChars = $this->_config->form->filenamemaskallowedspecialchars;

	    $inputValidationHelper = Zend_Controller_Action_HelperBroker::getStaticHelper ( 'InputValidationHelper' );

            switch ($action) {
                case 'reportfiles':
                        $reportFileDetails = $file_details->getReportFilesData($_REQUEST['reportId']);
                        $report_file_domain_name = $this->_config->domain->name;
                        if (count($reportFileDetails) > 0) {
                            $result['status'] = 'success';
                            $result['message'] = 'Report file details';
                            $result['data'] = array(
                                'reportFiles' => $reportFileDetails,
                                'DomainURL' => $this->_config->domain->name
                            );
                        } else {
                            $result['message'] = 'No records are found with the given report id.';
                        }
                    break;
                case 'addReportFile':
                case 'updateReportFile':
		    if (!empty($_REQUEST['filenamemask']) && !$inputValidationHelper->validateFilenameMask($_REQUEST['filenamemask']))
		    {
			$result['message'] = "Invalid filename mask.  Valid characters are alphanumeric and the following special characters:<br><br><bold><font style=\"color: blue;\">$specialChars</font></bold>";
	                print_r(json_encode($result));
        	        return;
		    }

                    $form_data = array(
                        'file_name' => isset($_REQUEST['reportFileName']) ? $_REQUEST['reportFileName'] : '',
                        'url' => isset($_REQUEST['reportFileURL']) ? $_REQUEST['reportFileURL'] : '',
                        'sp_needed' => (isset($_REQUEST['spNeeded']) && ($_REQUEST['spNeeded'] == 1 || $_REQUEST['spNeeded'] == 0)) ? $_REQUEST['spNeeded'] : null,
                        'spid' => (isset($_REQUEST['spId']) && ! empty($_REQUEST['spId'])) ? $_REQUEST['spId'] : null,
                        'file_type' => (isset($_REQUEST['reportFileType']) && strlen($_REQUEST['reportFileType']) > 0) ? $_REQUEST['reportFileType'] : null,
                        'embed_attachment' => isset($_REQUEST['embedAttachment']) ? $_REQUEST['embedAttachment'] : null,
                        'send_blank' => (isset($_REQUEST['sendBlank']) && $_REQUEST['sendBlank'] == 1) ? 1 : 0,
                        'template_file' => (isset($_REQUEST['templateName']) && $_REQUEST['templateName'] != null) ? $_REQUEST['templateName'] : null,
			'sequence' => (isset($_REQUEST['sequence']) && strlen($_REQUEST['sequence']) > 0) ? $_REQUEST['sequence'] : 0,
			'async' => isset($_REQUEST['async']) ? $_REQUEST['async'] : 0,
			'file_name_mask' => (isset($_REQUEST['filenamemask']) && $_REQUEST['filenamemask'] != null) ? $_REQUEST['filenamemask'] : null,
			'execution_type' => $_REQUEST['executiontype'],
                        'script_path' => (isset($_REQUEST['scriptpath']) && $_REQUEST['scriptpath'] != null) ? $_REQUEST['scriptpath'] : null
                    );
                    if ($action == 'addReportFile')
		    {
                        $form_data['report_Id'] = $_REQUEST['reportId'];
                        $reportFileId = $file_details->AddReportFile($form_data);
                        if (! empty($reportFileId)) {
                            $result['status'] = 'success';
                            $result['message'] = 'Successfully added new report file.';
                            $result['data'] = array(
                                'reportFileId' => $reportFileId,
                                'DomainURL' => $this->_config->domain->name
                            );
                            if (isset($_REQUEST['parameters']) && ! empty($_REQUEST['parameters']) && is_array($_REQUEST['parameters'])) {
                                $parameters = $_REQUEST['parameters'];
                                foreach ($parameters as $key => $parameter) {
                                    if ($parameter['pNew'] == "true" && $parameter['pDelete'] == "false") {
                                        $tsop_parameter->insertParameter($reportFileId, $parameter);
                                    }
                                }
                            }
                        } else {
                            $result['status'] = 'warning';
                            $result['message'] = 'Error while adding the report file.';
                        }
                    } 
		    else
		    { 
                        $fileSize = false;
                        $files_data = array();
                        if (isset($_FILES) && count($_FILES) > 0 && $_REQUEST['templateName'] != null) {
                            foreach ($_FILES as $file) {
                                $fileSize = $file["size"];
                                if ($fileSize > 0 && isset($file['tmp_name'])) {
                                    $fileContent = file_get_contents($file['tmp_name']);
                                    $files_data = array(
                                        'template_data' => $fileContent
                                    );
                                }
                            }
                        } elseif ($_REQUEST['fileName'] == '' && count($_FILES) == 0) {
                            $files_data = array(
                                'template_data' => null
                            );
                        }
                        $data = array_merge($form_data, $files_data);
                        $updated_file_details = $file_details->UpdateFileDataById($_REQUEST['reportFileId'], $data);
                        if (! empty($updated_file_details)) {
                            $result['status'] = 'success';
                            $result['message'] = 'Report file details are updated successfully.';
                            $result['data'] = array(
                                'DomainURL' => $this->_config->domain->name
                            );
                            if (isset($_REQUEST['parameters']) && ! empty($_REQUEST['parameters']) && is_array($_REQUEST['parameters'])) {
                                $parameters = $_REQUEST['parameters'];
                                foreach ($parameters as $key => $parameter) {
                                    if ($parameter['pNew'] == "true" && $parameter['pDelete'] == "false") {
                                        $tsop_parameter->insertParameter($_REQUEST['reportFileId'], $parameter);
                                    } else if ($parameter['pNew'] == "false" && $parameter['pDelete'] == "true") {
                                        $tsop_parameter->deleteParameter($parameter['pId']);
                                    } else if ($parameter['pNew'] == "false" && $parameter['pDelete'] == "false") {
                                        $tsop_parameter->updateParameter($_REQUEST['reportFileId'], $parameter);
                                    }
                                }
                            }
                        } else {
                            $result['status'] = 'warning';
                            $result['message'] = 'Error while updating the report file.';
                        }
                    } 
                    break;
                case 'deleteReportFile':
                    break;
                case 'getReportFileDetails':
                        $reportFileDetails = $file_details->getFileData($_REQUEST['reportFileId']);
                        $parameters = $tsop_parameter->getParameters($_REQUEST['reportFileId']);
                        if (count($reportFileDetails) > 0) {
                            $result['status'] = 'success';
                            $result['message'] = 'Report file details';
                            $result['data'] = array(
                                'reportId' => $reportFileDetails["report_Id"],
                                'reportFileId' => $reportFileDetails["Id"],
                                'reportFileName' => $reportFileDetails["file_name"],
                                'reportFileURL' => $reportFileDetails["url"],
                                'spNeeded' => $reportFileDetails["sp_needed"],
                                'spId' => $reportFileDetails["spid"],
                                'reportFileType' => $reportFileDetails["file_type"],
                                'embedAttachment' => $reportFileDetails["embed_attachment"],
                                'sendBlank' => $reportFileDetails["send_blank"],
                                'parameters' => $parameters,
                                'templateFile' => $reportFileDetails['template_file'],
                                'templateData' => $reportFileDetails['template_data'],
				'sequence' => $reportFileDetails['sequence'],
				'async' => $reportFileDetails['async'],
				'filenamemask' => $reportFileDetails['file_name_mask'],
				'executiontype' => $reportFileDetails['execution_type'],
				'scriptpath' => $reportFileDetails['script_path']
                            );
                        } else {
                            $result['message'] = 'No records are found with the given file Id.';
                        }
                    break;
                case 'deactivateReportFile':
                        $delete_report = $file_details->changeReportFileStatus($_REQUEST['reportFileId'], 0);
                        if ($delete_report == true) {
                            $result['status'] = 'success';
                            $result['message'] = 'Report file details are deactivated successfully.';
                        } else {
                            $result['message'] = 'Error while deactivating the report file.';
                        }
                    break;
                case 'activateReportFile':
                        $delete_report = $file_details->changeReportFileStatus($_REQUEST['reportFileId'], 1);
                        if ($delete_report == true) {
                            $result['status'] = 'success';
                            $result['message'] = 'Report file details are activated successfully.';
                        } else {
                            $result['message'] = 'Error while activating the report file.';
                        }
                    break;
            }
            print_r(json_encode($result));
        } catch (Exception $e) {
            $result['status'] = 'error';
            $result['message'] = 'Error while handling request.';
            $result['data'] = array();
            print_r(json_encode($result));
            Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString(), LOG_ERR);
        }
    }
    public function getfileAction()
    {
        try {
            $fileModel = $this->_modelFactory->ReportFiles();
            $id = $this->_getParam('doc_id');
            $filename = $this->_getParam('filename');
            $docdetails = $fileModel->getTemplateFileData($id, $filename);
            if ($docdetails['template_data'] != null) {
                echo $docdetails['template_data'];
                header('Content-type: application/octet-stream');
                exit();
            } else {
                echo "<h2>File Not found!</h2>";
                exit();
            }
        } catch (Exception $e) {
            Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString(), LOG_ERR);
        }
    }

    public function uploadfileactivitiesAction(){
        try{
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->layout->disableLayout();
                $result = array(
                    'status' => 'success',
                    'message' => ' ',
                    'count'=>'',
                    'data' => array()
                );
                if (isset($_FILES) && count($_FILES) > 0) {
                    $row = 0;
                    $success = 0;
                    $header_status = false;
                    foreach($_FILES as $file) {
                        $fileSize = $file["size"];
                        $tempName = $file["tmp_name"];
                        if ($fileSize > 0 && isset($file['tmp_name'])) {
                            $csv = new csv(file_get_contents($file['tmp_name']));
                            $data = $csv->rows();
                            if(count($data) < 3) $result['status'] = 'invalid';
                            $requiredHeaders = array('date','accountid','accountname','salescontact','tlocontact','credcontact','industry','billable','internal','transactiontype','description','amount','state','zip');
                            foreach($data as $value) {
                              $row++;
                              if($row == 1) continue;
                              if (count(array_filter($value)) == 0) {
                                if($row == 2){
                                  $result['status'] = 'invalid';
                                  break;
                                }
                                continue;
                              }
                              if($row == 2){
                                $csvHeader = array_map('strtolower', $value);
                                $csvHeader = preg_replace('/[^a-zA-Z]/', '', $csvHeader);
                                foreach ($requiredHeaders as $value) {
                                  if (!in_array($value, $csvHeader)) {
                                     $result['status'] = 'invalid';
                                     $header_status = true;
                                     break;
                                  }
                                }
                                if($header_status){
                                  break;
                                }
                                continue;
                              }else{
                                $value = array_combine($csvHeader, $value);
                                $loadledgerreport = $this->_modelFactory->LoadLedgerReport();
                                $data_status = $loadledgerreport->saveLoadLedgerReport($value);
                                if($data_status == "Success" ){
                                  $success++;
                                }
                              }
                            }
                        }
                    }
                }
                $result['count'] = $success;
                print_r(json_encode($result));
            }
        catch(Exception $e)
            {
              $result['status'] = 'error';
              $result['message'] = 'Error while handling request.';
              $result['data'] = array();
              print_r(json_encode($result));
              Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString() , LOG_ERR);
            }
      }


    public function reporteventsAction()
    {
        try {
            $tsopreport = $this->_modelFactory->TsopReport();
            $reportevents = $tsopreport->GetReportEvents();
	    $eventtypes = $tsopreport->GetEventTypes();
	    $reports = $tsopreport->getReports();

            $this->view->reportevents = $reportevents;
	    $this->view->eventtypes = $eventtypes;
	    $this->view->reports = $reports;

        } catch (Exception $e) {
            Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString(), LOG_ERR);
        }
    }

    private function isSpecified($s)
    {
	return isset($s) && strlen($s) > 0;
    }

    private function validReportEventsRequest($action)
    {
	switch ($action)
	{
	case "addReportEvent":
                return 	$this->isSpecified($_REQUEST['reportId']) && 
			$this->isSpecified($_REQUEST['eventTypeId']) && 
			$this->isSpecified($_REQUEST['threshold']) &&
                        ((!$this->isSpecified($_REQUEST['dataReadyConn']) && !$this->isSpecified($_REQUEST['dataReadySql'])) ||
                        ($this->isSpecified($_REQUEST['dataReadyConn']) && $this->isSpecified($_REQUEST['dataReadySql'])));
	case "updateReportEvent":
		return 	$this->isSpecified($_REQUEST['eventId']) &&
			$this->isSpecified($_REQUEST['reportId']) && 
			$this->isSpecified($_REQUEST['newReportId']) &&
			$this->isSpecified($_REQUEST['eventTypeId']) && 
			$this->isSpecified($_REQUEST['newEventTypeId']) &&
			$this->isSpecified($_REQUEST['threshold']) &&
			((!$this->isSpecified($_REQUEST['dataReadyConn']) && !$this->isSpecified($_REQUEST['dataReadySql'])) || 
                        ($this->isSpecified($_REQUEST['dataReadyConn']) && $this->isSpecified($_REQUEST['dataReadySql'])));
	case "getReportEventDetails":
	case "deleteReportEvent":
		return $this->isSpecified($_REQUEST['eventId']);
	}
    }

    public function reporteventsactivitiesAction() 
    {
    	try {
            	$this->_helper->viewRenderer->setNoRender(true);
            	$this->_helper->layout->disableLayout();
            	$result = array(
                	'status' => 'error',
                	'message' => ' ',
                	'data' => array()
            	);
            	$tsopreport = $this->_modelFactory->TsopReport();
            	$tsopscheduler = $this->_modelFactory->TsopScheduler();
            	$tsopreportrecipients = $this->_modelFactory->TsopReportRecipients();
            	$tsopcontroler = $this->_modelFactory->TsopControl();
            	$action = $_REQUEST['type'];

		if (!$this->validReportEventsRequest($action))
		{
                      	$result['message'] = 'Details are invaild. Please provide valid report name and schedule frequency.';
			$result['request'] = $_REQUEST;
			print_r(json_encode($result));
			return;
		}

            	switch ($action) {
		case "addReportEvent":
			if ($tsopreport->ReportEventExists($_REQUEST['reportId'], $_REQUEST['eventTypeId']))
			{
	                        $result['message'] = 'Event of this type exists for this report';
	                        print_r(json_encode($result));
        	                return;
			}

			$eventId = $tsopreport->AddReportEvent($_REQUEST['reportId'], $_REQUEST['eventTypeId'], $_REQUEST['threshold'], $_REQUEST['dataReadyConn'], $_REQUEST['dataReadySql']);	
	
			if (empty($eventId))
                        {
                                $result['message'] = 'Event of this type exists for this report';
                                print_r(json_encode($result));
                                return;
                        }

                        $result['status'] = 'success';
                        $result['message'] = 'Successfully added new report event';
                        $result['data'] = array(
                                'eventId' => $eventId
                            );
	
			break;
		case "updateReportEvent":
			if ($_REQUEST['reportId'] != $_REQUEST['newReportId'] || $_REQUEST['eventTypeId'] != $_REQUEST['newEventTypeId'])
			{
                        	if ($tsopreport->ReportEventExists($_REQUEST['newReportId'], $_REQUEST['newEventTypeId']))
                        	{
                                	$result['message'] = 'Event of this type exists for this report';
	                                print_r(json_encode($result));
        	                        return;
                	        }
			}

                        $tsopreport->UpdateReportEvent($_REQUEST['eventId'], $_REQUEST['newReportId'], $_REQUEST['newEventTypeId'],
								$_REQUEST['threshold'], $_REQUEST['dataReadyConn'], $_REQUEST['dataReadySql']);

                        $result['status'] = 'success';
                        $result['message'] = 'Successfully updated report event';
                        $result['data'] = array(
                                'eventId' => $eventId
                            );

			break;	
		case "deleteReportEvent":
			$tsopreport->DeleteReportEvent($_REQUEST['eventId']);
			
                        $result['message'] = 'Report event was deleted successfully';
                        $result['status'] = 'success';

			break;
		case "getReportEventDetails":
			$reportEvent = $tsopreport->GetReportEvents($_REQUEST['eventId']);
			if (empty($reportEvent))
			{
	                        $result['message'] = 'Details are invaild. Please provide valid report name and schedule frequency.';
        	                print_r(json_encode($result));
                	        return;
			}	

			$result['data'] = $reportEvent;
			$result['message'] = 'Report event was found successfully';
			$result['status'] = 'success';	
			break;
		}

		print_r(json_encode($result));
	}
	catch(Exception $e)
	{
              $result['status'] = 'error';
              $result['message'] = 'Error while handling request.';
              $result['data'] = array();
              print_r(json_encode($result));
              Zend_Registry::get('logger')->log('Exception : ' . $e->getMessage() . $e->getTraceAsString() , LOG_ERR);
	}
    }
}
