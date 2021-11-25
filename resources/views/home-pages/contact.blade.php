        <!-- start page-title -->
        <section class="page-title">
          <div class="container">
            <div class="row">
              <div class="col col-xs-12">
                <h2>Contact</h2>
                <ol class="breadcrumb">
                  <li><a href="{{$appUrl}}">Home</a></li>
                  <li>Contact</li>
                </ol>
              </div>
            </div> <!-- end row -->
          </div> <!-- end container -->
        </section>
        <!-- end page-title -->


        <!-- start contact-pg-contact-section -->
        <section class="contact-pg-contact-section section-padding">
          <div class="container">
            <div class="row">
              <div class="col col-md-6">
                <div class="section-title-s3">
                  <h2>Our Contacts</h2>
                </div>
                <div class="contact-details">
                  <p>Contact us for questions and support. We will love to hear from you and help you reach financial freedom.</p>
                  <ul>
                    <li>
                      <div class="icon">
                        <i class="ti-mobile"></i>
                      </div>
                      <h5>Phone</h5>
                      <p>{{env('TELEPHONE')}} <a target="display" href="{{env('SOCIAL_WHATSAPP')}}"><i class="ti-themify-favicon-alt"></i></a></p>
                    </li>
                    <li>
                      <div class="icon">
                        <i class="ti-mobile"></i>
                      </div>
                      <h5>Phone2</h5>
                      <p> {{env('TELEPHONE2')}} <a target="display" href="{{env('SOCIAL_WHATSAPP2')}}"><i class="ti-themify-favicon-alt"></i></a></p>
                    </li>
                    <li>
                      <div class="icon">
                        <i class="ti-email"></i>
                      </div>
                      <h5>Email</h5>
                      <p>{{env('EMAIL')}}</p>
                    </li>
                  </ul>
                </div>
              </div>
              <div class="col col-md-6">
                <div class="contact-form-area">
                  <div class="section-title-s3">
                    <h2>Quick Contact Form</h2>
                  </div>
                  <div class="contact-form">
                    <form method="post" class="contact-validation-active" id="contact-form">
                      <div>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name*">
                      </div>
                      <div>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Email*">
                      </div>
                      <div>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone*">
                      </div>
                      <div>
                        <input type="text" class="form-control" name="address" id="address" placeholder="Address*">
                      </div>
                      <div class="comment-area">
                        <textarea name="note" id="note" placeholder="Case description*"></textarea>
                      </div>
                      <div class="submit-area">
                        <button type="submit" class="theme-btn-s2">Submit Now</button>
                        <div id="loader">
                          <i class="ti-reload"></i>
                        </div>
                      </div>
                      <div class="clearfix error-handling-messages">
                        <div id="success">Thank you</div>
                        <div id="error"> Error occurred while sending email. Please try again later. </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

          </div> <!-- end container -->
        </section>
        <!-- end contact-pg-contact-section -->