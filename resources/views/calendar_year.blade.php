@include('vendor.calendar')

<ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-6">
      
<?php
$calendar = new Calendar(null, null, null, $orders, $capacity);
for ($i=1; $i<=12; $i++)
{
  //echo "string";
  print("<li>".$calendar->output_calendar($date->year, $i)."</li>");
}
?>

</ul>