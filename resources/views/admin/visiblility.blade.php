@include('includes.header')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css" />

<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<main class="px-3 pt-5 ">
    @include('admin.headtitle')

    <div class="d-flex flex-column align-items-center">
        <div class="columnsTable-wrapper">
            <table class="table border table-hover table-bordered" id="columnsTable">
                <thead>
                    <tr>
                        {{-- <th scope="col " class="bg-success text-light" style="max-width: 50px">#</th> --}}
                        <th scope="col " class="bg-success text-light">Board</th>
                        <th scope="col " class="bg-success text-light">Allowed Columns</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 10; $i++)
                        <tr>
                            <td class="p-0">
                                <div class="d-flex align-items-center justify-content-center" style="min-height:100px">
                                    <h6 class="text-secondary">34534564-d{{$i}}</h6>
                                </div>
                            </td>
                            <td class="m-0 p-0 " style="width:500px">
                                {{-- <div style="w-100"> --}}
                                <select class="js-example-basic-multiple w-100" name="states[]" multiple="multiple"
                                    style="max-width:500px">

                                    @for ($j = 0; $j < count($boards); $j++)
                                        <option value="{{ $boards[$j] }}">{{ $boards[$j] }}</option>
                                    @endfor
                                </select>
                            </td>
                        </tr>
                    @endfor

                </tbody>
            </table>
        </div>
    </div>


    <style>
        .select2-selection.select2-selection--multiple {
            min-height: 100px;
            width: 100%
        }

        .select2 {
            width: 100% !important;
            max-width: 500px;
        }

        .columnsTable-wrapper {
            min-width: 800px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
        });
        $('#columnsTable').DataTable();
    </script>
</main>

@include('includes.footer')
