<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Mail\Mailer;
use App\Http\Requests;
use Log;

use App\User;
use App\PledgeOptions;
use App\Pledges;
use App\PledgeTracker;
use App\BankAccount;
use Sms\EbulkSms;
use App\GreatElites;
use DB;
use Validator;

require(__DIR__ . '/../../EbulkSms.php');

class MatchController extends Controller
{
    private $predefinedData;

    private function sendMergedSms($user)
    {
        $username = env('SMS_USERNAME');
        $apikey = env('SMS_API_KEY');
        $sendername = env('SMS_TITLE');
        $recipients = $user->mobile;
        $flash = 0;
        $appName = env('APP_NAME');
        $composedMessage = "{$user->first_name} you have been merged to make payment. Login into your Greatelite account for user details.";
        //echo ($composedMessage);
        $message = substr($composedMessage, 0, 160); //Limit this message to one page.
        // $sms = new EbulkSms();

        #Use the next line for HTTP POST with JSON
        // $result = $sms->useJSON(env('SMS_JSON_URL'), $username, $apikey, $flash, $sendername, $message, $recipients);
    }

    private function sendRecommitSms($user)
    {
        $username = env('SMS_USERNAME');
        $apikey = env('SMS_API_KEY');
        $sendername = env('SMS_TITLE');
        $recipients = $user->mobile;
        $flash = 0;
        $appName = env('APP_NAME');
        $composedMessage = "{$user->first_name} your pledge is due. You need to recommit and fulfill another pledge. To withdraw due pledge.";
        //echo ($composedMessage);
        $message = substr($composedMessage, 0, 160); //Limit this message to one page.
        $sms = new EbulkSms();

        #Use the next line for HTTP POST with JSON
        $result = $sms->useJSON(env('SMS_JSON_URL'), $username, $apikey, $flash, $sendername, $message, $recipients);
    }

    private function missedMergedOpportunityNotPaidRegistrationFee($user)
    {
        $username = env('SMS_USERNAME');
        $apikey = env('SMS_API_KEY');
        $sendername = env('SMS_TITLE');
        $recipients = $user->mobile;
        $flash = 0;
        $appName = env('APP_NAME');
        $composedMessage = "{$user->first_name} you have been missed a merge opportunity because you not paid the registration fee.";
        //echo ($composedMessage);
        $message = substr($composedMessage, 0, 160); //Limit this message to one page.
        $sms = new EbulkSms();


        if ($user->send_missed_merged_sms == 'NO') {
            #Use the next line for HTTP POST with JSON
            // $result = $sms->useJSON(env('SMS_JSON_URL'), $username, $apikey, $flash, $sendername, $message, $recipients);
            $user->send_missed_merged_sms = 'YES';
            $user->save();
        }
    }


