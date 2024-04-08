@include('includes.header')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>

<main class="px-3 pt-5 ">
    @include('admin.headtitle')
    <div class="d-flex w-100 justify-content-center" style="gap:20px">

        <div title="coloumn visibility form" style="max-width:550px;width:100%;">
            <div class="bg-success text-white">
                <p class="p-2 m-0 fs-5"><strong>Board Wise Manage Columns </strong></p>
            </div>
            <div class="form_wrapper border border-success p-4 primary-shadow">
                <form id="column_view_form" class="text-start ">
                    <div class="mb-3" id="form-step-1">
                        <label for="exampleInputEmail1" class="form-label">Select Board</label>
                        <select id="input-board-select" class="form-select" id="exampleInputEmail1"
                            aria-label="Default select example"  >
                            <option value="">-- Select  Board --</option>
                            @for ($j = 0; $j < count($boards); $j++)
                            <option value="{{ $boards[$j]['id'] }}">{{ $boards[$j]['name'] }}</option>
                        @endfor
                        </select>
                    </div>

                    <div class="hiddenstep" class="" id="form-step-2">
                      <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">User Subheadings</label>
                        <select id="sub_headings" class="js-example-basic-multiple w-100"
                            name="subHeadings[]" multiple="multiple" style="max-width:500px" >

                        </select>
                        {{-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> --}}
                    </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Onboarding Status</label>
                            <select id="onboarding_columns" class="js-example-basic-multiple w-100"
                                name="onboarding_coulumns[]" multiple="multiple" style="max-width:500px" >

                            </select>
                            {{-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> --}}
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Candidate Information</label>
                            <select id="candidate_columns" class="js-example-basic-multiple w-100"
                                name="candidate_columns[]" multiple="multiple" style="max-width:500px" >

                            </select>
                        </div>
                        <div class="mb-3" id="icon_inputs-wrapper">
                            <label for="exampleInputPassword1" class="form-label">Provide candidate column details : </label>
                          
                            <a href="https://icons.getbootstrap.com/" target="_blank">Go to icons librery&nbsp;<i class="bi bi-box-arrow-up-right"></i></a>  
                             <ol id="icon_inputs">
                             </ol>
                        </div>
                      
                        
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Onboarding Updates</label>
                        <select id="onboarding-updates-option" class="form-select" aria-label="Default select example"  >
                        </select>
                    </div>

                    <h5 class="text-secondary mt-5 mb-3 pb-2 border-bottom">Card Columns:</h5>
                    <div class="mb-3">
                      <label for="exampleInputEmail1" class="form-label">Top Details column</label>
                      <select id="card-column-1" class="form-select" aria-label="Default select example"  >
                      </select>
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Mid Heading column</label>
                    <select id="card-column-2" class="form-select" aria-label="Default select example"  >
                    </select>
                </div>
                    </div>

                    <div class="d-grid gap-2 mt-5"> <button id="columns_details_submit" type="submit"
                            class="btn btn-success btn-lg">Submit</button>
                    </div>

                </form>
            </div>
        </div>

        <div title="status visibility manage" style="max-width:550px;width:100%;">
        
          <div class="bg-success text-white">
              <p class="p-2 m-0 fs-5"><strong>Manage Status Background</strong></p>
          </div>
          <div class="form_wrapper border border-success p-4 primary-shadow">
              <form id="status_view_form" class="text-start">
           
                   @foreach($coloursData as $key => $value)
                   <?php $val= implode(", ", $value) ?>
                   <div class="mb-3" >
                    <h5 class="p-2 " style="border-radius:20%;border-left:10px solid {{ strtolower($key );}};">Color: {{ $key}}</h3>
                    <ul style="list-style: none" class="color-section">
                      <li>
                        <div class="form-floating">
                        <textarea  current_color="{{ $key  }}" value="{{ $val }}" class="form-control color-input" placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px"></textarea>
                        <label for="floatingTextarea2">Write Status</label>
                      </div>
                    </li>
                    </ul>
                  </div>
                   @endforeach

                  <div class="d-grid gap-2 mt-5"> <button id="columns_details_submit" type="submit"
                          class="btn btn-success btn-lg">Submit</button>
                  </div>

              </form>
          </div>
      </div>
    </div>


    <style>
        form .hiddenstep {
            display: none
        }

        .select2-selection.select2-selection--multiple {
            min-height: 100px;
            width: 100%
        }

        .select2 {
            width: 100% !important;
            max-width: 500px;
        }

        .form_wrapper {
            border-radius: 0px 0px 5px 5px;
        }
    </style>
    <script src="{{ asset('script/columns-form.js') }}"></script>
    <script>
   
   $('#columnsTable').DataTable();
    </script>
</main>

  <div  id="full-loader" style="display: none">
    <div  class="full-loader d-flex align-items-center justify-content-center" >
      <img src="{{ asset('asset/loading5.gif') }}" alt="" >
      </div>
</div>

<style>
  .full-loader{
    position: fixed; 
    background-color:rgba(0, 94, 255, 0.297);
    height:100vh;
    width:100vw;
    top:0;
    left:0;
    border: 2px solid rgb(0, 94, 255);
  }
  .color-block>li>ul{
    margin-bottom: 15px
  }
</style>
@include('includes.footer')
