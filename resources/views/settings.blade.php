<form method="POST" action="/email/reset" data-abide>
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <fieldset>
    <legend>Settings</legend>

    <div class="row" data-equalizer>
	  <div class="large-6 columns panel" data-equalizer-watch>

	    <!-- <label for="password">New password
          <input type="password" id="password" placeholder="Password." name="password" required>
        </label>
        <small class="error">Passwords must be at least 8 characters with 1 capital letter, 1 number, and one special character.</small>

	    <label for="confirmPassword">Confirm Password
          <input type="password" id="confirmPassword" placeholder="Password Confirmation." name="confirmPassword" data-equalto="password" required>
        </label>
        <small class="error">Passwords must match.</small> -->

        <label for="email"> New Email
          <input type="email" name="email" id="email" placeholder="example@netbay.com.au" required>
        </label>
        <small class="error">Valid email required.</small>

	  </div>
	</div>

    <div class="row">
      <div class="large-12 columns">
        <button type="submit" class="medium button green right small">Save</button>
      </div>
    </div>

  </fieldset>
</form>