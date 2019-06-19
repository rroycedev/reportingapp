<?php


namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\models\Tsop_report;

class ReportManagementController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
    	$reports =DB::table('tsop.tsop_report')
                ->join('tsop.tsop_scheduler', 'tsop_report.Id', '=', 'tsop_scheduler.report_id')
                ->join('tsop.tsop_schedule_frequency', 'tsop_scheduler.frequency', '=', 'tsop_schedule_frequency.Id')
                ->join('tsop.tsop_report_recipients', 'tsop_report.Id', '=', 'tsop_report_recipients.report_Id')
                ->join('tsop.tsop_group', 'tsop_report_recipients.group_id', '=', 'tsop_group.Id')
                ->select('tsop_report.Id','tsop_report.name','tsop_schedule_frequency.frequency', 'tsop_scheduler.scheduled_day', 'tsop_scheduler.time', 'tsop_scheduler.active')
                ->selectRaw('GROUP_CONCAT(tsop_group.name) as group_names')
                ->groupBy(['Id', 'name', 'frequency', 'scheduled_day', 'time', 'active'])
                ->orderBy('tsop_report.Id')
                ->get();

//    	$reports = Tsop_report::get();

        return view('reportmaint.reportmgmt.index', [ "reports" => $reports ] );
    }
}


