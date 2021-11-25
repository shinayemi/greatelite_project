<?php

namespace App;

use App\User;
use App\PledgeOptions;
use App\Pledges;
use App\Fee;
use App\Faq;
use App\FeeAccount;
use App\PledgeTracker;
use App\Testimonials;
use Sms\EbulkSms;
use App\BankAccount;
use App\Issues;
use App\Configurations;
use DB;


class GreatElites
{

    private function sendUserIdToAdminForChecking($message)
    {
        $username = env('SMS_USERNAME');
        $apikey = env('SMS_API_KEY');
        $sendername = env('SMS_TITLE');
        $recipients = env('APP_ADMIN_CONTACT');
        $flash = 0;
        $appName = env('APP_NAME');
        $composedMessage = $message;
        //echo ($composedMessage);
        $message = substr($composedMessage, 0, 160); //Limit this message to one page.
        $sms = new EbulkSms();
        #Use the next line for HTTP POST with JSON
        $result = $sms->useJSON(env('SMS_JSON_URL'), $username, $apikey, $flash, $sendername, $message, $recipients);
    }

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

    public function getConfiguration($configName)
    {
        $resultSet = Configurations::where('name', $configName)->first();

        if (!empty($resultSet)) {
            return $resultSet->value;
        } else {
            return null;
        }
    }

    public function getUserUnfinishedPledges($userObj)
    {
        $resultSet = DB::select(DB::raw("SELECT * FROM pledges WHERE user_id = :user_id AND (process = :process) AND (can_withdraw = :can_withdraw)"), array(
            'user_id' => $userObj->id,
            'process' => 'INCOMPLETE',
            'can_withdraw' => 'NO'
        ));

        if (!empty($resultSet)) {
            return $resultSet;
        } else {
            return null;
        }
    }

    public function getUserBonusThatCanBeCollected($referrerID)
    {
        $totalBonusToCollect = 0;
        $bonusToPayArray = array();

        $allUnpaidBonus = Pledges::where('process', '=', 'COMPLETE')
            ->where('paid_bonus', '=', 'NO')
            ->where('referrer_id', '=', $referrerID)
            ->get();

        if (!empty($allUnpaidBonus)) {
            foreach ($allUnpaidBonus as $pledge) {
                $totalBonusToCollect += $pledge->referrer_bonus;

                $bonusToPayObj['bonusToCollect'] = $pledge->referrer_bonus;
                $bonusToPayObj['data'] = $pledge;
                array_push($bonusToPayArray, $bonusToPayObj);
            }
        }

        return array($bonusToPayArray, $totalBonusToCollect);
    }

    public function getUserFinishedPledges($userObj)
    {
        $resultSet = DB::select(DB::raw("SELECT * FROM pledges WHERE user_id = :user_id 
        AND (process = :process) AND (can_withdraw = :can_withdraw)"), array(
            'user_id' => $userObj->id,
            'process' => 'COMPLETE',
            'can_withdraw' => 'YES'
        ));

        if (!empty($resultSet)) {
            return $resultSet;
        } else {
            return null;
        }
    }

