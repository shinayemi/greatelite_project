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
                            <h4>Details of User To Pay</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>
                                    User To Send Money ({{$currency}}{{number_format($pledgeTrackerSending->amount)}})
                                </h5>
                            </div>
                            @if($userAccountOk)
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <h5 class="text-center" style="color: #ffffff"><i class="fa fa-user"></i> User Details</h5>
                                <hr>
                                <p>Name: {{$userAccount->first_name}} {{$userAccount->middle_name}} {{$userAccount->last_name}}</p>
                                <p>Email: {{$userAccount->email}}</p>
                                <p>Mobile: {{$userAccount->mobile}}</p>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="block form-block mb-4">
                                        @if(!empty($uploadMessage))
                                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                            <p><strong>Note : </strong> {{$uploadMessage}}</p>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        @endif
                                        <form enctype="multipart/form-data" method="post" action="{{url('viewMergedForPaying/')}}/{{$pledgeTrackerID}}">
                                            @if (!empty($uploadError))
                                            <div class="alert alert-danger">
                                                <ul>
                                                    <li>{{ $uploadError }}</li>
                                                </ul>
                                            </div>
                                            @endif
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <div class="fallback">
                                                <input name="file" type="file" required />
                                            </div>
                                            <hr>
                                            <div class="clearfix"></div>
                                            <button class="btn btn-primary mr-3" type="submit">Upload Proof Of Payment</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>Bank Account To Send Money ({{$currency}}{{number_format($pledgeTrackerSending->amount)}})</h5>
                            </div>
                            @if($userBankAccountOk)
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <h5 class="text-center" style="color: #ffffff"><i class="fa fa-money"></i> Active Bank Account</h5>
                                <hr>
                                <p>Bank: {{$bankAccount->bank}}</p>
                                <p>Account Name: {{$bankAccount->account_name}}</p>
                                <p>Account Number: {{$bankAccount->account_number}}</p>
                                <p>Mobile: {{$userAccount->mobile}}</p>
                                <hr>
                                <p>{{$user->first_name}} will be sending
                                    {{$currency}}{{number_format($pledgeTrackerSending->amount)}}, Please note payment needs to done and confirmed before
                                    {{date('l jS \of F Y h:i:s A', strtotime('+ 1 day',$dueDate))}}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</section>