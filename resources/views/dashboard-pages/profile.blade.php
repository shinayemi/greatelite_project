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
                            <li class="breadcrumb-item"><a href="#">{{$pageTitle}}</a></li>
                        </ol>
                    </div>
                    <div class="col-3 col-md-6 col-lg-4">
                        <!-- <a href="javascript:void(0);" class="btn btn-primary btn-round pull-right d-none d-md-block">Buy Theme Now</a> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">

                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="block task-block">

                            <div class="col-md-12">
                                <div class="section-title">
                                    <h4>Profile</h4>
                                </div>
                            </div>

                            <div class="block form-block mb-4">
                                <div class="block-heading">
                                    <h5>@{{fullName}}
                                        @if($user->confirm_mobile == 'YES' && !is_null($bankAccount) && $user->registration_fee == 'PAID')
                                        <span class="badge badge-success">VERIFIED</span>
                                        @endif

                                        @if($user->confirm_mobile == 'NO' || is_null($bankAccount) || $user->registration_fee != 'PAID')
                                        <span class="badge badge-warning">NOT VERIFIED</span>
                                        <br><br>
                                        <p class="text-muted">Verify account by
                                            <ol>
                                                <li style="margin-top: 10px">Add a valid mobile number and validating the mobile number</li>
                                                <li style="margin-top: 10px">Add valid bank account details.</li>
                                                <li style="margin-top: 10px">Pay registration fee {{$currency}}{{number_format(env('APP_REGISTRATION_FEE'))}} after making first pledge.</li>
                                            </ol>
                                        </p>
                                        @endif


                                        <p class="text-muted"><br><strong>Upline : </strong>{{$uplineUserObj->first_name}} {{$uplineUserObj->last_name}} (<strong>{{$uplineUserObj->mobile}}</strong>)</p>
                                    </h5>
                                </div>

                                @if($user->registration_fee == 'NOT PAID')
                                <div style="margin-left: 50px" class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <p><strong>NOTE : </strong> You need to pay one time registration fee of {{$currency}}{{env('APP_REGISTRATION_FEE')}} after making first pledge.</p>
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

                                <form action="{{url('profile/')}}" method="post" class="horizontal-form">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    @if(!empty($profileMessage))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <p><strong>Success : </strong> {{$profileMessage}}</p>
                                        <button style="margin-top: -30px" type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    @endif
                                    <div class="form-row">
                                        <div class="form-group col-md-9">
                                            <label>My Link</label>
                                            <input readonly class="form-control" type="text" value="{{$appUrl}}register/{{$user->user_ref}}" id="myInput">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <button type="button" style="margin-top: 35px;" class="btn btn-link" data-placement="right" data-toggle="tooltip" title="" data-original-title="Refereer link, click to copy" onclick="copyRefeerLink()"> <i class="fa fa-link"></i>Copy Link</button>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>First Name</label>
                                            <input required v-model="firstName" name="first_name" class="form-control" placeholder="First name" type="text">
                                        </div>
                                        <div class="form-group  col-md-4">
                                            <label>Middle Name</label>
                                            <input v-model="middleName" name="middle_name" class="form-control" placeholder="Middle name" type="text">
                                        </div>
                                        <div class="form-group  col-md-4">
                                            <label>Last Name</label>
                                            <input required v-model="lastName" name="last_name" class="form-control" placeholder="Last name" type="text">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input required v-model="email" name="email" class="form-control" placeholder="Email Address" type="text">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label>Gender </label>
                                            <select required class="custom-select form-control" name="gender">
                                                <option value="">Select Gender</option>
                                                <option :selected="isMale" value="MALE">MALE</option>
                                                <option :selected="isFemale" value="FEMALE">FEMALE</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Mobile Number
                                                @if(strlen($user->mobile) >= 7 && $user->confirm_mobile == 'NO')
                                                <a class="badge badge-danger" href="{{$appUrl}}sendMobileVerificationCode/{{$user->mobile}}/{{$user->user_ref}}"><i class="fa fa-envelope"></i> Send CODE</a>
                                                @endif
                                            </label>
                                            <input name="mobile" required v-model="mobile" class="form-control" placeholder="Mobile Number" type="number">
                                        </div>
                                    </div>
                                    <hr>
                                    <button class="btn btn-primary" type="submit"><i class="dripicons-user"></i> Update Profile</button>
                                </form>
                            </div>

                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="block form-block mb-4">
                            <div class="col-md-12">
                                <div class="section-title">
                                    <h4>Bank Account</h4>
                                </div>
                            </div>
                            @if(!is_null($bankAccount))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <h5 class="text-center" style="color: #ffffff"><i class="fa fa-money"></i> Active Bank Account</h5>
                                <p>Bank: {{$bankAccount->bank}}</p>
                                <p>Account Name: {{$bankAccount->account_name}}</p>
                                <p>Account Number: {{$bankAccount->account_number}}</p>
                            </div>
                            @endif

                            @if(is_null($bankAccount))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="text-center" style="color: #ffffff"><i class="fa fa-money"></i> An active bank account is needed</h5>
                            </div>
                            @endif


                            <form action="{{url('profile/')}}" method="post" class="horizontal-form">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                @if(!empty($bankMessage))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <p><strong>Note : </strong> {{$bankMessage}}</p>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                                <h5 class="text-center">Update Bank Account</h5><br>
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Bank Name</label>
                                        <div class="col-md-9">
                                            <input autocomplete="off" class="form-control" placeholder="Bank Name" type="text" name="bank_name" value="{{$formValues['bank_name']}}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Account Name</label>
                                        <div class="col-md-9">
                                            <input autocomplete="off" class="form-control" placeholder="Account Name" name="account_name" type="text" value="{{$formValues['account_name']}}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <label class="col-md-3">Account Number</label>
                                        <div class="col-md-9">
                                            <input autocomplete="off" class="form-control" placeholder="Account Number" name="account_number" type="text" value="{{$formValues['account_number']}}" required>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <button class="btn btn-primary mr-3" type="submit"><i class="fa fa-money"></i> Update</button>
                            </form>
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