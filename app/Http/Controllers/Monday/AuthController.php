<?php

namespace App\Http\Controllers\Monday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Traits\MondayApis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use App\Models\MondayUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    use MondayApis;

    public function login(Request $request)
    {
        $msg        = '';
        $status     = '';
        $heading    = "Log In";
        $subheading = "TASC Outsourcing KSA";
        if ($request->isMethod('post')) {
            $input = $request->all();
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required|min:6|max:100'
            ], $this->getErrorMessages());

           $userInDb = MondayUsers::loginUser(array( 'email' => trim($input['email']), 'password' =>  trim($input['password'])));

            $userCredential = $request->only('email','password');
            if(Auth::attempt($userCredential)){
                $route = $this->redirectDash();
                return redirect($route);
            }
            else{
            $msg    = "Email or Password is incorrect.";
            $status = "danger";
            return view('auth.login',compact('heading','subheading',  'msg', 'status'));
            }
        }
        return view('auth.login', compact('heading', 'subheading', 'msg', 'status'), );
    }
    public function signup(Request $request)
    {

        $msg    = '';
        $status = '';
        if ($request->isMethod('post')) {
            $input = $request->all();

            $validator = FacadesValidator::make($request->all(),[
                'name'         => 'required',
                'company_name' => 'required',
                'phone'        => 'required|max:10',
                'email'        => 'required|email|unique:monday_users',
                'password'     => 'required|min:6|max:100'
            // ]);
            ], $this->getErrorMessages());

            if ($validator->passes()) {
                $dataToSave = array(
                    'name'         => trim($input['name']),
                    'company_name' => trim($input['company_name']),
                    'phone'        => trim($input['phone']),
                    'email'        => trim($input['email']),
                    // 'password'     => trim($input['password']),
                    'created_at'   => date("Y-m-d H:i:s"),
                    'updated_at'   => date("Y-m-d H:i:s"),
                    'password'     => Hash::make(trim($input['password']))
                );
                $insertUserInDB = MondayUsers::createUser($dataToSave);
                if ($insertUserInDB['status'] == "success") {
                    $msg    = "User Created Successfully.";
                    $status = "success";
                } elseif ($insertUserInDB['status'] == "already") {
                    $msg    = "User Already Exists.";
                    $status = "danger";
                }
                // $msg    = "Something went wrong. Please try again.";
                // $status = "danger";
            }else{
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }
            $heading = "Sign Up";
            $subheading = "In order to sign up, you have to be invited by TASC KSA admin. Please complete the form below.";
            return view('auth.signup', compact('heading', 'subheading', 'msg', 'status'), );
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

    function getErrorMessages() {
        return [
            "required" => ":attribute is required.",
            "max"   => ":attribute should not be more then :max characters.",
            "min"   => ":attribute should not be less then :min characters."
        ];
    }

    public function redirectDash()
    {
        $redirect = '';

        // Super Admin
        if(Auth::user() && Auth::user()->role == 1){
            $redirect = 'monday/admin/create-admin';
        }
        // Admin
        else if(Auth::user() && Auth::user()->role == 2){
            $redirect = 'monday/admin/board-visiblilty';
        }// User
        else{
            $redirect = '/monday/form';
        }

        return $redirect;
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::guard('web')->logout();
        return redirect('/monday/login');
    }
}
