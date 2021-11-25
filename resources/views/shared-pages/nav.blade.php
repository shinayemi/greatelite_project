<body>

<!-- BACK TO TOP SECTION -->
<a href="#0" class="cd-top cd-is-visible cd-fade-out">Top</a>

<!-- HEADER -->
  <div class="header">
    <!-- TOPBAR -->
  <div class="topbar">
    <div class="container">
      <div class="row">
        <div class="col-sm-5 col-md-6">
          <div class="topbar-left">
            <div class="welcome-text">
              {{$appName}}
            </div>
          </div>
        </div>
        <div class="col-sm-7 col-md-6">
          <div class="topbar-right">
            <ul class="topbar-menu">
              <li><a href="{{$appUrl}}career" title="Career">Career</a></li>
              <li><a href="{{$appUrl}}contact" title="Contact Us">Contact Us</a></li>
            </ul>
            <ul class="topbar-sosmed">
            <li>
              <a href="{{$facebook}}"><i class="fa fa-facebook"></i></a>
            </li>
            <li>
              <a href="{{$twitter}}"><i class="fa fa-twitter"></i></a>
            </li>
            <li>
              <a href="{{$instagram}}"><i class="fa fa-instagram"></i></a>
            </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- TOPBAR LOGO SECTION -->
  <div class="topbar-logo">
    <div class="container">
      

      <div class="contact-info">
        <!-- INFO 1 -->
        <div class="box-icon-1">
          <div class="icon">
            <div class="fa fa-envelope-o"></div>
          </div>
          <div class="body-content">
            <div class="heading">Email Support</div>
            {{$email}}
          </div>
        </div>
        <!-- INFO 2 -->
        <div class="box-icon-1">
          <div class="icon">
            <div class="fa fa-phone"></div>
          </div>
          <div class="body-content">
            <div class="heading">Call Support</div>
            {{$telephone}}  
          </div>
        </div>
        <!-- INFO 3 -->
        <!-- <a href="{{$appUrl}}contact" title="" class="btn btn-cta pull-right">GET A QUOTE</a> -->

      </div>
    </div>
  </div>

  <!-- NAVBAR SECTION -->
  <div class="navbar navbar-main">
  
    <div class="container container-nav">
      <div class="rowe">
          
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
        </div>

        <a class="navbar-brand" href="{{$appUrl}}">
          <img src="images/COSLO_DRILLS.png" alt="{{$appName}}" title="{{$appName}}" style="width: 30%" />
        </a>

        <nav class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-left">
            <li>
              <a href="{{$appUrl}}">HOME </a>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" role="button" aria-haspopup="true" aria-expanded="false">ABOUT US <span class="caret"></span></a>
              <ul class="dropdown-menu">
              <li><a href="{{$appUrl}}about">ABOUT US</a></li>
              <li><a href="{{$appUrl}}hse">HSE POLICY</a></li>
              <li><a href="{{$appUrl}}team">OUR TEAM</a></li>
              </ul>
            </li>
            <li>
              <a href="{{$appUrl}}services" >OUR AREA OF EXPERTISE </a>
            </li>
            <li>
              <a href="{{$appUrl}}#clients">OUR CLIENTS</a>
            </li>
            <li>
              <a href="{{$appUrl}}contact">CONTACT US</a>
            </li>
            

          </ul>

          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-search"></i></a>
                <ul class="dropdown-menu">
                <li>
                  <form class="navbar-form navbar-left" role="search">
                    <div class="form-group">
                      <input type="text" class="form-control" placeholder="Type and hit enter">
                    </div>
                  </form>
                </li>
                </ul>
            </li>
          </ul>

        </nav>
          
      </div>
    </div>
    </div>

  </div>