    private function matchToAReceiverThatHasNotPaidAnyAmount()
    {
        // Log::info('Running job to matchToReceive');

        $toReceive = null;
        $userToReceive = null;
        $status = 'PAID';
        $currentDate = date("Y-m-d H:i:s", strtotime("now"));
        $nextDate = date("Y-m-d H:i:s", strtotime("+1 day"));
        $dueDate = date("Y-m-d H:i:s", strtotime("+5 day"));

        echo ("<br>Current Date :" . $currentDate . "<br><hr>");
        // Log::info($currentDate);
        $duePledges = Pledges::where('status', '=', $status)
            ->where('when_due', '<=', $currentDate)
            ->where('process', '=', 'INCOMPLETE')
            ->where('can_withdraw', '=', 'YES')
            ->take(1)
            // ->toSql();
            ->get();

        // echo ("<hr>");
        // print_r($duePledges);
        // echo ("<hr>");
        // exit();
        // need to check if you already have a pledge paid, so withdrawal is good
        if (!empty($duePledges)) {
            // Got due pledges
            foreach ($duePledges as $pledge) {

                $toReceive = $pledge->amount + $pledge->interest + $pledge->bonus_to_collect;
                $userToReceive = User::find($pledge->user_id);

                echo ("USER ID : " . $userToReceive->id . "<br>");
                echo ("PLEDGE ID : " . $pledge->id . "<br>");
                echo ("toReceive : " . $toReceive . "<br>");
                echo ("<br><hr>");

                $usersToPayPledges = Pledges::where('status', '=', 'NOT PAYED')
                    ->where('amount', '<=', $toReceive)
                    // ->where('amount_paid', '!=', 0)
                    ->where('amount_paid', '=', 0)
                    ->where('user_id', '!=', $userToReceive->id)
                    ->get();
                // ->toSql();

                echo ("<hr>");
                echo ("<br>USER TO PAY<br>");
                echo ("<br>$pledge->id<br>");
                // print_r($pledge);
                echo ("<hr>");
                Log::info($usersToPayPledges);
                // exit();

                echo ("<br> toReceive: $toReceive<br>");
                Log::info('<br> toReceive: toReceive<br>');
                // exit();
                if (!empty($usersToPayPledges)) {
                    // echo count($twoUsersToPayPledges);
                    static $moneyCounter = 0;
                    static $moneyFinalCounter = 0;
                    static $total = 0;
                    echo ("<br>$moneyCounter<br>");

                    foreach ($usersToPayPledges as $userToPayPledge) {
                        $moneyCounter = $userToPayPledge->amount + $moneyCounter;
                        $userToPayData = User::find($userToPayPledge->user_id);

                        if ($userToPayData->registration_fee == 'PAID' && $userToPayData->confirm_mobile == 'YES' && $userToPayData->agreed_to_terms == 'YES') {
                            if ($moneyCounter <= $toReceive) {
                                $total += $userToPayPledge->amount;

                                echo ("<br>");
                                print_r($userToPayPledge);
                                echo ("<br><hr>");

                                $pledgeTracker = PledgeTracker::create([
                                    'pledge_id' => $pledge->id,
                                    'amount' => $userToPayPledge->amount,
                                    'user_sending' => $userToPayPledge->user_id,
                                    'user_receiving' => $userToReceive->id,
                                    'status' => 'NOT CONFIRMED'
                                ]);
                                $saved = $pledgeTracker->save();

                                $userToPayPledge->amount_paid = $userToPayPledge->amount;
                                $userToPayPledge->status = 'PAID';
                                $userToPayPledge->when_due = $dueDate;
                                $userToPayPledge->save();
                                echo ("<br>send alert to sender {$userToPayPledge->amount}<br>");
                                // Log::info("<br>send alert to sender {$userToPayPledge->amount}<br>");

                                $userSendingMoney = User::find($userToPayPledge->user_id);
                                $this->sendMergedSms($userSendingMoney);
                            } else {
                                $lastAmount = $toReceive - $total;

                                $pledgeTracker = PledgeTracker::create([
                                    'pledge_id' => $pledge->id,
                                    'type' => 'PLEDGE',
                                    'amount' => $lastAmount,
                                    'user_sending' => $userToPayPledge->user_id,
                                    'user_receiving' => $userToReceive->id,
                                    'status' => 'NOT CONFIRMED'
                                ]);
                                $saved = $pledgeTracker->save();

                                $userToPayPledge->amount_paid = $lastAmount;

                                if ($lastAmount == $userToPayPledge->amount) {
                                    $userToPayPledge->status = 'PAID';
                                } else {
                                    $userToPayPledge->status = 'PAYING';
                                }

                                $userToPayPledge->when_due = $dueDate;
                                $userToPayPledge->save();

                                echo ("<br>FINAL DIFFERENT {$lastAmount}<br>");
                                echo ("<br>total {$total}<br>");
                                // Log::info("<br>FINAL DIFFERENT {$lastAmount}<br>");
                                // Log::info("<br>total {$total}<br>");

                                if (!empty($pledge->referrer_id) && $pledge->referrer_bonus == 0) {
                                    $userToGetBonusPledge = Pledges::where('status', '!=', 'NOT PAYED')
                                        ->where('process', '=', 'INCOMPLETE')
                                        ->where('user_id', '=', $pledge->referrer_id)
                                        ->where('referrer_bonus', '=', 0)
                                        ->get();

                                    if (!empty($userToGetBonusPledge)) {
                                        // add current bonus_to_collect and referrer_bonus
                                        $userToGetBonusPledge->bonus_to_collect = $userToGetBonusPledge->bonus_to_collect + $pledge->referrer_bonus;

                                        $pledge->paid_bonus = 'YES'; // ADDEDED BONUS USER NOT PAYED PLEDGE
                                        $pledge->save();
                                        $saved = $userToGetBonusPledge->save();
                                    }
                                }
                                $pledge->process = 'COMPLETE';

                                $pledge->save();
                                // Log::info("<br>MATCHING SUCCES<br>");
                                break;
                            }
                        } else {
                            $this->missedMergedOpportunityNotPaidRegistrationFee($userToPayData);
                        }
                    }
                }
            }
        }
    }

