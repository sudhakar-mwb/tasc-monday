@include('includes.header')
@php
    // dd($response);
@endphp
<main class="px-3 pt-5">

    <div class="animation-container" style="min-height: 200px; ">
        <div class="animation-content" style="transition: transform 1s ease, opacity 2s ease;">
            <h1 class="header-heading1 mt-2 fw-bold " style="color:rgb(3,96,132)">{{ $heading }}</h1>
            <p class="header-heading3 fs-6 mb-5 text-secondary">{{ $subheading }}</p>
        </div>
    </div>

    <div class="w-100 mt-3">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php $link = ''; ?>
                @for ($i = 1; $i <= count(Request::segments()); $i++)
                    @if (($i < count(Request::segments())) & ($i > 0))
                        <?php $link .= '/' . Request::segment($i); ?>
                        @if ($i == 1)
                            <li class="breadcrumb-item "> <a href="<?= $link ?>"
                                    class="inactive link-secondary text-decoration-none">
                                    {{ ucwords(str_replace('-', ' ', Request::segment($i))) }}</a></li>
                        @else
                            <li class="breadcrumb-item"> <a href="<?= $link ?>"
                                    class="inactive link-secondary text-decoration-none">{{ ucwords(str_replace('-', ' ', Request::segment($i))) }}</a>
                            </li>
                        @endif
                    @else
                        <li class="breadcrumb-item active"> <a class="inactive link-primary text-decoration-none"
                                href=""> {{ ucwords(str_replace('-', ' ', Request::segment($i))) }}</a></li>
                    @endif
                @endfor
            </ol>
        </nav>

        <form class="input-group mb-3" method="POST" action="{{ route('monday.trackOnboarding') }}">
            <input type="text" class="header-search-input form-control border-end-0 fs-6"
                placeholder="Start typing to search products..." aria-label="Recipient's username"
                aria-describedby="basic-addon2">

            <button type="button" class="input-group-text border-start-0" id="basic-addon2"><svg
                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-search" viewBox="0 0 16 16">
                    <path
                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                </svg></button>
        </form>

        <div class="w-100 d-flex align-items-center mb-3">
            <div class="p-2 d-flex align-items-center ">
                <span class="text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-sort-down-alt" viewBox="0 0 16 16">
                        <path
                            d="M3.5 3.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 12.293zm4 .5a.5.5 0 0 1 0-1h1a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h3a.5.5 0 0 1 0 1zm0 3a.5.5 0 0 1 0-1h5a.5.5 0 0 1 0 1zM7 12.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7a.5.5 0 0 0-.5.5" />
                    </svg></span>
                <span class="ms-2 mt-1 text-secondary">Sort</span>
            </div>
            <div class="p-2 d-flex align-items-center ">
                <span class="text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-funnel" viewBox="0 0 16 16">
                        <path
                            d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                    </svg></span>
                <span class="ms-2 mt-1 text-secondary">Filter</span>
            </div>

            <div class="p-2 d-flex align-items-center ">
                <span class="text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                        <path fill-rule="evenodd"
                            d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                    </svg></span>
                <span class="ms-2 mt-1 text-secondary">Export</span>
            </div>
        </div>

        <?php
        $trackdata = $response['data']['boards'][0]['items_page']['items'];
        $columns = $response['data']['boards'][0]['columns'];
        
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
        function getClass($input)
        {
            switch ($input) {
                case 'IN PROGRESS':
                    return 'bg-light-progress';
                case 'COMPLETED':
                    return 'bg-light-success';
                case 'STUCK':
                    return 'bg-light-danger';
                default:
                    return 'bg-light-progress';
            }
        }
        function dateFormater($dateString)
        {
            $date = new DateTime($dateString);
            $formattedDate = $date->format('F j, Y');
            return $formattedDate;
        }
        ?>
        @for ($x = 0; $x < count($trackdata); $x++)
            <div class="track-card-container animation-container mb-3" style="min-height:280px">
                <div class="animation-content">
                    <div
                        class="track-card h-100 p-4  @php echo getClass(strtoupper(getValueById($trackdata[$x]['column_values'],'status8','label'))) @endphp rounded-3">
                        <nav style="--bs-breadcrumb-divider: '|';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item text-secondary"><a
                                        class="text-secondary fs-s text-decoration-none" href="#">@php echo findElementByTitle('Hiring Type',$columns,$trackdata[$x],'label');@endphp</a>
                                </li>
                                <li class="breadcrumb-item  text-secondary" aria-current="page"><span class="fs-s"> Created at
                                    @php
                                    $date=findElementByTitle('Joining Date',$columns,$trackdata[$x],'value');

                                    $extract=json_decode($date, true);
                                    if($extract['date']!==null)
                                    echo dateFormater($extract['date']);
                                    @endphp
                                    
                                </span></li>
                            </ol>
                        </nav>
                        <h4 class="text-start mt-2 mb-2">@php echo $trackdata[$x]['name']; @endphp</h4>
                        <h5 class="text-start mt-4">@php echo findElementByTitle('Profession',$columns,$trackdata[$x],'value');@endphp</h5>
                        <h6 class="text-start mt-3 track-profession fw-bold">@php echo  strtoupper(findElementByTitle('Overall Status',$columns,$trackdata[$x],'label')); @endphp</h6>
                      <a class="text-decoration-none" href="/monday/form/track-request/{{$trackdata[$x]['id']}}">
                        <button class="btn btn-to-link btn-secondary mt-4 btn-gradiant  d-flex align-items-center"
                        type="button">
                        <span>
                            Track Request
                        </span>

                        <span class="icon-btn_track"><img
                                src="//res2.weblium.site/res/5efdf94ff3bc420021179c9f/5f1aa6a7f642dd002299dea7"
                                class="button__icon-image_1Ob" alt="icon"></span>
                    </button>

                      </a>
                    </div>
                </div>
            </div>
        @endfor

    </div>
    
    <div class="w-100 mt-5 mb-5 align-center text-secondary fs-6">
        <small>Powered by TASC Outsourcing®</small>
    </div>


</main>

@include('includes.footer')
