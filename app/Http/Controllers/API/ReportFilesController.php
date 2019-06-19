<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Product;
use Validator;


class ReportFilesController extends BaseController
{
	public function __construct()
	{
	    $this->middleware('client_credentials');
	}	

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request)
    {
    	$reportId = $request->input('report_id');

    	if ($reportId == "")
    	{
    		$reportFiles = array();
    	}
    	else
    	{
	    	$reportFiles = DB::select("select * from tsop.tsop_report_files where report_id = $reportId");    		
    	}

        return $this->sendResponse($reportFiles, 'Report files retrieved successfully.');
    }


}