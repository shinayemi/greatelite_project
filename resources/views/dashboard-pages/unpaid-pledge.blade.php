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
                            <h4>Unpaid Pledge</h4>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="block form-block mb-4">

                            <div class="block table-block mb-4">
                                <div class="block-heading">
                                    <p class="mt-2" style="color: #333333">Pledges that are not due for withdrawal</p>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Amount Paid</th>
                                                <th>Interest</th>
                                                <th class="text-left">Pledged Date</th>
                                                <th>Process</th>
                                                <th>Can Withdraw</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pledges as $item)
                                            <tr>
                                                <td>{{$item->id}}</td>
                                                <td style="white-space: nowrap" class="status pending">
                                                    @if($item->status == 'NOT PAYED')
                                                    NOT MERGED
                                                    @endif
                                                    @if($item->status == 'PAYING')
                                                    MERGED
                                                    @endif
                                                </td>
                                                <td style="white-space: nowrap">{{$currency}} {{number_format($item->amount)}}</td>
                                                <td style="white-space: nowrap">{{$currency}} {{number_format($item->amount_paid)}}</td>
                                                <td style="white-space: nowrap">{{$currency}} {{number_format($item->interest)}}</td>
                                                <td style="white-space: nowrap" class="text-left">{{date('l jS \of F Y h:i:s A', strtotime($item->created_at))}}</td>
                                                <td style="white-space: nowrap">{{$item->process}}</td>
                                                <td style="white-space: nowrap">{{$item->can_withdraw}}</td>
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