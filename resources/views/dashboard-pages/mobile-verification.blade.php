<!--Page Container-->
<section class="page-container" id="profileBlock">
    <div class="page-content-wrapper">

        <!--Main Content-->
        <div class="content sm-gutter">
            <div class="container-fluid padding-25 sm-padding-10">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h4>Verify Mobile Number</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="block form-block mb-4">
                            <div class="block-heading">
                                <h5>@{{fullName}}</h5>
                            </div>

                            <form action="{{url('confirmMobileVerificationCode')}}" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="mobile" value="{{$user->mobile}}">
                                <input type="hidden" name="user_ref" value="{{$user->user_ref}}">

                                <div class="form-group">
                                    <label>Verification Code</label>
                                    <input class="form-control" placeholder="Verification Code" name="code" type="text" required autocomplete="off">
                                </div>

                                <hr>
                                <button class="btn btn-primary" type="submit"><i class="dripicons-phone"></i> Verify</button>
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
        data: {
            firstName: "{{$user->first_name}}",
            middleName: "{{$user->middle_name}}",
            lastName: "{{$user->last_name}}",
            email: "{{$user->email}}",
            mobile: "{{$user->mobile}}",
            sex: "{{$user->sex}}",
            userRef: "{{$user->user_ref}}",
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