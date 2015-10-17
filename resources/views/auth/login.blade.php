@extends('essentials')
<!-- <form method="POST" action="/auth/login">
                    {!! csrf_field() !!}
                    <div class="row collapse">
                      <div class="small-2 columns">
                        <span class="prefix"><i class="fi-mail"></i></span>
                      </div>
                      <div class="small-10  columns">
                        <input type="email" placeholder="Email Address" name="email" value="{{ old('email') }}">
                      </div>
                    </div>
                    <div class="row collapse">
                      <div class="small-2 columns ">
                        <span class="prefix"><i class="fi-lock"></i></span>
                      </div>
                      <div class="small-10 columns ">
                        <input type="password" placeholder="Password" name="password" id="password">
                      </div>
                    </div>
                    <div class="row collapse">
                      <div class="small-12 columns text-center">
                        <button type="submit">Login</button>
                      </div>
                    </div>
                  </form> -->
@section('page')

<div class="login row">
  <div class="large-6 columns auth-plain">

    <div class="signup-panel left-solid text-center">
      <p class="welcome">Registered Users</p>
      <form data-abide method="POST" action="/auth/login">
        {!! csrf_field() !!}
        <div class="row collapse">
          <div class="small-2 columns">
            <span class="prefix"><i class="fi-mail"></i></span>
          </div>
          <div class="small-10  columns">
            <input required type="email" placeholder="Email Address" name="email" value="{{ old('email') }}">
            <small class="error">A valid email address is required.</small>
          </div>
        </div>
        <div class="row collapse">
          <div class="small-2 columns ">
            <span class="prefix"><i class="fi-lock"></i></span>
          </div>
          <div class="small-10 columns ">
            <input required type="password" placeholder="Password" name="password" id="password">
            <small class="error">A valid password is required.</small>
          </div>
        </div>
        <div class="row collapse">
          <div class="small-12 columns text-center">
            <button type="submit">Log In</button>
          </div>
        </div>
      </form>
    </div>

  </div>

  <div class="large-6 columns auth-plain">

    <div class="signup-panel newusers text-center">
      <div class="row collapse">
        <div class="small-12 columns text-center">
          <p class="welcome"> New User?</p>
          <p>By creating an account with us, you will be able to view orders, and more.</p><br>
        </div>
      </div>
      <div class="row collapse">
        <div class="small-12 columns text-center"><br>
          <button id="sign-up">Sign Up</button>
        </div>
    </div>
    </div>
  </div>

</div>   

@endsection