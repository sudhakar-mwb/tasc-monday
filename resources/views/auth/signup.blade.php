@include('auth.header')


<main class="px-3 pt-5">
    @include('admin.headtitle')
    <form action="thanks" class="form-auth" id="registration-custom-form">
        <input type="text" placeholder="Name*">
        <input type="text" placeholder="Company name*">
        <input type="text" placeholder="Phone*">
        <input type="text" placeholder="Email*">
        <input type="text" placeholder="Password*">
        <div class="w-100 d-flex justify-content-center">
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            {{-- secret key:  6LchU6gpAAAAAAgx4pH2xz9R0VYoDn-c-T4RQri7 --}}
            <div class="g-recaptcha" data-sitekey="6LchU6gpAAAAAPFn3HHkKDNlOJ_rn7CdX7y6b2XR"></div>

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
