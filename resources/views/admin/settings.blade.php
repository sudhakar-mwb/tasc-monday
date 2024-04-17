@include('includes.header')

<main class="px-3 pt-5">
    {{-- @include('admin.headtitle') --}}
    <?php   $settings=Session::get('settings'); ?>
    @include('includes.links',['active'=>'settings'])
    @if ($status != '')
        <div class="d-flex justify-content-center">
            <div class="alert alert-{{ $status }}" style="max-width:400px"> {{ $msg }} </div>
        </div>
    @endif
    <div class="w-100 d-flex flex-column align-items-center">
        <div class="col-md-7 col-lg-8 text-start">
            <h4 class="mb-3"><i class="bi bi-gear-fill"></i><span class="mt-1 ms-2">General Settings</h4>
            <hr>
            <form class="needs-validation"  id="general_settings_form" action="{{route('admin.post.settings')}}" method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="col-sm-12 mb-5">
                            <label for="site_bg" class="form-label">Background-color<i class="bi bi-pen"></i></label><br>
                            <input type="color" class="w-100" name="site_bg" id="site_bg" name="head" value="{{ $settings->site_bg }}"  required/>
                            @error('head')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-sm-12">
                            <label for="button_bg" class="form-label">Button background-color&nbsp;<i class="bi bi-pen"></i></label><br>
                            <input type="color" class="w-100" id="button_bg" name="button_bg" value="{{ $settings->button_bg }}"  required/>
                            @error('button_bg')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
                        </div>
                    </div>

                    <div class="col-sm-6 ">
                        <div class="">
                            <label for="logo_image" class="form-label">Choose Logo Image&nbsp;<i class="bi bi-pen"></i></label>
                            <input class="form-control" name="logo_image"  type="file" id="logo_image" value="{{ old('logo_image') }}">
                            @error('logo_image')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
                            <div id="imageContainer" class="card  mt-2"
                            style="max-width:200px;max-height:200px;width:150px;min-height:90px">
                            {{-- <img height="100" src="{{ asset('uploads/' . $settings->logo_image) }}" alt="TASC logo"> --}}
                          </div>
                        </div>
                    </div>

                    <div class="col-12 row mt-3">
                        <div class="col-sm-6">
                            <label for="banner_bg" class="form-label">Banner background color&nbsp;<i class="bi bi-pen"></i></label><br>
                            <input type="color" class="w-100" id="banner_bg" name="banner_bg"  value="{{ $settings->banner_bg }}" required/>
                            @error('banner_bg')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-sm-6">
                          <label for="header_bg" class="form-label">Header Backlground Color&nbsp;<i class="bi bi-pen"></i></label><br>
                          <input type="color" class="w-100" id=header_bg" name="header_bg"  value="{{ $settings->header_bg??null }}" required/>
                          @error('header_bg')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
                      </div>
                    </div>
<div class="col-12">
  <div class="col-sm-12">
    <label for="head_title_color" class="form-label">Heading Title Color&nbsp;<i class="bi bi-pen"></i></label><br>
    <input type="color" class="w-100" id=head_title_color" name="head_title_color"  value="{{ $settings->head_title_color??null }}" required/>
    @error('head_title_color')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
</div>
</div>
                    <div class="col-12">
                        <label for="banner_content" class="form-label">Banner <span class="text-muted">(Text)</span></label>
                        <textarea type="text" class="form-control" name="banner_content" id="banner_content" placeholder="Enter the banner text content ."
               >{{  $settings->banner_content }}</textarea>
                        @error('banner_content')<small class="text-danger text-start ms-2">{{ $message }}</small>@enderror
                    </div>
                <hr class="my-4">

                <button class="w-100 btn btn-primary btn-lg btn-custom" type="submit">SAVE SETTINGS</button>
            </form>
        </div>
    </div>
<style>
  .form-label{
cursor: pointer;
  }
  #imageContainer img {
        max-height: 170px; /* Adjust the maximum height as needed */
    }
</style>
<script>
    const base_url = "{{ url('/') }}/";
  $('#logo_image').on('change',function(e){
    var file = $(this)[0].files[0];       
        if (file) {   
        var reader = new FileReader();
            reader.onload = function(event) {
                var blobUrl = URL.createObjectURL(file);
                console.log(  e.target.result);
                $('#imageContainer').html('<img src="' + blobUrl + '" alt="Selected Image">');
            };
            reader.readAsDataURL(file);          
  }})
//   $(document).ready(function () {
// 		$("#general_settings_form").submit(async function (e) {
// 			e.preventDefault();
// 			var formData = {};
// 			$(this)
// 				.find("input, textarea")
// 				.each(function () {
// 					formData[$(this).attr("name")] = $(this)
// 						.val()
// 				});
// console.log({formData})
// 			// showLoader();
// 			try {
// 				const response = await fetch(
// 					base_url + "monday/admin/colour-mapping/",
// 					{
// 						method: "POST",
// 						headers: {
// 							"Content-Type": "application/json",
// 						},
// 						body: JSON.stringify(formData),
// 					}
// 				);

// 				if (!response.ok) throw new Error("HTTP status " + response.status);
// 				alert("status group saved");
// 			} catch (error) {
// 				console.log("Api error", error);
// 				alert("Something wents wrong.");
// 			}

// 			// hideLoader();
// 		});
// 	});




</script>
</main>
@include('includes.footer')
