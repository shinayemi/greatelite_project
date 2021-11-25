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
                            <h4>Unpaid bonus list</h4>
                        </div>
                    </div>
                    @if(!empty($userBonus))
                    <div class="col-md-12">
                        <hr>
                        <div class="block form-block mb-4">

                            <div class="block table-block mb-4">
                                <div class="block-heading">
                                    <p class="mt-2" style="color: #333333">
                                        <i class="fa fa-users fa-3x"></i> Unpaid bonus.
                                        <span class="badge badge-pill badge-success">{{count($userBonus)}}</span>
                                    </p>
                                </div>

                                <div class="table-responsive" id="dataBlock">
                                    <table id="dataTable1" class="display table table-striped" data-table="data-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Downline Name</th>
                                                <th>Downline Mobile</th>
                                                <th>User Pledge</th>
                                                <th>User Paid</th>
                                                <th>Bonus</th>
                                                <th>Bonus Paid</th>
                                                <th>Pledge Process Completed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($i = 0; $i < count($userBonus); $i++) <tr>
                                                <td>{{$userBonus[$i]->id}}</td>
                                                <td style="white-space: nowrap" class="name">{{$usersCollectingBonusFrom[$i]->first_name}} {{$usersCollectingBonusFrom[$i]->middle_name}} {{$usersCollectingBonusFrom[$i]->last_name}}</td>
                                                <td style="white-space: nowrap">{{$usersCollectingBonusFrom[$i]->mobile}}</td>
                                                <td style="white-space: nowrap">
                                                    {{$currency}} {{number_format($userBonus[$i]->amount)}}
                                                </td>
                                                <td style="white-space: nowrap">
                                                    {{$currency}} {{number_format($userBonus[$i]->amount_paid)}}
                                                </td>
                                                <td style="white-space: nowrap">
                                                    {{$currency}} {{number_format($userBonus[$i]->referrer_bonus)}}
                                                </td>
                                                <td style="white-space: nowrap">{{$userBonus[$i]->paid_bonus}}</td>
                                                <td class="status pending">{{$userBonus[$i]->process}}</td>
                                                </tr>
                                                @endfor
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