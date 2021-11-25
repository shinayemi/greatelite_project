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
                            <h4>Merge List (To make payment)</h4>
                        </div>
                    </div>
                    @if(!empty($pledgeTrackerSending))
                    <div class="col-md-12">
                        <hr>
                        <div class="block form-block mb-4">

                            <div class="block table-block mb-4">
                                <div class="block-heading">
                                    <p class="mt-2" style="color: #333333">
                                        <i class="fa fa-users fa-3x"></i> Merge List.
                                        <span class="badge badge-pill badge-success">{{count($pledgeTrackerSending)}}</span>
                                    </p>
                                </div>

                                <div class="table-responsive" id="dataBlock">
                                    <table id="dataTable1" class="display table table-striped" data-table="data-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Proof</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pledgeTrackerSending as $item)
                                            <tr>
                                                <td>{{$item->id}}</td>
                                                <td class="name">{{$currency}} {{number_format($item->amount)}}</td>
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
                                                @if($item->status != 'CONFIRMED')
                                                <td><a class="btn btn-success" href="{{$appUrl}}viewMergedForPaying/{{$item->id}}"><i class="fa fa-user"></i>
                                                        @if(!is_null($item->proof))
                                                        Paid User
                                                        @endif
                                                        @if(is_null($item->proof))
                                                        User To Pay
                                                        @endif
                                                    </a></td>
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

                </div>
            </div>
        </div>
    </div>

</section>

<script>
    var vm = new Vue({
        el: '#profileBlock',
        created: function() {
            if (this.sex == 'MALE') {
                this.isMale = true;
            } else {
                this.isFemale = true;
            }
        },
        data: {
            firstName: "{{$user->first_name}}",
            middleName: "{{$user->middle_name}}",
            lastName: "{{$user->last_name}}",
            email: "{{$user->email}}",
            mobile: "{{$user->mobile}}",
            sex: "{{$user->sex}}",
            userRef: "{{$user->user_ref}}",
            registrationFeePaid: "{{$user->registration_fee}}",
            amount: 0,
            amountFormated: "0",
            registrationFee: "{{$registration_fee}}",
            isMale: false,
            isFemale: false,
        },
        watch: {
            sex(value) {
                console.log(value);
                if (this.sex == 'MALE') {
                    this.isMale = true;
                } else {
                    this.isFemale = true;
                }
            },
            amount(value) {
                this.amountFormated = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
        },
        computed: {
            fullName: function() {
                return this.firstName + " " + this.lastName;
            }
        }
    })
</script>

<script>
    function copyRefeerLink() {
        var copyText = document.getElementById("myInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        // alert("Copied the text: " + copyText.value);
    }
</script>