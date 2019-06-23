var searchableCols =  [true, true, true, false, false, false, true, false];
var searchColWidths = ["100px", "100%", "100px", "100px", "100px", "100px", "100px", "100px"];

var apiToken = "";
var parameterTypes = ['Lookup', 'Parameter'];
var parametersDefined = [];
var uploadFiles = null;

function InitReportManagement()
{

	var choose = document.getElementById('template_file');
	FileAPI.event.on(choose, 'change', function (evt){
		var files = FileAPI.getFiles(evt); // Retrieve file list

		FileAPI.filterFiles(files, function (file, info/**Object*/){
			//if( /^image/.test(file.type) ){
			//	return	info.width >= 320 && info.height >= 240;
			//}
			//return	false;
			return true;
		}, function (files/**Array*/, rejected/**Array*/){
			uploadFiles = files;

			if( files.length ){
				$('#template_name').val(files[0].name);
				// Make preview 100x100
				//FileAPI.each(files, function (file){
				//	FileAPI.Image(file).preview(100).get(function (err, img){
				//		images.appendChild(img);
				//	});
				//});

				// Uploading Files
//				FileAPI.upload({
//					url: '/updatereportfile',
//					files: { images: files },
//					progress: function (evt){ /* ... */ },
//					complete: function (err, xhr){ /* ... */ }
//				});
			}
		});
	});	

	$('#report-table thead tr').clone(true).appendTo( '#report-table thead' );
	$('#report-table thead tr:eq(1) th').each( function (i) {
		if (searchableCols[i])
		{
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search" style="width: ' + searchColWidths[i] + ';"/>' );
	 
	        $( 'input', this ).on( 'keyup change', function () {
	            if ( table.column(i).search() !== this.value ) {
	                table
	                    .column(i)
	                    .search( this.value )
	                    .draw();
	            }
	        } );		    		
		}
		else 
		{
	        $(this).html( '' );		    		
		}
	} );

	var table = $('#report-table').DataTable({
		"dom": "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
		         "<'row'<'col-sm-12'tr>>" +
		         "<'row'<'col-sm-5'i><'col-sm-7'p>>",			
		"language": {
		    "lengthMenu": '<div style="width: 400px;"><div style="float: left;line-height: 41px;vertical-align: middle;color: #000000;">Display&nbsp;</div><div style="float: left;width: 50px;"><select class="form-control" style="float: left;">'+
		      '<option value="10">10</option>'+
		      '<option value="20">20</option>'+
		      '<option value="30">30</option>'+
		      '<option value="40">40</option>'+
		      '<option value="50">50</option>'+
		      '<option value="-1">All</option>'+
		      '</select></div><div style="float: left;line-height: 41px;vertical-align: middle;color: #000000;">&nbsp;records</div><div style="clear: both;"></div></div>'
		},
		"search": {
		    "smart": false
		},			
	 	"order": [[ 0, "asc" ], [1, "asc"], ['2', "asc"], ['3', 'asc'], [4, 'asc'], ['5', 'asc'], ['6', 'asc'], ['7', ''] ],
	//		 	"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"columnDefs": [
		    { "searchable": true, "targets": 0, "width": "5%" },
		    { "searchable": true, "targets": 1, "width": "50%"},
		    { "searchable": true, "targets": 2, "width": "6%" },
		    { "searchable": false, "targets": 3, "width": "10%" },
		    { "searchable": false, "targets": 4, "width": "5%" },
		    { "searchable": false, "targets": 5 },
		    { "searchable": true, "targets": 6, "width": "95px" },
		    { "searchable": false, "targets": 7, "width": "7%" },
		 ],	 	
		"ordering": true,
		"orderCellsTop": true,
	        "fixedHeader": true,
	        "autoWidth": false,		
	});

	$('.dataTables_length').addClass('bs-select');

	$('#report-table_filter').remove();

	$('.view-report').on('click', function(e) {
		var reportId = e.currentTarget.attributes['data-reportid'].value;

		if (e.currentTarget.attributes['data-status'].value == "closed")
		{
			var trElement = $('tr[data-reportid="' + reportId + '"]');

			e.currentTarget.innerHTML = 'Close';

			e.currentTarget.attributes['data-status'].value = "open";

			get_report_files(reportId, trElement)
		}
		else
		{
			var trElement = $('#report-file-div-' + reportId);

			trElement.remove();

			e.currentTarget.innerHTML = 'View';

			e.currentTarget.attributes['data-status'].value = "closed";
		}
	});

	$('.reportstatus').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();

		var reportId = e.currentTarget.attributes['data-reportid'].value;
		var status = e.currentTarget.attributes['data-status'].value;
		if (status == 0)
		{
			status = 1;
		}
		else
		{
			status = 0;
		}

		update_report_status(reportId, status, e.currentTarget);
	});


	$('#addReportFileSection').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget) 
		var title = button.data('title') 
		var saveButtonText = button.data('savebuttontext');

		var modal = $(this)
		modal.find('#addReportFileSectionTitle').text(title)
		modal.find('#save-btn').text(saveButtonText);

		 $("#addeditreportfileform").on("submit", function(e) {
		        var postData = $(this).serializeArray();
		        var formURL = $(this).attr("action");
		        $.ajax({
		            url: '/updatereportfile',
		            type: "POST",
		            data: postData,
		            success: function(data, textStatus, jqXHR) {
		                $('#contact_dialog .modal-header .modal-title').html("Result");
		                $('#contact_dialog .modal-body').html(data);
		                $("#submitForm").remove();
		            },
		            error: function(jqXHR, status, error) {
		                console.log(status + ": " + error);
		            }
		        });
		        e.preventDefault();
		    });

		$("#save-btn").on('click', function(e){
			document.addreportfileform.submit();

			var f = $('#template_file');

			var xhr = FileAPI.upload({
					url : '/updatereportfile',
					files : { file : uploadFiles },
					data : {},
					complete : function(err, xhr) {
						
					}
				});
		});


	})

	$('#errormsg').on('click', function(e) {
		$('#errormsg').hide();
	});


	$('#general-toggle').on('click', function (event) {
		expand_collapse_section('general');

	});

	$('#parameters-toggle').on('click', function (event) {
		expand_collapse_section('parameters');
	});

	$('#advanced-toggle').on('click', function (event) {
		expand_collapse_section('advanced');
	});

	parametersDefined = [];

	$('#add-parameter-button').on('click', function(event) {
		var paramName = $.trim($('#parameter-name-input').val());
		var paramValue = $.trim($('#parameter-value-input').val());
		var paramTypeName = parameterTypes[$('#parameter-type-select').val()];

		if ($.trim($('#parameter-name-input').val()) == '')
		{
			$('#reportfile_error_required').text('You must specify a parameter name');
			$('#reportfile_error_required').show();
			setTimeout(function(){ $('#reportfile_error_required').hide(); }, 5000);
			return;
		}

		if ($.trim($('#parameter-value-input').val()) == '')
		{
			$('#reportfile_error_required').text('You must specify a parameter value');
			$('#reportfile_error_required').show();
			setTimeout(function(){ $('#reportfile_error_required').hide(); }, 5000);			
			return;
		}

		parametersDefined.push({name: paramName, type: paramTypeName, value: paramValue});

		var html = '<tr data-paramname="' + paramName + '">' + 
				'<td style="border-color: #888686;">' + paramName + '</td>' +
				'<td style="border-color: #888686;">' + paramTypeName+ '</td>' +
				'<td style="border-color: #888686;">' + paramValue + '</td>' +
				'<td style="border-color: #888686;"><button class="btn-primary parameter-delete-btn" style="width: 100%;" data-paramname="' + paramName + '" >Delete</button></td>' +
			'</tr>';

		$(html).appendTo($('#parameter-table-body'));

		$('#parameters-table').show();

		$('.parameter-delete-btn').on('click', function(event){
			var paramToDelete = event.currentTarget.attributes['data-paramname'].value;

			parametersDefined = parametersDefined.filter(function( obj ) {
			    return obj.name !== paramToDelete;
			});

			$('#parameters-table tr[data-paramname="'+paramToDelete+'"]').remove();

			if (parametersDefined.length == 0)
			{
				$('#parameters-table').hide();
			}
		});

	});

	$('#parameter-type-select').on('change', function(event) {
		var v = $('#parameter-type-select').val();

		if (v != 0) 
		{
			$('#parameter-value-input').val('');
			$('#parameter-value-input').prop('readonly', true);
		}
		else
		{
			$('#parameter-value-input').prop('readonly', false);
		}
	});

}

