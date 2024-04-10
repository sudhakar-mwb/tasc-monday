@include('includes.header')
<?php
// dd($boardsData);
?>
<main class="px-3 pt-5">
    @include('admin.headtitle')
    <nav class="my-2" style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb">
      <ol class="breadcrumb">
          {{-- <li class="breadcrumb-item active"> <a class="inactive link-primary text-decoration-none"
                  href="users"><u> {{ ucwords('users list') }}</u></a></li> --}}
          <li class="breadcrumb-item active"> <a class="inactive link-primary text-decoration-none"
                  href="board-visiblilty"> {{ ucwords('Manage Columns Visibility') }}&nbsp;<i class="bi bi-box-arrow-up-right"></i></a></li>

      </ol>
  </nav>
    <script>
        function copyToBoard(val) {
            // Copy the text inside the text field
            navigator.clipboard.writeText(val);
        }

        function showLoader() {
            $("#full-loader").show();
        }

        function hideLoader() {
            $("#full-loader").hide();
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
            @if ($mondayUsers->isNotEmpty())
                @foreach ($mondayUsers as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->company_name }}</td>
                        <td>{{ $user->phone }}</td>
                        <td class="p-0">
                            <div class="d-flex align-items-center btn btn-outline-info border-0 rounded-0 text-dark"
                                style="gap:10px" onclick="copyToBoard('jaikroishnaverma@gmail.com')">
                                <span class="btn-copy" style="cursor:pointer;"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor" class="bi bi-copy"
                                        viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                                    </svg></span>

                                <span>{{ $user->email }}</span>
                            </div>
                        </td>

                        <td class="m-0 p-0"><select 
                          user_id="{{ $user->id }}"
                          email_id="{{  $user->email }}"
                                class="board_change form-select m-0 rounded-0 h-100 "
                                aria-label="Default select example">
                                <option class=" fs-5" selected>Not Assigned</option>
                                @if (count($boardsData['boards']) > 0)
                                    @foreach ($boardsData['boards'] as $board)
                                        <option class=" fs-5" value="{{ $board['id'] }}">{{ $board['name'] }}</option>
                                    @endforeach
                                @endif
                            </select></td>
                        <td class="p-0">
                            <div class="d-flex align-items-center btn btn-outline-info border-0 rounded-0 text-dark"
                                style="gap:10px" {{-- onclick="copyToBoard('#21$5{{ $i }}6&3tryrtyr*')" --}}>
                                <span class="btn-copy" style="cursor:pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                                    </svg>

                                </span>


                                <span>{{ $user->password }}</span>
                            </div>
                        </td>
                        <td>{{ $user->created_at }}</td>
                        <td class="text-info p-0 btn-primary">
                            <a href="http://localhost:8001/monday/forgot?email={{ $user->email }}" target="_blank"
                                class="btn m-0 fs-5 rounded-0" style="display: block !important">
                                <i class="bi bi-send-arrow-up-fill"></i>

                                </button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8"> Records Not Found. </td>
                </tr>
            @endif
            {{-- <div class="card-footer clearfix">
                    {{$user->links()}}
                </div> --}}
        </tbody>
    </table>
</main>
<script>
    const base_url = "{{ url('/') }}/";

    $('.board_change').on('change', setBoard)
    async function setBoard() {
        const user_id = $(this).attr('user_id')
        const email_id = $(this).attr('email_id')
        const board_id = $(this).val()
        showLoader();
        if (user_id && board_id) {
            try {

                const response = await fetch(
                    base_url + "monday/admin/colour-mapping/", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            user_id,
                            board_id,
                            email_id
                        }),
                    }
                );

                if (!response.ok) throw new Error("HTTP status " + response.status);
                alert("status group saved");
            } catch (error) {
                console.log("Api error", error);
                alert("Something wents wrong.");
            }
        }
        hideLoader();
    }
</script>


@include('includes.footer')
