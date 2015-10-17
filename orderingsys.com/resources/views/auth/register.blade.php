@extends('essentials')

@section('page')

  <div class="center row">
    <div class="section-container tabs large-12 columns" data-section="tabs">
      <section>
        <div class="content" data-section-content>
          <p>
            <div class="row">
              <div class="large-12 columns">
                <div class="signup-panel">
                  <p class="welcome">Advertisement Ordering System</p>

                  <form method="POST" action="/auth/register">
                    {!! csrf_field() !!}
                    <div class="row collapse">
                      <div class="small-2 columns">
                        <span class="prefix"><i class="fi-torso-female"></i></span>
                      </div>
                      <div class="small-10  columns">
                        <input type="text" placeholder="Full Name" name="name" value="{{ old('name') }}">
                      </div>
                    </div>

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
                      <div class="small-2 columns ">
                        <span class="prefix"><i class="fi-lock"></i></span>
                      </div>
                      <div class="small-10 columns ">
                        <input type="password" placeholder="Password Confirmation" name="password_confirmation">
                      </div>
                    </div>

                    <div class="row collapse">
                      <div class="small-12 columns text-center">
                        <button type="submit">Submit</button>
                      </div>
                    </div>
                  </form>

                </div>
              </div>
            </div>
          </p>
        </div>
      </section>
    </div>
  </div>

@endsection