    public function getUserUnPaidPledges($userObj)
    {
        // this will get paying and not paid pledges
        $unpaidPledgesResultSet = DB::select(DB::raw("SELECT * FROM pledges 
        WHERE user_id = :user_id AND (process = :process) 
        AND (status != :status)"), array(
            'user_id' => $userObj->id,
            'process' => 'INCOMPLETE',
            'status' => 'PAID'
        ));

        if (!empty($unpaidPledgesResultSet)) {
            foreach ($unpaidPledgesResultSet as $pledge) {
                if ($pledge->status == 'PAYING') {
                    //This means you paying pledge, sync amount paid with tracker and pledge 
                    $pledgeTrackerResultSet = DB::select(DB::raw("SELECT * FROM pledge_tracker 
                                                WHERE pledge_id_user_sending = :pledge_id_user_sending 
                                                AND (user_sending = :user_sending)"), array(
                        'pledge_id_user_sending' => $pledge->id,
                        'user_sending' => $pledge->user_id
                    ));

                    if (empty($pledgeTrackerResultSet)) {
                        $total = 0;
                        foreach ($pledgeTrackerResultSet as $pledgeTracker) {
                            $total += $pledgeTracker->amount;
                        }

                        if ($total != $pledge->amount_paid) {
                            $pledge->amount_paid = $total;
                            $pledge->save(); // since pledge tracker is the source of truth, pledge tracker is always right 
                        }
                    }
                } else if ($pledge->status == 'UNPAID') {
                    //This means you not sent money and pledge is still saying unpaid
                    $pledgeTrackerResultSet =
                        DB::select(DB::raw("SELECT * FROM pledge_tracker 
                    WHERE pledge_id_user_sending = :pledge_id_user_sending 
                    AND (user_sending = :user_sending)"), array(
                            'pledge_id_user_sending' => $pledge->id,
                            'user_sending' => $pledge->user_id
                        ));

                    if (!empty($pledgeTrackerResultSet)) {
                        // MEANS PLEDGE IS saying upaid but you sent money
                        $issue = Issues::create([
                            'status' => 'PENDING',
                            'user_id' => $pledge->user_id,
                            'pledge_id' => $pledge->id,
                            'message' => 'pledge tracker has record for this pledge but pledge says unpaid'
                        ]);
                        $saved = $issue->save();
                    }
                }
            }
            return $unpaidPledgesResultSet;
        } else {
            return null;
        }
    }

    public function checkIfThisIsFirstPledge($userObj)
    {
        $checkIfThisisFirstPledgesResults = Pledges::where('user_id', $userObj->id)->first();

        if (empty($checkIfThisisFirstPledgesResults)) {
            return true;
        } else {
            return false;
        }
    }

    public function getDuePledge()
    {
        $duePledge = Pledges::where('status', '=', 'PAID')
            ->where('when_due', '<=', date("Y-m-d H:i:s", strtotime("now")))
            ->where('process', '=', 'INCOMPLETE')
            ->where('can_withdraw', '=', 'YES')
            ->take(1)
            ->get();

        if (!empty($duePledge)) {
            return $duePledge[0];
        } else {
            return null;
        }
    }

    public function getDuePledges()
    {
        $duePledges = Pledges::where('status', '=', 'PAID')
            ->where('when_due', '<=', date("Y-m-d H:i:s", strtotime("now")))
            ->where('process', '=', 'INCOMPLETE')
            ->where('can_withdraw', '=', 'YES')
            ->get();

        if (!empty($duePledges)) {
            return $duePledges;
        } else {
            return null;
        }
    }

    public function getUserDueBonus($user)
    {
        $bonusToCollect = Pledges::where('referrer_id', '=', $user->id)
            ->where('when_due', '<=', date("Y-m-d H:i:s", strtotime("now")))
            ->where('process', '=', 'COMPLETE')
            ->where('can_withdraw', '=', 'YES')
            ->take(1)
            ->get();

        if (!empty($bonusToCollect)) {
            return $bonusToCollect[0];
        } else {
            return null;
        }
    }

    public function getUserAndPledgesResultSetByUserID($user)
    {
        $resultSet = DB::select(DB::raw("
        SELECT users.id,users.email,users.first_name,users.registration_fee,users.confirm_mobile,users.mobile,
        pledges.amount,pledges.amount_paid,amount_received_and_confirmed, 
        pledges.interest,pledges.process,pledges.status,pledges.can_withdraw,pledges.when_due FROM users
        INNER JOIN pledges ON users.id=pledges.user_id
        WHERE users.id = :user_id
        ORDER BY users.id;
        "), array(
            'user_id' => $user->id
        ));

        if (!empty($resultSet)) {
            return $resultSet;
        } else {
            return null;
        }
    }

    public function remergeAndChangeSendingUserStatusToDispute($pledgeTracker, $pledge)
    {
        $configName = "DO REMERGE";
        $trackerCreatedTimeStamp = strtotime($pledgeTracker->created_at);
        $currentTimeStamp = time();

        $diff = $currentTimeStamp - $trackerCreatedTimeStamp;
        $elapsedHours = floor($diff / 60);
        // echo ($elapsedHours);

        if (
            $elapsedHours >= env('APP_TIME_BEFORE_REMERGE')
            && $pledgeTracker->status = 'NOT CONFIRMED'
        ) {
            $returnValue = $this->getConfiguration($configName);
            if ($returnValue == 'YES') {

                if ($pledgeTracker->proof == "") {
                    $user = User::find($pledgeTracker->user_sending);
                    $user->status = 'DISPUTE';
                    $user->save();
                } else {
                    // There is proof and the user payment has not been confirmed
                    $user = User::find($pledgeTracker->user_sending);
                    $user->status = 'DISPUTE';
                    $user->save();

                    $user = User::find($pledgeTracker->user_receiving);
                    $user->status = 'DISPUTE';
                    $user->save();
                }

                $pledgeTracker = PledgeTracker::find($pledgeTracker->id);
                $pledgeTracker->status = 'REMERGED';
                $pledgeTracker->save();
            }
        }
    }

