
@include('auth.header')
<?php 
// dd($token);
?>
        <main class="px-3 pt-5">
            @include('admin.headtitle')
            @if($status!="")
                <div class="d-flex justify-content-center">
                    <div class="alert alert-{{$status}}" style="max-width:400px">    {{$msg}}</div>
                </div>
            @endif
            <form action="{{route('monday.createNewPasswordPost',['token'=>$token])}}" method="POST" class="form-auth">
                {{-- <input type="email" disabled placeholder="Email" name="email" value="{{ old('email') }}"> --}}
                {{-- @error('email')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror --}}
                <input type="text" name="token" hidden value="{{$token}}">
                <input type="password" placeholder="Password" name="password" value="{{ old('password') }}">
                @error('password')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
                <input type="password" placeholder="Confirm Password" name="conf_password">
                @error('password')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
                <button class="btn btn-to-link btn-secondary mt-4 btn-gradiant  d-flex align-items-center"
                    type="submit">
                    <span>
                        Change Password
                    </span>
                    <span class="icon-btn_track" style="margin-left:10px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                            <path
                                d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0M4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5z" />
                        </svg>
                    </span>
                </button>
                {{-- <div class="d-flex justify-content-between align-items-start w-100 mt-2">
                    <a href="forgot">Forgot Password?</a>
                    <a href="signup">Create New Account?</a>
                </div> --}}
            </form>
   
        </main>

        @include('includes.footer')
