@extends('layouts.app', ['activePage' => 'reportmgmt', 'titlePage' => __('Report Management')])

<style>
.chosen-disabled .chosen-choices{
    cursor: not-allowed;
    background-color: #eee !important;
}
.view_access{
    cursor: not-allowed !important;
}
.parameterName {
	-webkit-background-clip: padding-box;
	-moz-background-clip: padding;
	border: 1px solid #ccc;
	border-top-right-radius: 4px;
	border-top-left-radius: 4px;
	border-bottom-right-radius: 4px;
	border-bottom-left-radius: 4px;
	background-image: -webkit-linear-gradient(top, white 0%, #eeeeee 100%);
	background-image: -o-linear-gradient(top, white 0%, #eeeeee 100%);
	background-image: linear-gradient(to bottom, white 0%, #eeeeee 100%);
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFFFFFFF', endColorstr='#FFEEEEEE', GradientType=0);
	-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
	color: #333333;
	cursor: default;
	line-height: 13px;
	margin: 1px 1px 2px 2px;
	padding: 3px 3px 3px 5px;
	position: relative;
	list-style: none;
}
.parameterName span {
	word-break: break-all;
	color: #000;
	font-size: 13px;
}
.parameterBlock {
	list-style-type: none;
	padding-left: 0;
	margin-bottom: 0px;
}
#initDataTable_wrapper thead th:nth-child(1), #initDataTable_wrapper tbody tr td:nth-child(1)
	{
	min-width: 40px;
}
#initDataTable_wrapper thead th:nth-child(2), #initDataTable_wrapper tbody tr td:nth-child(2)
	{
	min-width: 90px;
}

#initDataTable_wrapper thead th:nth-child(3), #initDataTable_wrapper tbody tr td:nth-child(3)
	{
	min-width: 250px;
}

#initDataTable_wrapper thead th:nth-child(4), #initDataTable_wrapper tbody tr td:nth-child(4)
	{
	min-width: 130px;
}

#initDataTable_wrapper thead th:nth-child(5), #initDataTable_wrapper tbody tr td:nth-child(5)
	{
	min-width: 135px;
}

#initDataTable_wrapper thead th:nth-child(6), #initDataTable_wrapper tbody tr td:nth-child(6)
	{
	min-width: 75px;
}
#initDataTable_wrapper thead tr .groupField, #initDataTable_wrapper tbody tr .groupField
	{
	min-width: 450px;
}

#initDataTable_wrapper thead th:nth-child(8), #initDataTable_wrapper tbody tr td:nth-child(8)
	{
	min-width: 120px;
}
#initDataTable_wrapper thead th:nth-child(9), #initDataTable_wrapper tbody tr td:nth-child(9)
	{
	min-width: 150px;
}
#filter_section {
	background: #e2e2e2;
	padding: 10px;
}
.dataTables_wrapper .dataTables_paginate i {
    color: #333;
}

.addReportHeader {
	padding: 5px 16px;
	font-size: 16px;
	color: #0094bd;
}
.addReportLeft {
	padding: 5px 0px 5px 15px;
}

.addReportLeft .row {
	padding: 5px 0;
}

.actionTags.addreportfile {
	float: left;
}

.actionTags.viewfile {
	float: right;
}

#add_parameter {
	font-size: 12px;
	padding: 10px 8px;
}
.editParameter, .deleteParameter, .deleteParameter:active, .deleteParameter:hover, .deleteParameter:focus,
.editParameter:active, .editParameter:hover, .editParameter:focus {
	color: #464646;
	cursor: pointer;
}

.chosen-container .chosen-choices {
	max-height: 120px !important;
	overflow-y: auto;
}

.datepicker .next, .datepicker .prev {
	color: #000 !important;
}
@media ( min-width : 1601px) {
.addnewreport>.modal-dialog, .editreport>.modal-dialog,
.addreportfileForm>.modal-dialog, .editreportfileForm>.modal-dialog {
width: 65% !important;
}
.addnewgroup>.modal-dialog, .addParameterForm>.modal-dialog, .editParameterForm>.modal-dialog {
width: 50% !important;
}
}
@media ( min-width : 768px) and ( max-width : 1600px){
.addnewreport>.modal-dialog, .editreport>.modal-dialog,
.addreportfileForm>.modal-dialog, .editreportfileForm>.modal-dialog {
width: 83% !important;
}
.addnewgroup>.modal-dialog, .addParameterForm>.modal-dialog, .editParameterForm>.modal-dialog  {
width: 68% !important;
}
}
@media screen and (max-width:995px) {
	#add_group {
		float:left;
		margin-top:4%;
	}
}
@media screen and (max-width: 1065px) {
	#filter_section {
		padding-bottom:60px;
	}
}
.uploading_msg, .uploaded_msg {
	color: rgb(0, 166, 202);
    margin-left: 10px;
    display: none;
}
div .fa-plus-circle, div .fa-times-circle {
    font-size: 18px;
    cursor: pointer;
}
div .fa-times-circle {
    color: #ff7373;
    padding: 2px 0px;
}
.templateFileBlock a {
    font-size: 13px;
    padding: 2px 10px;
    min-width: 0px;
    float: right;
}
.templateFileBlock {
	display: block;
	padding: 4px 10px;
    border-radius: 3px;
    background: #e8e8e8;
}
.doc_name {
    display: inline-block;
    vertical-align: top;
}
.disabledButton {
	pointer-events: none;
	cursor: not-allowed;
	opacity: 0.5;
}
.actionsBox a,  .actionTags a:hover, .actionTags a:active {
	color: #0094bd !important;
	cursor: pointer;
}
.run_report_input[disabled], #run_report[disabled] {
	opacity:0.5;
	cursor: not-allowed;
	color:#f7f7f7;
}
.run_report_input {
	cursor: pointer;
	border: none;
	border: 1px solid #ccc;
	border-radius: 3px;
}
.addReportSection .blue_button {
	float:right;
	margin-top:5px;
}
.upload_file {
   display: none !important;
}
.file-uploader {
	display: block;
    width: 100%;
    height: 37px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #fff;
    background-color: #rgba(0, 166, 202, 1);
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
}
.file-uploader b {
    font-weight: normal;
    vertical-align: top;
    color: #fff;
    background-color: #rgba(0, 166, 202, 1);
 }
