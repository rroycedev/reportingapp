<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportManagementController extends Controller {
	/**
	 * Show the form for editing the profile.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index() {
		try {
			$reports = DB::table('tsop.tsop_report')
				->join('tsop.tsop_scheduler', 'tsop_report.Id', '=', 'tsop_scheduler.report_id')
				->join('tsop.tsop_schedule_frequency', 'tsop_scheduler.frequency', '=', 'tsop_schedule_frequency.Id')
				->join('tsop.tsop_report_recipients', 'tsop_report.Id', '=', 'tsop_report_recipients.report_Id')
				->join('tsop.tsop_group', 'tsop_report_recipients.group_id', '=', 'tsop_group.Id')
				->select('tsop_report.Id', 'tsop_report.name', 'tsop_schedule_frequency.frequency', 'tsop_scheduler.scheduled_day', 'tsop_scheduler.time', 'tsop_scheduler.active')
				->selectRaw('GROUP_CONCAT(tsop_group.name) as group_names')
				->groupBy(['Id', 'name', 'frequency', 'scheduled_day', 'time', 'active'])
				->orderBy('tsop_report.Id')
				->get();

			$fileTypes = DB::table('tsop.tsop_file_type')
				->select('tsop_file_type.Id')
				->selectRaw('UPPER(tsop_file_type.file_type) as file_type')
				->get();

			$executionTypes = DB::table('tsop.tsop_execution_type')
				->select('id', 'name')
				->get();

			$lookupValues = DB::table('tsop.tsop_parameter_value')
				->select('Id', 'value', 'description')
				->get();

		} catch (Exception $ex) {
			return view('reportmaint.reportmgmt.index', ["reports" => array(), "file_types" => array(), "execution_types" => array(), "lookup_values" => array()])
				->withErrors(array('errormsg' => "Error retrieving reports: " . $ex->getMessage()));
		}

		return view('reportmaint.reportmgmt.index', ["reports" => $reports, "file_types" => (object) $fileTypes, "execution_types" => (object) $executionTypes, "lookup_values" => (object) $lookupValues])
			->withErrors(array('errormsg' => ''));
	}

	public function updateReportFile(Request $request) {

		$fileContents = file_get_contents($_FILES['files']['tmp_name']);

		Log::emergency('Files uploaded: [' . json_encode($_FILES, JSON_PRETTY_PRINT));
		Log::emergency("File contents: [' . $fileContents . ']");
	}

}
