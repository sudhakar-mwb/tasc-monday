@include('auth.header')


<main class="px-3 pt-5">
    @include('admin.headtitle')

    @if ($status != '')
        <div class="d-flex justify-content-center">
            <div class="alert alert-{{ $status }}" style="max-width:400px"> {{ $msg }}</div>
        </div>
    @endif


    <form class="form-auth" id="registration-custom-form" action="{{ route('monday.post.signup') }}" method="POST">
        {{-- @csrf --}}
        <input type="text" placeholder="Name*" name="name" value="{{ old('name') }}">
        @error('name')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror
        <input type="text" placeholder="Company name*" name="company_name" value="{{ old('company_name') }}">
        @error('company_name')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror
        <input type="text" placeholder="Phone*" name="phone" value="{{ old('phone') }}">
        @error('phone')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror
        <input type="text" placeholder="Email*" name="email" value="{{ old('email') }}">
        @error('email')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror
        @include('includes.passwordinput')
        @error('password')
            <small class="text-danger text-start ms-2">{{ $message }}</small>
        @enderror

        <div class="w-100 d-flex justify-content-center">
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            {{-- secret key:  6LdmFMQpAAAAAJAlWcuFRebEfGgkMs6JsoisI2Lb --}}
            <div class="g-recaptcha" data-sitekey="6LdmFMQpAAAAAGwLfYZopzckKXOu0obCtpHW0obV"></div>

        </div>
        <button class="btn btn-to-link btn-secondary mt-4 btn-gradiant  d-flex align-items-center" type="submit"
            {{-- type="button" --}}>
            <span>
                {{-- <a href="thanks" class="text-decoration-none text-light"> --}}
                Request Access
                {{-- </a> --}}

            </span>

            <span class="icon-btn_track" style="margin-left:10px">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                    <path
                        d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z" />
                </svg>
            </span>
        </button>
    </form>


    <a href="login">Already have an Account?</a>
</main>
<script>
    var form = document.getElementById('registration-custom-form');
    form.addEventListener("submit", function(event) {
        if (grecaptcha.getResponse() === '') {
            event.preventDefault();
            alert('Please check the recaptcha');
        }
    }, false);
</script>
@include('includes.footer')
