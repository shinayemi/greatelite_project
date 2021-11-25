<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests;

use Mail;
use App\Mail\UserRegistration;
use App\Sms;
use App\User;
use App\Pledges;
use App\Testimonials;
use DB;
use Validator;


class WebController extends Controller
{

    const STATUS = array("ACTIVE", "NOT ACTIVE");
    const CONFIRM_EMAIL = array("YES", "NO");
    const loginWarningArray = array(
        "1" => "Email confirmed and account is activated",
        "2" => "Email has not been confirmed, a confirmation link was sent ",
        "99" => "Account has been suspended",
    );
    const DEFAULT_REFERRER = 1;
    private $predefinedData;

    private function generationEmailConfirmationKey($firstName, $email)
    {
        $keyCombination = $firstName . $email;
        $confirmationKey = md5($keyCombination);
        return $confirmationKey;
    }

    private function generateUserRef($firstName, $lastName, $email)
    {
        $keyCombination = $firstName . $lastName . $email;
        $confirmationKey = md5($keyCombination);
        return $confirmationKey;
    }

    private function getReferrerID($referrerID)
    {
        $user = User::where('user_ref', $referrerID)->first();

        if (!empty($user)) {
            return $user->id;
        } else {
            return self::DEFAULT_REFERRER; // 1 is the great elite default referrer
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
            'success' => false
        );
    }

