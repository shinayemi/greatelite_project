<!--Page Container-->
<section class="page-container">
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
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
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
                            <h4>{{$user->first_name}} {{$user->last_name}}
                                @if($user->confirm_mobile == 'YES' && !is_null($bankAccount) && $user->registration_fee == 'PAID')
                                <span class="badge badge-success">VERIFIED</span>
                                @endif

                                @if($user->confirm_mobile == 'NO' || is_null($bankAccount) || $user->registration_fee != 'PAID')
                                <a href="{{$appUrl}}profile" data-toggle="tooltip" data-placement="right" title="Click to profile, validate mobile and input bank details"><span class="badge badge-warning">NOT VERIFIED</span></a>
                                @endif

                            </h4>
                        </div>
                    </div>

                    @if(!empty($unCompletedPledgesObj))
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="block counter-block mb-4">
                            <div class="value text-danger">{{$currency}}{{number_format($unCompletedPledgesObj[0]->amount)}}</div>
                            <!-- <div class="trending trending-up">
                                <span>12%</span>
                                <i class="fa fa-caret-up"></i>
                            </div> -->
                            <p class="label">Pledge</p>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="block counter-block mb-4">
                            <div class="value text-warning  ">{{$currency}}{{number_format($unCompletedPledgesObj[0]->interest)}}</div>
                            <!-- <div class="trending trending-down-basic">
                                <span>12%</span>
                                <i class="fa fa-long-arrow-up"></i>
                            </div> -->
                            <p class="label">Excepted Profit</p>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="block counter-block mb-4">
                            <div class="value text-success">{{$currency}}{{number_format($unCompletedPledgesObj[0]->amount_paid_confirmed)}}</div>
                            <!-- <div class="trending trending-down-basic">
                                <span>12%</span>
                                <i class="fa fa-long-arrow-up"></i>
                            </div> -->
                            <p class="label">Paid And Confirmed</p>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="block counter-block mb-4">
                            <div class="value text-success">{{$currency}}{{number_format($unCompletedPledgesObj[0]->amount_received_and_confirmed)}}</div>
                            <!-- <div class="trending trending-down-basic">
                                <span>12%</span>
                                <i class="fa fa-long-arrow-up"></i>
                            </div> -->
                            <p class="label">Received And Confirmed</p>
                        </div>
                    </div>
                    @endif

                    @if(!empty($allUnpaidBonusTotal))
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="block counter-block mb-4">
                            <div class="value">{{$currency}}{{number_format($allUnpaidBonusTotal)}}</div>
                            <!-- <div class="trending trending-down-basic">
                                <span>12%</span>
                                <i class="fa fa-long-arrow-up"></i>
                            </div> -->
                            <p class="label">Excepted Bonus</p>
                        </div>
                    </div>
                    @endif


                    @if(!empty($fullFilledPledge->when_due))
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="block counter-block counter-bg-img mb-4" style="background: url(assets/images/counter-bg-img.jpg);">
                            <div class="fade-color">
                                <div class="value text-white">Due Date</div>
                                <!-- <div class="trending trending-up">
                                    <span>5%</span>
                                    <i class="fa fa-caret-up"></i>
                                </div> -->
                                <p class="label text-white">{{date('l jS \of F Y h:i:s A', strtotime($fullFilledPledge->when_due))}}</p>
                            </div>
                        </div>
                    </div>
                    @endif


                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <div class="block counter-block down mb-4" style="background-color: #1880c9">
                            <div class="value">{{$count + ENV('USERS_COUNTER')}}</div>
                            <div class="trending trending-up">
                                <span>{{rand(1, 50)}}%</span>
                                <i class="fa fa-caret-up"></i>
                            </div>
                            <p class="label">New Visitors</p>
                        </div>
                    </div>

                    <div class="clearfix"></div>


                    @if(!empty($unCompletedPledgesObj))
                    @if($user->registration_fee == 'NOT PAID')
                    <div style="margin-left: 50px" class="alert alert-warning alert-dismissible fade show" role="alert">
                        <p><strong>NOTE : </strong> You need to pay one time registration fee of {{$currency}}{{env('APP_REGISTRATION_FEE')}} to continue.</p>
                        <hr>
                        <p><strong>Bank :</strong> {{$feeAccount->bank}}</p>
                        <p><strong>Account Name :</strong> {{$feeAccount->account_name}}</p>
                        <p><strong>Account Number :</strong> {{$feeAccount->account_number}}</p>
                        <p><strong>Mobile :</strong> {{$feeAccount->telephone}}</p>

                        <button style="margin-top: -30px" type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                    @endif

                    @if(!empty($pledgeTrackerReceiving))
                    <div class="col-md-12">
                        <hr>
                        <div class="block form-block mb-4">

                            <div class="block table-block mb-4">
                                <div class="block-heading">
                                    <p class="mt-2" style="color: #333333">
                                        <i class="fa fa-users fa-3x"></i> Merge List (Receiving payment).
                                        <span class="badge badge-pill badge-success">{{count($pledgeTrackerReceiving)}}</span>
                                    </p>
                                </div>

                                <div class="table-responsive" id="dataBlock">
                                    <table id="dataTable1" class="display table table-striped" data-table="data-table">
                                        <thead>
                                            <tr>
                                                <th>Take Action</th>
                                                <th>ID</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Proof</th>
                                                <td>Confirmation Deadline</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pledgeTrackerReceiving as $item)
                                            <tr>
                                                <td><a class="btn btn-success" href="{{$appUrl}}viewMergedForUserPaying/{{$item->user_receiving}}"><i class="fa fa-thumbs-up"></i>Confirm Payment</a></td>
                                                <td>{{$item->id}}</td>
                                                <td class="name">{{$currency}} {{number_format($item->amount)}}</td>
                                                <td class="status pending">{{$item->status}}</td>
                                                @if(is_null($item->proof))
                                                <td class="name">NOT UPLOADED PROOF OF PAYMENT</td>
                                                @endif
                                                @if(!is_null($item->proof))
                                                <td>
                                                    <a target="display" href="{{asset($item->proof)}}">Proof Of Payment <i class="fa fa-file"></i></a>
                                                </td>
                                                @endif
                                                <td class="text-danger">{{date('l jS \of F Y h:i:s A', strtotime('+ 1 day',strtotime($item->updated_at)))}}</td>

                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!empty($pledgeTrackerSending))
                    <div class="col-md-12">
                        <hr>
                        <div class="block form-block mb-4">

                            <div class="block table-block mb-4">
                                <div class="block-heading">
                                    <p class="mt-2" style="color: #333333">
                                        <i class="fa fa-users fa-3x"></i> Merge List (To make Payment).
                                        <span class="badge badge-pill badge-success">{{count($pledgeTrackerSending)}}</span>
                                    </p>
                                </div>

                                <div class="table-responsive" id="dataBlock">
                                    <table id="dataTable1" class="display table table-striped" data-table="data-table">
                                        <thead>
                                            <tr>
                                                <th>Take Action</th>
                                                <th>ID</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Proof</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pledgeTrackerSending as $item)
                                            <tr>
                                                <td><a class="btn btn-success" href="{{$appUrl}}viewMergedForPaying/{{$item->id}}"><i class="fa fa-thumbs-up"></i>Details Of User To Pay</a></td>
                                                <td>{{$item->id}}</td>
                                                <td class="name">{{$currency}} {{number_format($item->amount)}}</td>
                                                <td class="status pending">{{$item->status}}</td>
                                                @if(is_null($item->proof))
                                                <td class="name">NOT UPLOADED PROOF OF PAYMENT</td>
                                                @endif
                                                @if(!is_null($item->proof))
                                                <td>
                                                    <a target="display" href="{{asset($item->proof)}}">View Proof Of Payment <i class="fa fa-file"></i></a>
                                                </td>
                                                @endif

                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($user->account_type == 'ADMIN')
                    <div class="col-12">
                        <div class="section-title">
                            <h4>ADMIN</h4>
                        </div>

                        <a class="btn btn-primary" href="{{$appUrl}}confirmRegistrationFee">
                            Confirm Registration Fee
                        </a>
                    </div>

                    @endif
                </div>
            </div>
        </div>
    </div>

</section>