function expand_collapse_section(section)
{
	if ($('#' + section + '-toggle')[0].attributes['data-toggle'].value  == 'expand')
	{
		$('#' + section + '-body').show();
		$('#' + section + '-toggle-icon').removeClass('fa-plus-square').addClass('fa-minus-square');
		$('#' + section + '-toggle')[0].attributes['data-toggle'].value = 'collapse';
	}
	else
	{
		$('#' + section + '-body').hide();
		$('#' + section + '-toggle-icon').removeClass('fa-minue-square').addClass('fa-plus-square');
		$('#' + section + '-toggle')[0].attributes['data-toggle'].value = 'expand';
	}

}

function update_report_file_status(reportFileId, status, e)
{

	get_api_token(perform_update_report_file_status, reportFileId, status, e)
}

function perform_update_report_file_status(token, args)
{
	var reportFileId = args[1];
	var status = args[2];
	var e = args[3];

	var URL = "/api/updatereportfilestatus?report_file_id=" + reportFileId + "&status=" + status;

	const AuthStr = 'Bearer '.concat(token); 

	$.ajax({
    		url: URL,
    		type: 'GET',
//    		data: { report_file_id: reportFileId, status: status},
    		dataType: 'json',
    		headers: { Authorization: AuthStr },
    		contentType: 'application/json; charset=utf-8',
   		success: function (result) {
   			if (!result.data.success)
   			{
   				display_error(result.data.message);
   				return;
   			}			

   			if (status == 0)
   			{
   				e.classList.remove("deactivate");
   				e.classList.add("activate");
   				e.innerText = "Inactive";
   				e.attributes['data-status'].value = 0;
   			}
   			else
   			{
   				e.classList.remove("activate");
   				e.classList.add("deactivate");
   				e.innerText = "Active";
   				e.attributes['data-status'].value = 1;
   			}

   			display_success('Report file status updated');
    		},
    		error: function (error) {
   			display_error(error);
    		}
    	});
}