#start_date[readonly] {
    background-color: #FFF !important;
    cursor:pointer;
}
.advancedRow {
	display: block;
}
.controlAdvancedRow {
	cursor: pointer;
}
.multiple_emails-container {
    max-width:100%;
    min-width:100%;
    position: relative;
    min-height: 78px;
}
.view {
    background: rgb(0, 166, 202) !important;
    border: none;
    color: #f7f7f7 !important;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    padding: 2px 10px;
    float: right;
}
.input-group .form-control {
    border-radius: 4px !important;
}
.input-group-addon {
	line-height: 1.44444;
}
.js-fileapi-wrapper i.fa-search {
  	cursor: pointer;
  	font-size: 20px;
  	float: right;
}
.upload_file {
   display: none !important;
}
.file-uploader {
	display: block;
    width: 100%;
    height: 37px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #fff;
    background-color: rgba(0, 166, 202, 1);
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
    box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
}
.file-uploader b {
    font-weight: normal;
    vertical-align: top;
 }
 @media screen and (max-width:730px) {
 	#add_report  {
		margin-left:-4px!important;
 	}
 }
 @media ( min-width : 1300px) and ( max-width : 1900px){
	.field_name,.space_nowrp
	{
		white-space: nowrap;
	}
}

#async {
	width: 65px;
}

#sequence {
	width: 65px;
	text-align: right;
}
#executiontype {
	width: 190px;
}

#file_type {
	width: 200px;
}

button {
	background-color: #ff0000;
}

