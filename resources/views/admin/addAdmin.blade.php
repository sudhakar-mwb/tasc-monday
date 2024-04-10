@include('includes.header')

<main class="px-3 pt-5">
    {{-- @include('admin.headtitle') --}}
    @include('includes.links', ['active' => 'create-admin'])
    @if ($status != '')
        <div class="d-flex justify-content-center">
            <div class="alert alert-{{ $status }}" style="max-width:400px"> {{ $msg }} </div>
        </div>
    @endif
    <div class="w-100 d-flex flex-column align-items-center">
    <div class="col-md-7 col-lg-8 text-start">
      <h4 class="mb-3 d-flex align-items-center "><i class="bi bi-person-fill-add"></i> &nbsp;<span class="mt-1 ms-2">Add Admin</span></h4>
      <hr>
    </div>
    <div class="d-flex flex-column align-items-center mb-5 col-12">
      
        <form class="text-start" style="width:100%;max-width:400px" action="{{ route('admin.post.storeAdmin') }}"
            method="POST">
            <div class="mb-3">
                <label for="fullname-input" class="form-label">Full name</label>
                <input type="text" class="form-control" id="fullname-input" aria-describedby="emailHelp" required
                    name="name" value="{{ old('name') }}">
                @error('name')
                    <small class="text-danger text-start ms-2">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email-input" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email-input" aria-describedby="emailHelp" required
                    name="email" value="{{ old('email') }}">
                @error('email')
                    <small class="text-danger text-start ms-2">{{ $message }}</small>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password-input" class="form-label">Create Password</label>
                <input type="password" class="form-control" id="password-input" required name="password">
                @error('password')
                    <small class="text-danger text-start ms-2">{{ $message }}</small>
                @enderror
            </div>

            <div class="d-grid gap-2 mt-5">
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</main>
@include('includes.footer')
