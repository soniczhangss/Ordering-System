<div class="row">
  @include('revealModal')
  <div class="large-11 large-offset-1 columns">

    <div class="my-panel clearfix">
      <section class="left">
        <ul class="secondary button-group">
          <li><a href="#" class="button small secondary"><</a></li>
          <li class="my-text-justify"><p><h3>August 2015</h3></p></li>
          <li><a href="#" class="button small secondary">></a></li>
        </ul>
      </section>

      <section class="right">
        <ul class="round secondary button-group toggle even-2">
          <li><a id="month" class="button secondary">Month</a></li>
          <li><a id="year" class="button secondary">Year</a></li>
        </ul>
      </section>
    </div>

    <div id="calendar">
      @include('calendar_month')
    </div>

  </div>
</div>