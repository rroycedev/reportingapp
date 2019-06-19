var searchableCols =  [true, true, true, false, false, false, true, false];
var searchColWidths = ["100px", "100%", "100px", "100px", "100px", "100px", "100px", "100px"];

function InitReportManagement()
{
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
		    { "searchable": true, "targets": 6, "width": "50px" },
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
}

function get_report_files(reportId, trElement)
{
	const data = {
		grant_type: "client_credentials",
		client_id: 1,
		client_secret: "TbIOd2RcnaeOCGPbyx96SD7Hgl4fYU9M9JbV8mRF",
		scope: "*"
	};

	var TOKEN_URL = "/oauth/token";
	var URL = "/api/reportfiles?report_id=" + reportId;

	var jqxhr = $.post( TOKEN_URL, data, function() {
	})
  	.done(function(response) {
		console.log(response.data);
		USER_TOKEN = response.access_token;
		console.log('userresponse ' + response.access_token); 


		const AuthStr = 'Bearer '.concat(USER_TOKEN); 

		$.ajax({
            		url: URL,
            		type: 'GET',
            		dataType: 'json',
            		headers: { Authorization: AuthStr },
            		contentType: 'application/json; charset=utf-8',
           		success: function (result) {
           			if (!result.success)
           			{
           				display_error(result.message);
           				return;
           			}

				var reportFileHtml = '<tr id="report-file-div-' + reportId + '"><td colspan="8"><div class="card"><div class="card-body"><table class="table table-striped table-bordered table-sm pagetable" style="width: 100%;"><thead><tr><th>File Name</th><th>File Name Mask</th></tr></thead><tbody>';


				result.data.forEach(function (d) {
					reportFileHtml += '<tr><td>' + d.file_name + '</td><td>' + d.file_name_mask + '</td></tr>';
				});

				reportFileHtml += '</tbody></table></div></div></td></tr>';

				$(reportFileHtml).insertAfter(trElement);				

           			display_success('Report files retrieved');
            		},
            		error: function (error) {
           			display_error(error);
            		}
        	});

  	})
  	.fail(function() {
		display_error('Error retrieving report files');
  	});


}