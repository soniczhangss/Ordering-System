<!-- Top bar -->
    <div class="fixed">
      <nav class="top-bar" data-topbar role="navigation">
        <ul class="title-area">

          <li class="name">
            <h1><a href="#"><b>Netbay Internet</b></a></h1>
          </li>

          <li class="toggle-topbar menu-icon"><a href="#">
            <span>Menu</span></a>
          </li>
          
        </ul>

        <section class="top-bar-section">
          <!-- Right Nav Section -->
          <ul class="right">
            <li class="has-dropdown">
              <a href="#">G'day, {{ \Auth::user()->name }}</a>
              <ul class="dropdown">
                <li><a href="/auth/logout"><i class="fi-power"></i></a></li>
              </ul>
            </li>
          </ul>
        </section>
      </nav>
    </div>
<!-- End of top bar -->