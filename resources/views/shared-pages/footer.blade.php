    <!-- FOOTER SECTION -->
    <div class="footer">
      
      <div class="container">
        
        <div class="row">
          <div class="col-sm-3 col-md-3">
            <div class="footer-item">
              <img style="background-color: #ffffff" src="images/logo.png" alt="{{$appName}}" title="{{$appName}}" class="logo-bottom img-rounded" />
              <p style="color: #ffffff;">Coslo Drills Energy and Integrated Services is an indigenous owned company who proffers consulting services for the Oil and Gas Company across the Equatorial Guinea. </p>
              <div class="footer-sosmed">
                <a href="{{$facebook}}" title="" target="display">
                  <div class="item">
                    <i class="fa fa-facebook"></i>
                  </div>
                </a>
                <a href="{{$twitter}}" title="" target="display">
                  <div class="item">
                    <i class="fa fa-twitter"></i>
                  </div>
                </a>
                <a href="{{$instagram}}" target="display" title="">
                  <div class="item">
                    <i class="fa fa-instagram"></i>
                  </div>
                </a>
              </div>
            </div>
          </div>
          <div class="col-sm-3 col-md-3">

          </div>
          <div class="col-sm-3 col-md-3">
            <div class="footer-item">
              <div class="footer-title">
                Our Services
              </div>
              <ul class="list">
                <li><a href="{{$appUrl}}services" title="">Spot Opportunity</a></li>
                <li><a href="{{$appUrl}}services" title="">Make sound Business Decisions</a></li>
                <li><a href="{{$appUrl}}services" title="">Grow your pipeline</a></li>
                <li><a href="{{$appUrl}}services" title="">Procurement</a></li>
                <li><a href="{{$appUrl}}services" title="">Project Management </a></li>
                <li><a href="{{$appUrl}}services" title="">Technical Manpower Supply</a></li>
              </ul>
            </div>
          </div>
          <div class="col-sm-3 col-md-3">
            <div class="footer-item">
              <div class="footer-title">
                Subscribe
              </div>
              <p> Stay updated by joining our newsletter list!</p>
              <form action="#" class="footer-subscribe">
                      <input type="email" name="EMAIL" class="form-control" placeholder="enter your email">
                      <input id="p_submit" type="submit" value="send">
                      <label for="p_submit"><i class="fa fa-envelope"></i></label>
                      <p>Get latest updates and offers.</p>
                    </form>
            </div>
          </div>
        </div>
      </div>
      
      <div class="fcopy">
        <div class="container">
          <div class="row">
            <div class="col-sm-12 col-md-12">
              <p class="ftex">&copy; {{date("Y")}} {{$appName}} - All Rights Reserved</p> 
            </div>
          </div>
        </div>
      </div>
      
    </div>
    
    <!-- JS VENDOR -->
    <script type="text/javascript" src="{{ asset('js/vendor/jquery.min.js') }} "></script>
    <script type="text/javascript" src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/jquery.superslides.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/owl.carousel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/bootstrap-hover-dropdown.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/jquery.magnific-popup.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/easings.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendor/isotope.pkgd.min.js') }}"></script>

    <!-- sendmail -->
    <script type="text/javascript" src="{{ asset('js/vendor/validator.min.js') }}"></script>
    <!-- <script type="text/javascript" src="{{ asset('js/vendor/form-scripts.js') }}"></script> -->
    
    <script type='text/javascript' src='https://maps.google.com/maps/api/js?sensor=false&amp;ver=4.1.5'></script>

    <script type="text/javascript" src="{{ asset('js/script.js') }}"></script>

      
  </body>
</html>