function update_report_status(reportId, status, e)
{

	get_api_token(perform_update_report_status, reportId, status, e)
}

function perform_update_report_status(token, args)
{
	var reportId = args[1];
	var status = args[2];
	var e = args[3];

	var URL = "/api/updatereportstatus?report_id=" + reportId + "&status=" + status;

	const AuthStr = 'Bearer '.concat(token); 

	$.ajax({
    		url: URL,
    		type: 'GET',
//    		data: { report_file_id: reportFileId, status: status},
    		dataType: 'json',
    		headers: { Authorization: AuthStr },
    		contentType: 'application/json; charset=utf-8',
   		success: function (result) {
   			if (!result.data.success)
   			{
   				display_error(result.data.message);
   				return;
   			}			

   			if (status == 0)
   			{
   				e.classList.remove("deactivate");
   				e.classList.add("activate");
   				e.innerText = "Inactive";
   				e.attributes['data-status'].value = 0;
	   			display_success('Report has been deactivated');
   			}
   			else
   			{
   				e.classList.remove("activate");
   				e.classList.add("deactivate");
   				e.innerText = "Active";
   				e.attributes['data-status'].value = 1;
	   			display_success('Report has been activated');
   			}
    		},
    		error: function (error) {
   			display_error(error);
    		}
    	});
}

