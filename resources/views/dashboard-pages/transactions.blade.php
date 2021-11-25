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
                            <h4>{{$user->first_name}} {{$user->last_name}} transactions</h4>
                        </div>
                    </div>
                    @if(!empty($pledgeTrackerSending))
                    <div class="col-md-12">
                        <hr>
                        <div class="block form-block mb-4">

                            <div class="block table-block mb-4">
                                <div class="block-heading">
                                    <p class="mt-2" style="color: #333333">
                                        <i class="dripicons-heart fa-3x"></i> Transactions
                                        <span class="badge badge-pill badge-success">{{count($pledgeTrackerSending) + count($pledgeTrackerReceiving)}}</span>
                                    </p>
                                </div>

                                <div class="table-responsive" id="dataBlock">
                                    <table id="dataTable1" class="display table table-striped" data-table="data-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Bonus ID</th>
                                                <th>Amount</th>
                                                <th>Bonus</th>
                                                <th>Transaction Type</th>
                                                <th>Status</th>
                                                <th>Proof</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pledgeTrackerSending as $item)
                                            <tr>
                                                <td>{{$item->id}}</td>
                                                <td>{{$item->plede_id_bonus}}</td>
                                                <td class="name">{{$currency}} {{number_format($item->amount)}}</td>
                                                <td class="name">{{$currency}} {{number_format($item->bonus)}}</td>
                                                @if($item->user_sending == $user->id)
                                                <td class="status pending text-center"><i class="fa fa-arrow-right"></i> Sending</td>
                                                @endif
                                                @if($item->user_receiving == $user->id)
                                                <td class="status success text-center"><i class="fa fa-arrow-left"></i> Receiving</td>
                                                @endif
                                                @if($item->status == 'NOT CONFIRMED')
                                                <td class="status pending">{{$item->status}}</td>
                                                @endif
                                                @if($item->status == 'CONFIRMED')
                                                <td class="status success">{{$item->status}}</td>
                                                @endif
                                                @if(is_null($item->proof))
                                                <td class="name">NOT UPLOADED PROOF OF PAYMENT</td>
                                                @endif
                                                @if(!is_null($item->proof))
                                                <td>
                                                    <a target="display" href="{{asset($item->proof)}}">View Proof Of Payment <i class="fa fa-file"></i></a>
                                                </td>
                                                @endif
                                                <td class="text-left" style="white-space: nowrap">{{date('l jS \of F Y h:i:s A', strtotime($item->updated_at))}}</td>
                                            </tr>
                                            @endforeach


                                            @foreach ($pledgeTrackerReceiving as $item)
                                            <tr>
                                                <td>{{$item->id}}</td>
                                                <td>{{$item->plede_id_bonus}}</td>
                                                <td class="name" style="white-space: nowrap">{{$currency}} {{number_format($item->amount)}}</td>
                                                <td class="name" style="white-space: nowrap">{{$currency}} {{number_format($item->bonus)}}</td>
                                                @if($item->user_sending == $user->id)
                                                <td class="status pending text-center"><i class="fa fa-arrow-right"></i> Sending</td>
                                                @endif
                                                @if($item->user_receiving == $user->id)
                                                <td class="status success text-center"><i class="fa fa-arrow-left"></i> Receiving</td>
                                                @endif
                                                @if($item->status == 'NOT CONFIRMED')
                                                <td class="status pending">{{$item->status}}</td>
                                                @endif
                                                @if($item->status == 'CONFIRMED')
                                                <td class="status success">{{$item->status}}</td>
                                                @endif
                                                @if(is_null($item->proof))
                                                <td class="name">NOT UPLOADED PROOF OF PAYMENT</td>
                                                @endif
                                                @if(!is_null($item->proof))
                                                <td>
                                                    <a target="display" href="{{asset($item->proof)}}">View Proof Of Payment <i class="fa fa-file"></i></a>
                                                </td>
                                                @endif
                                                <td class="text-left" style="white-space: nowrap">{{date('l jS \of F Y h:i:s A', strtotime($item->updated_at))}}</td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</section>