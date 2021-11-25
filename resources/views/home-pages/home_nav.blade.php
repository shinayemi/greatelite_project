<body>

  <!-- start page-wrapper -->
  <div class="page-wrapper">

    <!-- start preloader -->
    <div class="preloader">
      <div class="lds-ripple">
        <div></div>
        <div></div>
      </div>
    </div>
    <!-- end preloader -->

    <!-- Start header -->
    <header id="header" class="site-header header-style-1">
      <nav class="navigation navbar navbar-default">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="open-btn">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{env('APP_URL')}}"><img src="assets_public/images/logo2.png" alt></a>
          </div>
          <div id="navbar" class="navbar-collapse collapse navigation-holder">
            <button class="close-navbar"><i class="ti-close"></i></button>
            <ul class="nav navbar-nav">
              <li>
                <a href="{{$appUrl}}">Home</a>
              </li>
              <li>
                <a href="{{$appUrl}}about">About</a>
              </li>
              <li>
                <a href="{{$appUrl}}terms">Terms & Privacy</a>
              </li>
              <li><a href="{{$appUrl}}contactus">Contact</a></li>
            </ul>

          </div><!-- end of nav-collapse -->

          <div class="quote-btn">
            <!-- <a href="{{$appUrl}}" class="theme-btn" data-toggle="tooltip" data-placement="left" title="Great Elite is opening 7 September 2020 at 6pm WAT">COMING SOON!!!</a> -->
            <a href="{{$appUrl}}register" class="theme-btn">Register</a>
            <a href="{{$appUrl}}login" class="theme-btn">Login</a>
          </div>
        </div><!-- end of container -->
      </nav>
    </header>
    <!-- end of header -->