    private function matchToAReceiverThatHasNotPaidSomeAmount()
    {
        // Log::info('Running job to matchToReceive');

        $toReceive = null;
        $userToReceive = null;
        $status = 'PAID';
        $currentDate = date("Y-m-d H:i:s", strtotime("now"));
        $nextDate = date("Y-m-d H:i:s", strtotime("+1 day"));
        $dueDate = date("Y-m-d H:i:s", strtotime("+5 day"));

        echo ("<br>Current Date :" . $currentDate . "<br>");
        // Log::info($currentDate);
        $duePledges = Pledges::where('status', '=', $status)
            ->where('when_due', '<=', $currentDate)
            ->where('process', '=', 'INCOMPLETE')
            ->where('can_withdraw', '=', 'YES')
            ->take(1)
            // ->toSql();
            ->get();

        echo ("<hr>");
        print_r($duePledges);
        echo ("<hr>");
        // exit();
        // need to check if you already have a pledge paid, so withdrawal is good
        if (!empty($duePledges)) {
            // Got due pledges
            foreach ($duePledges as $pledge) {

                $toReceive = $pledge->amount + $pledge->interest + $pledge->bonus_to_collect;
                $userToReceive = User::find($pledge->user_id);

                $usersToPayPledges = Pledges::where('status', '!=', $status)
                    ->where('amount_paid', '<=', $toReceive)
                    ->get();
                // ->toSql();

                echo ("<hr>");
                print_r($usersToPayPledges);
                echo ("<hr>");
                Log::info($usersToPayPledges);
                // exit();

                echo ("<br> toReceive: $toReceive<br>");
                Log::info('<br> toReceive: toReceive<br>');
                if (!empty($usersToPayPledges)) {
                    // echo count($twoUsersToPayPledges);
                    static $moneyCounter = 0;
                    static $moneyFinalCounter = 0;
                    static $total = 0;
                    echo ("<br>$moneyCounter<br>");

                    foreach ($usersToPayPledges as $userToPayPledge) {
                        $moneyCounter = $userToPayPledge->amount + $moneyCounter;
                        $userToPayData = User::find($userToPayPledge->user_id);

                        if ($userToPayData->registration_fee == 'PAID' && $userToPayData->confirm_mobile == 'YES' && $userToPayData->agreed_to_terms == 'YES') {
                            if ($moneyCounter <= $toReceive) {
                                $total += $userToPayPledge->amount;
                                $pledgeTracker = PledgeTracker::create([
                                    'pledge_id' => $pledge->id,
                                    'amount' => $userToPayPledge->amount,
                                    'user_sending' => $userToPayPledge->user_id,
                                    'user_receiving' => $userToReceive->id,
                                    'status' => 'NOT CONFIRMED'
                                ]);
                                $saved = $pledgeTracker->save();

                                $userToPayPledge->amount_paid = $userToPayPledge->amount_paid + ($userToPayPledge->amount - $userToPayPledge->amount_paid);
                                $userToPayPledge->status = 'PAID';
                                $userToPayPledge->when_due = $dueDate;
                                $userToPayPledge->save();
                                echo ("<br>send alert to sender {$userToPayPledge->amount}<br>");
                                // Log::info("<br>send alert to sender {$userToPayPledge->amount}<br>");

                                $userSendingMoney = User::find($userToPayPledge->user_id);
                                $this->sendMergedSms($userSendingMoney);
                            } else {
                                $lastAmount = $toReceive - $total;

                                $pledgeTracker = PledgeTracker::create([
                                    'pledge_id' => $pledge->id,
                                    'type' => 'PLEDGE',
                                    'amount' => $lastAmount,
                                    'user_sending' => $userToPayPledge->user_id,
                                    'user_receiving' => $userToReceive->id,
                                    'status' => 'NOT CONFIRMED'
                                ]);
                                $saved = $pledgeTracker->save();

                                $userToPayPledge->amount_paid = $lastAmount;

                                if ($lastAmount == $userToPayPledge->amount) {
                                    $userToPayPledge->status = 'PAID';
                                } else {
                                    $userToPayPledge->status = 'PAYING';
                                }

                                $userToPayPledge->when_due = $dueDate;
                                $userToPayPledge->save();

                                echo ("<br>FINAL DIFFERENT {$lastAmount}<br>");
                                echo ("<br>total {$total}<br>");
                                // Log::info("<br>FINAL DIFFERENT {$lastAmount}<br>");
                                // Log::info("<br>total {$total}<br>");

                                if (!empty($pledge->referrer_id) && $pledge->referrer_bonus == 0) {
                                    $userToGetBonusPledge = Pledges::where('status', '!=', 'NOT PAYED')
                                        ->where('process', '=', 'INCOMPLETE')
                                        ->where('user_id', '=', $pledge->referrer_id)
                                        ->where('referrer_bonus', '=', 0)
                                        ->get();

                                    if (!empty($userToGetBonusPledge)) {
                                        // add current bonus_to_collect and referrer_bonus
                                        $userToGetBonusPledge->bonus_to_collect = $userToGetBonusPledge->bonus_to_collect + $pledge->referrer_bonus;

                                        $pledge->paid_bonus = 'YES'; // ADDEDED BONUS USER NOT PAYED PLEDGE
                                        $pledge->save();
                                        $saved = $userToGetBonusPledge->save();
                                    }
                                }
                                $pledge->process = 'COMPLETE';

                                $pledge->save();
                                // Log::info("<br>MATCHING SUCCES<br>");
                                break;
                            }
                        } else {
                            $this->missedMergedOpportunityNotPaidRegistrationFee($userToPayData);
                        }
                    }
                }
            }
        }
    }

