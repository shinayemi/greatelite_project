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
                            <h4>{{$pageTitle}}</h4>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="block form-block mb-4">

                            <div class="block table-block mb-4">
                                <div class="block-heading">
                                    <p class="mt-2" style="color: #333333">Users that are yet to pay or not confirmed.
                                        <span class="badge badge-pill badge-warning">{{count($users)}}</span>
                                    </p>
                                </div>

                                @if(!empty($payment_confirmed))
                                <div style="margin-left: 50px" class="alert alert-success alert-dismissible fade show" role="alert">
                                    <p>{{$payment_confirmed}}</p>
                                    <button style="margin-top: -30px" type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                                <div class="table-responsive" id="dataBlock">
                                    <table id="dataTable1" class="display table table-striped" data-table="data-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>First Name</th>
                                                <th>Middle Name</th>
                                                <th>Last Name</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Registration Fee</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $item)
                                            <tr>
                                                <td>{{$item->id}}</td>
                                                <td class="name">{{$item->first_name}}</td>
                                                <td class="name">{{$item->middle_name}}</td>
                                                <td class="name">{{$item->last_name}}</td>
                                                <td>{{$item->email}}</td>
                                                <td>{{$item->mobile}}</td>
                                                <td class="price">{{$item->registration_fee}}</td>
                                                <td><a class="confirmation btn btn-success" href="{{$appUrl}}confirmRegistrationFee/{{$item->id}}"><i class="fa fa-thumbs-up"></i> Confirm Payment</a></td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                    </table>

                                </div>
                            </div>
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