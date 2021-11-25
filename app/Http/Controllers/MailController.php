<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use App\Mail\NewMail;

class MailController extends Controller
{

    public function basic_email()
    {
        $data = array('name' => "Virat Gandhi");

        Mail::send(['text' => 'emails.mail'], $data, function ($message) {
            $message->to('greendublin007@gmail.com', 'Tutorials Point')->subject('Laravel Basic Testing Mail');
            $message->from('site@greatelites.com', 'Bernard Dublin-Green');
        });
        echo "Basic Email Sent. Check your inbox.";
    }

    public function html_email()
    {
        $data = array('name' => "Virat Gandhi");
        Mail::send('emails.mail', $data, function ($message) {
            $message->to('greendublin007@gmail.com', 'Tutorials Point')->subject('Laravel HTML Testing Mail');
            $message->from('site@greatelites.com', 'Bernard Dublin-Green');
        });
        echo "HTML Email Sent. Check your inbox.";
    }

    public function test_registration()
    {


        $data = array(
            'name' => env('APP_NAME'),
            'logo' => env('APP_URL') . "assets/logo.png",
            'username' => 'greenDevNG',
            'linkKey' => 'fff',
            'confirmationLink' => env('APP_URL') . 'confirmEmail/'
        );

        Mail::send('emails.user-registration', $data, function ($message) {
            $message->to('greendublin007@gmail.com', 'Tutorials Point')->subject('Laravel HTML Testing Mail');
            $message->from('site@greatelites.com', 'Bernard Dublin-Green');
        });
        echo "HTML Email Sent. Check your inbox.";
    }

    public function attachment_email()
    {
        $data = array('name' => "Virat Gandhi");
        Mail::send('emails.mail', $data, function ($message) {
            $message->to('abc@gmail.com', 'Tutorials Point')->subject('Laravel Testing Mail with Attachment');
            $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
            $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
            $message->from('xyz@gmail.com', 'Virat Gandhi');
        });
        echo "Email Sent with attachment. Check your inbox.";
    }
}
