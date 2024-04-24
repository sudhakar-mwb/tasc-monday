@include('includes.header')
<?php
// dd($boardsData);
?>
<main class="px-3 pt-5">
    @include('admin.headtitle')
    @include('includes.links', ['active' => 'users'])
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

                <th scope="col " class="bg-success site-bg text-light">#</th>
                <th scope="col " class="bg-success site-bg text-light">Name</th>
                <th scope="col " class="bg-success site-bg text-light">Company Name</th>
                <th scope="col " class="bg-success site-bg text-light">Phone</th>
                <th scope="col " class="bg-success site-bg text-light">Email</th>
                <th scope="col " class="bg-success site-bg text-light">Assign Board</th>
                <th scope="col " class="bg-success site-bg text-light">Password</th>
                <th scope="col " class="bg-success site-bg text-light">Created Date</th>
                <th scope="col " class="bg-success site-bg text-light">Forgot Pass</th>
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
                                style="gap:10px" onclick="copyToBoard('{{ $user->email }}' )">
                                <span class="btn-copy" style="cursor:pointer;"><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor" class="bi bi-copy"
                                        viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z" />
                                    </svg></span>

                                <span>{{ $user->email }}</span>
                            </div>
                        </td>

                        <td class="m-0 p-0"><select user_id="{{ $user->id }}" email_id="{{ $user->email }}"
                                class="board_change form-select m-0 rounded-0 h-100 "
                                aria-label="Default select example">
                                <option class=" fs-5">Not Assigned</option>
                                @if (count($boardsData['boards']) > 0)
                                    @foreach ($boardsData['boards'] as $board)
                                        <option class=" fs-5" value="{{ $board['id'] }}"
                                            {{ $board['id'] == $user->board_id ? 'selected' : '' }}>{{ $board['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select></td>
                        <td class="p-0">
                            <div class="d-flex align-items-center btn btn-outline-info border-0 rounded-0 text-dark"
                                style="gap:10px" onclick="copyToBoard('{{ $user->password  }}')">
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
                            <a href="{{ url('/') }}/monday/forgot?email={{ $user->email }}" target="_blank"
                                class="btn m-0 fs-5 rounded-0" style="display: block !important">
                                <i class="bi bi-send-arrow-up-fill"></i>

                                </button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9"> Records Not Found. </td>
                </tr>
            @endif
            {{-- <div class="card-footer clearfix">
                    {{$user->links()}}
                </div> --}}
        </tbody>
    </table>
    <div>
        <div class="card-footer clearfix">
            {{ $mondayUsers->links() }}
        </div>
    </div>
    <style>
      .card-footer svg{
          height: 40px;
      }
      .clearfix>nav>div:first-child{
        visibility: hidden;
      }
    </style>
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
                    base_url + "monday/admin/users", {
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
                alert("Board assigned successfully.");
            } catch (error) {
                console.log("Api error", error);
                alert("Something wents wrong.");
            }

        }
        hideLoader();
    }
</script>


@include('includes.footer')
