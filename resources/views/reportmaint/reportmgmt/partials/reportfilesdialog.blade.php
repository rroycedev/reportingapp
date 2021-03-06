<div id="addReportFileSection" style="display: none;">
	<form class="addReportFileSection">
		<div class="row"><span class="addReportHeader">Report File</span></div>
		<div class="addReportLeft">
			<div class="row">
				<div class="col-md-2 col-xs-12 field_name">File Name<span id="filenamerequired" class="red">*</span></div>
				<div class="col-md-4 col-xs-12">
					<input type="text" tabindex="0" class="form-control" id="reportfilename" name="reportfilename"/>
				</div>
				<div class="col-md-2 col-xs-12 field_name">File Type<span id="filetyperequired" class="red">*</span></div>
				<div class="col-md-4 col-xs-12">
					<select id = "file_type" name ="file_type" class = "form-control" data-required="true">
						<option value = "">Select File Type</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-xs-12 field_name">URL<span id="urlrequired" class="red">*</span></div>
				<div class="col-md-4 col-xs-12">
					<input tabindex="0" class="form-control" id="reportfileurl" name="reportfileurl"/>
				</div>
				<div class="col-md-2 col-xs-12 field_name">Template Name</div>
				<div class="col-md-4 col-xs-12">
					<input tabindex="0" class="form-control" data-type="" id="template_name" name="template_name"/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-sm-2 col-xs-12">Embed Attachment</div>
				<div class="col-md-4 col-xs-12">
					<input type="radio" name="embed_attachment" value="1" /> Yes
					&nbsp;&nbsp;
					<input type="radio" name="embed_attachment" value="0" checked="checked" /> No
				</div>
				<div class="col-md-2 col-xs-12">Template Data</div>
				<div class="col-md-4 col-xs-12 js-fileapi-wrapper">
					<span class="file-uploader"><i class="fa fa-search"></i> <b>No file chosen</b></span>
					<input type="file" name="template_file" class="upload_file" disabled="disabled"  />
					<div class="uploading_msg">File is uploading...</div>
					<div class="uploaded_msg">Your file is successfully uploaded</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-sm-2 col-xs-12">Sequence<span id="sequencerequired" class="red">*</span></div>
				<div class="col-md-4 col-xs-12">
					<input class="form-control sequence" type="number" id="sequence" name="sequence" value="" />
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
				<div class="col-md-2 col-sm-2 col-xs-12 field_name">Filename Mask</div>
				<div class="col-md-4 col-xs-12">
					<input class="form-control" id="filenamemask" name="filenamemask" tabindex="0" autocomplete="off" />
				</div>
				<div class="col-md-2 col-xs-12">Execution Type<span class="red">*</span></div>
				<div class="col-md-4 col-xs-12 js-fileapi-wrapper">
					<select class="form-control async" id="executiontype" name="executiontype" onchange="executionTypeChanged(this)">
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2 col-sm-2 col-xs-12 field_name">Script Path<span id="scriptpathrequired" class="red" style="display: none;">*</span></div>
				<div class="col-md-4 col-xs-12">
					<input class="form-control" id="scriptpath" name="scriptpath" tabindex="0" autocomplete="off" />
				</div>
			</div>
		</div>
		<div class="row parametersRow"><span class="addReportHeader">Parameters</span></div>
		<div class="addReportLeft parametersRow">
			<div class="row">
				<div class="col-md-6 col-xs-12">
					<div id="parameterList"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-xs-12">
					<span id="add_parameter" class="blue_button" tabindex="0">Add Parameter</span>
				</div>
			</div>
		</div>
		<div class="row"><span class="addReportHeader">Advanced</span><span class="controlAdvancedRow"><i class="fa fa-plus"></i></span></div>
		<div class="addReportLeft advancedRow">
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
		<div id="reportfile_error_required" class="error_required red"></div>
	</form>
</div>