    public function checkPledgeSendingHealth()
    {
        // this will get paying and not paid pledges
        $resultSet = DB::select(DB::raw("SELECT * FROM pledges 
            WHERE user_id = :user_id AND (process = :process) 
            AND (status != :status)"), array(
            'user_id' => $userObj->id,
            'process' => 'INCOMPLETE',
            'status' => 'PAID'
        ));

        if (!empty($unpaidPledgesResultSet)) {
            foreach ($unpaidPledgesResultSet as $pledge) {
                if ($pledge->status == 'PAYING') {
                    //This means you paying pledge, sync amount paid with tracker and pledge 
                    $pledgeTrackerResultSet = DB::select(DB::raw("SELECT * FROM pledge_tracker 
                                                    WHERE pledge_id_user_sending = :pledge_id_user_sending 
                                                    AND (user_sending = :user_sending)"), array(
                        'pledge_id_user_sending' => $pledge->id,
                        'user_sending' => $pledge->user_id
                    ));

                    if (empty($pledgeTrackerResultSet)) {
                        $total = 0;
                        foreach ($pledgeTrackerResultSet as $pledgeTracker) {
                            $total += $pledgeTracker->amount;
                        }

                        if ($total != $pledge->amount_paid) {
                            $pledge->amount_paid = $total;
                            $pledge->save(); // since pledge tracker is the source of truth, pledge tracker is always right 
                        }
                    }
                } else if ($pledge->status == 'UNPAID') {
                    //This means you not sent money and pledge is still saying unpaid
                    $pledgeTrackerResultSet =
                        DB::select(DB::raw("SELECT * FROM pledge_tracker 
                        WHERE pledge_id_user_sending = :pledge_id_user_sending 
                        AND (user_sending = :user_sending)"), array(
                            'pledge_id_user_sending' => $pledge->id,
                            'user_sending' => $pledge->user_id
                        ));

                    if (!empty($pledgeTrackerResultSet)) {
                        // MEANS PLEDGE IS saying upaid but you sent money
                        $issue = Issues::create([
                            'status' => 'PENDING',
                            'user_id' => $pledge->user_id,
                            'pledge_id' => $pledge->id,
                            'message' => 'pledge tracker has record for this pledge but pledge says unpaid'
                        ]);
                        $saved = $issue->save();
                    }
                }
            }
            return $unpaidPledgesResultSet;
        } else {
            return null;
        }
    }

    public function checkPledgeReceivingHealth($pledgeObj)
    {
        $totalToPay = $pledgeObj->amount + $pledgeObj->interest;
        $currentDate = date("Y-m-d H:i:s", strtotime("now"));

        // this will get results for user receiving pledge
        $resultSet = DB::select(DB::raw("
        SELECT pledges.id,pledges.user_id,pledges.amount,pledges.amount_paid,pledges.amount_paid_confirmed,
        pledges.amount_received_and_confirmed,pledges.bonus_to_collect,pledges.interest,
        pledges.referrer_bonus,pledges.referrer_id,pledges.paid_bonus,
        pledges.status,pledges.process,pledges.can_withdraw,pledges.sent_recommit_sms,
        pledges.when_due,
        pledge_tracker.pledge_id,pledge_tracker.pledge_id_user_sending,pledge_tracker.pledge_id_bonus,
        pledge_tracker.user_receiving,pledge_tracker.bonus,pledge_tracker.amount AS pledge_tracker_amount,pledge_tracker.status AS pledge_tracker_status,pledge_tracker.proof,
        pledge_tracker.created_at
        FROM pledges
        INNER JOIN pledge_tracker ON pledges.id=pledge_tracker.pledge_id
        WHERE pledges.id = :pledge_id"), array(
            'pledge_id' => $pledgeObj->id
        ));

        if (!empty($resultSet)) {
            static $add = 0;

            foreach ($resultSet as $item) {
                $add += $item->pledge_tracker_amount;
            }
            if ($add > $pledgeObj->amount) {
                $pledgeObj->status = 'PAID';
                $pledgeObj->amount_paid = $pledgeObj->amount;
            }

            $pledgeObj->save();
            if ($totalToPay != $add && $pledgeObj->can_withdraw == 'YES' &&  $pledgeObj->when_due <= $currentDate) {
                echo ("<br>Add : " . $add);
                return $pledgeObj;
            }
        } else {
            return null;
        }
    }

