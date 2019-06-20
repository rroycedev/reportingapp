@extends('layouts.app', ['activePage' => 'reportmgmt', 'titlePage' => __('Report Management')])

 <style>
  #draggable { width: 100px; height: 100px; padding: 0.5em; float: left; margin: 10px 10px 10px 0; }
  #droppable { width: 150px; height: 150px; padding: 0.5em; float: left; margin: 10px; }
  </style>

@section('content')
  <div class="content">
	<div id="errordiv" class="alert alert-danger alert-dismissible fade" role="alert">
	  <div id="errormsg"></div>
	  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	    <span aria-hidden="true">&times;</span>
	  </button>
	</div>
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
                				<td><span class="reportstatus actionTags deactivate">{{ $report->active ? 'Active' : 'Inactive'}}</span></td>
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

@endsection

@push('js')
	<script>
    	    	$(document).ready(function() {
			InitReportManagement();
		});
	</script>
@endpush
