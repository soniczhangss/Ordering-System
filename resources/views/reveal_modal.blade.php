<!-- Reveal Modal -->
<div id="myModal" class="reveal-modal text-center tiny" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
	<br />
	<h2 id="modalTitle"></h2>
	<br />
	@yield('accordion')
	<br />
	<a class="button success">Confirm</a>
	<a class="button alert my-close-reveal-modal">Cancel</a>
	<a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<!-- End of reveal modal -->