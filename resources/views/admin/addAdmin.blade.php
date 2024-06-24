@include('includes.header')

<main class="px-3 pt-5">
    {{-- @include('admin.headtitle') --}}
    @include('includes.links', ['active' => 'create-admin'])
    @if ($status != '')
        <div class="d-flex justify-content-center">
            <div class="alert alert-{{ $status }}" style="max-width:400px"> {{ $msg }} </div>
        </div>
    @endif
    <div class="w-100 d-flex flex-column align-items-center p-2">
        <div class="col-md-7 col-lg-8 text-start">
            <h4 class="mb-3 d-flex align-items-center "><i class="bi bi-person-fill-add"></i> &nbsp;<span
                    class="mt-1 ms-2">Add Admin/User</span></h4>
            <hr>
        </div>
        <div class="d-flex flex-column align-items-center mb-5 col-12">

            <form class="text-start" style="width:100%;max-width:400px" action="{{ route('admin.post.storeAdmin') }}"
                method="POST">
                <div class="mb-3">
                    <label for="role-input" class="form-label">Role</label>
                    <select class="form-select" id="role-input" name="role" aria-label="Default select example">
                        <option selected value="2">Admin</option>
                        <option value="1">Super Admin</option>
                        <option value="0">User</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="fullname-input" class="form-label">Full name</label>
                    <input type="text" class="form-control" placeholder="write full name" id="fullname-input"
                        aria-describedby="emailHelp" required name="name" value="{{ old('name') }}">
                    @error('name')
                        <small class="text-danger text-start ms-2">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email-input" class="form-label">Email address</label>
                    <input type="email" class="form-control" placeholder="demo@gmail.com" id="email-input"
                        aria-describedby="emailHelp" required name="email" value="{{ old('email') }}">
                    @error('email')
                        <small class="text-danger text-start ms-2">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password-input" class="form-label">Create Password</label>
                    @include('includes.passwordinput')
                    @error('password')
                        <small class="text-danger text-start ms-2">{{ $message }}</small>
                    @enderror
                </div>

                <!-- New Fields for User Role -->
                <div id="user-fields" style="display: none;">
                    <div class="mb-3">
                        <label for="phone-input" class="form-label">Company Name</label>
                        <input type="text" class="form-control" placeholder="Company name" name="company_name" value="{{ old('company_name') }}">
                        @error('company_name')
                            <small class="text-danger text-start ms-2">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="address-input" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" placeholder="+966 011 XXX XXXX" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <small class="text-danger text-start ms-2">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="d-grid gap-2 mt-5">
                    <button type="submit" class="btn btn-success btn-lg btn-custom">Submit</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</main>
<script>
    document.getElementById('role-input').addEventListener('change', function () {
        var userFields = document.getElementById('user-fields');
        if (this.value == '0') { // User role selected
            userFields.style.display = 'block';
        } else {
            userFields.style.display = 'none';
        }
    });
</script>
@include('includes.footer')