    public function index($status = 0)
    {
        if (View::exists('home_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Welcome to ' . strtoupper(env('APP_NAME'));
            $data['canonicalUrl'] = env('APP_URL');

            $testimionials = Testimonials::where('status', 'ACCEPT')
                ->orderBy('id', 'DESC')
                ->take(7)
                ->get();

            $data['testimonials'] = "";
            $data['testimonialUsers'] = "";

            if (!empty($testimionials)) {
                $data['testimonials'] = $testimionials;
                $testimonialUsers = array();

                foreach ($testimionials as $item) {
                    $pledgeObj = Pledges::find($item->pledge_id);
                    $userObj = User::find($pledgeObj->user_id);
                    if (!empty($userObj)) {
                        array_push($testimonialUsers, $userObj);
                    }
                }
                $data['testimonialUsers'] = $testimonialUsers;
            }

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

    public function about($status = 0)
    {
        if (View::exists('about_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'About ' . strtoupper(env('APP_NAME'));
            $data['canonicalUrl'] = env('APP_URL') . 'about';

            $data['status'] = $status;
            $data['canonicalUrl'] = env('APP_URL') . 'index';
            return view('about_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function terms($status = 0)
    {
        if (View::exists('terms_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Terms And Privacy ' . strtoupper(env('APP_NAME'));
            $data['canonicalUrl'] = env('APP_URL') . 'terms';

            $data['status'] = $status;
            $data['canonicalUrl'] = env('APP_URL') . 'index';
            return view('terms_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function contactus($status = 0)
    {
        if (View::exists('contact_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Contact ' . strtoupper(env('APP_NAME'));
            $data['canonicalUrl'] = env('APP_URL');

            $data['status'] = $status;
            $data['canonicalUrl'] = env('APP_URL') . 'index';
            return view('contact_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function confirmEmail($name, $linkKey, Request $request)
    {
        $name = urldecode($name);
        $linkKey = urldecode($linkKey);

        $data = $this->predefinedData;
        $data['pageTitle'] = 'Confirm Email';
        $data['canonicalUrl'] = env('APP_URL') . 'confirmEmail';

        $user = User::where('email_confirmation_key', $linkKey)->first();
        $user->status = self::STATUS[0];
        $user->confirmed_email = self::CONFIRM_EMAIL[0];

        $confirmLinkKey = $this->generationEmailConfirmationKey($name, $user->email);
        if ($confirmLinkKey ==  $linkKey) {
            $saved = $user->save();

            if ($saved) {
                $data['warning'] = 'Email confirmed and account is activated';
            } else {
                $data['warning'] = 'Something went wrong with email comfirmation';
            }
        } else {
            $data['warning'] = 'Activation key mismatch';
        }

        if (View::exists('login_combined')) {
            $data['formValues'] = array(
                'email' => $request->old('email'),
            );

            $input = $request->all();

            if (!empty($input)) {
                if (!empty($input['email'])) {
                    $email = strtolower();
                }

                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
                ]);

                if ($validator->fails()) {
                    $data['errors'] = $validator->errors();
                } else {

                    $hashed = Hash::make($input['password']);

                    $data['formValues'] = array(
                        'email' => $email,
                    );

                    // Find the user by email and status
                    $user = User::where('email', $email)->first();

                    if (!$user) {
                        $data['errors'] = array();
                        if ($user->confirmed_email == 'NO') {
                            $data['errors'] . push("{$user->email} has not been confirmed");
                        } elseif ($user->status == 'NOT ACTIVE') {
                            $data['errors'] . push("account ({$user->email}) is not active");
                        }
                    }

                    $password = $input['password'];
                    if (Hash::check($password, $user->password)) {
                        $request->session()->put('email', $user->email);
                        $request->session()->put('status', $user->status);
                        $request->session()->put('id', $user->id);
                        return redirect()->action('DashboardController@dashboard');
                    }
                }
            }

            return view('login_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function passwordReset($name, $linkKey, Request $request)
    {
        $name = urldecode($name);
        $linkKey = urldecode($linkKey);

        $data = $this->predefinedData;
        $data['pageTitle'] = 'Password Reset';
        $data['canonicalUrl'] = env('APP_URL') . 'passwordReset';
        $data['name'] = $name;
        $data['linkKey'] = $linkKey;

        $user = User::where('email_confirmation_key', $linkKey)->first();;
        $confirmLinkKey = $this->generationEmailConfirmationKey($name, $user->email);
        if ($confirmLinkKey !=  $linkKey) {
            $data['warning'] = 'Password Reset key mismatch';
        }

        if (View::exists('password_reset_combined')) {
            $data['formValues'] = array(
                'email' => $request->old('email'),
            );

            $input = $request->all();

            if (!empty($input)) {

                $validator = Validator::make($request->all(), [
                    'password' => 'required|confirmed|min:6'
                ]);

                if ($validator->fails()) {
                    $data['errors'] = $validator->errors();
                } else {

                    $hashed = Hash::make($input['password']);
                    $user = User::where('email_confirmation_key', $linkKey)->first();;

                    $user->password = $hashed;
                    $saved = $user->save();
                    if ($saved) {
                        $data['success'] = true;
                    }
                }
            }

            return view('password_reset_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function signup($referrer = self::DEFAULT_REFERRER, Request $request)
    {

        if (View::exists('signup_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Signp';
            $data['canonicalUrl'] = env('APP_URL') . 'signup';

            $request->flashOnly(['first_name', 'last_name', 'email']);

            if (!empty($referrer)) {
                $data['referrer'] = urldecode($referrer);
                $referrerCode = urldecode($data['referrer']);
                $referrerID = $this->getReferrerID($referrerCode);
                $data['uplineUserObj'] = User::find($referrerID);
            }

            $data['formValues'] = array(
                'first_name' => $request->old('first_name'),
                'last_name' => $request->old('last_name'),
                'email' => $request->old('email'),
                'agreed_to_terms' => $request->old('agreed_to_terms')
            );

            $input = $request->all();

            if (!empty($input['email'])) {

                $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'password' => 'required|confirmed|min:6',
                    'last_name' => 'required',
                    'email' => 'required|email|unique:users,email'
                ]);

                if ($validator->fails()) {
                    $data['errors'] = $validator->errors();
                } else {

                    $hashed = Hash::make($input['password']);
                    $confirmLinkKey = $this->generationEmailConfirmationKey($input['first_name'], $input['email']);
                    $user_ref = $this->generateUserRef($input['first_name'], $input['last_name'], $input['email']);

                    $referrerID = null;
                    if (!empty($input['referrer'])) {
                        $data['referrer'] = urldecode($input['referrer']);
                        $referrerCode = urldecode($input['referrer']);
                        $referrerID = $this->getReferrerID($referrerCode);
                        $data['uplineUserObj'] = User::find($referrerID);
                    }

                    $user = User::create([
                        'first_name' => $input['first_name'],
                        'last_name' => $input['last_name'],
                        'email' => strtolower($input['email']),
                        'password' => $hashed,
                        'agreed_to_terms' => 'YES',
                        'user_ref' => $user_ref,
                        'referrer' => $referrerID,
                        'email_confirmation_key' => $confirmLinkKey,
                        'status' => self::STATUS[0], // THIS WILL GO OUT AFTER IMPLEMENTING EMAIL VALIDATION
                        'confirmed_email' => self::CONFIRM_EMAIL[0] //// THIS WILL GO OUT AFTER IMPLEMENTING EMAIL VALIDATION
                    ]);
                    //$saved = $user->save();

                    $confirmLinkKey = $this->generationEmailConfirmationKey($input['first_name'], $input['email']);

                    $mailData = array(
                        'name' => env('APP_NAME'),
                        'logo' => env('APP_URL') . "assets/logo.png",
                        'username' => $user->first_name,
                        'linkKey' => $confirmLinkKey,
                        'confirmationLink' => env('APP_URL') . 'confirmEmail/'
                    );

                    Mail::send('emails.user-registration', $mailData, function ($message) use ($user) {
                        $message->to($user->email, $user->first_name)->subject('Great Elites Email Confirmation');
                        $message->from(env('MAIL_USERNAME'), env('APP_NAME'));
                    });

                    $data['success'] = true;
                }

                $data['formValues'] = array(
                    'first_name' => $input['first_name'],
                    'last_name' => $input['last_name'],
                    'email' => $input['email'],
                    'agreed_to_terms' => $input['agreed_to_terms'],
                );
            }
            return view('signup_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function login($message = null, Request $request)
    {
        if (View::exists('login_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Login';
            $data['canonicalUrl'] = env('APP_URL') . 'login';

            $sessionID = $request->session()->get('id');
            $sessionEmail = $request->session()->get('email');
            $sessionStatus = $request->session()->get('status');

            if ($request->session()->has('id')) {
                return redirect()->action('DashboardController@dashboard');
            }

            $data['formValues'] = array(
                'email' => $request->old('email'),
            );

            $input = $request->all();

            $data['message'] = $message;
            if (!is_null($data['message'])) {
                $data['message'] = urlencode($message);
            }

            if (!empty($input) && !empty($input['password']) && !empty($input['email'])) {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
                ]);

                if ($validator->fails()) {
                    $data['errors'] = $validator->errors();
                } else {
                    $hashed = Hash::make($input['password']);

                    $data['formValues'] = array(
                        'email' => $input['email'],
                    );

                    // Find the user by email and status
                    $user = User::where('email', $input['email'])->first();
                    $password = $input['password'];

                    if (!empty($user)) {
                        if ($user->status = "ACTIVE") {
                            if ($user->confirmed_email == 'YES') {
                                if (Hash::check($password, $user->password)) {
                                    $request->session()->put('email', $user->email);
                                    $request->session()->put('status', $user->status);
                                    $request->session()->put('id', $user->id);
                                    return redirect()->action('DashboardController@dashboard');
                                } else {
                                    $data['message'] = "Email and password combination incorrect.";
                                }
                            } else {
                                $data['message'] = "{$user->email} has not been confirmed";
                            }
                        } else {
                            $data['message'] = "{$user->email} has been deactivated, contact support {$data['telephone']}";
                        }
                    }
                }
            }

            return view('login_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function passwordRecovery($message = null, Request $request)
    {
        if (View::exists('password_recovery_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Password Recovery';
            $data['canonicalUrl'] = env('APP_URL') . 'passwordRecovery';

            $request->session()->forget('id');
            $request->session()->forget('email');
            $request->session()->forget('status');

            $data['formValues'] = array(
                'email' => $request->old('email'),
            );

            $input = $request->all();

            $data['message'] = $message;
            if (!is_null($data['message'])) {
                $data['message'] = urlencode($message);
            }

            if (!empty($input) && !empty($input['email'])) {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email'
                ]);

                if ($validator->fails()) {
                    $data['errors'] = $validator->errors();
                } else {
                    $data['formValues'] = array(
                        'email' => $input['email'],
                    );

                    // Find the user by email and status
                    $user = User::where('email', $input['email'])->first();

                    if (!empty($user)) {
                        if ($user->status = "ACTIVE") {
                            if ($user->confirmed_email == 'YES') {
                                $confirmLinkKey = $this->generationEmailConfirmationKey($user->first_name, $input['email']);

                                $mailData = array(
                                    'name' => env('APP_NAME'),
                                    'logo' => env('APP_URL') . "assets/logo.png",
                                    'username' => $user->first_name,
                                    'linkKey' => $confirmLinkKey,
                                    'passwordResetLink' => env('APP_URL') . 'passwordReset/'
                                );

                                $data['message'] = "A password recovery link has been send to {$user->email}.";

                                Mail::send('emails.password-recovery', $mailData, function ($message) use ($user) {
                                    $message->to($user->email, $user->first_name)->subject('Great Elites Password Recovery');
                                    $message->from(env('MAIL_USERNAME'), env('APP_NAME'));
                                });
                            } else {
                                $data['message'] = "{$user->email} has not been confirmed";
                            }
                        } else {
                            $data['message'] = "{$user->email} has been deactivated, contact support {$data['telephone']}";
                        }
                    } else {
                        $data['message'] = "{$input['email']} invalid, email does not belong to a user.";
                    }
                }
            }

            return view('password_recovery_combined', $data);
        } else {
            if (View::exists('page_not_found_combined')) {
                $data = $this->predefinedData;
                $data['pageTitle'] = 'Page Not Found';
                return view('page_not_found_combined', $data);
            }
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget('id');
        $request->session()->forget('email');
        $request->session()->forget('status');
        $request->session()->flush();
        return redirect()->action('WebController@login');
    }

    public function pageNotFound()
    {
        if (View::exists('page_not_found_combined')) {
            $data = $this->predefinedData;
            $data['pageTitle'] = 'Page not found';

            $data['canonicalUrl'] = env('APP_URL') . 'pageNotFound';
            return view('page_not_found_combined', $data);
        }
    }
}