    public function sumPledgesAmount($userID)
    {
        $resultSet = DB::select(DB::raw("SELECT SUM(amount) AS total FROM pledges
          WHERE user_id = :user_id;
         "), array(
            'user_id' => $userID
        ));

        if (!empty($resultSet)) {
            return $resultSet;
        } else {
            return null;
        }
    }

    public function sumConfirmedPledgesReceived($userID)
    {
        $resultSet = DB::select(DB::raw("SELECT SUM(amount) AS total FROM pledge_tracker
        WHERE user_receiving = :user_receiving AND status = 'CONFIRMED';
       "), array(
            'user_receiving' => $userID
        ));

        if (!empty($resultSet)) {
            return $resultSet;
        } else {
            return null;
        }
    }

    public function sumPledgesCompleted($userID)
    {
        $resultSet = DB::select(DB::raw("SELECT SUM(amount) AS total FROM pledges
        WHERE user_id = :user_id AND process = 'COMPLETE' AND can_withdraw = 'YES';
       "), array(
            'user_id' => $userID
        ));

        if (!empty($resultSet)) {
            return $resultSet;
        } else {
            return null;
        }
    }

    public function sumReceivedAmount($userID)
    {
        $returnArray = array();

        $resultSet = DB::select(DB::raw("SELECT SUM(amount) AS total FROM pledge_tracker
          WHERE user_receiving = :user_receiving AND status = 'CONFIRMED';
         "), array(
            'user_receiving' => $userID
        ));

        $resultSet2 = DB::select(DB::raw("SELECT SUM(amount) AS total FROM pledge_tracker
          WHERE user_receiving = :user_receiving;
         "), array(
            'user_receiving' => $userID
        ));

        if (!empty($resultSet) && !empty($resultSet2)) {
            $returnArray['confirmed'] = $resultSet;
            $returnArray['unconfirmed'] = $resultSet2;
            return $returnArray;
        } else {
            return null;
        }
    }

    public function sumSentAmount($userID)
    {
        $returnArray = array();

        $resultSet = DB::select(DB::raw("SELECT SUM(amount) AS total FROM pledge_tracker
          WHERE user_sending = :user_sending AND status = 'CONFIRMED';
         "), array(
            'user_sending' => $userID
        ));

        $resultSet2 = DB::select(DB::raw("SELECT SUM(amount) AS total FROM pledge_tracker
          WHERE user_sending = :user_sending;
         "), array(
            'user_sending' => $userID
        ));

        if (!empty($resultSet) && !empty($resultSet2)) {
            $returnArray['confirmed'] = $resultSet;
            $returnArray['unconfirmed'] = $resultSet2;
            return $returnArray;
        } else {
            return null;
        }
    }

    public function sumPledgeBySentAndConfirmedTracker($userID, $pledgeID)
    {
        $resultSet = DB::select(DB::raw("SELECT SUM(amount) AS total FROM pledge_tracker
        WHERE user_sending = :user_sending AND pledge_id_user_sending = :pledge_id_user_sending;
       "), array(
            'user_sending' => $userID,
            'pledge_id_user_sending' => $pledgeID
        ));

        if (!empty($resultSet)) {
            return $resultSet;
        } else {
            return null;
        }
    }

