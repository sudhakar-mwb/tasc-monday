@include('auth.header')

<main class="pt-3">
    @include('admin.headtitle')
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
    <form action="{{ route('monday.post.login') }}" id="loginform" method="POST" class="form-auth" style="max-width:440px;padding:30px;box-shadow: 0 4px 16px #11111a1a, 0 8px 24px #11111a1a, 0 16px 56px #11111a1a;">
    <div>
       <img src="{{ asset('asset/tasc.svg') }}" alt="No Preview" style="max-width:220px;">
       </div>
       <div style="font-size:24px;font-weight:600;    font-family: Work Sans,sans-serif;">
           Sign In
      </div>
        <input type="text" placeholder="Email" name="email" value="{{ old('email') }}" style="background: #ececec;border: 0;border-radius: 50px;flex-direction: column;gap: 10px;padding: 10px 15px;">
        @error('email')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror
        {{-- <input type="password" placeholder="Password" name="password"> --}}
        {{-- <div class="input-group flex-nowrap" id="password-filled">
            <input class="form-control" id="input-password" type="password" placeholder="Password" name="password">
            <span class="input-group-text fs-5 encrypted" style="cursor:pointer" id="controller"><i
                    class="bi bi-eye-slash-fill"></i></span>
        </div> --}}
        @include('includes.passwordinput')
        @error('password')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror

        <button id="login-button" class="btn btn-to-link btn-secondary mt-4 btn-gradiant  d-flex align-items-center" type="button" style="background: #ececec;border: 0;border-radius: 50px;gap: 10px;padding: 15px;display:flex;align-items:center;justify-content:center;">
            <span>
                Log In
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
            <a href={{ route('monday.forgot') }}>Forgot Password?</a>
            <a href={{ route('monday.get.signup') }}>Create New Account?</a>
        </div>
        <div class="login-footer">
        @include('includes.footer')
        </div>
    </form>
<script>
  $("#login-button").on('click',function(){
    localStorage.removeItem('hide-banner');
    $("#loginform").submit()
  })

</script>
</main>
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
    margin-bottom:1rem !important;
}

</style>