    public function __construct()
    {
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
            'currency' => env('APP_DEFAULT_CURRENCY')
        );
    }

    private function sendSmsToUserThatPledgeIsDue()
    {

        $currentDate = date("Y-m-d H:i:s", strtotime("now"));
        // echo ("<br>Current Date :" . $currentDate . "<br>");

        $duePledges = Pledges::where('when_due', '<=', $currentDate)
            ->where('process', '=', 'COMPLETE')
            ->where('can_withdraw', '=', 'NO')
            ->get();

        if (!empty($duePledges)) {
            foreach ($duePledges as $pledge) {

                $userToGetBonusPledge = Pledges::where('status', '!=', 'NOT PAYED')
                    ->where('process', '=', 'INCOMPLETE')
                    ->where('user_id', '=', $pledge->referrer_id)
                    ->where('referrer_bonus', '=', 0)
                    ->get();

                if (!empty($userToGetBonusPledge[0])) {

                    $singleUserToGetBonus = $userToGetBonusPledge[0];

                    $singleUserToGetBonus->bonus_to_collect = $singleUserToGetBonus->bonus_to_collect + $pledge->referrer_bonus;

                    $singleUserToGetBonus->save();

                    $pledge->paid_bonus = 'YES';
                }
                $user = User::find($pledge->user_id);
                if ($pledge->sent_recommit_sms == 'NO') {
                    $this->sendRecommitSms($user);
                    $pledge->sent_recommit_sms = 'YES';
                }

                $pledge->save();
            }
        }
    }

    public function getPledgesToReceive()
    {
        $greatElites = new GreatElites();
        return $greatElites->getPledgesToReceive();
    }

    public function getPledgesToPay()
    {
        $greatElites = new GreatElites();
        return $greatElites->getPledgesToPay();
    }

    public function matchTest(Request $request)
    {
        $greatElites = new GreatElites();
        $pledgesToReceive = $this->getPledgesToReceive();
        // echo ("<br> pledgesToReceive COUNT : " . count($pledgesToReceive));
        // echo ("<br><hr>");
        // print_r($pledgesToReceive);

        $pledgesToPay = $this->getPledgesToPay();
        echo ("TO PAY COUNT >>> " . count($pledgesToPay));
        // foreach ($pledgesToPay as $pledge) {
        //     echo ("remainderToPay : " . $pledge['remainderToPay'] . "<br>");
        //     echo ("pledgedToPay : " . $pledge['pledgedToPay'] . "<br>");
        //     echo ("data ID : " . $pledge['data']->id . "<br>");
        //     echo ("data USER_ID: " . $pledge['data']->user_id . "<br>");
        //     // print_r($pledge);
        //     echo ("<br><hr>");
        // }
        // // print_r($pledgesToPay);
        // echo ("<br><hr>");
        // exit();

        foreach ($pledgesToReceive as $receiver) {
            $pledgeObj = $receiver['data'];

            $bonusToCollectData = $greatElites->getUserBonusThatCanBeCollected($pledgeObj->user_id);
            print_r($bonusToCollectData);
            exit();

            $remainderToReceive = $receiver['remainderToReceive'];
            $pledgedToReceive = $receiver['pledgedToReceive'];
            $pledgesToPay = $this->getPledgesToPay();

            // echo ("<br>remainderToReceive start " . $receiver['data']->id . " : " . $remainderToReceive . "<br><hr>");
            foreach ($pledgesToPay as $sender) {
                // echo ("<br>PAY : " . $sender['remainderToPay'] . "<br>");

                if ($sender['remainderToPay'] <= $remainderToReceive) {
                    echo ("<br>PAY : " . $sender['remainderToPay'] . "<br><hr>");
                    echo ("remainderToReceive : " . $remainderToReceive . "<br><hr>");
                    $testData = [
                        'pledge_id' => $receiver['data']->id,
                        'pledge_id_user_sending' => $sender['data']->id,
                        'amount' => $sender['remainderToPay'],
                        'user_sending' => $sender['data']->user_id,
                        'user_receiving' => $pledgeObj->user_id,
                        'status' => 'NOT CONFIRMED'
                    ];
                    print_r($testData);
                    echo ("<br><hr>");

                    // $pledgeTracker = PledgeTracker::create([
                    //     'pledge_id' => $receiver['data']->id,
                    //     'pledge_id_user_sending' => $sender['data']->id,
                    //     'amount' => $sender['remainderToPay'],
                    //     'user_sending' => $sender['data']->user_id,
                    //     'user_receiving' => $pledgeObj->user_id,
                    //     'status' => 'NOT CONFIRMED'
                    // ]);

                    // $userSendingMoney = User::find($sender['data']->user_id);
                    // $this->sendMergedSms($userSendingMoney);
                    $remainderToReceive = $remainderToReceive - $sender['remainderToPay'];
                    if ($remainderToReceive == 0) {
                        break; // jump out of loop
                    }

                    // continue; //  breaks one iteration (in the loop)

                } else {
                    echo ("CONDITION : " . $sender['remainderToPay'] . "<=" . $remainderToReceive);
                    echo ("<br>PAY 2: " . $sender['remainderToPay'] . "<br>");
                    echo ("remainderToReceive : " . $remainderToReceive . "<br><hr>");
                    echo ("<br>ELSE remainderToReceive : $remainderToReceive <br>");
                    // print_r($sender);
                    $testData = [
                        'pledge_id' => $receiver['data']->id,
                        'pledge_id_user_sending' => $sender['data']->id,
                        'amount' => $remainderToReceive,
                        'user_sending' => $sender['data']->user_id,
                        'user_receiving' => $pledgeObj->user_id,
                        'status' => 'NOT CONFIRMED'
                    ];
                    print_r($testData);
                    echo ("<br><hr>");
                    // $pledgeTracker = PledgeTracker::create([
                    //     'pledge_id' => $receiver['data']->id,
                    //     'pledge_id_user_sending' => $sender['data']->id,
                    //     'amount' => $remainderToReceive,
                    //     'user_sending' => $sender['data']->user_id,
                    //     'user_receiving' => $pledgeObj->user_id,
                    //     'status' => 'NOT CONFIRMED'
                    // ]);

                    // $userSendingMoney = User::find($sender['data']->user_id);
                    // $this->sendMergedSms($userSendingMoney);
                    $remainderToReceive = $remainderToReceive - $remainderToReceive;
                    if ($remainderToReceive == 0) {
                        break; // jump out of loop
                    }

                    // continue; //  breaks one iteration (in the loop)

                }

                // echo ("<br>remainderToReceive end " . $receiver['data']->id . " : " . $remainderToReceive . "<br><hr>");
            }
        }
    }

    public function totalPayout(Request $request)
    {
        $pledgesToReceive = $this->getPledgesToReceive();
        $total  = 0;

        foreach ($pledgesToReceive as $pledge) {
            $total += $pledge->amount;
        }

        echo ("<br>NUMBER TO PAY : " . count($pledgesToReceive) . "<br><hr>");
        echo ("<br>TOTAL : " . env('APP_DEFAULT_CURRENCY') . number_format($total) . "<br>");
    }

    public function pledgesToPayout(Request $request)
    {
        $pledgesToReceive = $this->getPledgesToReceive();
        $total  = 0;

        foreach ($pledgesToReceive as $pledge) {
            $total += $pledge->amount;
            echo ("<br>PLEDGE ID : $pledge->id<br>");
            echo ("<br>USER ID : $pledge->user_id<br>");
            echo ("<br>AMOUNT : $pledge->amount<br>");
            echo ("<br>DUE DATE : $pledge->when_due<br><hr>");
        }
    }
}
