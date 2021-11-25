<!--Page Container-->
<section class="page-container" id="profileBlock">
    <div class="page-content-wrapper">
        <!--Header Fixed-->
        <div class="header fixed-header">
            <div class="container-fluid" style="padding: 10px 25px">
                <div class="row">
                    <div class="col-9 col-md-6 d-lg-none">
                        <a id="toggle-navigation" href="javascript:void(0);" class="icon-btn mr-3"><i class="fa fa-bars"></i></a>
                        <span class="logo">Great Elites</span>
                    </div>
                    <div class="col-lg-8 d-none d-lg-block">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Great Elites {{$pageTitle}}</a></li>
                        </ol>
                    </div>
                    <div class="col-3 col-md-6 col-lg-4">
                        <!-- <a href="javascript:void(0);" class="btn btn-primary btn-round pull-right d-none d-md-block">Buy Theme Now</a> -->
                    </div>
                </div>
            </div>
        </div>


        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Confirm Payments</h4>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>
                                    Users that has sent proof of payment
                                </h5>
                            </div>

                            @for ($i = 0; $i < count($usersPayingArray); $i++) <p class="text-muted"><strong>Name:</strong> {{$usersPayingArray[$i]->first_name}} {{$usersPayingArray[$i]->middle_name}} {{$usersPayingArray[$i]->last_name}}</p>
                                <p class="text-muted"><strong>Email:</strong> {{$usersPayingArray[$i]->email}}</p>
                                <p class="text-muted"><strong>Mobile:</strong> {{$usersPayingArray[$i]->mobile}}</p>
                                <p class="text-muted"><strong>Amount:</strong> {{$currency}} {{number_format($pledgeTrackerReceiving[$i]->amount)}}</p>


                                @if(!empty($pledgeTrackerReceiving[$i]->proof))
                                <p class="text-muted"><a target="display" href="{{asset($pledgeTrackerReceiving[$i]->proof)}}">Proof Of Payment <i class="fa fa-file"></i></a></p>
                                <br>
                                <a class="confirmation btn btn-success" href="{{$appUrl}}confirmPayment/{{$pledgeTrackerReceiving[$i]->id}}/{{$pledgeTrackerReceiving[$i]->user_sending}}"><i class="fa fa-thumbs-up"></i> Confirm {{$usersPayingArray[$i]->first_name}} Payment</a>
                                @endif
                                <hr>

                                @endfor

                                <hr>
                                <form action="{{url('viewMergedForUserPaying/')}}/{{$user->id}}" method="post" class="horizontal-form">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    @if(!empty($testimonialMessage))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <p><strong>Note : </strong> {{$testimonialMessage}}</p>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    @endif

                                    <h5 class="text-center">
                                        Drop a testimonial please!
                                    </h5><br>
                                    <div class="form-group">
                                        <div class="form-row">
                                            <label class="col-md-3">Testimonial Message (Tell the world the good news!)</label>
                                            <div class="col-md-9">
                                                <textarea autocomplete="on" spellcheck="true" autocapitalize="on" autocomplete="off" class="form-control" required name="testimonial"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <button class="btn btn-primary mr-3" type="submit"><i class="fa fa-bullhorn"></i> Post Testimonial</button>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

<script>
    var elems = document.getElementsByClassName('confirmation');
    var confirmIt = function(e) {
        if (!confirm('Are you sure?')) e.preventDefault();
    };
    for (var i = 0, l = elems.length; i < l; i++) {
        elems[i].addEventListener('click', confirmIt, false);
    }
</script>