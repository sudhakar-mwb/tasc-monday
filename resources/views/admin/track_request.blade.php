@include('includes.header')
<?php

$trackdata = $response['data']['boards'][0]['items_page']['items'];
$total_data = $response['data']['boards']['totalMondayData'];
$status_color = $data['status_color'];

// $cs = $response['data']['boards'][0]['items_page']['cursor'];
$disable = $_SERVER['REQUEST_METHOD'] === 'GET';
$columns = isset($response['data']['boards'][0]['columns'])?$response['data']['boards'][0]['columns']:[];

function getValueById($columnValues, $id, $key = 'value')
{
    foreach ($columnValues as $item) {
        if ($item['id'] === $id) {
            return trim($item[$key], '"') ? trim($item[$key], '"') : 'N/A';
        }
    }
    return null; // Return null if no matching id found
}
function findElementByTitle($name, $data, $trackdata, $key = 'value')
{
    $columnsid = null;
    foreach ($data as $element) {
        if (isset($element['title']) && $element['title'] === $name) {
            $columnsid = $element['id'];
        }
    }

    if ($columnsid !== null) {
        return getValueById($trackdata['column_values'], $columnsid, $key);
    } // Return null if element not found
    return null;
}
function matchStatus($inputString, $statusArray)
{
    // Convert input string to uppercase
    $inputString = strtoupper($inputString);

    // Loop through the status array
    foreach ($statusArray as $statusObject) {
        foreach ($statusObject as $statusName => $statusValues) {
            // Check if any of the status values match the input string
            foreach ($statusValues as $statusValue) {
                if (strtoupper($statusValue) === $inputString) {
                    // Return the name of the array if a match is found
                    return $statusName;
                }
            }
        }
    }

    // If no match is found, return false
    return false;
}
function getClass($str, $status_color)
{

    $input = matchStatus($str, $status_color);
    switch ($input) {
        case 'IN PROGRESS':
            return 'bg-light-progress';
        case 'COMPLETED':
            return 'bg-light-success';
        case 'STUCK':
            return 'bg-light-danger';
        default:
            return 'bg-light-default';
    }
}

function dateFormater($dateString)
{
    $date = new DateTime($dateString);
    $formattedDate = $date->format('F j, Y');
    return $formattedDate;
}

?>


