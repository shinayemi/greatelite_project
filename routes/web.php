<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'WebController@index');
Route::get('/pageNotFound', 'WebController@pageNotFound');

Route::any('/login/{message?}/', 'WebController@login');
Route::any('/register/{referrer?}/', 'WebController@signup'); //referrer is optional
Route::any('/confirmEmail/{companyName}/{linkKey}', 'WebController@confirmEmail');
Route::any('/passwordReset/{companyName}/{linkKey}', 'WebController@passwordReset');
Route::any('/passwordRecovery', 'WebController@passwordRecovery');
Route::any('/dashboard', 'DashboardController@dashboard');
Route::any('/profile', 'DashboardController@profile');
Route::any('/pledge', 'DashboardController@pledge');
Route::any('/unpaidPledge', 'DashboardController@unpaidPledge');
Route::get('/faq', 'DashboardController@faq');
Route::get('/about', 'WebController@about');
Route::get('/contactus', 'WebController@contactus');
Route::get('/terms', 'WebController@terms');
Route::any('/paidPledge', 'DashboardController@paidPledge');
Route::any('/confirmRegistrationFee/{userID?}/', 'DashboardController@confirmRegistrationFee');
Route::any('/downlines', 'DashboardController@downlines');
Route::any('/viewMergedForPaying/{pledgeTrackerID?}/', 'DashboardController@viewMergedForPaying');
Route::any('/viewMergedForUserPaying/{pledgeTrackerID?}/', 'DashboardController@viewMergedForUserPaying');
Route::any('/mergeList', 'DashboardController@mergeList');
Route::any('/logout', 'DashboardController@logout');
Route::any('/isLogin', 'DashboardController@isLogin');
Route::any('/mergeListTracker/{pledgeID?}/', 'DashboardController@mergeListTracker');
Route::any('/bonusTracker', 'DashboardController@unpaidBonus');
Route::any('/confirmPayment/{pledgeTrackerID?}/{pledgeTrackerUserSendingID?}', 'DashboardController@confirmPayment');
Route::any('/transactions', 'DashboardController@transactions');

Route::any('/matchTest', 'MatchController@matchTest');
Route::any('/totalPayout', 'MatchController@totalPayout');
Route::any('/pledgesToPayout', 'MatchController@pledgesToPayout');



Route::get('/basic_email', 'MailController@basic_email');
Route::get('/html_email', 'MailController@html_email');
Route::get('/test_registration', 'MailController@test_registration');

Route::any('/logout', 'WebController@logout');

Route::get('/sendMobileVerificationCode/{mobile}/{userRef}/', 'DashboardController@sendMobileVerificationCode');
Route::any('/confirmMobileVerificationCode', 'DashboardController@confirmMobileVerificationCode');
