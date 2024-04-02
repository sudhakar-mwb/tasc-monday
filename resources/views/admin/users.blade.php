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
                <th scope="col " class="bg-warning">#</th>
                <th scope="col " class="bg-warning">Name</th>
                <th scope="col " class="bg-warning">Company Name</th>
                <th scope="col " class="bg-warning">Phone</th>
                <th scope="col " class="bg-warning">Email</th>
                {{-- <th scope="col " class="bg-warning">Hiring Type</th> --}}
                {{-- <th scope="col " class="bg-warning">Status</th> --}}
                <th scope="col " class="bg-warning">Assign Board</th>
                <th scope="col " class="bg-warning">Password</th>
                <th scope="col " class="bg-warning">Created Date</th>
                {{-- <th scope="col " class="bg-danger text-light">Action</th> --}}
            </tr>
        </thead>
        <tbody>
            @if ($mondayUsers-> isNotEmpty())
                @foreach ($mondayUsers as $user)
                <tr>
                    {{-- <th scope="row">{{ $i + 1 }}</th> --}}
                    <td>{{$user->id}}</td>
                    <td>{{$user->name}}</td>
                    <td>{{$user->company_name}}</td>
                    <td>{{$user->phone}}</td>
                    <td class="p-0">
                        <div class="d-flex align-items-center btn btn-outline-info border-0 rounded-0 text-dark"
                            style="gap:10px" onclick="copyToBoard('jaikroishnaverma@gmail.com')">
                            <span class="btn-copy" style="cursor:pointer;"><svg xmlns="http://www.w3.org/2000/svg"
                                    width="16" height="16" fill="currentColor" class="bi bi-copy"
                                    viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                                </svg></span>

                            <span>{{$user->email}}</span>
                        </div>
                    </td>
                    {{-- <td>Hiring Type</td> --}}
                    {{-- <td>Active</td> --}}
                    {{-- {{$boardsData}} --}}
                    
                    <td class="m-0 p-0"><select class="form-select m-0 rounded-0 h-100 "
                            aria-label="Default select example">
                            <option class=" fs-5" selected>Not Assigned</option>
                            @if ( count($boardsData['boards']) > 0)
                            @foreach ($boardsData['boards'] as $board)
                                <option class=" fs-5" value="{{$board['id']}}">{{$board['name']}}</option>
                            @endforeach
                            @endif
                        </select></td>
                    <td class="p-0">
                        <div class="d-flex align-items-center btn btn-outline-info border-0 rounded-0 text-dark"
                            style="gap:10px" 
                            {{-- onclick="copyToBoard('#21$5{{ $i }}6&3tryrtyr*')" --}}
                            >
                            <span class="btn-copy" style="cursor:pointer;"><svg xmlns="http://www.w3.org/2000/svg"
                                    width="16" height="16" fill="currentColor" class="bi bi-copy"
                                    viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                                </svg></span>

                            <span>{{$user->password}}</span>
                        </div>
                    </td>
                    <td>{{$user->created_at}}</td>
                    {{-- <td class="text-danger p-0">
                        <button class="btn btn-outline-danger btn-outline m-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-trash-fill" viewBox="0 0 16 16">
                                <path
                                    d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0" />
                            </svg>
                        </button>
                    </td> --}}
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5"> Records Not Found. </td>
                </tr>
                @endif
                {{-- <div class="card-footer clearfix">
                    {{$user->links()}}
                </div> --}}
        </tbody>
    </table>
</main>

@include('includes.footer')