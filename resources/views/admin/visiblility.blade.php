@include('includes.header')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<main class="px-3">
    @include('admin.headtitle')
    @include('includes.links', ['active' => 'board-visiblilty'])
    <div class="d-flex w-100 justify-content-center onboarding-flex-direction-column" style="gap:20px">

        <div title="coloumn visibility form" style="max-width:550px;width:100%;">
            <div class="bg-success text-white site-bg">
                <p class="p-2 m-0 fs-5"><strong>Manage Boards settings</strong></p>
            </div>
            <div class="form_wrapper border border-success p-4 primary-shadow">
                <form id="column_view_form" class="text-start ">
                    <div class="mb-3" id="form-step-1">
                        <label for="exampleInputEmail1" class="form-label">Select Board</label>
                        <select id="input-board-select" class="form-select" id="exampleInputEmail1"
                            aria-label="Default select example">
                            <option value="">-- Select Board --</option>
                            @if(!empty($boards))
                                @for ($j = 0; $j < count($boards); $j++)
                                    <option value="{{ $boards[$j]['id'] }}">{{ $boards[$j]['name'] }}</option>
                                @endfor
                            @endif
                        </select>
                    </div>
                    <div class="hiddenstep" class="" id="form-step-2">
                        <div class="mb-3">
                            <label for="form_embed_code" class="form-label">Form Embed Code &nbsp;<i class="bi bi-clipboard-fill"></i></label><br>
                            <input type="text" id="form_embed_code" class="form-control" placeholder=""
                                aria-label="Example text with button addon" aria-describedby="button-addon1">
                        </div>
                        <div class="mb-3">
                            <label for="chart_embed_code" class="form-label">Chart Embed Code&nbsp;<i class="bi bi-clipboard-fill"></i></label><br>
                            <input type="text" id="chart_embed_code" class="form-control" placeholder=""
                                aria-label="Example text with button addon" aria-describedby="button-addon1">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">User Subheadings Columns</label>
                            <select id="sub_headings" class="js-example-basic-multiple w-100" name="subHeadings[]"
                                multiple="multiple" style="max-width:500px">

                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Onboarding Status Columns</label>
                            <select id="onboarding_columns" class="js-example-basic-multiple w-100"
                                name="onboarding_coulumns[]" multiple="multiple" style="max-width:500px">
                            </select>
                        </div>
                        {{-- <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Document Columns</label>
                            <select id="documents_columns" class="js-example-basic-multiple w-100"
                                name="documents_columns[]" multiple="multiple" style="max-width:500px">
                            </select>
                        </div> --}}
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Candidate Information Columns</label>
                            <select id="candidate_columns" class="js-example-basic-multiple w-100"
                                name="candidate_columns[]" multiple="multiple" style="max-width:500px">
                            </select>
                        </div>
                        <div class="mb-3" id="icon_inputs-wrapper">
                            <label for="exampleInputPassword1" class="form-label">Provide candidate column details :
                            </label>

                            <a href="https://icons.getbootstrap.com/" target="_blank">Go to icons librery&nbsp;<i
                                    class="bi bi-box-arrow-up-right"></i></a>
                            <ol id="icon_inputs">
                            </ol>
                        </div>

                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Onboarding Updates</label>
                            <select id="onboarding-updates-option" class="form-select"
                                aria-label="Default select example">
                            </select>
                        </div>
                        <h5 class="text-secondary mt-5 mb-3 pb-2 border-bottom">Card Columns:</h5>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Top Details column</label>
                            <select id="card-column-1" class="form-select" aria-label="Default select example">
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Mid Heading column</label>
                            <select id="card-column-2" class="form-select" aria-label="Default select example">
                            </select>
                        </div>
                        <h5 class="text-secondary mt-5 mb-3 pb-2 border-bottom">Required Columns (For Search & Filter):</h5>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Profession column</label>
                            <select id="profession_column" class="form-select" aria-label="Default select example" required>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Overall Status column</label>
                            <select id="overall_status" class="form-select" aria-label="Default select example" required>
                            </select>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-5"> <button id="columns_details_submit" type="submit"
                            class="btn btn-success btn-lg">Submit</button>
                    </div>

                </form>
            </div>
        </div>
        <?php
        if(auth()->user()->role==1)
        {
        ?>
        <div title="status visibility manage" style="max-width:550px;width:100%;">
            <div class="bg-success text-white site-bg">
                <p class="p-2 m-0 fs-5"><strong>Manage Status Background</strong></p>
            </div>
            <div class="form_wrapper border border-success p-4 primary-shadow">
                <form id="status_view_form" class="text-start">
                    @if(!empty($coloursData))
                        @foreach ($coloursData as $key => $value)
                            <?php $val = implode(', ', $value['val']); ?>
                            <div class="mb-3">
                                <h5 class="p-2 "
                                    style="border-radius:20%;border-left:10px solid {{ $value['rgb_code'] }};">Color:
                                    {{ $key }}</h3>
                                    <ul style="list-style: none" class="color-section">
                                        <li>
                                            <div class="form-floating">
                                                <textarea name="{{ $key }}" current_color="{{ $key }}" class="form-control color-input"
                                                    placeholder="Leave a comment here" id="floatingTextarea2" style="height: 100px">{{ $val }}</textarea>
                                                <label for="floatingTextarea2">Write Status</label>
                                            </div>
                                        </li>
                                    </ul>
                            </div>
                        @endforeach
                    @endif
                    <div class="d-grid gap-2 mt-5"> <button id="columns_details_submit" type="submit"
                            class="btn btn-success btn-lg">Submit</button>
                    </div>
                </form>
            </div>
        </div>
       <?php } ?>
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

</main>


{{-- full page loader --}}

<script>
    const base_url = "{{ url('/') }}/";
</script>

<style>
    .color-block>li>ul {
        margin-bottom: 15px
    }

    @media screen and (max-width: 572px) {
      
        .onboarding-flex-direction-column{
            flex-direction:column;
        }
    
    }

</style>
@include('includes.footer')
