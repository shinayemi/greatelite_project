<body>

    <!---Preloader Starts Here--->
    <div id="ip-container" class="ip-container">
        <header class="ip-header">
            <h1 class="ip-logo text-center"><img class="img-fluid" src="{{$appUrl}}assets/images/logo-c.png" alt="" class="ip-logo text-center" /></h1>
            <div class="ip-loader">
                <svg class="ip-inner" width="60px" height="60px" viewBox="0 0 80 80">
                    <path class="ip-loader-circlebg" d="M40,10C57.351,10,71,23.649,71,40.5S57.351,71,40.5,71 S10,57.351,10,40.5S23.649,10,40.5,10z" />
                    <path id="ip-loader-circle" class="ip-loader-circle" d="M40,10C57.351,10,71,23.649,71,40.5S57.351,71,40.5,71 S10,57.351,10,40.5S23.649,10,40.5,10z" />
                </svg>
            </div>
        </header>
    </div>
    <!---Preloader Ends Here--->

    <section style="background: url({{$appUrl}}assets/images/macbook-laptop-ipad-apple-38519.jpeg?w=940&amp;h=650&amp;auto=compress&amp;cs=tinysrgb);background-size: cover">
        <div class="height-100-vh bg-primary-trans">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-7 col-lg-5">
                        <div class="register-div">
                            <p class="logo mb-1"><a href="{{$appUrl}}" target="blank"><i class="fa fa-home"></i></a>{{$appName}} - Register</p>
                            <p class="mb-4" style="color: #a5b5c5">Create an account.</p>

                            @if($success)
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <p><strong>Registration Success!</strong> Confirm your email ({{$formValues['email']}}) by clicking the confirmation link sent.</p>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif

                            <form method="post" id="needs-validation" action="{{url('register/')}}/{{$referrer}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="referrer" value="{{$referrer}}">

                                @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <div class="form-group">
                                    <label>First Name</label>
                                    <input value="{{$formValues['first_name']}}" class="form-control input-lg" name="first_name" placeholder="Enter first name" type="text" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>

                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input value="{{$formValues['last_name']}}" class="form-control input-lg" name="last_name" placeholder="Enter last name" type="text" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input value="{{$formValues['email']}}" class="form-control input-lg" name="email" placeholder="Enter email address" type="email" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>

                                <div class="form-group">
                                    <label>Create Password</label>
                                    <input class="form-control input-lg" name="password" placeholder="Create strong password" type="password" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>

                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input class="form-control input-lg" name="password_confirmation" placeholder="Confirm password" type="password" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>

                                <div class="checkbox">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" required name="agreed_to_terms" value="yes">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">I agree to the {{$appName}} <a href="{{$appUrl}}terms" target="blank" class="btn-link">Terms and Privacy</a>.</span>
                                    </label>
                                </div>
                               
                                <button class="btn btn-primary mt-2">Register</button>
                                <br>
                                <small class="text-muted mb-1 d-block"><strong>Upline :</strong>  {{$uplineUserObj->first_name}} {{$uplineUserObj->last_name}}</small>
                            
                                <small class="text-muted mt-5 mb-1 d-block">Already have an account? <a href="{{$appUrl}}login">Login Now!</a></small>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>