.parameter-table {
	width: 1010px !important;
	margin: auto !important;
	border: 1px solid gray !important;
	table-layout: fixed !important;
}



		.b-button {
			display: inline-block;
			*display: inline;
			*zoom: 1;
			position: relative;
			overflow: hidden;
			cursor: pointer;
			padding: 4px 15px;
			vertical-align: middle;
			border: 1px solid #ccc;
			border-radius: 3px;
			background-color: #f5f5f5;
			background: -moz-linear-gradient(top, #fff 0%, #f5f5f5 49%, #ececec 50%, #eee 100%);
			background: -webkit-linear-gradient(top, #fff 0%,#f5f5f5 49%,#ececec 50%,#eee 100%);
			background: -o-linear-gradient(top, #fff 0%,#f5f5f5 49%,#ececec 50%,#eee 100%);
			background: linear-gradient(to bottom, #fff 0%,#f5f5f5 49%,#ececec 50%,#eee 100%);
			-webkit-user-select: none;
			-moz-user-select: none;
			user-select: none;
		}
			.b-button_hover {
				border-color: #fa0;
				box-shadow: 0 0 2px #fa0;
			}
			.b-button__text {
			}
			.b-button__input {
				cursor: pointer;
				opacity: 0;
				filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);
				top: -10px;
				right: -40px;
				font-size: 50px;
				position: absolute;
			}

</style>

@section('content')
  <div class="content">
         <div id="errormsg" class="alert alert-danger error pl-3" for="email" style="display: <?php echo $errors->first('errormsg') != '' ? 'block' : 'none'; ?>">
          {{ $errors->first('errormsg') }}<span style="display: inline-block;float: right;font-size: 20px;cursor: pointer;" >&times;</span>
        </div>


<!--
	        	<div id="errordiv" class="alert alert-danger alert-dismissible fade" role="alert">
	  <div id="errormsg"><strong>{{ $errors->first('errormsg') }}</strong></div>
	  <button type="button" class="close" data-hide="alert" aria-label="Close" onclick="$('#errordiv').removeClass('show');">
	    <span aria-hidden="true">&times;</span>
	  </button>
	</div>
-->

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <form method="post" action="{{ route('profile.update') }}" autocomplete="off" class="form-horizontal">
            @csrf
            @method('put')

            <div class="card ">
              <div class="card-header card-header-primary">
                <h4 class="card-title">{{ __('Reports') }}</h4>
              </div>
              <div class="card-body ">
                @if (session('status'))
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <i class="material-icons">close</i>
                        </button>
                        <span>{{ session('status') }}</span>
                      </div>
                    </div>
                  </div>
                @endif
                <div class="row">
                	<table id="report-table" class="table table-striped table-bordered table-sm pagetable">
                		<thead>
                			<tr>
	                			<th>Report ID</th>
	                			<th>Report Name</th>
	                			<th>Frequency</th>
	                			<th>Scheduled Day</th>
	                			<th>Time</th>
	                			<th class="report-group-names">Groups</th>
	                			<th>Status</th>
	                			<th>Actions</th>
	                		</tr>
                		</thead>
                		<tbody>
                			@foreach($reports as $report)
                			<tr data-reportid="{{ $report->Id }}">
                				<td>{{ $report->Id }}</td>
                				<td>{{ $report->name }}</td>
                				<td>{{ $report->frequency }}</td>
                				<td>{{ $report->scheduled_day }}</td>
                				<td>{{ $report->time }}</td>
                				<td class="report-group-names" title="{{ $report->group_names }}">{{ $report->group_names }}</td>
                				<td><div class="reportstatus actionTags {{ $report->active ? 'deactivate' : 'activate'}}" data-reportid="{{ $report->Id }}" data-status="{{ $report->active}}">{{ $report->active ? 'Active' : 'Inactive'}}</div></td>
                				<td><div style="text-align: center;"><a href="#">Edit</a>&nbsp;/&nbsp;<a href="#">Add</a>&nbsp;/&nbsp;<a class="view-report" data-status="closed" data-reportid="{{ $report->Id }}" href="#">View</a></div></td>
                			</tr>
                			@endforeach
                		</tbody>
                	</table>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>

<div id="myModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Modal body text goes here.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Report File Dialog -->

<div class="modal fade" id="addReportFileSection" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog mw-100 w-50" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addReportFileSectionTitle">New message</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
 				<form method="post" action="/updatereportfile" enctype="multipart/form-data" id="addreportfileform" name="addreportfileform">
				            @csrf
				         <div id="reportfile_error_required" class="alert alert-danger error pl-3" for="email" style="display: none">

				        </div>


					<div style="margin-top: 20px;margin-bottom: 20px;">
						<div class="section-header">General<span id="general-toggle" data-toggle="collapse" style="display: inline-block;cursor: pointer;">
										<i id="general-toggle-icon" class="fa fa-minus-square expand-collapse-icon-blue" style="font-size: 25px;vertical-align: middle;"></i></div>
						<div id="general-body" class="card-body" style="display: block;">
							<div class="row">
								<div class="col-md-2 col-xs-12 field_name">File Name<span id="filenamerequired" class="red">*</span></div>
								<div class="col-md-4 col-xs-12">
									<input type="text" tabindex="0" class="form-control" id="reportfilename" name="reportfilename"/>
								</div>
								<div class="col-md-2 col-xs-12 field_name">File Type<span id="filetyperequired" class="red">*</span></div>
								<div class="col-md-4 col-xs-12">
									<select id = "file_type" name ="file_type" class = "form-control" data-required="true">
										<option value = "">Select File Type</option>
										@foreach($file_types as $file_type)
										<option value="{{ $file_type->Id }}">{{ $file_type->file_type }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2 col-xs-12 field_name">URL<span id="urlrequired" class="red">*</span></div>
								<div class="col-md-4 col-xs-12">
									<input tabindex="0" class="form-control" id="reportfileurl" name="reportfileurl"/>
								</div>
								<div class="col-md-2 col-xs-12 field_name">Template</div>
								<div class="col-md-4 col-xs-12">
									<input tabindex="0" class="form-control" style="width: 200px;float: left;" data-type="" id="template_name" name="template_name"/>
								<div class="js-fileapi-wrapper" style="display: inline-block; float: left;">
									<div class="b-button__text"><span class="file-uploader"><i class="fa fa-search"></i></span></div>
									<input name="files" id="template_file" class="b-button__input" type="file" />
								</div>
								<div style="clear: both;"></div>

								</div>
							</div>
							<div class="row">
								<div class="col-md-2 col-sm-2 col-xs-12">Embed Attachment</div>
								<div class="col-md-4 col-xs-12">
									<input type="radio" name="embed_attachment" value="1" /> Yes
									&nbsp;&nbsp;
									<input type="radio" name="embed_attachment" value="0" checked="checked" /> No
								</div>
								<div class="col-md-2 col-xs-12">Asynchronous</div>
								<div class="col-md-4 col-xs-12 js-fileapi-wrapper">
									<select class="form-control async" id="async" name="async">
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2 col-sm-2 col-xs-12">Sequence<span id="sequencerequired" class="red">*</span></div>
								<div class="col-md-4 col-xs-12">
									<input class="form-control sequence" type="number" id="sequence" name="sequence" value="" />
								</div>
								<div class="col-md-2 col-xs-12">Execution Type<span class="red">*</span></div>
								<div class="col-md-4 col-xs-12 js-fileapi-wrapper">
									<select class="form-control async" id="executiontype" name="executiontype" onchange="executionTypeChanged(this)">
										<option value = "">Select Execution Type</option>
										@foreach($execution_types as $execution_type)
										<option value="{{ $execution_type->id }}">{{ $execution_type->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2 col-sm-2 col-xs-12 field_name">Filename Mask</div>
								<div class="col-md-4 col-xs-12">
									<input class="form-control" id="filenamemask" name="filenamemask" tabindex="0" autocomplete="off" />
								</div>
								<div class="col-md-2 col-sm-2 col-xs-12 field_name">Script Path<span id="scriptpathrequired" class="red" style="display: none;">*</span></div>
								<div class="col-md-4 col-xs-12">
									<input class="form-control" id="scriptpath" name="scriptpath" tabindex="0" autocomplete="off" />
								</div>
							</div>

						</div>
					</div>
					<div style="margin-top: 20px;margin-bottom: 20px;">
						<div class="section-header">Parameters<span id="parameters-toggle" data-toggle="collapse" style="display: inline-block;cursor: pointer;">
										<i id="parameters-toggle-icon" class="fa fa-minus-square expand-collapse-icon-blue" style="font-size: 25px;vertical-align: middle;"></i></div>
						<div id="parameters-body" class="card-body" style="display: block;">
							<div>
								<div class="row">
									<table class="table table-striped table-bordered table-sm pagetable parameter-table">
										<thead>
											<tr>
												<th style="width: 200px;text-align: center;border: 1px solid gray;">Name</th>
												<th style="width: 325px;text-align: center;border: 1px solid gray;">Type</th>
												<th style="width: 400px;text-align: center;border: 1px solid gray;">Value</th>
												<th style="width: 80px;text-align: center;border: 1px solid gray;">&nbsp;</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td style="border-color: #888686;width: 200px;"><input id="parameter-name-input" type="text" class="form-control" style="width: 100%;" /></td>
												<td style="border-color: #888686;width: 325px;">
													<select id="parameter-type-select" class="form-control" style="width: 100%;">
														@foreach($lookup_values as $lookup_value)
														<option value="{{ $lookup_value->Id }}">Lookup: {{ $lookup_value->description }}</option>
														@endforeach
														<option value="0">Parameter</option>
													</select>
												</td>
												<td style="border-color: #888686;width: 400px;"><input id="parameter-value-input" type="text" class="form-control" style="width: 100%;" readonly="true"  /></td>
												<td style="border-color: #888686;width: 80px;"><button id="add-parameter-button" type="button" class="btn-primary" class="form-control" style="width: 100%;" >Add</button></td>
											</tr>
										</tbody>
									</table>

								</div>
								<div class="row">
									<table  id="parameters-table" class="table table-striped table-bordered table-sm pagetable parameter-table" style="margin-top: 30px;display: none;">
										<thead>
											<tr>
												<th style="width: 200px;text-align: center;border: 1px solid gray;">Name</th>
												<th style="width: 325px;text-align: center;border: 1px solid gray;">Type</th>
												<th style="width: 400px;text-align: center;border: 1px solid gray;">Value</th>
												<th style="width: 80px;text-align: center;border: 1px solid gray;">&nbsp;</th>
											</tr>
										</thead>
										<tbody id="parameter-table-body">
										</tbody>
									</table>
								</div>
								<div style="clear: both;"></div>
							</div>
						</div>
					</div>
<!--
					<div style="margin-top: 20px;margin-bottom: 20px;">
						<div class="section-header">Advanced<span id="advanced-toggle" data-toggle="expand" style="display: inline-block;cursor: pointer;">
										<i id="advanced-toggle-icon" class="fa fa-plus-square expand-collapse-icon-blue" style="line-height: 20px;vertical-align: middle;"></i></div>
						<div id="advanced-body" class="card-body" style="display: none;">
							<div class="row">
								<div class="col-md-2 col-sm-2 col-xs-12 field_name">Execute Stored Procedure</div>
								<div class="col-md-4 col-sm-4 col-xs-12">
									<input type="radio" name="sp_needed" value="1" /> Yes
									&nbsp;&nbsp;
									<input type="radio" name="sp_needed" value="0" checked="" /> No
								</div>
								<div class="col-md-2 col-sm-2 col-xs-12">Stored Procedure</div>
								<div class="col-md-4 col-sm-4 col-xs-12">
									<select name="spid" id="spid" class="form-control" disabled="disabled">
										<option value="" >Select Stored Procedure</option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2 col-sm-2 col-xs-12">Is Blank?</div>
								<div class="col-md-4 col-sm-4 col-xs-12">
									<input type="radio" name="send_blank" value="1" /> Yes
									&nbsp;&nbsp;
									<input type="radio" name="send_blank" value="0" checked=""/> No
								</div>
							</div>
						</div>
					</div>
-->
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button id="save-btn" type="button" form="addreportfileform" class="btn btn-primary">Send message</button>
			</div>
		</div>
	</div>
</div>

@endsection


@push('js')
	<script>
    	    	$(document).ready(function() {
			InitReportManagement();
		});
	</script>
@endpush
