<div class="sidebar" data-color="orange" data-background-color="white" data-image="{{ asset('material') }}/img/sidebar-1.jpg">
  <!--
      Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

      Tip 2: you can also add an image using data-image tag
  -->
	<div class="logo">
    		<a href="https://creative-tim.com/" class="simple-text logo-normal">
      			<img src="/images/TU-logo_footer.gif" />
    		</a>
  	</div>
  	<div class="sidebar-wrapper">
    		<ul class="nav">
      			<li class="nav-item{{ $activePage == 'dashboard' ? ' active' : '' }}">
        			<a class="nav-link" href="{{ route('home') }}">
          				<i class="material-icons">dashboard</i>
            				<p>Reporting Portal</p>
        			</a>
      			</li>
      			<li class="nav-item {{ ($activePage == 'reportmgmt' || $activePage == 'groupmgmt') ? ' active' : '' }}">
        			<a class="nav-link" data-toggle="collapse" href="#reportmaintdiv" aria-expanded="true">
        	  			<i class="fa fa-folder-open {{ ($activePage == 'reportmgmt' || $activePage == 'groupmgmt') ? ' font-color-white' : '' }}"></i>
	          			<p>Report Maintenance
            					<b class="caret"></b>
          				</p>
        			</a>
				<div class="collapse show" id="reportmaintdiv" style="margin-left: 20px;margin-right: 20px;">
					<ul class="nav">
						<li class="nav-item{{ $activePage == 'reportmgmt' ? ' active' : '' }}">
                                                	<a class="nav-link" href="/reportmaint/reportmgmt"><i class="fa fa-chart-bar" style="color: rgba(0, 166, 202, 1);"></i>Reports</a>
                                                </li>
                                                <li class="nav-item{{ $activePage == 'groupmgmt' ? ' active' : '' }}">
                                                        <a class="nav-link" href="/reportmaint/groupmgmt"><i class="fa fa-envelope" style="color: rgba(0, 166, 202, 1);"></i>Email Groups</a>
                                                </li>
          				</ul>
        			</div>
      			</li>
			<!--  TSOP -->

                        <li class="nav-item {{ ($activePage == 'tsopjobstatus') ? ' active' : '' }}">
                                <a class="nav-link" data-toggle="collapse" href="#tsopdiv" aria-expanded="true">
                                        <i class="fa fa-folder-open {{ ($activePage == 'tsopjobstatus') ? ' font-color-white' : '' }}"></i>
                                        <p>TSOP
                                                <b class="caret"></b>
                                        </p>
                                </a>
                                <div class="collapse show" id="tsopdiv" style="margin-left: 20px;margin-right: 20px;">
                                        <ul class="nav">
                                                <li class="nav-item{{ $activePage == 'bizopsemployeesmgmt' ? ' active' : '' }}">
                                                        <a class="nav-link" href="/bizops/employees">Job Status</a>
                                                </li>
                                        </ul>
                                </div>
                        </li>

			<!--  Biz Ops -->

                        <li class="nav-item {{ ($activePage == 'bizopsemployeesmgmt' || $activePage == 'bizopsterritoriesmgmt') ? ' active' : '' }}">
                                <a class="nav-link" data-toggle="collapse" href="#bizopsdiv" aria-expanded="true">
                                        <i class="fa fa-folder-open {{ ($activePage == 'bizopsemployeesmgmt' || $activePage == 'bizopsterritoriesmgmt') ? ' font-color-white' : '' }}"></i>
                                        <p>Biz Ops Management
                                                <b class="caret"></b>
                                        </p>
                                </a>
                                <div class="collapse show" id="bizopsdiv" style="margin-left: 20px;margin-right: 20px;">
                                        <ul class="nav">
                                                <li class="nav-item{{ $activePage == 'bizopsemployeesmgmt' ? ' active' : '' }}">
                                                        <a class="nav-link" href="/bizops/employees">Employees</a>
                                                </li>
                                                <li class="nav-item{{ $activePage == 'bizopsterritoriesmgmt' ? ' active' : '' }}">
                                                        <a class="nav-link" href="/bizops/territories">Territories</a>
                                                </li>
                                        </ul>
                                </div>
                        </li>
                        <li class="nav-item {{ ($activePage == 'cidtranstraceback' || $activePage == 'cidiptracking') ? ' active' : '' }}">
                                <a class="nav-link" data-toggle="collapse" href="#ciddiv" aria-expanded="true">
                                        <i class="fa fa-folder-open {{ ($activePage == 'cidtranstraceback' || $activePage == 'cidiptracking')  ? ' font-color-white' : '' }}"></i>
                                        <p>CID Management
                                                <b class="caret"></b>
                                        </p>
                                </a>
                                <div class="collapse show" id="ciddiv" style="margin-left: 20px;margin-right: 20px;">
                                        <ul class="nav">
                                                <li class="nav-item{{ $activePage == 'cidtranstraceback' ? ' active' : '' }}">
                                                        <a class="nav-link" href="/cid/transactiontraceback">Transaction Traceback</a>
                                                </li>
                                                <li class="nav-item{{ $activePage == 'cidiptracking' ? ' active' : '' }}">
                                                        <a class="nav-link" href="/cid/iptracking">IP Tracking</a>
                                                </li>
                                        </ul>
                                </div>
                        </li>
                        <li class="nav-item {{ ($activePage == 'eomreportapproval') ? ' active' : '' }}">
                                <a class="nav-link" data-toggle="collapse" href="#financediv" aria-expanded="true">
                                        <i class="fa fa-folder-open {{ ($activePage == 'cidtranstraceback' || $activePage == 'cidiptracking')  ? ' font-color-white' : '' }}"></i>
                                        <p>Finance Management
                                                <b class="caret"></b>
                                        </p>
                                </a>
                                <div class="collapse show" id="financediv" style="margin-left: 20px;margin-right: 20px;">
                                        <ul class="nav">
                                                <li class="nav-item{{ $activePage == 'eomreportapproval' ? ' active' : '' }}">
                                                        <a class="nav-link" href="/reportmaint/reportmgmt">EOM Report Approval</a>
                                                </li>
                                        </ul>
                                </div>
                        </li>

    		</ul>
  	</div>
</div>
