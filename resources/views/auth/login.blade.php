<!-- <div class="header-login"> -->
@include('auth.login_header')
<!-- </div> -->
<div class="inc-auth-container">
    <div class="container auth-container text-center">
        <div class="cover-container w-100 h-100 pb-2" >
<main class="">
    <div class="login-onboardify-header">
    @include('admin.headtitle')
    </div>
    <?php
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $msg = $_GET['msg'];
    }
    ?>
    @if ($status != '')
        <div class="d-flex justify-content-center">
            <div class="alert alert-{{ $status }}" style="max-width:400px"> {{ $msg }}</div>
        </div>
    @endif
    <div  style="max-width:440px;padding:30px;box-shadow: 0 2px 6px #0003;margin:0 auto;background:#fff;">

    <div>
       <img src="{{ asset('asset/tasc.svg') }}" alt="No Preview" style="max-width:220px;">
       </div>
       <div style="font-size:24px;font-weight:600;font-family: Work Sans,sans-serif;margin-bottom:8px;color:#434343;">
           Sign In
      </div>
      <form action="{{ route('monday.post.login') }}" id="loginform" method="POST" class="form-auth">
        <input type="text" class="form-control" id="email-input-login" placeholder="Email" name="email" value="{{ old('email') }}" style="background: #e8f0fe;border: 0;border-radius: 50px;flex-direction: column;gap: 10px;padding: 10px 15px;">
        @error('email')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror
        {{-- <input type="password" placeholder="Password" name="password"> --}}
        {{-- <div class="input-group flex-nowrap form-control" id="password-filled">
            <input class="form-control" id="input-password" type="password" placeholder="Password" name="password">
            <span class="input-group-text fs-5 encrypted" style="cursor:pointer" id="controller"><i
                    class="bi bi-eye-slash-fill"></i></span>
        </div> --}}
        @include('includes.passwordinput')
        @error('password')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror

        <button id="login-button" class="btn btn-to-link btn-secondary btn-gradiants mt-4 d-flex align-items-center" type="button" style="border: 0;border-radius: 50px;gap: 10px;padding: 15px;display:flex;align-items:center;justify-content:center;
    transition: 0.5s;height:46px;">
            <span style="font-family: Montserrat!important;
    font-size: 12px;font-weight:700;">
                Sign In
            </span>
            <span class="icon-btn_track" style="margin-left:10px">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                    <path
                        d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z" />
                </svg>
            </span>
        </button>
        <div class="d-flex justify-content-between align-items-start w-100 mt-2">
            <a href={{ route('monday.forgot') }} style="font-size:13px;color:#434343;">Forgot Password?</a>
            <a href={{ route('monday.get.signup') }} style="font-size:13px;color:#434343;">Create New Account?</a>
        </div>
        <div class="login-footer" >
        @include('includes.footer')
        </div>
    </form>
        </div>
<script>
  function isEmailValid(email) {
    let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailPattern.test(email);
  }

  function checkInputFields() {
    let email = $("#email-input-login").val().trim();
    let password = $("#input-password").val().trim();
    if (email === '' || password === '' || !isEmailValid(email)) {
        $("#login-button").css('opacity', '0.65');
    } else {
        $("#login-button").css('opacity', '1');
    }
  }

  $("#login-button").on('click', function(){
    localStorage.removeItem('hide-banner');
    let email = $("#email-input-login").val().trim();
    let password = $("#input-password").val().trim();
    if (email !== '' && password !== '' && isEmailValid(email)) {
        $("#loginform").submit();
    }
  });

  $("#email-input-login, #input-password").on('change keyup', function() {
    checkInputFields();
  });

  $(document).ready(function(){
    checkInputFields();
  });

</script>
</main>
</div>
</div>
</div>
<style>
  .animation-container{
    margin: 0px !important;
  }
  header{
    padding: 0px !important
  }
  .input-group-text > i:before{
    vertical-align:middle;
  }
.login-footer > footer > div {
    margin-top:1rem !important;
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
        min-height:100vh;
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
