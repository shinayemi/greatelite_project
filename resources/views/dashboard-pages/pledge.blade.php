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
                            <h4>Pledge</h4>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>@{{fullName}}
                                    @if($user->confirm_mobile == 'YES')
                                    <span class="badge badge-success">VERIFIED</span>
                                    @endif

                                    @if($user->confirm_mobile == 'NO')
                                    <span class="badge badge-warning">NOT VERIFIED</span>
                                    <br><br>
                                    <p class="text-muted">Verify account by adding a valid mobile number
                                        <a href="{{$appUrl}}profile">Verify Account</a>
                                    </p>
                                    @endif

                                </h5>
                            </div>

                            <form action="{{url('pledge/')}}" method="post" class="horizontal-form">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="user" value="{{ $user->id }}">
                                @if($user->registration_fee == 'NOT PAID')
                                <input type="hidden" name="registration_fee" value="{{ $registration_fee }}">
                                @endif

                                @if($user->registration_fee == 'PAID')
                                <input type="hidden" name="registration_fee" value="0">
                                @endif

                                <p class="text-muted">Withdrawal for this investment will be due in {{$investment_duration}}. After pledge is fulfilled.</p>
                                <p class="text-muted">Please note to withdraw your investment when due. <strong>You need to recommit (pledge again).</strong></p>
                                @if(!empty($pledgeMessage))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <p><strong>Success : </strong> {{$pledgeMessage}}</p>
                                    <button style="margin-top: -30px" type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @if($user->registration_fee == 'NOT PAID')
                                <div style="margin-left: 50px" class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <p><strong>NOTE : </strong> You need to pay one time registration fee of {{$currency}}{{env('APP_REGISTRATION_FEE')}} to before you can be merged to receive continue.</p>
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

                                @if(!empty($pledgeWarning))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <p><strong>Warning : </strong> {{$pledgeWarning}}</p>
                                    <button style="margin-top: -30px" type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                                <br>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Pledge Options </label>
                                        <select v-model="amount" required class="custom-select form-control" name="pledge">
                                            <option value="0">Pledge Amount Options</option>
                                            @foreach ($pledgeOptions as $item)
                                            <option value="{{ $item->amount }}">{{$currency}}{{number_format("$item->amount")}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        @if($user->registration_fee == 'NOT PAID')
                                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                            <p><strong>NOTE : </strong> On first pledge, you are to pay a one time fee of {{$currency}}{{number_format($registration_fee)}}</p>
                                            <button style="margin-top: -30px" type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                    <h2>
                                        {{$currency}}@{{amountFormated}}

                                        @if($user->registration_fee == 'NOT PAID')
                                        + {{$currency}}@{{registrationFee}} <span class="text-muted" style="font-size: 0.5em">(registration fee)</span>
                                        @endif
                                    </h2>
                                </div>
                                <hr>
                                <button class="btn btn-primary" style="font-size: 1.2em;" type="submit"><i class="dripicons-heart"></i> Pledge</button>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-6">
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