<?php

namespace App\Http\Controllers\Monday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Traits\MondayApis;
use Carbon\Carbon;

class AuthController extends Controller
{
    use MondayApis;

    public function login()
    {
        $heading = "Log In";
        $subheading = "TASC Outsourcing KSA";
        return view('auth.login', compact('heading', 'subheading'), );
    }
    public function signup()
    {
        $heading = "Sign Up";
        $subheading = "In order to sign up, you have to be invited by TASC KSA admin. Please complete the form below.";
        return view('auth.signup', compact('heading', 'subheading'), );
    }
    public function forgot()
    {
        $heading = "Forgot Password";
        $subheading = "Please provide the email associated with your account.";
        return view('auth.forgot', compact('heading', 'subheading'));
    }
    public function thankssignup()
    {
        $heading = "Thanks";
        $subheading = "Our team will be in touch within the next 48 hours to activate your account.";
        return view('auth.thankssignup', compact('heading', 'subheading'));
    }
}
