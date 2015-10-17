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

                  <form data-abide method="POST" action="/auth/register">
                    {!! csrf_field() !!}
                    <div class="row collapse">
                      <div class="small-2 columns">
                        <span class="prefix"><i class="fi-torso-female"></i></span>
                      </div>
                      <div class="small-10  columns">
                        <input required type="text" placeholder="Full Name" name="name" value="{{ old('name') }}">
                        <small class="error">A valid name is required.</small>
                      </div>
                    </div>

                    <div class="row collapse">
                      <div class="small-2 columns">
                        <span class="prefix"><i class="fi-torsos-all"></i></span>
                      </div>
                      <div class="small-10  columns">
                        <select class="select-register" required name="company">
                          <option value="" selected disabled>Please choose a company</option>
                          @foreach (\App\Company::all() as $company)
                              <option value="{{$company->name}}">{{$company->name}}</option>
                          @endforeach
                        </select>
                        <small class="error">A valid company name is required.</small>
                      </div>
                    </div>

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
                      <div class="small-2 columns ">
                        <span class="prefix"><i class="fi-lock"></i></span>
                      </div>
                      <div class="small-10 columns ">
                        <input required type="password" placeholder="Password Confirmation" name="password_confirmation" data-equalto="password">
                        <small class="error">The password did not match</small>
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