    public function getUserSendingTrackersForPledge($pledgeID, $userID)
    {
        $resultSet = DB::select(DB::raw("SELECT * FROM pledge_tracker
          WHERE user_sending = :user_sending AND pledge_id_user_sending = :pledge_id_user_sending;
         "), array(
            'user_sending' => $userID,
            'pledge_id_user_sending' => $pledgeID
        ));


        if (!empty($resultSet)) {
            return $resultSet;
        } else {
            return null;
        }
    }

    public function getUserReceivingTrackersForPledge($pledgeID, $userID)
    {
        $resultSet = DB::select(DB::raw("SELECT * FROM pledge_tracker
          WHERE user_receiving = :user_receiving AND pledge_id = :pledge_id;
         "), array(
            'user_receiving' => $userID,
            'pledge_id' => $pledgeID
        ));


        if (!empty($resultSet)) {
            return $resultSet;
        } else {
            return null;
        }
    }

    public function checkIfUserCanPledge($id)
    {
        $user = User::find($id);

        if (
            $user->registration_fee == 'PAID' && $user->confirmed_email == 'YES'
            && $user->confirm_mobile == 'YES' && $user->agreed_to_terms == 'YES'
            && $user->status = 'ACTIVE'
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function dropIssue($userID, $pledgeID, $message)
    {
        $issues = Issues::where('user_id', '=', $userID)
            ->where('pledge_id', '=', $pledgeID)
            ->get();

        if (empty($issues)) {
            $issue = Issues::create([
                'status' => 'PENDING',
                'user_id' => $userID,
                'pledge_id' => $pledgeID,
                'message' => $message
            ]);
            $saved = $issue->save();
        }
    }

    public function getUserUnpaidBonus($userID)
    {
        $resultSet = DB::select(DB::raw("SELECT * FROM pledge
        WHERE referrer_id = :referrer_id AND paid_bonus = :paid_bonus;
       "), array(
            'referrer_id' => $userID,
            'paid_bonus' => 'NO'
        ));


        if (!empty($resultSet)) {
            return $resultSet;
        } else {
            return null;
        }
    }

    public function getUserSendingTransactions($userID)
    {
        $transactions = PledgeTracker::where('user_sending', '=', $userID)->get();

        return $transactions;
    }

    public function getUserReceivingTransactions($userID)
    {
        $transactions = PledgeTracker::where('user_receiving', '=', $userID)->get();

        return $transactions;
    }

    public function getPledgesToPay()
    {
        $this->sendSmsToUserThatPledgeIsDue();
        $pledgesToPay = array();
        $allUsers = User::all();
        $currentDate = date("Y-m-d H:i:s", strtotime("now"));

        foreach ($allUsers as $user) {
            $canSendOrReceivePledge = $this->checkIfUserCanPledge($user->id);

            if ($canSendOrReceivePledge && $user->account_type == 'REGULAR') {

                $userPledges = Pledges::where('user_id', '=', $user->id)
                    ->get();

                if (!empty($userPledges)) {

                    foreach ($userPledges as $pledge) {

                        $canSendOrReceivePledge = $this->checkIfUserCanPledge($pledge->user_id);
                        $pledgeTrackerForSendingUserAndPledge = $this->getUserSendingTrackersForPledge($pledge->id, $pledge->user_id);

                        if (!empty($pledgeTrackerForSendingUserAndPledge)) {
                            // remove pledges that has not paid within the time allowed
                            foreach ($pledgeTrackerForSendingUserAndPledge as $tracker) {
                                $this->remergeAndChangeSendingUserStatusToDispute($tracker, $pledge);
                            }
                        }

                        $pledgeTrackerSumArray = $this->sumSentAmount($user->id);
                        $sumPledges = $this->sumPledgesAmount($pledge->user_id);
                        $sumPledgesCompleted = $this->sumPledgesCompleted($pledge->user_id);

                        $pledgeTrackerForSendingUserAndPledge = $this->getUserSendingTrackersForPledge($pledge->id, $pledge->user_id);

                        // echo ("<br>USER : $user->id<br>");
                        // echo ("<br>PLEDGE : $pledge->id<br>");
                        // print_r($sumPledges);
                        // echo ("<br><hr><br>");
                        // print_r($pledgeTrackerForSendingUserAndPledge);
                        // echo ("<br><hr><br>");
                        // echo ($pledgeTrackerSumArray['unconfirmed'][0]->total);
                        // exit();

                        if ($canSendOrReceivePledge && !empty($pledgeTrackerForSendingUserAndPledge)) {
                            $total = 0;
                            foreach ($pledgeTrackerForSendingUserAndPledge as $tracker) {
                                //echo ("<br>Tracker status $tracker->status <br>");

                                if ($tracker->status != 'REMERGED') {
                                    $total += $tracker->amount;
                                }
                            }

                            // echo ("<br>TOTAL : $total - $pledge->amount - PLEDGE ID : $pledge->id  - USER ID - $pledge->user_id<br>");
                            // exit();

                            if ($total < $pledge->amount) {
                                // NOT PAYED COMPLETE
                                $pledge->amount_paid = $total;
                                $pledge->amount_paid_confirmed = $total;
                                $pledge->status = 'PAYING';

                                $pledgeToPayObj['remainderToPay'] = $pledge->amount - $total;
                                $pledgeToPayObj['pledgedToPay'] = $total;
                                $pledgeToPayObj['data'] = $pledge;
                                array_push($pledgesToPay, $pledgeToPayObj);
                            } else if ($total == $pledge->amount) {
                                // ALL GOOD NO NEED TO PAY
                                $pledge->amount_paid = $total;
                                $pledge->amount_paid_confirmed = $total;
                                $pledge->status = 'PAID';
                            } else if ($total == 0) {
                                // NOT PAID AT ALL
                                $pledge->amount_paid = $total;
                                $pledge->amount_paid_confirmed = $total;
                                $pledge->status = 'NOT PAYED';

                                $pledgeToPayObj['remainderToPay'] = $pledge->amount - $total;
                                $pledgeToPayObj['pledgedToPay'] = $total;
                                $pledgeToPayObj['data'] = $pledge;
                                array_push($pledgesToPay, $pledgeToPayObj);
                            } else if ($total > $pledge->amount) {
                                // OVER PAID
                                $this->dropIssue($pledge->user_id, $pledge->id, 'User has over paid pledge');
                            }
                        } else if ($canSendOrReceivePledge && empty($pledgeTrackerForSendingUserAndPledge)) {
                            // first pledge
                            $pledge->amount_paid = $total;
                            $pledge->amount_paid_confirmed = $total;
                            $pledge->status = 'NOT PAYED';

                            $pledgeToPayObj['remainderToPay'] = $pledge->amount;
                            $pledgeToPayObj['pledgedToPay'] = $pledge->amount;
                            $pledgeToPayObj['data'] = $pledge;
                            array_push($pledgesToPay, $pledgeToPayObj);
                        } else {
                            $this->dropIssue($pledge->user_id, $pledge->id, 'User cannot send or receive pledges');
                        }
                        $pledge->save();
                    }
                }
            }
        }

        return $pledgesToPay;
    }

    public function getPledgesToReceive()
    {
        $this->sendSmsToUserThatPledgeIsDue();
        $pledgesToReceive = array();
        $allUsers = User::all();
        $configName = "PAY BONUS";

        foreach ($allUsers as $user) {
            $canSendOrReceivePledge = $this->checkIfUserCanPledge($user->id);
            if (
                $canSendOrReceivePledge && $user->account_type != 'ADMIN'
            ) {
                $currentDate = date("Y-m-d H:i:s", strtotime("now"));

                $userDuePledges = Pledges::where('when_due', '<=', $currentDate)
                    ->where('user_id', '=', $user->id)
                    ->orderBy('when_due')
                    ->get();

                foreach ($userDuePledges as $pledge) {
                    $pledgeTrackerForReceivingUserAndPledge = $this->getUserReceivingTrackersForPledge($pledge->id, $user->id);

                    if (!empty($pledgeTrackerForReceivingUserAndPledge)) {
                        // remove pledges that has not paid within the time allowed
                        foreach ($pledgeTrackerForReceivingUserAndPledge as $tracker) {
                            $this->remergeAndChangeSendingUserStatusToDispute($tracker, $pledge);
                        }
                    }
                }

                $userDuePledges = Pledges::where('when_due', '<=', $currentDate)
                    ->where('user_id', '=', $user->id)
                    ->orderBy('when_due')
                    ->get();

                $userPledges = Pledges::where('user_id', '=', $user->id)
                    ->orderBy('id')
                    ->get();

                if (!empty($userDuePledges) && !empty($userPledges)) {
                    $userDuePledgesCount = count($userDuePledges);
                    $userPledgesCount = count($userPledges);

                    if (($userDuePledgesCount + 1) == $userPledgesCount) {

                        $pledgeTrackerSumArray = $this->sumSentAmount($user->id);
                        $sumPledges = $this->sumPledgesAmount($user->id);

                        if ($pledgeTrackerSumArray['confirmed'][0]->total == $sumPledges[0]->total) {

                            foreach ($userDuePledges as $pledge) {
                                // which pledge is due to receive and how much
                                $canSendOrReceivePledge = $this->checkIfUserCanPledge($pledge->user_id);
                                $sumPledgeTrackerSending = $this->sumPledgeBySentAndConfirmedTracker($user->id, $pledge->id);

                                if ($canSendOrReceivePledge) {
                                    if ($sumPledgeTrackerSending[0]->total == $pledge->amount) {
                                        // PAID PLEDGES FOR DUE PLEDGE
                                        // NOW CHECK HOW MUCH USER HAS COLLECTED FOR DUE PLEDGE

                                        foreach ($userDuePledges as $pledge) {
                                            $pledgeTrackerForReceivingUserAndPledge = $this->getUserReceivingTrackersForPledge($pledge->id, $user->id);
                                            $total = 0;

                                            if (!empty($pledgeTrackerForReceivingUserAndPledge)) {
                                                foreach ($pledgeTrackerForReceivingUserAndPledge as $tracker) {
                                                    if ($tracker->status != 'REMERGED') {
                                                        $total += $tracker->amount;
                                                    }
                                                }
                                            }

                                            if (($pledge->amount + $pledge->interest) == $total) {
                                                // receive pledge without bonus
                                                // echo ("receive pledge without bonus<br>");
                                                // echo ("<br>PLEDGE ID : $pledge->id , USER ID : $pledge->user_id , AMOUNT = $pledge->amount , TOTAL : $total<br><hr>");
                                            } else if ($total > ($pledge->amount + $pledge->interest)) {
                                                // receiver pledge with bonus
                                                // check bonus
                                                // echo ("receiver pledge with bonus<br>");
                                                // echo ("<br>PLEDGE ID : $pledge->id , USER ID : $pledge->user_id , AMOUNT = $pledge->amount , TOTAL : $total<br><hr>");
                                            } else if ($total == 0) {
                                                // not receiver pledge
                                                // echo ("not receiver pledge<br>");
                                                // echo ("<br>PLEDGE ID : $pledge->id , USER ID : $pledge->user_id , AMOUNT = $pledge->amount , TOTAL : $total<br><hr>");

                                                $returnValue = $this->getConfiguration($configName);
                                                if ($returnValue == 'YES') {
                                                    $bonusToCollectData = $this->getUserBonusThatCanBeCollected($pledge->user_id);
                                                    if ($bonusToCollectData[1] != 0) {
                                                        // FOR LATER IMPLEMENT BONUS  
                                                    }
                                                } else {
                                                    $pledgeToReceiverObj['remainderToReceive'] = ($pledge->amount + $pledge->interest) - $total;
                                                    $pledgeToReceiverObj['pledgedToReceive'] = $pledge->amount + $pledge->interest;
                                                    $pledgeToReceiverObj['data'] = $pledge;
                                                }

                                                array_push($pledgesToReceive, $pledgeToReceiverObj);
                                            } else if ($total < ($pledge->amount + $pledge->interest)) {
                                                // receiver pledge but not complete
                                                // echo ("receiver pledge but not complete<br>");
                                                // echo ("<br>PLEDGE ID : $pledge->id , USER ID : $pledge->user_id , AMOUNT = $pledge->amount , TOTAL : $total<br><hr>");

                                                $returnValue = $this->getConfiguration($configName);
                                                if ($returnValue == 'YES') {
                                                    $bonusToCollectData = $this->getUserBonusThatCanBeCollected($pledge->user_id);
                                                    if ($bonusToCollectData[1] != 0) {
                                                        // FOR LATER IMPLEMENT BONUS  
                                                    }
                                                } else {
                                                    $pledgeToReceiverObj['remainderToReceive'] = ($pledge->amount + $pledge->interest) - $total;
                                                    $pledgeToReceiverObj['pledgedToReceive'] = $pledge->amount + $pledge->interest;
                                                    $pledgeToReceiverObj['data'] = $pledge;
                                                }

                                                array_push($pledgesToReceive, $pledgeToReceiverObj);
                                            }
                                        }
                                    }
                                } else {
                                    $this->dropIssue($pledge->user_id, $pledge->id, 'User cannot send or receive pledges');
                                }
                            }
                        }
                    } else if (($userDuePledgesCount + 1) > $userPledgesCount) {
                        $this->dropIssue($user->id, 0, 'User account not right due pleges more than pledges.');
                    } else if (($userDuePledgesCount + 1) < $userPledgesCount) {
                        $this->dropIssue($user->id, 0, 'User account not right due pleges less than pledges.');
                    }
                }
            }
        }

        return $pledgesToReceive;
    }


    public function automaticMerge()
    {
        $configName = "AUTOMATIC MERGE";
        $configName2 = "PAY BONUS";

        $returnValue = $this->getConfiguration($configName);
        if ($returnValue == 'YES') {
            $pledgesToReceive = $this->getPledgesToReceive();
            $pledgesToPay = $this->getPledgesToPay();

            foreach ($pledgesToReceive as $receiver) {
                $pledgeObj = $receiver['data'];

                $remainderToReceive = $receiver['remainderToReceive'];
                $pledgedToReceive = $receiver['pledgedToReceive'];
                $pledgesToPay = $this->getPledgesToPay(); // Need to update the pledgesToPay each time through the sender loop

                foreach ($pledgesToPay as $sender) {
                    if ($sender['remainderToPay'] <= $remainderToReceive) {

                        $configName2 = "PAY BONUS";

                        $returnValue = $this->getConfiguration($configName2);
                        if ($returnValue == 'YES') {
                            $bonusToCollectData = $this->getUserBonusThatCanBeCollected($pledgeObj->user_id);

                            if ($bonusToCollectData[1] != 0) {
                                // FOR LATER IMPLEMENT BONUS  
                                // foreach($bonusToCollectData[0] as $bonusToPayObj){
                                // }
                                // $bonusToPayObj['bonusToCollect'] = $pledge->referrer_bonus;
                                // $bonusToPayObj['data'] = $pledge;

                                // $pledgeTracker = PledgeTracker::create([
                                //     'pledge_id' => $receiver['data']->id,
                                //     'pledge_id_user_sending' => $sender['data']->id,
                                //     'amount' => $sender['remainderToPay'],
                                //     'user_sending' => $sender['data']->user_id,
                                //     'user_receiving' => $pledgeObj->user_id,
                                //     'status' => 'NOT CONFIRMED'
                                // ]);
                            }
                        } else {
                            $pledgeTracker = PledgeTracker::create([
                                'pledge_id' => $receiver['data']->id,
                                'pledge_id_user_sending' => $sender['data']->id,
                                'amount' => $sender['remainderToPay'],
                                'user_sending' => $sender['data']->user_id,
                                'user_receiving' => $pledgeObj->user_id,
                                'status' => 'NOT CONFIRMED'
                            ]);
                        }

                        $userSendingMoney = User::find($sender['data']->user_id);
                        $this->sendMergedSms($userSendingMoney);
                        $remainderToReceive = $remainderToReceive - $sender['remainderToPay'];

                        if ($remainderToReceive == 0) {
                            break; // jump out of loop
                        }

                        continue; //  breaks one iteration (in the loop)
                    } else {
                        $pledgeTracker = PledgeTracker::create([
                            'pledge_id' => $receiver['data']->id,
                            'pledge_id_user_sending' => $sender['data']->id,
                            'amount' => $remainderToReceive,
                            'user_sending' => $sender['data']->user_id,
                            'user_receiving' => $pledgeObj->user_id,
                            'status' => 'NOT CONFIRMED'
                        ]);

                        $userSendingMoney = User::find($sender['data']->user_id);
                        $this->sendMergedSms($userSendingMoney);
                        $remainderToReceive = $remainderToReceive - $remainderToReceive;
                        if ($remainderToReceive == 0) {
                            break; // jump out of loop
                        }
                    }
                }
            }
        }
    }
}
