<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\API\RequestPayload;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends BaseController {
	public function __construct() {
		$this->middleware('client_credentials');
	}

	public function updateStatus(Request $request) {
		$reportId = $request->input('report_id');
		$status = $request->input('status');

		if (!isset($reportId)) {
			$responsePayload = new RequestPayload(false, "Missing report id in request", null);

			return $this->sendResponse($responsePayload->response(), 'Failure');
		}

		if (!isset($status)) {
			$responsePayload = new RequestPayload(false, "Missing status in request", null);

			return $this->sendResponse($responsePayload->response(), 'Failure');
		}

		try
		{
			DB::table('tsop.tsop_scheduler')
				->where('report_Id', $reportId)
				->update(['active' => $status]);

			$responsePayload = new RequestPayload(true, "", null);

			return $this->sendResponse($responsePayload->response(), 'Success');

		} catch (Exception $ex) {
			$responsePayload = new RequestPayload(false, "Error updating table tsop_report: " . $ex->getMessage(), null);

			return $this->sendResponse($responsePayload->response(), 'Failure');
		}
	}

}