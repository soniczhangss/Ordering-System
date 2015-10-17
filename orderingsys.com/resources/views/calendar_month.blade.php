@include('vendor.calendar')
<ul class="small-block-grid-1 medium-block-grid-1 large-block-grid-1">

<?php
$calendar = new Calendar(null, null, null, $orders);
print("<li>".$calendar->output_calendar()."</li>");
?>
</ul>