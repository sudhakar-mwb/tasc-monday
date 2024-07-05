@include('auth.login_header')
<div class="inc-auth-container">
<div class="container auth-container text-center">
<main class="">
    <div class="login-onboardify-header">
        @include('admin.headtitle')
    </div>

    @if ($status != '')
        <div class="d-flex justify-content-center">
            <div class="alert alert-{{ $status }}" style="max-width:400px"> {{ $msg }}</div>
        </div>
    @endif
    <div style="box-shadow: 0 2px 6px #0003;background:#fff;max-width:440px;padding:30px;margin:auto;">
        <div>
            <img src="{{ asset('asset/tasc.svg') }}" alt="No Preview" style="max-width:220px;">
        </div>
        <div style="font-size:24px;font-weight:600;font-family: Work Sans,sans-serif;margin-bottom:16px;color:#434343;">
            Sign Up
        </div> 

    <form class="form-auth" id="registration-custom-form" action="{{ route('monday.post.signup') }}" method="POST">
         
        {{-- @csrf --}}
        <input type="text" class="form-control" placeholder="Name*" name="name" value="{{ old('name') }}" style="background: #e8f0fe;border: 0;border-radius: 50px;flex-direction: column;gap: 10px;padding: 10px 15px;">
        @error('name')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror
        <input type="text" placeholder="Company name*" class="form-control" name="company_name" value="{{ old('company_name') }}" style="background: #e8f0fe;border: 0;border-radius: 50px;flex-direction: column;gap: 10px;padding: 10px 15px;">
        @error('company_name')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror
        <input type="text" placeholder="+966 011 XXX XXXX" class="form-control" name="phone" value="{{ old('phone') }}" style="background: #e8f0fe;border: 0;border-radius: 50px;flex-direction: column;gap: 10px;padding: 10px 15px;">
        @error('phone')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror
        <input type="text" placeholder="Email*" class="form-control" name="email" value="{{ old('email') }}" style="background: #e8f0fe;border: 0;border-radius: 50px;flex-direction: column;gap: 10px;padding: 10px 15px;">
        @error('email')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror
        @include('includes.passwordinput')
        @error('password')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror

        <div class="w-100 d-flex justify-content-center">
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <div class="g-recaptcha" data-sitekey="6LdmFMQpAAAAAGwLfYZopzckKXOu0obCtpHW0obV" data-callback="recaptchaCallback"></div>
        </div>
        <button id="signup-button" class="btn btn-to-link btn-gradiants btn-secondary mt-4 d-flex align-items-center" type="submit" style="height:47px;border: 0;border-radius: 50px;gap: 10px;padding: 15px;display:flex;align-items:center;justify-content:center;">
            <span style="font-family: Montserrat!important; font-size: 12px;font-weight:700;">
                Sign Up
            </span>
            <span class="icon-btn_track" style="margin-left:10px">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z"/>
                </svg>
            </span>
        </button>
        <a href="/" style="font-size:13px;margin-top:9px;color:#434343;">Already have an Account?</a>
        <div class="login-footer">
            @include('includes.footer')
        </div>
    </form>
</div>
</main>
</div>
<div>

<script>
    var form = document.getElementById('registration-custom-form');
    var inputs = form.querySelectorAll('input');
    var button = document.getElementById('signup-button');
    var recaptchaResponse = '';

    function checkInputs() {
        let allFilled = true;
        inputs.forEach(function(input) {
            if (input.value === '') {
                allFilled = false;
            }
        });
        if (allFilled && recaptchaResponse !== '') {
            button.style.opacity = '1';
            button.disabled = false;
        } else {
            button.style.opacity = '0.65';
            button.disabled = true;
        }
    }

    inputs.forEach(function(input) {
        input.addEventListener('input', checkInputs);
    });

    form.addEventListener("submit", function(event) {
        if (recaptchaResponse === '') {
            event.preventDefault();
            alert('Please check the reCAPTCHA');
        }
    }, false);

    function recaptchaCallback(response) {
        recaptchaResponse = response;
        checkInputs();
    }

    // Initial check
    checkInputs();
</script>

<style>
    .animation-container{
        margin: 0px !important;
    }
    .input-group-text > i:before{
        vertical-align:middle;
    }
    .login-footer > footer > div {
        margin-top:0rem !important;
        margin-bottom:0rem !important;
    }
    .login-footer > footer > div > small {
        font-size:13px !important;
        width:380px !important;
        font-family: "Work Sans", sans-serif !important;
        color:#808080;
        }
    .login-onboardify-header{
        /* margin-top:20px; */
    }
    .login-onboardify-header > div > div > h1{
        font-size:50px;
        font-weight:500 !important;
        color:#212529 !important;
       

    }
    .login-cover-container{
        background-image: url({{ asset('/authbg.svg') }});
        background-size: 100%;
    }
    .form-auth{
        padding:0px;
    }

    .inc-auth-container{
        display:flex;
        align-items:center;
        justify-content:center;
        height:100vh;
    }
    .auth-container {
        /* min-height: 90vh; */
    }
    .text-center {
        text-align: center!important;
    }
    .pb-2 {
        padding-bottom: 0.5rem!important;
    } 
    .h-100 {
        height: 100%!important;
    }
    .w-100 {
        width: 100%!important;
    }
</style>