<main class="px-3 ">
    @include('admin.headtitle')
    <div class="w-100 mt-3">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"> <a class="inactive link-secondary text-decoration-none"
                        href="/onboardify/form"><u> {{ ucwords('Home') }}</u></a></li>
                <li class="breadcrumb-item active"> <a class="inactive link-primary text-decoration-none" href="">
                        {{ ucwords('Request Tracking') }}</a></li>


            </ol>
        </nav>

        <form class="input-group mb-3" id="search-form" method="POST" action="">
            <input type="text" id="search-input" class="header-search-input form-control border-end-0 fs-6"
                name="search" value="{{ $searchquery }}" placeholder="Search by name or profession"
                aria-label="Recipient's username" aria-describedby="basic-addon2">



            <button type="button" id="search-button" type="button" class="input-group-text border-start-0">
                <i class="bi bi-search"></i>
            </button>


            <div class="w-100 d-flex align-items-center mb-3" id="topbar">
                <div class="p-2 d-flex align-items-center nav-item dropdown ">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                                <path fill-rule="evenodd"
                                    d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                            </svg></span>
                        <span class="ms-2 mt-1 text-secondary">Sort</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown" onclick="event.stopPropagation();">

                        <li><span class="dropdown-item">Sort By Date</span></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <span class="dropdown-item">
                                <label class="form-check " for="flexCheckDefault_asc">
                                    <input class="form-check-input" name="sort_by_date"
                                        {{ $sortbyname == 'asc' ? 'checked' : '' }} type="radio" value="asc"
                                        id="flexCheckDefault_asc">
                                    <label class="form-check-label" for="flexCheckDefault_asc">
                                        &#65516;<span class=" ms-2 ps-auto pe-auto">Asc</span>
                                    </label>
                                </label>
                            </span>
                        </li>
                        <li>
                            <span class="dropdown-item">
                                <label class="form-check" for="flexCheckDefault_desc">
                                    <input class="form-check-input" name="sort_by_date"
                                        {{ $sortbyname == 'desc' ? 'checked' : '' }} type="radio" value="desc"
                                        id="flexCheckDefault_desc">
                                    <label class="form-check-label" for="flexCheckDefault_desc">
                                        &#65514;<span class=" ms-2">Desc</span>
                                    </label>
                                </label>
                            </span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><button class="w-100 rounded-0 btn btn-primary">APPLY</button></li>
                        {{-- <li><a class="dropdown-item" href="#">Another action</a></li> --}}
                        {{-- <li><hr class="dropdown-divider"></li> --}}
                        {{-- <li><a class="dropdown-item" href="#">Something else here</a></li> --}}
                    </ul>
                </div>
                <div class="p-2 d-flex align-items-center nav-item dropdown">

                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-funnel" viewBox="0 0 16 16">
                                <path
                                    d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                            </svg></span>
                        <span class="ms-2 mt-1 text-secondary">Filter</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown" onclick="event.stopPropagation();">
                        <li><a class="dropdown-item" href="#">Filter By Status</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a href="{{ $disable ? '#' : '' }}"
                                class="dropdown-item btn btn-link w-100 d-flex justify-content-start {{ $disable ? 'text-decoration-none' : '' }} ">
                                <span class="">
                                    <label class="form-check w-100" for="flexCheckDefault_status_all">
                                        <input class="form-check-input" name="status_filter" type="radio"
                                            value=""
                                            {{ $status_filter == '' || $status_filter == null ? 'checked' : '' }}
                                            id="flexCheckDefault_status_all">
                                        <label class="form-check-label" for="flexCheckDefault_status_all">
                                            <span class=" ms-2 ps-auto pe-auto">All</span>
                                        </label>
                                    </label>
                                </span>
                            </a>
                        </li>
                        <li>
                          <span class="dropdown-item">
                              <label class="form-check " for="flexCheckDefault_status_pending">
                                  <input class="form-check-input" name="status_filter" type="radio"
                                      {{ $status_filter == '2' ? 'checked' : '' }} value="2"
                                      id="flexCheckDefault_status_pending">
                                  <label class="form-check-label" for="flexCheckDefault_status_pending">
                                      <span class=" ms-2 ps-auto pe-auto">Pending</span>
                                  </label>
                              </label>
                          </span>
                      </li>
                      <li>
                        <span class="dropdown-item">
                            <label class="form-check " for="flexCheckDefault_status_inprogress">
                                <input class="form-check-input" name="status_filter" type="radio"
                                    {{ $status_filter == '0' ? 'checked' : '' }} value="0"
                                    id="flexCheckDefault_status_inprogress">
                                <label class="form-check-label" for="flexCheckDefault_status_inprogress">
                                    <span class=" ms-2 ps-auto pe-auto">In Progress</span>
                                </label>
                            </label>
                        </span>
                    </li>
                        <li>
                            <span class="dropdown-item">
                                <label class="form-check " for="flexCheckDefault_status_completed">
                                    <input class="form-check-input" name="status_filter" type="radio"
                                        {{ $status_filter == '1' ? 'checked' : '' }} value="1"
                                        id="flexCheckDefault_status_completed">
                                    <label class="form-check-label" for="flexCheckDefault_status_completed">
                                        <span class=" ms-2 ps-auto pe-auto">Completed</span>
                                    </label>
                                </label>
                            </span>
                        </li>
                    

                  

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><button class="w-100 rounded-0 btn btn-primary">APPLY</button></li>
                    </ul>


                </div>

                <a href="?export=true" class="text-decoration-none">
                    <div class="p-2 d-flex align-items-center ">

                        <span class="text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                                <path fill-rule="evenodd"
                                    d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                            </svg></span>
                        <span class="ms-2 text-secondary">Export</span>
                    </div>
                </a>


                {{-- <div class="p-2 d-flex align-items-center nav-item dropdown">

                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                                <path
                                    d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                            </svg></span>
                        <span class="ms-2 mt-2 text-secondary">Count Per Page</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown" onclick="event.stopPropagation();">
                        @foreach ([3, 9, 12, 16] as $count)
                            <li>
                                <span class="dropdown-item">
                                    <label class="form-check " for="flexCheckDefault_status_completed ">
                                        <input class="form-check-input" name="limit" type="radio"
                                            {{ $limit == $count ? 'checked' : '' }} value="{{ $count }}"
                                            id="flexCheckDefault_count_{{ $count }}">
                                        <label class="form-check-label"
                                            for="flexCheckDefault_count_{{ $count }}">
                                            <span class=" ps-auto pe-auto">{{ $count }}<span
                                                    class="ms-2 text-secondary">Per Page </span></span>
                                        </label>
                                    </label>
                                </span>
                            </li>
                        @endforeach

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><button class="w-100 rounded-0 btn btn-primary">APPLY</button></li>
                    </ul>


                </div> --}}
            </div>

        </form>
        @for ($x = 0; $x < count($trackdata); $x++)
            <?php
            $column1 = '';
            $column2 = '';
            if ($trackdata && $data['column1'] && $data['column2']) {
                $column1 = getValueById($trackdata[$x]['column_values'], $data['column1'], 'text');
                $column2 = getValueById($trackdata[$x]['column_values'], $data['column2'], 'text');
            }
            ?>
            <div class="track-card-container animation-container mb-4" style="min-height:280px">
                <div class="animation-content card-card-details"
                    style="  transition: transform .3s ease 0.5s, opacity 1s ease 0.5s;">
                    <div
                        class="track-card h-100 p-4  @php echo getClass(strtoupper(findElementByTitle('Overall Status',$columns,$trackdata[$x],'text')),$status_color) @endphp rounded-3">
                        <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item text-secondary"><a
                                        class="text-secondary fs-s text-decoration-none"
                                        href="#">{{ $column1 }}</a>
                                </li>
                                <li class="breadcrumb-item  text-secondary" aria-current="page"><span class="fs-s">
                                        Created at {{ dateFormater($trackdata[$x]['created_at']) }}

                                    </span></li>
                            </ol>
                        </nav>
                        <h4 class="card-name ff-hind text-start mt-2 mb-2">@php echo $trackdata[$x]['name']; @endphp</h4>
                        <h5 class="card-column2 text-start mt-4">{{ $column2 }}</h5>
                        <h6 class="card-status text-start mt-3 track-profession fw-bold">@php echo  strtoupper(findElementByTitle('Overall Status',$columns,$trackdata[$x],'text')); @endphp</h6>
                        <a class="text-decoration-none"
                            href="/onboardify/form/track-request/{{ $trackdata[$x]['id'] }}/{{ str_replace(' ', '_', $trackdata[$x]['name']) }}">
                            <button class="btn btn-to-link btn-secondary mt-4 btn-gradiant  d-flex align-items-center"
                                type="button">
                                <span>
                                    Track Request
                                </span>

                                <span class="icon-btn_track">
                                    <svg height="25px" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px"
                                        viewBox="0 0 100 100" enable-background="new 0 0 100 100"
                                        xml:space="preserve" style="max-width:100%" height="100%">
                                        <g style="" fill="currentColor">
                                            <path
                                                d="M16,50.02C16.001,68.787,31.214,84,49.979,84c18.767,0,33.98-15.213,33.98-33.98c0-18.765-15.214-33.978-33.981-33.979   C31.213,16.041,16.001,31.255,16,50.02z M72.114,52.356c-0.033,0.044-0.065,0.088-0.102,0.13c-0.064,0.078-0.135,0.156-0.212,0.232   c-0.001,0.003-0.005,0.006-0.007,0.008L54.815,69.705c-1.494,1.494-3.914,1.494-5.408,0c-1.492-1.493-1.492-3.914,0-5.406   l10.457-10.455H30.866c-2.111,0-3.824-1.712-3.824-3.824c0-2.11,1.713-3.822,3.824-3.822h28.998L49.409,35.742   c-1.494-1.492-1.494-3.914,0-5.408c1.492-1.491,3.912-1.491,5.404,0l16.982,16.983c0.082,0.081,0.153,0.16,0.22,0.243   c0.03,0.033,0.058,0.069,0.085,0.104c0.021,0.027,0.045,0.058,0.062,0.087c0.472,0.635,0.752,1.416,0.754,2.269   c-0.002,0.854-0.283,1.638-0.755,2.274C72.147,52.315,72.13,52.335,72.114,52.356z"
                                                style="" fill="currentColor"></path>
                                        </g>
                                    </svg>
                            </button>

                        </a>
                    </div>
                </div>
            </div>
        @endfor
    </div>
    @if ($cs == null && count($trackdata) > 2)
        <small class="mt-3 text-secondary">No More Data Available.</small>
    @endif
    @if (count($trackdata) == 0 && trim($searchquery) !== '')
        <div class="d-flex flex-column align-items-center justify-content-center text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor"
                class="bi bi-truck-flatbed" viewBox="0 0 16 16">
                <path
                    d="M11.5 4a.5.5 0 0 1 .5.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-4 0 1 1 0 0 1-1-1v-1h11V4.5a.5.5 0 0 1 .5-.5M3 11a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2m1.732 0h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4a2 2 0 0 1 1.732 1" />
            </svg>
            <h3 class="mt-3 text-secondary ">No Search Found.</h3>
        </div>
    @elseif(count($trackdata) == 0 && trim($searchquery) === '')
        <div class="d-flex flex-column align-items-center justify-content-center text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor"
                class="bi bi-truck-flatbed" viewBox="0 0 16 16">
                <path
                    d="M11.5 4a.5.5 0 0 1 .5.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-4 0 1 1 0 0 1-1-1v-1h11V4.5a.5.5 0 0 1 .5-.5M3 11a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2m1.732 0h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4a2 2 0 0 1 1.732 1" />
            </svg>
            <h3 class="mt-3 text-secondary ">No Request Available.</h3>
        </div>
    @endif

    @if ($total_data)
        <div class="d-flex align-items-center justify-content-center mt-3" style="gap:20px">
            <form action="" id="bottom-form" class="d-flex justify-content-end align-items-center w-100"
                style="gap:20px" method="POST">
                <div class="dropdown" style="margin: auto 0;">
                    Request Per Page: &nbsp;
                    <button class="btn-count dropdown-toggle" style="min-width: 80px" type="button"
                        id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $limit }}&nbsp;
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">

                        @foreach ([25, 50, 75, 100] as $item)
                            <li class="item-per-page {{ $limit == $item ? 'bg-primary' : '' }}"
                                count="{{ $item }}">

                                <span
                                    class="dropdown-item px-3 py-2 {{ $limit == $item ? 'text-light' : '' }}">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @csrf


                <input type="hidden" id="per-page-limit" name="limit" value="{{ $limit }}">
                <input type="hidden" id="cs-input" name="cursor" value="{{ $cs }}">

                <span class="p-0 m-0 mt-2">
                    {{ $cs * $limit - ($limit - 1) }}-{{ $cs * $limit > $total_data ? $total_data : $cs * $limit }} of
                    {{ $total_data }}</span>

                <div class="buttons d-flex justify-content-end align-items-center">

                    <button type="button" {{ $cs == 1 ? 'disabled' : '' }}
                        class="{{ $cs == 1 ? 'disabled-btn' : '' }} fs-2 navigation-buttons"
                        cursor="{{ $cs - 1 }}"><i class="bi bi-arrow-left-circle"></i></button>
                    <?php
                    $isNextDisable = $cs == null || ($cs * $limit > $total_data ? $total_data : $cs * $limit) == $total_data;
                    ?>
                    <button type="button" {{ $isNextDisable ? 'disabled' : '' }}
                        class="{{ $isNextDisable ? 'disabled-btn' : '' }} fs-2 navigation-buttons"
                        cursor="{{ $isNextDisable ? $cs : $cs + 1 }}"><i
                            class="bi bi-arrow-right-circle"></i></button>

                </div>



                {{-- <nav aria-label="Page navigation example">
              <ul class="pagination justify-content-end">
                <li class="page-item disabled">
                  <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                </li>
                <li class="page-item">
                  {{-- <a class="page-link" href="#">Next</a> --}}
                {{-- @csrf --}}
                {{-- <input type="hidden" name="limit" value="{{ $limit }}">
                  <input type="hidden" name="cursor" value="{{ $cs }}">
                  <button class="page-link" type="submit">
                      NEXT
                  </button>
                </li>
              </ul>
            </nav>  --}}




            </form>
            {{-- @endif --}}

        </div>
    @endif
</main>
<style>
    .card-card-details .card-name {
        color: #434343 !important;
        font-size: 26px;
        font-weight: 700
    }

    .card-card-details .card-column2 {
        color: #434343 !important;
        font-size: 21px;
    }

    .card-card-details .card-status {
        font-size: 17px;
    }

    .card-card-details .breadcrumb li * {
        font-size: 15px;
        color: #928F8F;
    }

    #topbar .dropdown-toggle::after {
        display: none !important;
    }


    .item-per-page.bg-primary>.dropdown-item:hover {
        color: black !important;
        cursor: pointer !important;
    }

    .item-per-page>span.dropdown-item {
        cursor: pointer !important;
    }

    .dropdown-menu {
        min-width: 80px
    }

    .btn-count {
        padding: 10px 20px;
        margin-top: 5px;
        border: 1px solid rgb(201, 201, 201);
        border-radius: 5px;
        background-color: transparent
    }

    .disabled-btn {
        color: grey;
        cursor: not-allowed
    }
</style>
<script>
    $(document).ready(function(e) {
        $('.navigation-buttons').on('click', function() {
            $cur = $(this).attr('cursor')
            $isDisable = $(this).attr('disabled')
            if ($cur && !$isDisable) {
                $('#cs-input').val($cur);
                $("#bottom-form").submit();
            }
        })

        $('.item-per-page').on('click', function() {
            let count = $(this).attr('count');
            $('#per-page-limit').val(count);
            $('#cs-input').remove()
            $("#bottom-form").submit();
        })

        $("#search-input").on('input', function() {
            $txt = $(this).val();
            if ($txt) {
                $("#search-button").html('<i class="bi fs-5 bi-file-x-fill text-primary"></i>');
            } else {
                $("#search-button").html('<i class="bi bi-search"></i>');
            }
        })

        $txt = $('#search-input').val()
        if ($txt) {
            $("#search-button").html('<i class="bi fs-5 bi-file-x-fill text-primary"></i>');
        } else {
            $("#search-button").html('<i class="bi bi-search"></i>');
        }

        $("#search-button").on('click', function() {
            $txt = $('#search-input').val()
            if ($txt) {
                $("#search-input").val('');
                $("#search-button").html('<i class="bi bi-search"></i>');
                $('#search-form').submit()
            } else {
                $('#search-form').submit()
            }
        })
    })
</script>

@include('includes.footer')