function get_api_token()
{
	var funcargs = arguments;

	if (apiToken != "")
	{
		funcargs[0](apiToken, funcargs);
		return;
	}

	const data = {
		grant_type: "client_credentials",
		client_id: 1,
		client_secret: "TbIOd2RcnaeOCGPbyx96SD7Hgl4fYU9M9JbV8mRF",
		scope: "*"
	};

	var TOKEN_URL = "/oauth/token";

	var jqxhr = $.post( TOKEN_URL, data, function() {
	})
  	.done(function(response) {
		console.log(response.data);
		apiToken = response.access_token;
		console.log('userresponse ' + response.access_token); 

		funcargs[0](apiToken, funcargs);
  	})
  	.fail(function() {
		display_error('Error retrieving authorization token');
  	});

}

function load_report_files(token, args)
{
	var reportId = args[1];
	var trElement = args[2];
	
	const report_files_header_names = [ 'Report File Id', 'Execution Type', 'Script Path', 'File Name', 'File Name Mask', 'URL', 'Template File', 'Status', 'Action'];
	const report_files_column_names = [ 'Id', 'execution_type_name', 'script_path', 'file_name', 'file_name_mask', 'url', 'template_file', 'active', 'action'];

	var URL = "/api/reportfiles?report_id=" + reportId;

	const AuthStr = 'Bearer '.concat(token); 

	$.ajax({
    		url: URL,
    		type: 'GET',
    		dataType: 'json',
    		headers: { Authorization: AuthStr },
    		contentType: 'application/json; charset=utf-8',
   		success: function (result) {
   			if (!result.data.success)
   			{
   				display_error(result.data.message);
   				return;
   			}

			var reportFileHtml = '<tr id="report-file-div-' + reportId + '">' + '<td colspan="8"><div class="card"><div class="card-body"><table class="table table-striped table-bordered table-sm pagetable" style="width: 100%;"><thead><tr>';

			report_files_header_names.forEach(function (c) {
				reportFileHtml += '<th>' + c + '</th>';
			});

			reportFileHtml += '</tr></thead><tbody>';

			var colValue, tdValue, className;

			result.data.data.forEach(function (d) {
				reportFileHtml += '<tr>';

				report_files_column_names.forEach(function(c){

					if (c == 'active')
					{
						colValue = d[c] ? 'Active' : 'Inactive';
						className = d[c] ? 'deactivate' : 'activate';
						tdValue = '<td><a href="javascript:void(0)" class="reportfilestatus"><span class="reportfilestatus actionTags ' + className + '" data-reportfileid="' + d.Id + '" data-status="' + d[c] + '">' + colValue + '</span></a></td>';
					}
					else if (c == 'action')
					{
						tdValue = '<td><div style="text-align: center;"><a class="reportfileedit btn btn-primary" data-toggle="modal" data-title="Edit Report File" data-savebuttontext="Update" data-target="#addReportFileSection" data-reportfileid="' + d.Id + '">Edit</a></div></td>';
					}
					else {
						if (d[c])
						{
							colValue = d[c];
						}
						else
						{
							colValue = '&nbsp;';
						}

						tdValue = '<td>' + colValue + '</td>';
					}					

					reportFileHtml += tdValue;
				});

				reportFileHtml += '</tr>';
			});

			reportFileHtml += '</tbody></table></div></div></td></tr>';

			$(reportFileHtml).insertAfter(trElement);				

			$('.reportfilestatus').on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var reportFileId = e.currentTarget.attributes['data-reportfileid'].value;
				var status = e.currentTarget.attributes['data-status'].value;
				if (status == 0)
				{
					status = 1;
				}
				else
				{
					status = 0;
				}

				update_report_file_status(reportFileId, status, e.currentTarget);
			});

   			display_success('Report files retrieved');
    		},
    		error: function (error) {
   			display_error(error.statusText);
    		}
	});

}
function get_report_files(reportId, trElement)
{
	get_api_token(load_report_files, reportId, trElement);
}