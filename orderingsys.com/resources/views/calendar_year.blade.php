@include('vendor.calendar')
<ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">
      
<?php
$calendar = new Calendar(null, null, null, $orders);
for ($i=1; $i<=12; $i++)
{
  //echo "string";
  print("<li>".$calendar->output_calendar("2015", $i)."</li>");
}
?>

</ul>