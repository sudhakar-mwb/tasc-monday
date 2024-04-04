@include('includes.header')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>

<main class="px-3 pt-5 ">
    @include('admin.headtitle')
    <div class="d-flex w-100 justify-content-center" style="">

        <div style="max-width:500px;width:100%;">
            <div class="bg-success text-white">
                <p class="p-2 m-0"><strong>MANAGE COLUMNS</strong></p>
            </div>
            <div class="form_wrapper border border-success p-4">
                <form id="column_view_form" class="text-start ">
                    <div class="mb-3" id="form-step-1">
                        <label for="exampleInputEmail1" class="form-label">Select Board</label>
                        <select id="input-brand" class="form-select" id="exampleInputEmail1"
                            aria-label="Default select example" required>
                            <option value="">-- Select  Brand --</option>
                            @for ($j = 0; $j < count($boards); $j++)
                            <option value="{{ $boards[$j] }}">{{ $boards[$j] }}</option>
                        @endfor
                        </select>
                    </div>

                    <div class="hiddenstep" class="" id="form-step-2">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Onboarding Status</label>
                            <select id="onboarding_columns" class="js-example-basic-multiple w-100"
                                name="onboarding_coulumns[]" multiple="multiple" style="max-width:500px" >

                                @for ($j = 0; $j < count($boards); $j++)
                                    <option value="{{ $boards[$j] }}">{{ $boards[$j] }}</option>
                                @endfor
                            </select>
                            {{-- <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div> --}}
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Candidate Information</label>
                            <select id="candidate_columns" class="js-example-basic-multiple w-100"
                                name="candidate_columns[]" multiple="multiple" style="max-width:500px" >

                                @for ($j = 0; $j < count($boards); $j++)
                                    <option value="{{ $boards[$j] }}">{{ $boards[$j] }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3" id="icon_inputs-wrapper">
                            <label for="exampleInputPassword1" class="form-label">Provide candidate column details : </label>
                            <ul id="icon_inputs">
                            </ul>
                        </div>
                        <a ="https://icons.getbootstrap.com/" target="_blank">Go to icon librery&nbsp;<i class="bi bi-box-arrow-up-right"></i></a> 
                        <hr class="mt-4">
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Onboarding Updates</label>
                        <select id="onboarding-updates-option" class="form-select" aria-label="Default select example" required>
                    </select>
                    </div>
                    </div>

                    <div class="d-grid gap-2 mt-5"> <button id="columns_details_submit" type="submit"
                            class="btn btn-success ">Submit</button>
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
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
        });
        $('#columnsTable').DataTable();
    </script>
</main>

@include('includes.footer')
