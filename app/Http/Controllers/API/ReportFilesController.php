<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\API\RequestPayload;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportFilesController extends BaseController {
	public function __construct() {
		$this->middleware('client_credentials');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function find(Request $request) {
		$reportId = $request->input('report_id');

		if (!isset($reportId)) {
			$reportFiles = array();
		} else {
			$reportFiles = DB::select("select tsop_report_files.*, tsop_execution_type.name as execution_type_name from tsop.tsop_report_files left join tsop.tsop_execution_type on (tsop_execution_type.Id = tsop_report_files.execution_type) where report_id = $reportId");
		}

		$responsePayload = new RequestPayload(true, "", $reportFiles);

		return $this->sendResponse($responsePayload->response(), 'Success');
	}

	public function updateStatus(Request $request) {
		$reportFileId = $request->input('report_file_id');
		$status = $request->input('status');

		$all = $request->all();

		if (!isset($reportFileId)) {
			$responsePayload = new RequestPayload(false, "Missing report file id in request", $all);

			return $this->sendResponse($responsePayload->response(), 'Failure');
		}

		if (!isset($status)) {
			$responsePayload = new RequestPayload(false, "Missing status in request", $all);

			return $this->sendResponse($responsePayload->response(), 'Failure');
		}

		try
		{
			DB::table('tsop.tsop_report_files')
				->where('Id', $request->input('report_file_id'))
				->update(['active' => $request->input('status')]);

			$responsePayload = new RequestPayload(true, "", null);

			return $this->sendResponse($responsePayload->response(), 'Success');

		} catch (Exception $ex) {
			$responsePayload = new RequestPayload(false, "Error updating table tsop_report_files", null);

			return $this->sendResponse($responsePayload->response(), 'Failure');
		}
	}

}