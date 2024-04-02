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
        $msg    = '';
        $status = '';
        if ($request->isMethod('post')) {
            $input = $request->all();
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required|min:6|max:100'
            ], $this->getErrorMessages());

           $userInDb = MondayUsers::loginUser(array( 'email' => trim($input['email']), 'password' =>  trim($input['password'])));

           echo '<pre>'; print_r( $userInDb ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
            $userCredential = $request->only('email','password');
            if(Auth::attempt($userCredential)){
                echo '<pre>'; print_r( 'in' ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
                $route = $this->redirectDash();
                return redirect($route);
            }
            else{
                echo '<pre>'; print_r( 'out' ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
                return back()->with('error','Username & Password is incorrect');
            }
            echo '<pre>'; print_r( 'here' ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
        }
        $heading = "Log In";
        $subheading = "TASC Outsourcing KSA";
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
                'phone'        => 'required',
                'email'        => 'required|email',
                'password'     => 'required|min:6|max:100'
            // ]);
            ], $this->getErrorMessages());

            if ($validator->passes()) {
                $dataToSave = array(
                    'name'         => trim($input['name']),
                    'company_name' => trim($input['company_name']),
                    'phone'        => trim($input['phone']),
                    'email'        => trim($input['email']),
                    'password'     => trim($input['password']),
                    'created_at'   => date("Y-m-d H:i:s"),
                    'updated_at'   => date("Y-m-d H:i:s"),
                    // 'password'     => Hash::make(trim($input['password']))
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

    // public function redirectDash()
    // {
    //     $redirect = '';

    //     if(Auth::user() && Auth::user()->role == 1){
    //         $redirect = '/super-admin/dashboard';
    //     }
    //     else if(Auth::user() && Auth::user()->role == 2){
    //         $redirect = '/admin/dashboard';
    //     }
    //     else{
    //         $redirect = '/dashboard';
    //     }

    //     return $redirect;
    // }

    // public function logout(Request $request)
    // {
    //     $request->session()->flush();
    //     Auth::logout();
    //     return redirect('/');
    // }
}
