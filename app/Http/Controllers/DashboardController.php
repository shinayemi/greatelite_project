<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Mail\Mailer;
use App\Http\Requests;

//use Mail;
//use App\Mail\UserRegistration;
use App\User;
use App\PledgeOptions;
use App\Pledges;
use App\Ledger;
use App\Fee;
use App\Faq;
use App\FeeAccount;
use App\PledgeTracker;
use App\Testimonials;
use Sms\EbulkSms;
use App\BankAccount;
use App\GreatElites;

use DB;
use Validator;

require(__DIR__ . '/../../EbulkSms.php');

class DashboardController extends Controller
{
    private $predefinedData;

    private function getActionDueDate()
    {
        return date('Y-m-d H:i:s', strtotime(env('APP_ACTION_DURATION')));
    }

    private function calculateInvestmentDueDate()
    {

        return date('Y-m-d H:i:s', strtotime(env('APP_PLEDGE_DURATION')));
    }

    private function calculatePledgeBonus($amount)
    {
        $appBonusPercentage = env('APP_PLEDGE_BONUS');

        if ($amount > 0) {
            return ($appBonusPercentage / 100) * $amount;
        } else {
            return 0;
        }
    }

    private function calculatePercentage($total, $per)
    {
        if ($total > 0) {
            //My number is 928.
            $myNumber = $total;
            //I want to get 25% of 928.
            $percentToGet = $per;

            //Convert our percentage value into a decimal.
            $percentInDecimal = $percentToGet / 100;

            //Get the result.
            $percent = $percentInDecimal * $myNumber;

            //Print it out - Result is 232.
            return $percent;
        } else {
            return 0;
        }
    }

    private function calculatePledgeInvestmentTotal($amount)
    {
        $totalAmount = 0;
        if ($amount > 0) {
            $bonusOnInvestment = $amount / 2;
            $totalAmount = $bonusOnInvestment + $amount;
        }
        return $totalAmount;
    }

    private function genMobileCode()
    {
        return rand(100000, 999999);
    }

    private function isLogin($request)
    {
        $data = $this->predefinedData;
        $sessionID = $request->session()->get('id');
        $sessionEmail = $request->session()->get('email');
        $sessionStatus = $request->session()->get('status');

        if (!empty($sessionID)) {
            return $sessionID;
        } else {

            $request->session()->forget('id');
            $request->session()->forget('email');
            $request->session()->forget('status');
            $request->session()->flush();

            header("Location: {$data['appUrl']}/login");
            exit();
            // return redirect()->action('DashboardController@logout');
        }
    }

    public function __construct()
    {
        $feeAccount = FeeAccount::all();
        $random = rand(0, (count($feeAccount) - 1));

        $this->predefinedData = array(
            'appUrl' => env('APP_URL'),
            'appName' => env('APP_NAME'),
            'email' => env('EMAIL'),
            'email2' => env('EMAIL2'),
            'email3' => env('EMAIL3'),
            'telephone' => env('TELEPHONE'),
            'telephone2' => env('TELEPHONE2'),
            'telephone3' => env('TELEPHONE3'),
            'telephone4' => env('TELEPHONE4'),
            'address' => env('ADDRESS'),
            'address2' => env('ADDRESS2'),
            'address3' => env('ADDRESS3'),
            'samagolaUrl' => env('SAMAGOLA_URL'),
            'highlanderUrl' => env('HIGHLANDER_URL'),
            'smsTitle' => env('SMS_TITLE'),
            'facebook' => env('SOCIAL_FACEBOOK'),
            'twitter' => env('SOCIAL_TWITTER'),
            'instagram' => env('SOCIAL_INSTAGRAM'),
            'whatsapp' => env('SOCIAL_WHATSAPP'),
            'rss' => env('SOCIAL_RSS'),
            'canonicalUrl' => env('APP_URL'),
            'success' => false,
            'investment_duration' => env('APP_PLEDGE_DURATION'),
            'registration_fee' => env('APP_REGISTRATION_FEE'),
            'currency' => env('APP_DEFAULT_CURRENCY'),
            'action_duration' => env('APP_ACTION_DURATION'),
            'feeAccount' => $feeAccount[$random],
        );
    }

