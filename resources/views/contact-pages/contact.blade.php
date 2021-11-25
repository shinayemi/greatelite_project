	<!-- BANNER -->
	<div class="section banner-page about">
		<div class="container">
			<div class="row">
				<div class="col-sm-12 col-md-12">
					<div class="title-page">Contact Us</div>
					<ol class="breadcrumb">
						<li><a href="{{$appUrl}}">Home</a></li>
						<li class="active">Contact Us</li>
					</ol>
				</div>
			</div>
		</div>
	</div>

	<!-- Contact -->
	<div class="section contact overlap">
		<div class="container">
			<div class="row">
				<div class="col-sm-4 col-md-4 col-md-push-8">
					<div class="widget download">
						<a href="#" class="btn btn-secondary btn-block btn-sidebar"><span class="fa  fa-file-pdf-o"></span> Company Brochure</a>
					</div>
					<div class="widget contact-info-sidebar">
						<div class="widget-title">
							Contact Info
						</div>
						<ul class="list-info">
							<li>
								<div class="info-icon">
									<span class="fa fa-map-marker"></span>
								</div>
								<div class="info-text">{{$address}}</div> </li>
							<li>
								<div class="info-icon">
									<span class="fa fa-phone"></span>
								</div>
								<div class="info-text">{{$telephone}}</div>
							</li>
							<li>
								<div class="info-icon">
									<span class="fa fa-envelope"></span>
								</div>
								<div class="info-text">{{$email}}</div>
							</li>
							<li>
								<div class="info-icon">
									<span class="fa fa-clock-o"></span>
								</div>
								<div class="info-text">Mon - Sat 09:00 - 17:00</div>
							</li>
						</ul>
					</div> 

				</div>
				<div class="col-sm-8 col-md-8 col-md-pull-4">
					<div class="content">
						<p class="section-heading-3">Do reachout to us via telephone or email.</p>
						<div class="margin-bottom-30"></div>
						<h3 class="section-heading-2">
							Contact Details
						</h3>
						<form action="#" class="form-contact" id="contactForm" data-toggle="validator" novalidate="true">
							<div class="form-group">
								<input type="text" class="form-control" id="p_name" placeholder="Full Name..." required="">
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group">
								<input type="email" class="form-control" id="p_email" placeholder="Enter Address..." required="">
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group">
								<input type="text" class="form-control" id="p_subject" placeholder="Subject...">
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group">
								 <textarea id="p_message" class="form-control" rows="6" placeholder="Write message"></textarea>
								<div class="help-block with-errors"></div>
							</div>
							<div class="form-group">
								<div id="success"></div>
								<button type="submit" class="btn btn-secondary disabled" style="pointer-events: all; cursor: pointer;">SEND MESSAGE</button>
							</div>
						</form>
						<div class="margin-bottom-50"></div>
						<!-- <p><em>Note: Consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</em></p> -->
					 </div>
				</div>

			</div>
			
		</div>
	</div>	

	<!-- MAPS -->
	<div class="maps-wraper visible-md visible-lg">
		<div id="cd-zoom-in"></div>
		<div id="cd-zoom-out"></div>
    <div class="mapouter"><div class="gmap_canvas"><iframe width="1582" height="500" id="gmap_canvas" src="https://maps.google.com/maps?q=Corporate%20office%3A%20Nigeria%20Reinsurance%20House%2046%20Marina%2C%20Lagos.&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://www.whatismyip-address.com/nordvpn-coupon/">nordvpn best deal</a></div><style>.mapouter{position:relative;text-align:right;height:500px;width:1582px;}.gmap_canvas {overflow:hidden;background:none!important;height:500px;width:1582px;}</style></div>

	<!-- INFO BOX -->
	<div class="section info overlap-bottom">
		<div class="container">
			<div class="row">
				
				<div class="col-sm-4 col-md-4">
					<!-- BOX 1 -->
					<div class="box-icon-4">
						<div class="icon"><i class="fa fa-phone"></i></div>
						<div class="body-content">
							<div class="heading">CALL US NOW</div>
							{{$telephone}}
						</div>
					</div>
				</div>
				<div class="col-sm-4 col-md-4">
					<!-- BOX 2 -->
					<div class="box-icon-4">
						<div class="icon"><i class="fa fa-map-marker"></i></div>
						<div class="body-content">
							<div class="heading">COME VISIT US</div>
							{{$address}}
						</div>
					</div>
				</div>
				<div class="col-sm-4 col-md-4">
					<!-- BOX 3 -->
					<div class="box-icon-4">
						<div class="icon"><i class="fa fa-envelope"></i></div>
						<div class="body-content">
							<div class="heading">SEND US A MESSAGE</div>
							{{$email}}
						</div>
					</div>
				</div>
				
			</div>

		</div>
	</div>