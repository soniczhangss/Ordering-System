@extends('essentials')

@section('page')

  	<div class="off-canvas-wrap" data-offcanvas>
	    <div class="inner-wrap">

	    <nav class="tab-bar">
		    <section class="left-small">
		    	<a class="left-off-canvas-toggle menu-icon" ><span></span></a>
		    </section>

		    <a class="fi-power my-logout-button right"></a>
		    <div class="right"><h1>G'day {{ Auth::user()->name }}</h1></div>
	    </nav>

	    <aside class="left-off-canvas-menu">
	        <ul class="off-canvas-list">
		        <li><label>Netbay Internet</label></li>
		        <li class="has-submenu"><a href="#">Locations</a>
		            <ul class="left-submenu">
		                <li class="back"><a href="#">Back</a></li>
		                @foreach ($locations as $location)
		                	<li><a class="location">{{ $location->name }}</a></li>
						@endforeach
		            </ul>
		        </li>
		        <li><a id="settings">Settings</a></li>
		        @if ((\Auth::user()->role_id != 4) and (\Auth::user()->role_id != 3))
		        	<li><a id="location-management">Location Management</a></li>
				    <li><a id="account-management">Account Management</a></li>
				    <li><a id="company-management">Company Management</a></li>
				    <li><a id="report">Report</a></li>
				@endif
		        
	        </ul>
	    </aside>

	    <section class="main-section">
	        <div class="row">

		  		<div id="reveal-modal-container"></div>
		  		
		  		<div class="large-12 columns">
		  			<ul class="breadcrumbs">
						<li><a href="/">Home</a></li>
						<li><a id="bc-location" href="#">{{ $locations->first()->name }}</a></li>
					</ul>

					<div id="content-container">
						<ul class="button-group radius left">
					        <li><a class="tiny button secondary fi-arrow-left"></a></li>
					    	<li><a class="tiny button secondary fi-arrow-right"></a></li>
					    	<li><label class="nav-date"></label></li>
					  	</ul>

						<ul class="button-group radius right">
					        <li><a class="tiny button secondary switch_month_year">Month</a></li>
					    	<li><a class="tiny button secondary switch_month_year">Year</a></li>
					  	</ul>

					  	<div id="container" class="panel">
					  		
					  	</div>
					</div>
		  			
		    	</div>
	  	    </div>
	    </section>
	    

	  <a class="exit-off-canvas"></a>

	  </div>
	</div>
  	
@endsection