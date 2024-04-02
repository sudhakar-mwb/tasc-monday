@include('includes.header')
<main class="px-3 pt-5">
    @include('admin.headtitle')
    <script>
        function copyToBoard(val) {
            // Copy the text inside the text field
            navigator.clipboard.writeText(val);
        }
    </script>
    <table class="table border table-hover table-bordered" id="users-list-table">
        <thead>
            <tr>
                <th scope="col " class="bg-success text-light">#</th>
                <th scope="col " class="bg-success text-light">Name</th>
                <th scope="col " class="bg-success text-light">Company Name</th>
                <th scope="col " class="bg-success text-light">Phone</th>
                <th scope="col " class="bg-success text-light">Email</th>
                {{-- <th scope="col " class="bg-warning">Hiring Type</th> --}}
                {{-- <th scope="col " class="bg-warning">Status</th> --}}
                <th scope="col " class="bg-success text-light">Assign Board</th>
                <th scope="col " class="bg-success text-light">Password</th>
                <th scope="col " class="bg-success text-light">Created Date</th>
                <th scope="col " class="bg-success text-light">Forgot Pass</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 10; $i++)
                <tr>
                    <th scope="row">{{ $i + 1 }}</th>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>demouser@</td>
                    <td class="p-0">
                        <div class="d-flex align-items-center btn btn-outline-info border-0 rounded-0 text-dark"
                            style="gap:10px" onclick="copyToBoard('jaikroishnaverma@gmail.com')">
                            <span class="btn-copy" style="cursor:pointer;"><svg xmlns="http://www.w3.org/2000/svg"
                                    width="16" height="16" fill="currentColor" class="bi bi-copy"
                                    viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                                </svg></span>

                            <span>jaikrishnaverma@gmail.com</span>
                        </div>
                    </td>
                    {{-- <td>Hiring Type</td> --}}
                    {{-- <td>Active</td> --}}
                    <td class="m-0 p-0"><select class="form-select m-0 rounded-0 h-100 "
                            aria-label="Default select example">
                            <option class=" fs-5" selected>Unassigned</option>
                            <option class=" fs-5" value="1">B-345668</option>
                            <option class=" fs-5" value="2">B-346346</option>
                            <option class=" fs-5" value="3">B-3453453</option>
                        </select></td>
                    <td class="p-0">
                        <div class="d-flex align-items-center btn btn-outline-info border-0 rounded-0 text-dark"
                            style="gap:10px" onclick="copyToBoard('#21$5{{ $i }}6&3tryrtyr*')">
                            <span class="btn-copy" style="cursor:pointer;"><svg xmlns="http://www.w3.org/2000/svg"
                                    width="16" height="16" fill="currentColor" class="bi bi-copy"
                                    viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                                </svg></span>

                            <span>#21$5{{ $i }}6&3tryrtyr</span>
                        </div>
                    </td>
                    <td>11-Mar-2023</td>
                    <td class="p-0">
                        <button class="btn btn-outline-info btn-outline border-0 m-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                              </svg>
                           
                        </button>
                    </td>
                </tr>
            @endfor

        </tbody>
    </table>
</main>

@include('includes.footer')
