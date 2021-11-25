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
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="login-div">
                            <p class="logo mb-1"><a href="{{$appUrl}}" target="blank"><i class="fa fa-home"></i></a>{{$appName}} - Login</p>
                            <p class="mb-4" style="color: #a5b5c5">login into your account</p>
                            <form method="post" id="needs-validation" action="{{url('login/')}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                @if(!empty($message))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <p><strong>Note : </strong> {{$message}}</p>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
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
                                    <label>Email Address</label>
                                    <input name="email" class="form-control input-lg" placeholder="email" type="email" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input name="password" class="form-control input-lg" placeholder="password" type="password" required>
                                    <div class="invalid-feedback">This field is required.</div>
                                </div>

                                <div class="checkbox">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">Remember me</span>
                                    </label>
                                </div>
                                <small style="font-size: 0.5em;" class="text-muted mt-5 mb-1 d-block"><a href="{{$appUrl}}passwordRecovery">Forgot password </a></small>

                                <button class="btn btn-primary mt-2">Login</button>

                                <small class="text-muted mt-5 mb-1 d-block">Don't have an account? <a href="{{$appUrl}}register">Register Now!</a></small>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>