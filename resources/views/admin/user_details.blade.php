@include('includes.header')


<?php

function countryFlag($countryCode)
{
    $countryCode = strtolower($countryCode);
    if (File::exists(public_path('asset/flags/' . $countryCode . '.svg'))) {
        return asset('asset/flags/' . $countryCode . '.svg');
    } else {
        return '';
    }
    // Convert each letter of the country code to its corresponding regional indicator symbol
    $flag = '';
    foreach (str_split($countryCode) as $char) {
        // Check if the character is an uppercase letter
        if (ctype_upper($char)) {
            // Convert the letter to its corresponding Unicode regional indicator symbol
            $flag .= mb_convert_encoding('&#' . (ord($char) + 127397) . ';', 'UTF-8', 'HTML-ENTITIES');
        } else {
            // If the character is not an uppercase letter, append it as is
            $flag .= $char;
        }
    }

    return $flag;
}
$status_color = $data['status_color'];
// print_r($status_color);
$candidate_coulmns = $data['candidate_coulmns'];
$onboarding_columns = $data['onboarding_columns'];
$sub_headings_column = $data['sub_headings_column'];
$onboarding_updates_columns = $data['extra_details']['key'];
$columns = $response['data']['boards'][0]['columns'];
$activityLog = $response['data']['boards'][0]['activity_logs'] ?? [];
$trackdata = $response['data']['items'][0];
$columns_val = $response['data']['items'][0]['column_values'];
$created_at = $response['data']['items'][0]['created_at'];

function getValueById($columnValues, $id, $key = 'value')
{
    foreach ($columnValues as $item) {
        if ($item['id'] === $id) {
            return trim($item[$key], '"') ? trim($item[$key], '"') : 'N/A';
        }
    }
    return null; // Return null if no matching id found
}

function validText($txt)
{
    return $txt && $txt != 'N/A' && $txt !== 'NA' && strtolower($txt) != 'not available' && strtolower($txt) != 'not-applicable' && strtolower($txt) != 'not applicable';
}

function dynamicChecker($txt)
{
    if (strtotime($txt) !== false) {
        return dateFormater($txt);
    } else {
        return $txt;
    }
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
            return 'warning';
        case 'COMPLETED':
            return 'success';
        case 'STUCK':
            return 'danger';
        default:
            return 'secondary';
    }
}

function dateFormater($dateString)
{
    if ($dateString == null) {
        return '';
    }
    $date = new DateTime($dateString);
    $formattedDate = $date->format('F j, Y');
    return $formattedDate;
}
function initiatedDate($activityLog, $detail)
{
    foreach ($activityLog as $activity) {
        $activity_data = json_decode($activity['data'], true);
        if ($activity['data'] !== null) {
            foreach ($activity_data as $key => $value) {
                if ($key == 'column_title' && $value == $detail) {
                    // dd($activity['created_at']);
                    return date('F j, Y', $activity['created_at'] / 10000000);
                }
            }
        }
    }
    return false;
}
$profession = ucwords(findElementByTitle('Profession', $columns, $trackdata, 'value'));
$profileStatus = Str::upper(strtoupper(findElementByTitle('Overall Status', $columns, $trackdata, 'label')));
$requestTracking = ucwords('Request Tracking');
$name = ucwords($trackdata['name']);
$whatsappNumber = findElementByTitle('Candidate Contact Number (Whatsapp Number)', $columns, $trackdata, 'value');
$VisaIssuanceValue = json_decode(findElementByTitle('Visa Issuance', $columns, $trackdata, 'value'), true);
$VisaIssuancestatus = findElementByTitle('Visa Issuance', $columns, $trackdata, 'label');
$joiningDate = $trackdata['created_at'];
if ($joiningDate !== null && validText($joiningDate)) {
    // dd($joiningDate);
    $joiningDate = dateFormater($joiningDate);
} else {
    $joiningDate = 'NA';
}