    public function index($status = 0)
    {
        if (View::exists('home_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Welcome to ' . strtoupper(env('APP_NAME'));
            $data['canonicalUrl'] = env('APP_URL');

            $data['status'] = $status;
            $data['canonicalUrl'] = env('APP_URL') . 'index';
            return view('home_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function dashboard(Request $request)
    {
        if (View::exists('dashboard_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Dashboard';
            $data['canonicalUrl'] = env('APP_URL') . 'dashboard';

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);

            $allUsers = User::all();
            $data['count'] = count($allUsers);

            if (empty($user)) {
                return redirect()->action('WebController@logout');
            }
            $data['user'] = $user;

            $bankAccountObj = BankAccount::where('user_id', $user->id)->first();
            $data['bankAccount'] = $bankAccountObj;

            $pledgeTrackerSending = PledgeTracker::where('user_sending', '=', $user->id)
                ->orderBy('id')
                ->get();

            $allUnpaidBonus = Pledges::where('process', '=', 'INCOMPLETE')
                ->where('paid_bonus', '=', 'NO')
                ->where('referrer_id', '=', $user->id)
                ->get();
            $data['allUnpaidBonus'] = $allUnpaidBonus;
            $data['allUnpaidBonusTotal'] = 0;
            if (!empty($allUnpaidBonus)) {
                foreach ($allUnpaidBonus as $pledge) {
                    $data['allUnpaidBonusTotal'] += $pledge->referrer_bonus;
                }
            }
            // echo ($data['allUnpaidBonusTotal']);
            // exit();

            $fullFilledPledge = Pledges::where('can_withdraw', '=', 'NO')
                ->where('user_id', '=', $user->id)
                ->where('status', '=', 'PAID')
                ->take(1)
                ->get();
            if (count($fullFilledPledge) > 0) {
                $data['fullFilledPledge'] = $fullFilledPledge[0];
            } else {
                $data['fullFilledPledge'] = "";
            }

            if (count($pledgeTrackerSending) == 0) {
                $data['pledgeTrackerSending'] = "";
            } else {
                $data['pledgeTrackerSending'] = $pledgeTrackerSending;
            }

            $pledgeTrackerReceiving = PledgeTracker::where('status', '=', 'NOT CONFIRMED')
                ->where('user_receiving', '=', $user->id)
                ->get();
            $data['pledgeTrackerReceiving'] = $pledgeTrackerReceiving;

            if (count($pledgeTrackerReceiving) == 0) {
                $data['pledgeTrackerReceiving'] = "";
            } else {
                $data['pledgeTrackerReceiving'] = $pledgeTrackerReceiving;
            }

            $processToCheck = 'INCOMPLETE';
            $checkIfUserHasUnfinishedPledgesResults = DB::select(DB::raw("SELECT * FROM pledges WHERE user_id = :user_id_param AND process = :process_param LIMIT 1"), array(
                'user_id_param' => $user->id,
                'process_param' => $processToCheck
            ));
            $data['unCompletedPledgesCount'] = count($checkIfUserHasUnfinishedPledgesResults);
            $data['unCompletedPledgesObj'] = $checkIfUserHasUnfinishedPledgesResults;

            return view('dashboard_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function confirmPayment($pledgeTrackerID, $pledgeTrackerUserSendingID, Request $request)
    {
        if (empty($pledgeTrackerID) || empty($pledgeTrackerUserSendingID)) {
            return redirect()->action('DashboardController@dashboard');
        }

        $sessionID = $this->isLogin($request);
        $user = User::find($sessionID);
        $bankAccountObj = null;

        $pledgeTrackerToConfirm = PledgeTracker::find($pledgeTrackerID);
        if (!empty($pledgeTrackerToConfirm)) {
            $pledgeTrackerToConfirm->status = 'CONFIRMED';

            $pledgeUserReceiving = Pledges::find($pledgeTrackerToConfirm->pledge_id);
            $pledgeUserSending = Pledges::find($pledgeTrackerToConfirm->pledge_id_user_sending);

            if (!empty($pledgeUserSending)) {

                if ($pledgeUserSending->status != 'PAID') {
                    $pledgeUserSending->amount_paid_confirmed =
                        $pledgeUserSending->amount_paid_confirmed + $pledgeTrackerToConfirm->amount;
                }

                if ($pledgeUserSending->amount_paid_confirmed == $pledgeUserSending->amount) {
                    $pledgeUserSending->process = 'INCOMPLETE';

                    //OPEN PREVIOUS PLEDGE FOR WITHDRAWAL                    
                    $currentDate = date("Y-m-d H:i:s", strtotime("now"));
                    $duePledges = Pledges::where('when_due', '<=', $currentDate)
                        ->where('process', '=', 'INCOMPLETE')
                        ->where('can_withdraw', '=', 'NO')
                        ->where('status', '=', 'PAID')
                        ->orderBy('id', 'ASC')
                        ->take(1)
                        ->get();
                    // >toSql();

                    if (!empty($duePledges)) {
                        foreach ($duePledges as $pledge) {
                            $pledge->process = 'INCOMPLETE';
                            $pledge->can_withdraw = 'YES';
                            $pledge->save();
                        }
                    }
                }
            }

            if ($pledgeUserReceiving->amount_paid_confirmed == $pledgeTrackerToConfirm->amount) {
                $pledgeUserReceiving->amount_received_and_confirmed =
                    $pledgeUserReceiving->amount_received_and_confirmed +
                    $pledgeTrackerToConfirm->amount;
                $pledgeUserReceiving->process =  'INCOMPLETE';
            } else {
                $pledgeUserReceiving->amount_received_and_confirmed = $pledgeUserReceiving->amount_received_and_confirmed + $pledgeTrackerToConfirm->amount;
            }

            $minToReceiveToMarkAsComplete = $pledgeUserReceiving->amount + $pledgeUserReceiving->interest;
            if ($pledgeUserReceiving->amount_received_and_confirmed >= $minToReceiveToMarkAsComplete) {
                $pledgeUserReceiving->process =  'COMPLETE';
            }

            // print_r($pledgeTrackerToConfirm);
            // print_r($pledgeUserReceiving);
            // print_r($pledgeUserSending);

            $pledgeTrackerToConfirm->save();
            $pledgeUserReceiving->save();
            $pledge->save();
            if (!empty($pledgeUserSending)) {
                $pledgeUserSending->save();
            }
            return redirect()->action('DashboardController@dashboard');
        }
    }


    public function profile(Request $request)
    {
        if (View::exists('profile_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Profile';
            $data['canonicalUrl'] = env('APP_URL') . 'profile';

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);
            $data['uplineUserObj'] = User::find($user->referrer);
            $bankAccountObj = null;

            $input = $request->all();
            if (!empty($sessionID)) {
                $data['user'] = $user;
                $data['bankMessage'] = "";
                if (!empty($user)) {
                    $bankAccountObj = BankAccount::where('user_id', $user->id)->first();
                } else {
                    return redirect()->action('WebController@logout');
                }
            }

            if (!empty($input['email'])) {
                $user->first_name = $input['first_name'];
                $user->middle_name = $input['middle_name'];
                $user->last_name = $input['last_name'];
                $user->email = $input['email'];
                $user->sex = $input['gender'];

                if ($user->mobile == $input['mobile']) {
                    $user->mobile = $input['mobile'];
                    $data['profileMessage'] = "Profile updated";
                } else {
                    $user->mobile = $input['mobile'];
                    $user->confirm_mobile = 'NO';
                    $data['profileMessage'] = "Profile updated, mobile number ({$user->mobile}) needs to be confirmed. Click the send code button next to the mobile number below   ";
                }
                $saved = $user->save();
            }

            if (!empty($input['bank_name'])) {
                $data['formValues'] = array(
                    'bank_name' => $input['bank_name'],
                    'account_name' => $input['account_name'],
                    'account_number' => $input['account_number'],
                );

                if (!empty($bankAccountObj)) {
                    $bankAccountObj->user_id = $user->id;
                    $bankAccountObj->bank = $input['bank_name'];
                    $bankAccountObj->account_name =  $input['account_name'];
                    $bankAccountObj->account_number = $input['account_number'];
                    $updates = $bankAccountObj->save();
                } else {
                    $newBankAccount = BankAccount::create([
                        'user_id' => $user->id,
                        'bank' => $input['bank_name'],
                        'account_name' => $input['account_name'],
                        'account_number' => $input['account_number']
                    ]);

                    if ($user->confirm_mobile == 'YES') {
                        $username = env('SMS_USERNAME');
                        $apikey = env('SMS_API_KEY');
                        $sendername = env('SMS_TITLE');
                        $recipients = $user->mobile;
                        $flash = 0;
                        $appName = env('APP_NAME');
                        $composedMessage = "{$appName}, Your bank details has been updated!";
                        $message = substr($composedMessage, 0, 160); //Limit this message to one page.
                        $sms = new EbulkSms();
                        #Use the next line for HTTP POST with JSON
                        $result = $sms->useJSON(env('SMS_JSON_URL'), $username, $apikey, $flash, $sendername, $message, $recipients);
                    }
                    $saved = $newBankAccount->save();
                }
                $data['bankMessage'] = "Bank Details has been updated";
            } else {
                $data['formValues'] = array(
                    'bank_name' => "",
                    'account_name' => "",
                    'account_number' => "",
                );
            }

            if (!empty($bankAccountObj)) {
                $data['bankAccount'] = $bankAccountObj;
            } else {
                $data['bankAccount'] = null;
            }


            return view('profile_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function confirmMobileVerificationCode(Request $request)
    {
        $input = $request->all();
        $user = User::where('user_ref', $input['user_ref'])->first();
        if (!empty($user)) {
            if ($user->mobile_verification_code == $input['code']) {
                $user->mobile = $input['mobile'];
                $user->confirm_mobile = 'YES';
                $user->agreed_to_terms = 'YES';
                $saved = $user->save();

                //echo($user->confirm_mobile);

            }
        }

        return redirect()->action('DashboardController@profile');
    }

    public function sendMobileVerificationCode($mobile, $userRef, $code = null, Request $request)
    {
        $data = $this->predefinedData;
        $data['pageTitle'] = 'Verify Mobile Number';
        $data['canonicalUrl'] = env('APP_URL') . 'sendMobileVerificationCode';

        $sessionID = $this->isLogin($request);

        $code = $this->genMobileCode();
        $user = User::where('user_ref', $userRef)->first();

        if (!empty($user)) {
            $data['user'] = $user;
            if ($mobile == $user->mobile) {
                $user->mobile_verification_code = $code;
                $saved = $user->save();

                if ($saved) {
                    $username = env('SMS_USERNAME');
                    $apikey = env('SMS_API_KEY');
                    $sendername = env('SMS_TITLE');
                    $recipients = $mobile;
                    $flash = 0;
                    $appName = env('APP_NAME');
                    $composedMessage = "{$appName}, verification Code is {$code}";
                    //echo ($composedMessage);
                    $message = substr($composedMessage, 0, 160); //Limit this message to one page.
                    $sms = new EbulkSms();

                    #Use the next line for HTTP POST with JSON
                    $result = $sms->useJSON(env('SMS_JSON_URL'), $username, $apikey, $flash, $sendername, $message, $recipients);

                    if (View::exists('mobile_verification_combined')) {
                        return view('mobile_verification_combined', $data);
                    } else {
                        if (View::exists('page_not_found_combined')) {
                            $data = $this->predefinedData;
                            $data['pageTitle'] = 'Page Not Found';
                            return view('page_not_found_combined', $data);
                        }
                    }
                }
            } else {
                return redirect()->action('DashboardController@profile');
            }
        } else {
            return redirect()->action('DashboardController@profile');
        }
    }

    public function pledge(Request $request)
    {
        if (View::exists('pledge_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Pledge';
            $data['canonicalUrl'] = env('APP_URL') . 'pledge';

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);

            $data['user'] = $user;
            $data['pledgeMessage'] = "";

            $processToCheck = 'INCOMPLETE';
            $checkIfUserHasUnfinishedPledgesResults =
                DB::select(DB::raw("SELECT * FROM pledges WHERE user_id = :user_id_param AND (process = :process_param) AND (status = :paid_param)"), array(
                    'user_id_param' => $user->id,
                    'process_param' => $processToCheck,
                    'paid_param' => 'PAID',
                ));

            $checkIfUserHasUnPaidPledgeResults =
                DB::select(DB::raw("SELECT * FROM pledges WHERE user_id = :user_id_param AND (process = :process_param) AND (status = :paid_param)"), array(
                    'user_id_param' => $user->id,
                    'process_param' => 'INCOMPLETE',
                    'paid_param' => 'NOT PAYED',
                ));

            $currentDate = date("Y-m-d H:i:s", strtotime("now"));
            $checkIfUserHasPaidLastPledge =
                DB::select(DB::raw("SELECT * FROM pledges 
                WHERE user_id = :user_id_param 
                AND (process = :process_param) AND (status = :status_param)
                AND (when_due <= :when_due_param) AND (can_withdraw = :can_withdraw_param)
                LIMIT 1"), array(
                    'user_id_param' => $user->id,
                    'process_param' => 'INCOMPLETE',
                    'status_param' => 'PAID',
                    'when_due_param' => $currentDate,
                    'can_withdraw_param' => 'NO'
                ));

            $checkIfThisisFirstPledgesResults = DB::select(DB::raw("SELECT * FROM pledges WHERE user_id = :user_id_param"), array(
                'user_id_param' => $user->id
            ));


            $lastPledge = Pledges::where('user_id', '=', $user->id)
                ->orderBy('id', 'DESC')
                ->get();

            if (count($lastPledge) == 0) {
                $pledgeOptions = PledgeOptions::orderBy('amount')->get();
                $data['pledgeOptions'] = $pledgeOptions;
            } else {
                $lastPledge = $lastPledge[0];
                $pledgeOptions = DB::select(DB::raw("SELECT * FROM pledge_options WHERE amount >= :amount_param"), array(
                    'amount_param' => $lastPledge->amount
                ));
                if (empty($pledgeOptions)) {
                    $pledgeOptions = PledgeOptions::orderBy('amount')->get();
                }
                $data['pledgeOptions'] = $pledgeOptions;
            }

            $input = $request->all();
            if (!empty($input['pledge'])) {
                $formatPledge = number_format($input['pledge']);

                if (empty($checkIfThisisFirstPledgesResults)) {
                    if ($user->status == 'DISPUTE') {
                        $data['pledgeWarning'] = "You cannot make a new pledge, you have a pending dispute.";
                        $data['pledgeMessage'] = "";
                    } else if ($user->status == 'NOT ACTIVE') {
                        $data['pledgeWarning'] = "You cannot make a new pledge, account has been blocked.";
                        $data['pledgeMessage'] = "";
                    } else {
                        // no pending pledge all PAID
                        $pledgeOptionsObj = PledgeOptions::where('amount', $input['pledge'])->first(); // to make use you pledge defined amount
                        if (!empty($pledgeOptionsObj)) {
                            $toPay = 0;
                            $registrationFee = $input['registration_fee'];

                            $checkIfFeeInSystem = Fee::where('user_id', $user->id)->first();
                            if (empty($checkIfFeeInSystem)) {
                                $fee = Fee::create([
                                    'user_id' => $user->id,
                                    'status' => 'NOT PAID',
                                    'amount' => $registrationFee
                                ]);
                                $fee->save();
                                $user->save();
                            }

                            $toPay = $pledgeOptionsObj->amount;

                            if (count($checkIfThisisFirstPledgesResults) > 0) {
                                $referrer_bonus = $this->calculatePercentage($toPay, env('APP_REFERRAL_BONUS'));
                            } else {
                                $referrer_bonus = $this->calculatePercentage($toPay, env('APP_REFERRAL_FIRST_BONUS'));
                            }

                            $interest_bonus = $this->calculatePercentage($toPay, env('APP_PLEDGE_BONUS'));

                            $pledges = Pledges::create([
                                'user_id' => $user->id,
                                'status' => 'NOT PAYED',
                                'amount' => $toPay,
                                'amount_paid' => 0,
                                'referrer_id' => $user->referrer,
                                'referrer_bonus' => $referrer_bonus,
                                'interest' => $interest_bonus,
                                'registration_fee' => $registrationFee
                            ]);
                            $saved = $pledges->save();

                            if ($saved) {

                                if ($checkIfFeeInSystem) {
                                    $data['pledgeMessage'] = "Pledge has been made and you need to pay registration fee of ₦{$registrationFee}. Check your dashboard for account to pay fee to.";
                                    $data['pledgeWarning'] = "";
                                } else {
                                    $data['pledgeMessage'] = "Pledge has been made";
                                    $data['pledgeWarning'] = "";
                                }
                            }
                        } else {
                            $formatPledge = number_format($input['pledge']);
                            $data['pledgeWarning'] = "You cannot make a pledge of {$data['currency']}{$formatPledge} (below last pledge)";
                            $data['pledgeMessage'] = "";
                        }
                    }
                } else {

                    if ($user->status == 'DISPUTE') {
                        $data['pledgeWarning'] = "You cannot make a new pledge, you have a pending dispute.";
                        $data['pledgeMessage'] = "";
                    } else if ($user->status == 'NOT ACTIVE') {
                        $data['pledgeWarning'] = "You cannot make a new pledge, account has been blocked.";
                        $data['pledgeMessage'] = "";
                    }
                    // else if ($user->registration_fee != 'PAID') {
                    //     $data['pledgeWarning'] = "You cannot make a pledge, not paid registration fee.";
                    //     $data['pledgeMessage'] = "";
                    // } 
                    else {
                        // no pending pledge all PAID
                        $pledgeOptionsObj = PledgeOptions::where('amount', $input['pledge'])->first(); // to make use you pledge defined amount

                        if (!empty($pledgeOptionsObj)) {
                            $toPay = 0;
                            $registrationFee = $input['registration_fee'];

                            $checkIfFeeInSystem = Fee::where('user_id', $user->id)->first();
                            if (empty($checkIfFeeInSystem)) {
                                $fee = Fee::create([
                                    'user_id' => $user->id,
                                    'status' => 'NOT PAID',
                                    'amount' => $registrationFee
                                ]);
                                $fee->save();
                                $user->save();
                            }

                            $toPay = $pledgeOptionsObj->amount;

                            if (count($checkIfThisisFirstPledgesResults) > 0) {
                                $referrer_bonus = $this->calculatePercentage($toPay, env('APP_REFERRAL_BONUS'));
                            } else {
                                $referrer_bonus = $this->calculatePercentage($toPay, env('APP_REFERRAL_FIRST_BONUS'));
                            }

                            $checkIfUserHasUnPaidPledgeResults =
                                DB::select(DB::raw("SELECT * FROM pledges WHERE user_id = :user_id_param AND (process = :process_param) AND (status = :paid_param)"), array(
                                    'user_id_param' => $user->id,
                                    'process_param' => 'INCOMPLETE',
                                    'paid_param' => 'NOT PAYED',
                                ));

                            // count($checkIfUserHasUnPaidPledgeResults);
                            // exit();
                            if (count($checkIfUserHasUnPaidPledgeResults) == 0) {
                                // no unpaid pledge
                                $interest_bonus = $this->calculatePercentage($toPay, env('APP_PLEDGE_BONUS'));

                                $pledges = Pledges::create([
                                    'user_id' => $user->id,
                                    'status' => 'NOT PAYED',
                                    'amount' => $toPay,
                                    'amount_paid' => 0,
                                    'referrer_id' => $user->referrer,
                                    'referrer_bonus' => $referrer_bonus,
                                    'interest' => $interest_bonus,
                                    'registration_fee' => $registrationFee
                                ]);
                                $saved = $pledges->save();
                                $data['pledgeMessage'] = "Recommited pledge has been made and you need fulfill this pledge to be be automatically merged to collect the former pledge.";
                                $data['pledgeWarning'] = "";
                            } else {
                                $data['pledgeWarning'] = "You cannot make a pledge of {$data['currency']}{$formatPledge}. Your previous pledge is not fully paid";
                                $data['pledgeMessage'] = "";
                            }
                        } else {
                            $data['pledgeWarning'] = "You cannot make a pledge of {$data['currency']}{$formatPledge}";
                            $data['pledgeMessage'] = "";
                        }
                    }
                }
            }
            return view('pledge_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function confirmRegistrationFee($userID = 0, Request $request)
    {
        if (View::exists('confirm_registration_fee_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Confirm Registration Fee';
            $data['canonicalUrl'] = env('APP_URL') . 'Confirm Registration Fee';

            if ($userID != 0) {
                $data['userID'] = urldecode($userID);
                $user = User::find($userID);
                $user->registration_fee = 'PAID';
                $user->confirmed_by = $user->id;
                $fee = Fee::where('user_id', $user->id)->first();
                if (!empty($fee)) {
                    $fee->status = 'PAID';
                    $fee->save();
                }
                $user->save();
                $data['payment_confirmed'] = "{$user->first_name} {$user->last_name} with email:({$user->email}) and mobile: ({$user->mobile}) payment has been confirmed.";
            } else {
                $data['payment_confirmed'] = "";
            }

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);
            $data['user'] = $user;

            $usersThatHasNotPaid = DB::select(DB::raw("SELECT * FROM users WHERE registration_fee = :somevariable"), array(
                'somevariable' => 'NOT PAID'
            ));
            $data['users'] = $usersThatHasNotPaid;

            return view('confirm_registration_fee_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function mergeList(Request $request)
    {
        if (View::exists('merge_list_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Merge List';
            $data['canonicalUrl'] = env('APP_URL') . 'mergeList';

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);
            $data['user'] = $user;

            $pledgeTrackerSending = PledgeTracker::where('user_sending', '=', $user->id)
                ->orderBy('status')
                ->get();

            if (empty($pledgeTrackerSending)) {
                $data['pledgeTrackerSending'] = "";
            } else {
                $data['pledgeTrackerSending'] = $pledgeTrackerSending;
            }

            return view('merge_list_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function transactions(Request $request)
    {
        if (View::exists('transactions_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Transactions';
            $data['canonicalUrl'] = env('APP_URL') . 'transactions';

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);
            $data['user'] = $user;

            $greatElites = new GreatElites();

            $pledgeTrackerSending = $greatElites->getUserSendingTransactions($user->id);
            $pledgeTrackerReceiving = $greatElites->getUserReceivingTransactions($user->id);

            if (empty($pledgeTrackerSending)) {
                $data['pledgeTrackerSending'] = "";
            } else {
                $data['pledgeTrackerSending'] = $pledgeTrackerSending;
            }

            if (empty($pledgeTrackerReceiving)) {
                $data['pledgeTrackerReceiving'] = "";
            } else {
                $data['pledgeTrackerReceiving'] = $pledgeTrackerReceiving;
            }

            return view('transactions_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function viewMergedForPaying($pledgeTrackerID = 0, Request $request)
    {

        if (View::exists('merged_for_payment_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Merged For Payment';
            $data['canonicalUrl'] = env('APP_URL') . 'viewMergedForPaying';

            $data['pledges'] = '';
            if ($pledgeTrackerID != 0) {
                $data['pledgeTrackerID'] = $pledgeTrackerID;

                $sessionID = $this->isLogin($request);
                $user = User::find($sessionID);
                $data['user'] = $user;

                $pledgeTrackerSending = PledgeTracker::where('id', '=', $pledgeTrackerID)->first();
                $userSending = null;
                $userSendingBank = null;
                $data['userAccountOk'] = false;
                $data['userBankAccountOk'] = false;
                $data['userAccount'] = null;
                $data['bankAccount'] = null;
                $data['pledgeTrackerSending'] = null;
                $data['uploadMessage'] = "";

                if (!empty($pledgeTrackerSending)) {
                    $data['pledgeTrackerSending'] = $pledgeTrackerSending;
                    $data['dueDate'] = strtotime($pledgeTrackerSending->updated_at);

                    $userSending = User::find($pledgeTrackerSending->user_receiving);
                    $userSendingBank = BankAccount::where('user_id', '=', $userSending->id)->first();

                    if (!empty($userSending->mobile) && $userSending->confirm_mobile == 'YES') {
                        $data['userAccountOk'] = true;
                        $data['userAccount'] = $userSending;
                    }

                    if (
                        !empty($userSendingBank->bank)
                        && !empty($userSendingBank->account_name)
                        && !empty($userSendingBank->account_number)
                    ) {
                        $data['userBankAccountOk'] = true;
                        $data['bankAccount'] = $userSendingBank;
                    }

                    $input = $request->all();
                    $data['uploadError'] = "";

                    if ($request->hasFile('file')) {
                        $limit = 5061227; //5061227
                        $fileSize = $request->file('file')->getSize();

                        if ($fileSize > $limit) {
                            $data['uploadError'] = 'File too large must be less than 5MB';
                        } else {
                            $data['uploadError'] = "";
                            $file = $request->file('file');
                            $documentName = $file->getClientOriginalName();
                            $uniqueDocumentName = md5_file($request->file('file')->getRealPath()) . time();
                            $ext = pathinfo(storage_path() . $documentName, PATHINFO_EXTENSION);
                            $destinationPath = "uploads/proof/";


                            $request->file('file')->move($destinationPath, $uniqueDocumentName . "." . $ext);

                            $pledgeTrackerSending->proof = $destinationPath . $uniqueDocumentName . '.' . $ext;
                            $saved = $pledgeTrackerSending->save();

                            $data['uploadMessage'] = "Proof Uploaded, call the mobile line {$userSending->mobile} to inform the user of payment and await confirmation.";
                        }
                    }
                }
            }
            return view('merged_for_payment_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function mergeListTracker($pledgeID, Request $request)
    {
        if (View::exists('merged_list_for_pledge_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Merge List Tracker';
            $data['canonicalUrl'] = env('APP_URL') . 'viewMergeListForPledge';

            $data['pledges'] = '';
            if (!empty($pledgeID)) {
                $data['pledgeID'] = $pledgeID;

                $sessionID = $this->isLogin($request);
                $user = User::find($sessionID);
                $data['user'] = $user;

                $usersPayingArray = array();
                $usersReceivingArray = array();

                $mergeListForPledge = PledgeTracker::where('pledge_id', '=', $pledgeID)
                    ->get();

                foreach ($mergeListForPledge as $item) {
                    $userSending = User::find($item->user_sending);
                    array_push($usersPayingArray, $userSending);

                    $userReceiving = User::find($item->user_receiving);
                    array_push($usersReceivingArray, $userReceiving);
                }

                $data['pledgeTrackerReceiving'] = $mergeListForPledge;
                $data['usersPayingArray'] = $usersPayingArray;
                $data['usersReceivingArray'] = $usersReceivingArray;
            }
            return view('merged_list_for_pledge_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function viewMergedForUserPaying($userReceivingID = 0, Request $request)
    {

        if (View::exists('merged_for_receiving_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Merged For Receiving Payment';
            $data['canonicalUrl'] = env('APP_URL') . 'viewMergedForReceiving';

            $data['pledges'] = '';
            if ($userReceivingID != 0) {
                $data['pledgeTrackerID'] = $userReceivingID;

                $sessionID = $this->isLogin($request);
                $user = User::find($sessionID);
                $data['user'] = $user;
                $data['testimonialMessage'] = "";

                $usersPayingArray = array();
                // $pledgeTrackerReceiving = PledgeTracker::where('user_receiving', '=', $userReceivingID)->get();

                $pledgeTrackerReceiving = PledgeTracker::where('user_receiving', '=', $userReceivingID)
                    ->where('status', '=', 'NOT CONFIRMED')
                    // ->where('proof', '!=', '')
                    ->get();

                foreach ($pledgeTrackerReceiving as $item) {
                    $user = User::find($item->user_sending);
                    array_push($usersPayingArray, $user);
                }

                $data['pledgeTrackerReceiving'] = $pledgeTrackerReceiving;
                $data['usersPayingArray'] = $usersPayingArray;
                // print_r($usersPayingArray[0]->first_name);
                // exit();

                $input = $request->all();
                if (!empty($input['testimonial'])) {
                    $testimonial = $input['testimonial'];

                    if (!empty($pledgeTrackerReceiving)) {
                        $testimonial = Testimonials::create([
                            'user_id' => $data['user']->id,
                            'first_name' => $data['user']->first_name,
                            'pledge_id' => $pledgeTrackerReceiving[0]->pledge_id,
                            'message' => $input['testimonial']
                        ]);

                        $saved = $testimonial->save();
                        if ($saved) {
                            $data['testimonialMessage'] = "Thanks you {$data['user']->first_name} for posting a testimonial. It really helps the platform!";
                        }
                    }
                }
            }
            return view('merged_for_receiving_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function unpaidPledge(Request $request)
    {
        if (View::exists('unpaid_pledges_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Unpaid Pledges';
            $data['canonicalUrl'] = env('APP_URL') . 'unpaidPledge';

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);
            $data['user'] = $user;

            $unpaidPledgesResults = DB::select(DB::raw("SELECT * FROM pledges WHERE user_id = :somevariable AND status != :anothervariable"), array(
                'somevariable' => $user->id,
                'anothervariable' => 'PAID'
            ));
            $data['pledges'] = $unpaidPledgesResults;

            return view('unpaid_pledges_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function unpaidBonus(Request $request)
    {
        if (View::exists('bonus_tracker_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Bonus Tracker';
            $data['canonicalUrl'] = env('APP_URL') . 'bonusTracker';

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);
            $data['user'] = $user;

            $userBonus = Pledges::where('referrer_id', '=', $user->id)
                ->where('paid_bonus', '=', 'NO')
                ->orderBy('id')
                ->get();

            $usersCollectingBonusFrom = array();

            if (empty($userBonus)) {
                $data['userBonus'] = "";
                $data['usersCollectingBonusFrom'] = "";
            } else {
                $data['userBonus'] = $userBonus;

                foreach ($userBonus as $item) {
                    $usersCollectingBonusObj = User::find($item->user_id);
                    array_push($usersCollectingBonusFrom, $usersCollectingBonusObj);
                }

                $data['usersCollectingBonusFrom'] = $usersCollectingBonusFrom;
            }

            return view('bonus_tracker_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function paidPledge(Request $request)
    {
        if (View::exists('paid_pledges_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Paid Pledges';
            $data['canonicalUrl'] = env('APP_URL') . 'paidPledge';

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);
            $data['user'] = $user;

            $paidPledgesResults = DB::select(DB::raw("SELECT * FROM pledges WHERE user_id = :somevariable AND status = :anothervariable"), array(
                'somevariable' => $user->id,
                'anothervariable' => 'PAID'
            ));
            $data['pledges'] = $paidPledgesResults;

            return view('paid_pledges_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function faq(Request $request)
    {
        if (View::exists('faq_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'FAQ';
            $data['canonicalUrl'] = env('APP_URL') . 'faq';

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);
            $data['user'] = $user;
            $data['faq'] = Faq::all();

            return view('faq_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function downlines(Request $request)
    {
        if (View::exists('downlines_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Downlines';
            $data['canonicalUrl'] = env('APP_URL') . 'downlines';

            $sessionID = $this->isLogin($request);
            $user = User::find($sessionID);
            $data['user'] = $user;

            $allUsersUnderYou = DB::select(DB::raw("SELECT * FROM users WHERE referrer = :somevariable"), array(
                'somevariable' => $user->id
            ));
            $data['allUsersUnderYou'] = $allUsersUnderYou;

            return view('downlines_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }
}
