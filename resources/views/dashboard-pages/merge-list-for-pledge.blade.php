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
                            <h4>Merge List Tracker</h4>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <p class="text-muted">
                                    This will help you know the user paying and receiving for a pledge
                                </p>
                            </div>

                            @for ($i = 0; $i < count($usersPayingArray); $i++) <p class="text-muted"><strong>From:</strong> {{$usersPayingArray[$i]->email}} to {{$usersReceivingArray[$i]->email}}</p>
                                <p class="text-muted"><strong>Sender Mobile:</strong> {{$usersPayingArray[$i]->mobile}}</p>
                                <p class="text-muted"><strong>Amount:</strong> {{$currency}} {{number_format($pledgeTrackerReceiving[$i]->amount)}}</p>
                                <p class="text-muted"><a target="display" href="{{asset($pledgeTrackerReceiving[$i]->proof)}}">Proof Of Payment <i class="fa fa-file"></i></a></p>
                                <br>
                                @endfor

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