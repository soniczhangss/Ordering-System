<!-- Reveal Modal -->
<div id="myModal" class="reveal-modal text-center small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
	<br />
	<h2 id="modalTitle"></h2>
	<br />
	<br />
	<div class="row">
	    <div class="small-3 columns">
	      <label for="location" class="right inline">Location</label>
	    </div>
	    <div class="small-9 columns">
	      <input type="text" id="location" placeholder="Location name">
	    </div>
	</div>
	<div class="row">
	    <div class="small-3 columns">
	      <label for="allowance" class="right inline">Total Allowance</label>
	    </div>
	    <div class="small-9 columns">
	      <input type="text" id="allowance" placeholder="Maximum number of orders for this location">
	    </div>
	</div>
	<a class="button success">Confirm</a>
	<a class="button alert my-close-reveal-modal">Cancel</a>
	<a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<!-- End of reveal modal -->