@include('auth.login_header')


<main class="px-3 pt-5">
<div class="login-onboardify-header">
    @include('admin.headtitle')
    </div>

    @if ($status != '')
        <div class="d-flex justify-content-center">
            <div class="alert alert-{{ $status }}" style="max-width:400px"> {{ $msg }}</div>
        </div>
    @endif
    <form action="{{ route('monday.post.forgot') }}" method="POST" class="form-auth" style="max-width:440px;padding:30px;box-shadow: 0 4px 16px #11111a1a, 0 8px 24px #11111a1a, 0 16px 56px #11111a1a;">
    <div>
       <img src="{{ asset('asset/tasc.svg') }}" alt="No Preview" style="max-width:220px;">
       </div>
       <div style="font-size:24px;font-weight:600;    font-family: Work Sans,sans-serif;">
           Forgot Password
      </div>  
    <div class="w-100">
        <input class="w-100" type="text" placeholder="Email" name="email" value="{{ old('email') ?? ($_GET['email'] ?? '') }}" style="background: #ececec;border: 0;border-radius: 50px;flex-direction: column;gap: 10px;padding: 10px 15px;"
        required />
    @error('email')
        <p class="text-danger text-start pt-1 my-0 ms-2 " style="font-size: .875em">{{ $message }}</small>
    @enderror
       </div>
        <button class="btn btn-to-link btn-secondary  d-flex align-items-center" type="submit" style="background: #ececec;border: 0;border-radius: 50px;gap: 10px;padding: 15px;display:flex;align-items:center;justify-content:center;background-image: linear-gradient( to right, #28dd7a 0%, #185a9d 51%, #45ce43 100% );">
            <span>
                Email me a recovery link
            </span>
            <span class="icon-btn_track" style="margin-left:10px">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                    <path
                        d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z" />
                </svg>
            </span>
        </button>
        <div class="d-flex justify-content-center align-items-start w-100 mt-2">
            <a href="{{ route('monday.get.login') }}">Back to Login?</a>
        </div>
        <div class="login-footer">
        @include('includes.footer')
        </div>
    </form>

</main>

<style>
    .login-footer > footer > div {
    margin-top:1rem !important;
    margin-bottom:1rem !important;
}
.login-onboardify-header{
    margin-top:60px;
 
}

.login-onboardify-header > div > div > h1{
    font-size:50px;
    font-weight:500 !important;
    color:#212529 !important;
}
</style>


