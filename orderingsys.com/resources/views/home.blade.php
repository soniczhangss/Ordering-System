@extends('essentials')

@section('page')

  	@include('topbar')
  	@include('sidebar')
  	<div class="row">

  		<div id="reveal-modal-container"></div>

  		<div class="large-11 large-offset-1 columns">

  			<div class="my-panel clearfix">
	            <!-- <section class="left">
		            <ul class="secondary button-group">
			             <li><a href="#" class="button small secondary"><</a></li>
			             <li class="my-text-justify"><p><h3>August 2015</h3></p></li>
			             <li><a href="#" class="button small secondary">></a></li>
		            </ul>
	            </section> -->

	          <section class="right">
		           <ul class="round secondary button-group toggle even-2">
			           <li><a class="button secondary switch_month_year">Month</a></li>
			           <li><a class="button secondary switch_month_year">Year</a></li>
		           </ul>
	          </section>

	          <div id="container">
	          </div>
	        </div>

	        <!-- <div id="container">
	        </div> -->
    		
    	</div>
  	</div>
  	
@endsection