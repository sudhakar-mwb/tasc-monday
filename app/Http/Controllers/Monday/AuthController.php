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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use DB;
use Illuminate\Support\Facades\Cache;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\YieldFrom;
use \Mailjet\Resources;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthController extends Controller
{
    use MondayApis;

    public function setSetting(){
      $get_data = SiteSettings::where('id', '=', 1)->first()->toArray()['ui_settings'];
      // Store data in the session
      session(['settings' => json_decode($get_data)]);
    }
    public function login(Request $request)
    {
        $msg        = '';
        $status     = '';
        $heading    = "Log In";
        $subheading = "";
        $this->setSetting();
        if ($request->isMethod('post')) {
            $input = $request->all();
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required|min:6|max:100'
            ], $this->getErrorMessages());

           $userInDb = MondayUsers::loginUser(array( 'email' => trim($input['email']), 'password' =>  trim($input['password'])));
           if ($userInDb['status'] == 'success') {
            $userCredential = $request->only('email','password');
            if(Auth::attempt($userCredential)){
                // JWTAuth
                $token = JWTAuth::attempt([
                    "email" => $request->email,
                    "password" => $request->password
                ]);

                if(!empty($token)){
                    
                    $userData = MondayUsers::getUser(['email' => trim($input['email'])]);
                    $cacheUserDetails = Cache::forever('loginUserDetails', $userData);

                    return response()->json([
                        "status" => true,
                        "message" => "User logged in succcessfully",
                        "token" => $token
                    ]);
                }
                $route = $this->redirectDash();
                return redirect($route);
            }
           }elseif ($userInDb['status'] == 'not_verified'){

            $msg    = "Your email has not been verified yet. Please check your email inbox";
            $status = "danger";
            return view('auth.login',compact('heading','subheading',  'msg', 'status'));
           }elseif ($userInDb['status'] == 'wrong_pass'){

            $msg    = "Email or Password is incorrect.";
            $status = "danger";
            return view('auth.login',compact('heading','subheading',  'msg', 'status'));
           }elseif ($userInDb['status'] == 'not_found'){

            $msg    = "This user not found in database.";
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
        $this->setSetting();
        if ($request->isMethod('post')) {
            $input = $request->all();

            $validator = FacadesValidator::make($request->all(),[
                'name'         => 'required',
                'company_name' => 'required',
                // 'phone'        => 'required|regex:/^[+]{1}(?:[0-9\-\(\)\/\.]\s?){6,15}[0-9]{1}$/',
                'phone'        => 'required|regex:/^\+(?:[0-9] ?){6,14}[0-9]$/',
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
                    'password'     => Hash::make(trim($input['password'])),
                    'board_id'     => 1472085729,
                );
                $insertUserInDB = MondayUsers::createUser($dataToSave);
                if ($insertUserInDB['status'] == "success") {
                    $msg    = "User Created Successfully.";
                    $status = "success";
                    // send verification email
                    $this->sendVerificationEmail($dataToSave);
                    //
                    return $this->thankssignup();
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
            $subheading = "We’re excited to have you join us! To complete your sign-up, please fill in your information below.";
            return view('auth.signup', compact('heading', 'subheading', 'msg', 'status'), );
    }
    public function forgot(Request $request)
    {
        $userDetails = auth()->user();
        $msg        = '';
        $status     = '';
        $heading    = "Forgot Password";
        $subheading = "Please provide the email associated with your account.";
        $this->setSetting();
        if ($request->isMethod('post')) {
            $input = $request->all();
            $this->validate($request, [
                'email' => 'required|email',
            ], $this->getErrorMessages());
            $getUser = MondayUsers::getUser( array( 'email' => trim($input['email']) ) );
            if ($getUser) {
                $dataToEncrypt = array(
                    'email'      => trim($input['email']),
                    // 'current'  => date("Y-m-d H:i:s"),
                    'email_exp'  => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . "+1 hour")),
                    'id' => trim($getUser->id),
                );

                $linkHash        = Crypt::encrypt(json_encode($dataToEncrypt));
                $verificationURL = url('/').'/onboardify/create-password/'.$linkHash;
                $verificationData = array(
                    'emailType'  => 'forget_password_verification',
                    'name'       => $getUser->name,
                    'recipients' => $getUser->email,
                    'email'      => $getUser->email,
                    'link'       => $verificationURL
                );

                $admin_email = env('MAIL_FROM_ADDRESS'); // admin ,mail
                // return view('mail.forget-password', ['mail_data' => $verificationData]);
                // $mail_body   = view('mail.forget-password', ['mail_data' => $verificationData]);
                $get_data   = SiteSettings::where('id', '=', 1)->first()->toArray();
                $logo_image = json_decode($get_data['ui_settings']);
                // return $next($request);
                $mail_body   = '<!DOCTYPE html>
                <html>
                <head>
                    <title>MakeWebBetter | Reset Password</title>
                    <style>
                        /* Inline CSS styles */
                        body {
                            font-family: Arial, sans-serif;
                            font-size: 14px;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            background-color: #F9F9F9;
                            border-radius: 5px;
                        }
                        .logo {
                            text-align: center;
                        }
                        .logo img {
                            width: 100px;
                        }
                        .message {
                            margin-top: 20px;
                            margin-bottom: 20px;
                        }
                        .button {
                            display: inline-block;
                            padding: 10px 20px;
                            background-color: #007BFF;
                            color: #fff;
                            text-decoration: none;
                            border-radius: 5px;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="logo" style="width: 100%; justify-content:center">
                            <img src="https://onboardify.tasc360.com/uploads/onboardify.png" alt="TASC Logo">
                        </div>
                        <div class="message">
                            <p>Hello ' . $verificationData['name'] . ',</p>
                            <p>We received a request to reset your password. If you did not make this request, please ignore this email.</p>
                            <p>To reset your password, click the button below:</p>
                            <p><a style="color:#ffff;" href="' .$verificationData['link']  . '" class="button">Reset Password</a></p>
                            <p>If you cannot click the button, please copy and paste the following URL into your browser:</p>
                            <p> ' .$verificationData['link']. ' </p>
                            <p>This link will expire in 1 hr for security reasons.</p>
                            <p>If you have any questions, please contact us at KSAAutomation@tascoutsourcing.com</p>
                        </div>
                    </div>
                </body>
                </html>';
                try
                {
                    // $a = Mail::html( $mail_body, function( $mailMsg ) use ($admin_email,$verificationData) {
                    //     $mailMsg->to( trim($verificationData['email']) );
                    //     $mailMsg->from( $admin_email );
                    //     $mailMsg->subject("Reset Password" );
                    // });

                    // $response = DB::table('monday_users')->where('id', $getUser->id)->update(['email_exp' => $dataToEncrypt['email_exp']]);
                    // $msg    = 'Success, Verification Mail Sent.';
                    // $status = 'success';
                    // return view('auth.forgot', compact('heading', 'subheading', 'msg', 'status'), );

                    // $mj = new \Mailjet\Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3.1']);
                    // $body = [
                    //     'Messages' => [
                    //         [
                    //             'From' => [
                    //                 'Email' => "noreply@tasc360.com",
                    //                 'Name'  => "TASC"
                    //             ],
                    //             'To' => [
                    //                 [
                    //                     'Email' => $verificationData['email'],
                    //                     'Name'  => $verificationData['name'],
                    //                 ]
                    //             ],
                    //             'Subject'  => "Reset Password",
                    //             'TextPart' => "Greetings from Mailjet!",
                    //             'HTMLPart' => $mail_body
                    //         ]
                    //     ]
                    // ];

                    // $response = $mj->post(Resources::$Email, ['body' => $body]);

                    // if ($response->getData()['StatusCode'] == 200) {
                    //     $response = DB::table('monday_users')->where('id', $getUser->id)->update(['email_exp' => $dataToEncrypt['email_exp']]);
                    //     $msg    = 'Success, Verification Mail Sent.';
                    //     $status = 'success';
                    //     return view('auth.forgot', compact('heading', 'subheading', 'msg', 'status'), );
                    // }else{
                    //     $msg    = 'Forgot Password mail not send. Sinch mailjet response -> '.$response->getData()['ErrorMessage'];
                    //     $status = 'danger';
                    //     if (!is_null($userDetails) && ($userDetails->role == 1 || $userDetails->role == 2) ) {
                    //         return view('auth.forgotForAdmin', compact('heading', 'subheading', 'msg', 'status'), );
                    //     }
                    //     return view('auth.forgot', compact('heading', 'subheading', 'msg', 'status'), );
                    // }

                    $email   = "KSAAutomation@tascoutsourcing.com";
                    $body    =  $mail_body;
                    $subject = "Reset Password";

                    $data = array(
                        "personalizations" => array(
                            array(
                                "to" =>array(
                                    array(

                                        "email" => $verificationData['email'],
                                        "name"  => $verificationData['name']
                                    )
                                )
                            )
                        ),

                        "from" => array(
                            "email"=> $email
                        ),

                        "subject" =>$subject,
                        "content" =>array(
                            array(
                                "type" => "text/html",
                                "value" => $body
                            )
                        )
                    );

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.sendgrid.com/v3/mail/send',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($data),
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . env('SENDGRID_API_KEY'),
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);

                    if ($err) {
                        $msg    = 'Forgot Password mail not send. Please check sendgrid activity log.';
                        $status = 'danger';
                        if (!is_null($userDetails) && ($userDetails->role == 1 || $userDetails->role == 2) ) {
                            return view('auth.forgotForAdmin', compact('heading', 'subheading', 'msg', 'status'), );
                        }
                        return view('auth.forgot', compact('heading', 'subheading', 'msg', 'status'), );
                    } else {
                        $response = DB::table('monday_users')->where('id', $getUser->id)->update(['email_exp' => $dataToEncrypt['email_exp']]);
                        $msg    = 'Success, Verification Mail Sent.';
                        $status = 'success';
                        if (!is_null($userDetails) && ($userDetails->role == 1 || $userDetails->role == 2) ) {
                            return view('auth.forgotForAdmin', compact('heading', 'subheading', 'msg', 'status'), );
                        }
                        return view('auth.forgot', compact('heading', 'subheading', 'msg', 'status'), );
                    }
                }
                catch(\Exception $e)
                {
                    $msg    = 'Something went wrong during mail send. Please try again.';
                    $status = 'danger';
                    return view('auth.forgot', compact('heading', 'subheading', 'msg', 'status'), );
                }
            }else{
                $msg    = 'Invalid Email.';
                $status = 'danger';
                return view('auth.forgot', compact('heading', 'subheading', 'msg', 'status'), );
            }
        }
        if (!is_null($userDetails) && ($userDetails->role == 1 || $userDetails->role == 2) ) {
            return view('auth.forgotForAdmin', compact('heading', 'subheading', 'msg', 'status'), );
        }
        return view('auth.forgot', compact('heading', 'subheading', 'msg', 'status'), );
    }
    public function thankssignup()
    {
        $heading = "Verify Your Email Address";
        $subheading = "Just verify your email address to confirm that you want to use this email for your <br> TASC 360 account.";
        $status=true;
        $this->setSetting();
        return view('auth.thankssignup', compact('heading', 'subheading','status'));
    }

    function getErrorMessages() {
        return [
            "required" => ":attribute is required.",
            "max"   => ":attribute should not be more then :max characters.",
            "min"   => ":attribute should not be less then :min characters.",
            "regex" => "please enter phone number input field with + country code",
        ];
    }

    public function redirectDash()
    {
        $redirect = '';

        // Super Admin
        if(Auth::user() && Auth::user()->role == 1){
            $redirect = 'onboardify/admin/users';
        }
        // Admin
        else if(Auth::user() && Auth::user()->role == 2){
            $redirect = 'onboardify/admin/users';
        }// User
        else{
            $redirect = '/onboardify/form';
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
        return redirect('/');
    }

    // public function createNewPassword (Request $request){
    //     $input =  $request->all();
    //     echo '<pre>'; print_r( $input ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
    //     $this->validate($request, [
    //         'token' => 'required',
    //         'password' => 'required|min:6|max:100'
    //     ], $this->getErrorMessages());
    //     $token = trim( $input['token'] );
    //     $password = trim( $input['password'] );
    //     try {
    //         $decryptedData = Crypt::decrypt($token);
    //         $decryptedData = json_decode($decryptedData, true);
    //         $email = $decryptedData['email'];
    //         $getUser = User::getUser( array( 'email' => $email ) );
    //         if ($getUser) {
    //             $dataToUpdate = array(
    //                 'password' => Hash::make($password),
    //                 'status' => '1',
    //                 'updated_at' => date("Y-m-d H:i:s")
    //             );
    //             $updatePassword = User::setUser( array( 'id' => $getUser->id ), $dataToUpdate );
    //             if ($updatePassword) {
    //                 return response( json_encode( array( 'response' => true, 'status' => true, 'message' => "Password Created Successfully." ) ) );
    //             }
    //             return response( json_encode( array( 'response' => true, 'status' => false, 'message' => "Some error occured." ) ) );
    //         }
    //         return response( json_encode( array( 'response' => true, 'status' => false, 'message' => "Invalid User." ) ) );
    //     } catch (\Throwable $th) {
    //         return response( json_encode( array( 'response' => true, 'status' => false, 'message' => "Invalid Token." ) ) );
    //     }
    //     echo '<pre>'; print_r( 'in' ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
    // }

    public function createNewPassword (Request $request){
        $heading       = "Enter New Password";
        $decryptedData = Crypt::decrypt($request->token);
        $decryptedData = json_decode($decryptedData, true);
        $this->setSetting();
        if (!empty($decryptedData)) {
            $getUser = MondayUsers::getUser( array( 'email' => trim($decryptedData['email']) ) );

            if ( date("Y-m-d H:i:s") <= $decryptedData['email_exp'] && !empty($getUser->email_exp)) {
                if (!empty($decryptedData['email'])) {
                    $subheading = 'for '.$decryptedData['email'];
                }
                $msg    = '';
                $status = '';
                $token  = $request->token;
                return view('auth.create_password', compact('heading', 'subheading', 'msg', 'status', 'token'));
            }else{
                $heading    = "Forgot Password";
                $subheading = "Please provide the email associated with your account.";
                $msg    = 'Your forgot password link is expired. Please re-create a new link';
                $status = 'danger';
                return view('auth.forgot', compact('heading', 'subheading', 'msg', 'status'), );
            }
        }
    }

    public function createNewPasswordPost (Request $request){
      $this->setSetting();
        $this->validate($request, [
            'password' => 'required|min:6|max:100',
            'conf_password' => 'required|min:6|max:100',
        ], $this->getErrorMessages());
        $decryptedData = Crypt::decrypt($request->token);
        $decryptedData = json_decode($decryptedData, true);
        if (trim($request->password) !== trim($request->conf_password)) {
            $heading       = "Enter New Password";
            $subheading    = 'for '.$decryptedData['email'];
            $msg           = 'current password and confirm new password not matched';
            $status        = 'danger';
            $token         =  $request->token;
            return view('auth.create_password', compact('heading', 'subheading', 'msg', 'status', 'token'));
        }elseif (trim($request->password) == trim($request->conf_password)) {
            $getUser = MondayUsers::getUser( array( 'email' => $decryptedData['email'] ) );
            $dataToUpdate = array(
                'password'   => Hash::make($request->password),
                'email_exp'  => Null,
                'updated_at' => date("Y-m-d H:i:s")
            );
            $updatePassword = MondayUsers::setUser( array( 'id' => $getUser->id ), $dataToUpdate );
            if ($updatePassword) {
                $heading       = "Login";
                $subheading    = 'for '.$decryptedData['email'];
                $msg           = 'Password updated successfully. Now you can login with new password';
                $status        = 'success';
                return redirect('/');
            }else{
              $heading       = "Enter New Password";
              $subheading    = 'for '.$decryptedData['email'];
              $msg           = 'Current password not updated. Please try again.';
              $status        = 'danger';
              $token         =  $request->token;
              return redirect('onboardify/create-password'.$request->token,compact('heading', 'subheading', 'msg', 'status', 'token'));
            }
            // return view('auth.login', compact('heading', 'subheading', 'msg', 'status', 'token'));
        }
    }

    public function test(){
      return $this->getSiteSettings();
    }

    public function sendVerificationEmail ($userData){

        $dataToEncrypt = array(
            'email' => trim($userData['email']),
            'name'  => trim($userData['name']),
        );

        $linkHash         = Crypt::encrypt(json_encode($dataToEncrypt));
        $verificationURL  = url('/').'/onboardify/verify/'.$linkHash;
        $verificationData = array(
            'name'       => trim($userData['name']),
            'email'      => trim($userData['email']),
            'link'       => $verificationURL
        );

        $get_data   = SiteSettings::where('id', '=', 1)->first()->toArray();
        $logo_image = json_decode($get_data['ui_settings']);

        $mail_body   = '<!DOCTYPE html>
        <html>
        <head>
            <title>MakeWebBetter | Reset Password</title>
            <style>
                /* Inline CSS styles */
                body {
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #F9F9F9;
                    border-radius: 5px;
                }
                .logo {
                    text-align: center;
                }
                .logo img {
                    width: 100px;
                }
                .message {
                    margin-top: 20px;
                    margin-bottom: 20px;
                }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #007BFF;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo" style="width: 100%; justify-content:center">
                    <img src="https://onboardify.tasc360.com/uploads/onboardify.png" alt="TASC Logo">
                </div>
                <div class="message">
                    <p>Hello ' . $verificationData['name'] . ',</p>
                    <p>We received a request to verify your account. If you did not make this request, please ignore this email.</p>
                    <p>To verify your account, click the button below:</p>
                    <p><a style="color:#ffff;" href="' .$verificationData['link']  . '" class="button">Verify Account</a></p>
                    <p>If you cannot click the button, please copy and paste the following URL into your browser:</p>
                    <p> ' .$verificationData['link']. ' </p>
                    <p>If you have any questions, please contact us at KSAAutomation@tascoutsourcing.com</p>
                </div>
            </div>
        </body>
        </html>';
        try
        {
            $email   = "KSAAutomation@tascoutsourcing.com";
            $body    =  $mail_body;
            $subject = "Verify Account";

            $data = array(
                "personalizations" => array(
                    array(
                        "to" =>array(
                            array(

                                "email" => $verificationData['email'],
                                "name"  => $verificationData['name']
                            )
                        )
                    )
                ),

                "from" => array(
                    "email"=> $email
                ),

                "subject" =>$subject,
                "content" =>array(
                    array(
                        "type" => "text/html",
                        "value" => $body
                    )
                )
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.sendgrid.com/v3/mail/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('SENDGRID_API_KEY'),
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            // return true;
        }
        catch(\Exception $e)
        {
            // return true;
        }
    }

    public function verify (Request $request){

        if (!empty($request->token)) {
            $decryptedData = Crypt::decrypt($request->token);
            $decryptedData = json_decode($decryptedData, true);
            if (!empty($decryptedData['email'])) {
                $userInDb = MondayUsers::getUser(array( 'email' => $decryptedData['email'] ));
                if ($userInDb) {
                    if ($userInDb->status != '1') {
                        $dataToUpdate = array(
                            'status'     => '1',
                            'updated_at' => date("Y-m-d H:i:s")
                        );
                        $params = array(
                            'id' => $userInDb->id
                        );
                        $updateRes = MondayUsers::setUser( $params, $dataToUpdate );
                        if ($updateRes) {
                            $msg    = "Thank you, Your account verify successfully.";
                            $status = "success";
                            return redirect()->route('monday.get.login',compact('msg', 'status'));
                        }else{
                            $msg    = "Thank you, Your account has already been verified.";
                            $status = "success";
                            return redirect()->route('monday.get.login',compact('msg', 'status'));
                        }
                    }elseif($userInDb->status = '1'){
                        $msg    = "Thank you, Your Account is already verified.";
                        $status = "success";
                        return redirect()->route('monday.get.login',compact('msg', 'status'));
                    }
                }else {
                    $msg    = "This user ".$decryptedData['email']." record does not exist in our database.";
                    $status = "danger";
                    return redirect()->route('monday.get.login',compact('msg', 'status'));
                }
            }else{
                $msg    = "Something went wrong. Your account verification link is not valid.";
                $status = "danger";
                return redirect()->route('monday.get.login',compact('msg', 'status'));
            }
        }else{
            $msg    = "Your account verification link is not exist.";
            $status = "danger";
            return redirect()->route('monday.get.login',compact('msg', 'status'));
        }
    }

    public function loginUserDetails()
    {
        // Retrieve user details from the cache
        $userDetails = Cache::get('loginUserDetails');

        // Check if user details exist
        if ($userDetails) {

            $userDetails = json_decode(json_encode($userDetails), true);
            
            // Return success response with user details
            return response()->json([
                'success' => true,
                'data' => [
                    'email' => $userDetails['email'],
                    'name' => $userDetails['name'],
                ],
                'message' => 'User details retrieved successfully.'
            ]);
        } else {
            // Return failure response indicating user not found
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }
    }
}
