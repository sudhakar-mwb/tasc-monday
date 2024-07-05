@include('auth.login_header')
<?php 
// dd($token);
?>
<div class="inc-auth-container">
    <div class="container auth-container text-center">
        <div class="cover-container w-100 h-100 pb-2" >
            <main class="">
                <div class="login-onboardify-header">
                    @include('admin.headtitle')
                </div>
                @if($status!="")
                    <div class="d-flex justify-content-center">
                        <div class="alert alert-{{$status}}" style="max-width:400px">
                            {{$msg}}
                        </div>
                    </div>
                @endif
                <form action="{{route('monday.createNewPasswordPost',['token'=>$token])}}" method="POST" class="form-auth" style="max-width:440px;padding:30px;box-shadow: 0 4px 16px #11111a1a, 0 8px 24px #11111a1a, 0 16px 56px #11111a1a;">
                    <div>
                        <img src="{{ asset('asset/tasc.svg') }}" alt="No Preview" style="max-width:220px;">
                    </div>
                    <div style="font-size:24px;font-weight:600; font-family: Work Sans,sans-serif;">
                        Change Password
                    </div>
                    <input type="text" name="token" hidden value="{{$token}}">
                    <input type="password" id="password" class="form-control" placeholder="Password" name="password" value="{{ old('password') }}" style="background: #ececec;border: 0;border-radius: 50px;flex-direction: column;gap: 10px;padding: 10px 15px;">
                    @error('password')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
                    <input type="password" id="conf_password" class="form-control" placeholder="Confirm Password" name="conf_password" style="background: #ececec;border: 0;border-radius: 50px;flex-direction: column;gap: 10px;padding: 10px 15px;">
                    @error('password')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
                    <button id="change-password-button" class="btn btn-gradiants btn-to-link btn-secondary mt-4 d-flex align-items-center" style="border: 0;border-radius: 50px;gap: 10px;padding: 15px;display:flex;align-items:center;justify-content:center;" type="submit" disabled>
                        <span style="font-family: Montserrat!important; font-size: 12px;font-weight:700;">
                            Change Password
                        </span>
                        <span class="icon-btn_track" style="margin-left:10px">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                                <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z" />
                            </svg>
                        </span>
                    </button>
                    <div class="login-footer">
                        @include('includes.footer')
                    </div>
                </form>
            </main>
        </div>
    </div>
</div>

<style>
.animation-container {
    margin: 0px !important;
}
header {
    padding: 0px !important
}
.input-group-text > i:before {
    vertical-align: middle;
}
.login-footer > footer > div {
    margin-top: 1rem !important;
    margin-bottom: 0rem !important;
}
.login-footer > footer > div > small {
    font-size: 13px !important;
    width: 380px !important;
    font-family: "Work Sans", sans-serif !important;
    color: #808080;
}
.login-onboardify-header {
    /* margin-top: 20px; */
}
.login-onboardify-header > div > div > h1 {
    font-size: 50px;
    font-weight: 500 !important;
    color: #212529 !important;
}
.login-cover-container {
    background-image: url({{ asset('/authbg.svg') }});
    background-size: 100%;
}
.form-auth {
    padding: 0px;
}
.inc-auth-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
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
.btn-gradiants {
	background-image: linear-gradient( to right, #28dd7a 0%, #185a9d 51%, #45ce43 100% );
    transition: 0.5s;
    background-size: 200% auto;
    color: white;
    border: none;
    font-weight: 700;
    box-shadow: 0 0 20px #eee;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 14px;
    min-width: 110px;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const password = document.getElementById('password');
    const confPassword = document.getElementById('conf_password');
    const changePasswordButton = document.getElementById('change-password-button');

    function checkInputs() {
        if (password.value === '' || confPassword.value === '') {
            changePasswordButton.style.opacity = 0.65;
            changePasswordButton.disabled = true;
        } else {
            changePasswordButton.style.opacity = 1;
            changePasswordButton.disabled = false;
        }
    }

    password.addEventListener('input', checkInputs);
    confPassword.addEventListener('input', checkInputs);
});
</script>
