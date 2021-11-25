<!--Navigation-->
<nav id="navigation" class="navigation-sidebar bg-primary">
    <div class="navigation-header">
        <a href="#"><span class="logo">{{$appName}}</span></a>
        <!--<img src="logo.png" alt="logo" class="brand" height="50">-->
    </div>

    <!--Navigation Profile area-->
    <div class="navigation-profile">
        <img class="profile-img rounded-circle" src="{{$appUrl}}assets/logo.png" alt="profile image">

        @if(empty($user))
        {{exit()}}
        @endif
        <h4 class="name">{{$user->first_name}} {{$user->last_name}} </h4>
        <span class="designation">{{$user->email}}</span>

        <a id="show-user-menu" href="javascript:void(0);" class="circle-white-btn profile-setting"><i class="fa fa-cog"></i></a>

        <!--logged user hover menu-->
        <div class="logged-user-menu bg-white">
            <div class="avatar-info">

                @if($user->sex == 'MALE')
                <img class="profile-img rounded-circle" src="{{$appUrl}}assets/images/male.jpeg" alt="profile image">
                @endif

                @if($user->sex == 'FEMALE')
                <img class="profile-img rounded-circle" src="{{$appUrl}}assets/images/female.png" alt="profile image">
                @endif
                <h4 class="name">{{$user->first_name}} {{$user->last_name}}</h4>
                @if($user->confirm_mobile == 'YES')
                <span style="color: #ffffff" class="badge badge-success designation">VERIFIED</span>
                @endif

                @if($user->confirm_mobile == 'NO')
                <span class="badge badge-warning designation">NOT VERIFIED</span>
                @endif
            </div>

            <ul class="list-unstyled">
                <li>
                    <a href="{{$appUrl}}profile">
                        <i class="ion-ios-person-outline"></i>
                        <span>My Profile</span>
                    </a>
                </li>
                <li>
                    <a href="{{$appUrl}}passwordRecovery">
                        <i class="ion-ios-locked-outline"></i>
                        <span>Change Password</span>
                    </a>
                </li>
                <li>
                    <a href="{{$appUrl}}logout">
                        <i class="ion-log-out"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!--Navigation Menu Links-->
    <div class="navigation-menu">

        <ul class="menu-items custom-scroll">
            <li>
                <a href="{{$appUrl}}dashboard">
                    <span class="icon-thumbnail"><i class="dripicons-browser"></i></span>
                    <span class="title">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="have-submenu">
                    <span class="icon-thumbnail"><i class="dripicons-user"></i></span>
                    <span class="title">Profile</span>
                </a>
                <!--Submenu-->
                <ul class="sub-menu">
                    <li>
                        <a href="{{$appUrl}}profile">
                            <span class="title">Manage Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{$appUrl}}downlines">
                            <span class="title">Downlines</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="javascript:void(0);" class="have-submenu">
                    <span class="icon-thumbnail"><i class="dripicons-heart"></i></span>
                    <span class="title">Pledge</span>
                </a>
                <!--Submenu-->
                <ul class="sub-menu">
                    <li>
                        <a href="{{$appUrl}}pledge">
                            <span class="title">New Pledge</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{$appUrl}}unpaidPledge">
                            <span class="title">Unpaid Pledge</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{$appUrl}}paidPledge">
                            <span class="title">Paid Pledges</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{$appUrl}}mergeList">
                            <span class="title">Merge List</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{$appUrl}}bonusTracker">
                            <span class="title">Unpaid Bonus</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{$appUrl}}transactions">
                            <span class="title">Transactions</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- <li>
                <a href="javascript:void(0);" class="have-submenu">
                    <span class="icon-thumbnail"><i class="fa fa-money"></i></span>
                    <span class="title">Withdrawal</span>
                </a>
                <ul class="sub-menu">
                    <li>
                        <a href="#">
                            <span class="title" data-toggle="tooltip" data-placement="left" title="Not due for withdrawal">Withdraw</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span class="title" data-toggle="tooltip" data-placement="left" title="Not due for withdrawal">All Withdrawals</span>
                        </a>
                    </li>
                </ul>
            </li> -->
            <li>
                <a href="{{$appUrl}}faq">
                    <span class="icon-thumbnail"><i class="dripicons-document"></i></span>
                    <span class="title">FAQ</span>
                </a>
            </li>
            <li>
                <a href="{{$appUrl}}logout">
                    <span class="icon-thumbnail"><i class="dripicons-power"></i></span>
                    <span class="title">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</nav>