?>
<main class="px-3 pt-2 onboarding-paddingtop">
    <div class="w-100 mt-3">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb onboarding-fs-14">
                <li class="breadcrumb-item active"> <a class="inactive link-secondary text-decoration-none"
                        href="/onboardify/form"><u> {{ ucwords('Home') }}</u></a></li>
                <li class="breadcrumb-item active"> <a class="inactive link-secondary text-decoration-none"
                        href="/onboardify/form/track-request"><u> {{ $requestTracking }}</u></a></li>
                <li class="breadcrumb-item active"> <a class="inactive link-primary text-decoration-none"
                        href="">
                        {{ $name }}</a></li>
            </ol>
        </nav>
    </div>
    <div class="w-100">
        <div class="d-flex mt-5 w-100 onboarding-flexcolumn" style="gap:20px">
            <div class="col-6 d-flex flex-column onboarding-width" style="gap:30px">
                <div class="d-flex mb-2" style="gap:16px">
                    <div
                        class="rounded-circle bg-{{ getClass($profileStatus, $status_color) }} p-4 onboarding-rounded-circle">
                        <div class="icon-size text-light" style="height: 50px;width:50px;">
                            <svg xmlns:x="http://ns.adobe.com/Extensibility/1.0/"
                                xmlns:i="http://ns.adobe.com/AdobeIllustrator/10.0/"
                                xmlns:graph="http://ns.adobe.com/Graphs/1.0/" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.2" baseProfile="tiny" x="0px"
                                y="0px" viewBox="0 0 100 100" xml:space="preserve" style="max-width:100%"
                                height="100%">
                                <metadata style="" fill="currentColor">
                                    <sfw xmlns="http://ns.adobe.com/SaveForWeb/1.0/" style="" fill="currentColor">
                                        <slices style="" fill="currentColor"></slices>
                                        <slicesourcebounds width="92.603" height="88.807" x="3.375" y="-94.057"
                                            bottomleftorigin="true" style="" fill="currentColor">
                                        </slicesourcebounds>
                                    </sfw>
                                </metadata>
                                <path
                                    d="M27.521,94c0.162-12,9.944-21.717,22.004-21.717C61.584,72.283,71.366,82,71.529,94h24.445c0,0,0.003,0.092,0.003,0.042  c0-25.572-20.73-46.206-46.301-46.206c-25.572,0-46.302,20.586-46.302,46.156C3.375,94.043,3.378,94,3.378,94H27.521z"
                                    style="" fill="currentColor" xmlns="http://www.w3.org/2000/svg"></path>
                                <circle cx="49.677" cy="24.856" r="19.607" style="" fill="currentColor">
                                </circle>
                            </svg>
                        </div>
                    </div>
                    <div class="d-flex flex-column justify-content-around">
                        <h5 class="text-start m-0 card-user-name onboarding-fs-24">{{ $name }}</h5>
                        <p class="profession m-0 text-start user-candidate-column onboarding-fs-14"
                            style="font-weight: 400">
                            <?php
                            
                            $str = [];
                            for ($cnt = 0; $cnt < count($sub_headings_column); $cnt++) {
                                $el = $sub_headings_column[$cnt];
                                $txt = getValueById($columns_val, $el['id'], 'text');
                                if (validText($txt)) {
                                    if (count($str) > 0 && $cnt < count($sub_headings_column) && $txt !== '') {
                                        array_push($str, '|');
                                    }
                                    array_push($str, $txt);
                                }
                            }
                            
                            ?>

                            {{ implode(' ', $str) }}
                        </p>
                        <h6
                            class="fs-17 status m-0 text-start text-{{ getClass($profileStatus, $status_color) }} fw-bold">
                            {{ $profileStatus }}</h6>
                    </div>
                </div>
                <div class="w-100">
                    <div class="card border-0 border-1 p-4">
                        <p class="column-head text-start head-color fw-bold pb-4 border-bottom onboarding-fs-20">
                            Candidate Information
                            </h4>
                        <ul class="list-group list-group-flush">
                            @foreach ($candidate_coulmns as $col)
                                <?php 
                            $text=getValueById($columns_val, $col['id'], 'text')??"NA";
                            // dd($columns_val);
                            $flag="";
                     
                            if(str_contains( $col['id'],'country')||str_contains( $col['id'],'national')){
                              $flag = getValueById($columns_val, $col['id'], 'value');
                              $flag = json_decode($flag);
                              $flag = $flag->countryCode ?? null;
                           
                              $flag = $flag ? (countryFlag($flag)?? null) : null;
                            
                            }
                          
                            if(validText($text))
                           {
                            ?>
                                <li class="user-candidate-column list-group-item d-flex pb-0 align-items-center border-0 text-start "
                                    style="background: inherit;gap:12px"><span class="mb-2">
                                        <i class="bi {{ $col['icon'] ? $col['icon'] : 'bi-info-circle' }}"></i>
                                    </span><span>
                                        {{ $col['custom_title'] ? $col['custom_title'] . ': ' : '' }}{{ $text }}</span>
                                    <span>
                                        @if ($flag !== '')
                                            <img height="20" width="22" src="{{ $flag }}"
                                                alt="{{ $flag }}">
                                        @endif
                                    </span>
                                </li>
                                <?php }?>
                            @endforeach

                        </ul>
                    </div>
                    <div class="card border-0 border-1 p-4">
                        <h4 class="text-start head-color fw-bold pb-4 border-bottom onboarding-fs-20">Onboarding Status
                        </h4>
                        <ul class="list-group list-group-flush">
                            @foreach ($onboarding_columns as $step)
                                <?php
                      
                                $valued = json_decode(getValueById($columns_val, $step['id'], 'value'), true);
                              
                                $status = getValueById($columns_val, $step['id'], 'text');
                                
                                if(validText($status))
{
                                ?>
                                <li class="list-group-item d-flex align-items-start border-0 text-start mb-1"
                                    style="background: inherit;gap:10px">
                                    <span style="width: 20px;height:20px"
                                        class="text-{{ getClass(Str::upper($status), $status_color) }} mt-1">
                                        <svg viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"
                                            style="max-width:100%" height="100%">
                                            <path
                                                d="M6 0c3.3137 0 6 2.6863 6 6s-2.6863 6-6 6-6-2.6863-6-6 2.6863-6 6-6zm2.6464 3.6464L5 7.293 3.3536 5.6464l-.7072.7072L5 8.707l4.3536-4.3535-.7072-.7072z"
                                                fill="currentColor" fill-rule="evenodd" style=""></path>
                                        </svg>
                                    </span>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-secondary fs-5">{{ $step['name'] }}</span>
                                        <span class="text-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                                            </svg>
                                            {{-- {{ $joiningDate }} --}}
                                            Initiated | {{ $joiningDate }}</span>
                                        @if ($status != null)
                                            <span class="text-secondary">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-arrow-right-short"
                                                    viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd"
                                                        d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                                                </svg> {{ $status }} |
                                                {{ isset($valued['changed_at']) && $valued !== null ? dateFormater($valued['changed_at']) : 'Pending' }}
                                            </span>
                                        @endif
                                    </div>
                                </li>
                                <?php } ?>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-6 d-flex flex-column onboarding-width" style="gap:30px">
                <div class="card border-0 border-1 p-4" style="background: rgba(111, 116, 144, 0.06)">
                    <p class="second-heading text-start head-color fw-bold pb-4 border-bottom">Onboarding Updates</h5>
                    <h6 class="text-start mt-2 mb-4 fw-bold text-secondary">
                        {{ dateFormater($created_at) ?? '' }}</h6>
                    <?php 
                        $desc= getValueById($columns_val, $onboarding_updates_columns, 'text');
                        if(validText($desc)){
                        ?>
                    <p class="text-start text-secondary">
                        {{ $desc ?? '' }}
                    </p>
                    <?php } ?>
                </div>

                {{-- <div class="card border-0 border-1 p-4">
                    <h5 class="text-start head-color fw-bold pb-4 border-bottom">Documents</h5>
                    @for ($j = 0; $j < count($documents_columns); $j++)
                        <div class="d-flex align-items-center mt-1 mb-2" style="gap:8px">

                            <?php
                            //  $col = $documents_columns[$j];
                            //  $str= getValueById($columns_val, $col['id'], 'text');
                            //  $str=explode(",",$str);
                            //  for($i=0;$i < count($str) && $i < 6;$i++)
                            //  {
                            ?>

                            <a href="{{ $str[$i] }}" target="_blank" class="zoom" style="display: block">
                                <span class=" text-start mt-1 fw-bold text-secondary text-danger"
                                    style="cursor: pointer">
                                    <i class="bi bi-file-earmark-pdf-fill fs-3"></i>
                                </span>
                            </a>
                            <?php
                            //  }
                            //     $remainings=count($str) -6;
                            //     if($remainings>0){
                            ?>
                            <span
                                class="d-flex align-items-center mt-2 justify-content-center btn btn-dark rounded-circle p-0"
                                style="min-height: 30px;min-width:30px">+{{ $remainings }}</span>
                            <?php
                            // }
                            ?>
                        </div>
                    @endfor
                </div> --}}
            </div>
        </div>
    </div>
</main>
<style>
    .zoom:hover {
        transform: scale(1.5);
        /* (150% zoom - Note: if the zoom is too large, it will go outside of the viewport) */
    }

    .user-candidate-column {
        font-size: 17px;
        color: #928F8F;
    }

    .card-user-name {
        color: #434343;
        font-size: 26px;
        font-weight: 700
    }

    .column-head {
        font-weight: 700;
        font-size: 26px;
    }

    .second-heading {
        font-size: 19px
    }

    .onboarding-button {
        display: flex;
        flex-direction: row;
        justify-content: space-evenly;
    }
</style>
@include('includes.footer')
