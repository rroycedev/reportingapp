<?php

namespace App\Helpers;

use App\Application_Model_Constants;
use App\Exceptions\Gatekeeper\GatekeeperAuthConnectException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use \Zend\XmlRpc\Client;

class GatekeeperHelper {
	public static function sendRequest($command, array $params = []) {
		$url = env('GATEKEEPER_AUTH_URL', '');
		$client = new Client($url);

		try {
			return $client->call($command, $params);
		} catch (Exception $ex) {
			Log::emergency("Throwing connect exception");
			throw new GatekeeperAuthConnectException();
		}
	}

	public static function getPermissions($auth_permissions_data) {
		$auth_permissions = array();
		if (is_array($auth_permissions_data) === true && array_key_exists('permission', $auth_permissions_data) === true) {
			if (is_array($auth_permissions_data['permission']) === true) {
				$auth_permissions = $auth_permissions_data['permission'];
			} else {
				$auth_permissions = array(
					$auth_permissions_data['permission'],
				);
			}
		}

		return $auth_permissions;
	}

	public static function checkPermissionsOfUser($auth_permissions) {
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_REPORTSERVER_ACCESS, $auth_permissions) === true)) {
			Session::put('is_reportserver_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_REPORTMANAGEMENT_EDIT, $auth_permissions) === true)) {
				Session::put('is_reportmanagement_editaccess', true);
			} else if ((in_array(Application_Model_Constants::BUSINESSREPORTING_REPORTMANAGEMENT_VIEW, $auth_permissions) === true)) {
				Session::put('is_reportmanagement_viewaccess', true);
			}
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_ADVANCED_EDIT, $auth_permissions) === true)) {
				Session::put('is_reportadvanced_edit', true);
			}
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_GROUPSERVER_ACCESS, $auth_permissions) === true)) {
			Session::put('is_groupserver_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_GROUPMANAGEMENT_EDIT, $auth_permissions) === true)) {
				Session::put('is_groupmanagement_editaccess', true);
			} else if ((in_array(Application_Model_Constants::BUSINESSREPORTING_GROUPMANAGEMENT_VIEW, $auth_permissions) === true)) {
				Session::put('is_groupmanagement_viewaccess', true);
			}
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_TSOPSTATUS_ACCESS, $auth_permissions) === true)) {
			Session::put('is_tsopstatus_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_TSOPSTATUS_VIEW, $auth_permissions) === true)) {
				Session::put('is_tsopstatus_viewaccess', true);
			}
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_LEDGERREPORT_ACCESS, $auth_permissions) === true)) {
			Session::put('is_ledgerreport_access', true);
			Session::put('is_ledgerreport_viewaccess', true);
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_EMPLOYEEMANAGEMENT_ACCESS, $auth_permissions) === true)) {
			Session::put('is_employeemanagement_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_EMPLOYEEMANAGEMENT_EDIT, $auth_permissions) === true)) {
				Session::put('is_employeemanagement_editaccess', true);
			} else if ((in_array(Application_Model_Constants::BUSINESSREPORTING_EMPLOYEEMANAGEMENT_VIEW, $auth_permissions) === true)) {
				Session::put('is_employeemanagement_viewaccess', true);
			}
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_TERRITORYMANAGEMENT_ACCESS, $auth_permissions) === true)) {
			Session::put('is_territorymanagement_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_TERRITORYMANAGEMENT_EDIT, $auth_permissions) === true)) {
				Session::put('is_territorymanagement_editaccess', true);
			} else if ((in_array(Application_Model_Constants::BUSINESSREPORTING_TERRITORYMANAGEMENT_VIEW, $auth_permissions) === true)) {
				Session::put('is_territorymanagement_viewaccess', true);
			}
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_TERRITORYMANAGEMENT_EXPORT, $auth_permissions) === true)) {
				Session::put('is_territorymanagement_export', true);
			}
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_ANOAMLYSETUP_ACCESS, $auth_permissions) === true)) {
			Session::put('is_anoamlysetup_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_ANOAMLYSETUP_EDIT, $auth_permissions) === true)) {
				Session::put('is_anoamlysetup_editaccess', true);
			} else if ((in_array(Application_Model_Constants::BUSINESSREPORTING_ANOAMLYSETUP_VIEW, $auth_permissions) === true)) {
				Session::put('is_anoamlysetup_viewaccess', true);
			}
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_ANOAMLYSETUP_SQLEDIT, $auth_permissions) === true)) {
				Session::put('is_anoamlysetup_sqleditaccess', true);
			}
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_ANOMALYEXCEPTION_ACCESS, $auth_permissions) === true)) {
			Session::put('is_anomalyexception_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_ANOMALYEXCEPTION_EDIT, $auth_permissions) === true)) {
				Session::put('is_anomalyexception_editaccess', true);
			} else if ((in_array(Application_Model_Constants::BUSINESSREPORTING_ANOMALYEXCEPTION_VIEW, $auth_permissions) === true)) {
				Session::put('is_anomalyexception_viewaccess', true);
			}
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_TRANSACTIONHISTORY_ACCESS, $auth_permissions) === true)) {
			Session::put('is_transactiontraceback_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_TRANSACTIONHISTORY_EDIT, $auth_permissions) === true)) {
				Session::put('is_transactiontraceback_editaccess', true);
			}
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_ANOMALYREPORT_ACCESS, $auth_permissions) === true)) {
			Session::put('is_anomalyreport_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_ANOMALYREPORT_EDIT, $auth_permissions) === true)) {
				Session::put('is_anomalyreport_editaccess', true);
			}
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_IPTRACKING_ACCESS, $auth_permissions) === true)) {
			Session::put('is_iptracking_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_IPTRACKING_EDIT, $auth_permissions) === true)) {
				Session::put('is_iptracking_editaccess', true);
			}
		}
		if ((in_array(Application_Model_Constants::BUSINESSREPORTING_LEDGERREPORTAPPROVAL_ACCESS, $auth_permissions) === true)) {
			Session::put('is_ledgerreportapproval_access', true);
			if ((in_array(Application_Model_Constants::BUSINESSREPORTING_LEDGERREPORTAPPROVAL_EDIT, $auth_permissions) === true)) {
				Session::put('is_ledgerreportapproval_editaccess', true);
			} else if ((in_array(Application_Model_Constants::BUSINESSREPORTING_LEDGERREPORTAPPROVAL_VIEW, $auth_permissions) === true)) {
				Session::put('is_ledgerreportapproval_viewaccess', true);
			}
		}
	}

}
