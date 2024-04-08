@include('includes.header')


<?php
// dd(['data'=>$data,'response'=>$response]);
$countryInfo = [
    'AF' => ['flag' => '🇦🇫', 'calling_code' => '+93'],
    'AL' => ['flag' => '🇦🇱', 'calling_code' => '+355'],
    'DZ' => ['flag' => '🇩🇿', 'calling_code' => '+213'],
    'AD' => ['flag' => '🇦🇩', 'calling_code' => '+376'],
    'AO' => ['flag' => '🇦🇴', 'calling_code' => '+244'],
    'AR' => ['flag' => '🇦🇷', 'calling_code' => '+54'],
    'AM' => ['flag' => '🇦🇲', 'calling_code' => '+374'],
    'AU' => ['flag' => '🇦🇺', 'calling_code' => '+61'],
    'AT' => ['flag' => '🇦🇹', 'calling_code' => '+43'],
    'AZ' => ['flag' => '🇦🇿', 'calling_code' => '+994'],
    'BS' => ['flag' => '🇧🇸', 'calling_code' => '+1'],
    'BH' => ['flag' => '🇧🇭', 'calling_code' => '+973'],
    'BD' => ['flag' => '🇧🇩', 'calling_code' => '+880'],
    'BB' => ['flag' => '🇧🇧', 'calling_code' => '+1'],
    'BY' => ['flag' => '🇧🇾', 'calling_code' => '+375'],
    'BE' => ['flag' => '🇧🇪', 'calling_code' => '+32'],
    'BZ' => ['flag' => '🇧🇿', 'calling_code' => '+501'],
    'BJ' => ['flag' => '🇧🇯', 'calling_code' => '+229'],
    'BT' => ['flag' => '🇧🇹', 'calling_code' => '+975'],
    'BO' => ['flag' => '🇧🇴', 'calling_code' => '+591'],
    'BA' => ['flag' => '🇧🇦', 'calling_code' => '+387'],
    'BW' => ['flag' => '🇧🇼', 'calling_code' => '+267'],
    'BR' => ['flag' => '🇧🇷', 'calling_code' => '+55'],
    'BN' => ['flag' => '🇧🇳', 'calling_code' => '+673'],
    'BG' => ['flag' => '🇧🇬', 'calling_code' => '+359'],
    'BF' => ['flag' => '🇧🇫', 'calling_code' => '+226'],
    'BI' => ['flag' => '🇧🇮', 'calling_code' => '+257'],
    'KH' => ['flag' => '🇰🇭', 'calling_code' => '+855'],
    'CM' => ['flag' => '🇨🇲', 'calling_code' => '+237'],
    'CA' => ['flag' => '🇨🇦', 'calling_code' => '+1'],
    'CV' => ['flag' => '🇨🇻', 'calling_code' => '+238'],
    'CF' => ['flag' => '🇨🇫', 'calling_code' => '+236'],
    'TD' => ['flag' => '🇹🇩', 'calling_code' => '+235'],
    'CL' => ['flag' => '🇨🇱', 'calling_code' => '+56'],
    'CN' => ['flag' => '🇨🇳', 'calling_code' => '+86'],
    'CO' => ['flag' => '🇨🇴', 'calling_code' => '+57'],
    'KM' => ['flag' => '🇰🇲', 'calling_code' => '+269'],
    'CG' => ['flag' => '🇨🇬', 'calling_code' => '+242'],
    'CD' => ['flag' => '🇨🇩', 'calling_code' => '+243'],
    'CR' => ['flag' => '🇨🇷', 'calling_code' => '+506'],
    'HR' => ['flag' => '🇭🇷', 'calling_code' => '+385'],
    'CU' => ['flag' => '🇨🇺', 'calling_code' => '+53'],
    'CY' => ['flag' => '🇨🇾', 'calling_code' => '+357'],
    'CZ' => ['flag' => '🇨🇿', 'calling_code' => '+420'],
    'DK' => ['flag' => '🇩🇰', 'calling_code' => '+45'],
    'DJ' => ['flag' => '🇩🇯', 'calling_code' => '+253'],
    'DM' => ['flag' => '🇩🇲', 'calling_code' => '+1'],
    'DO' => ['flag' => '🇩🇴', 'calling_code' => '+1'],
    'EC' => ['flag' => '🇪🇨', 'calling_code' => '+593'],
    'EG' => ['flag' => '🇪🇬', 'calling_code' => '+20'],
    'SV' => ['flag' => '🇸🇻', 'calling_code' => '+503'],
    'GQ' => ['flag' => '🇬🇶', 'calling_code' => '+240'],
    'ER' => ['flag' => '🇪🇷', 'calling_code' => '+291'],
    'EE' => ['flag' => '🇪🇪', 'calling_code' => '+372'],
    'ET' => ['flag' => '🇪🇹', 'calling_code' => '+251'],
    'FJ' => ['flag' => '🇫🇯', 'calling_code' => '+679'],
    'FI' => ['flag' => '🇫🇮', 'calling_code' => '+358'],
    'FR' => ['flag' => '🇫🇷', 'calling_code' => '+33'],
    'GA' => ['flag' => '🇬🇦', 'calling_code' => '+241'],
    'GM' => ['flag' => '🇬🇲', 'calling_code' => '+220'],
    'GE' => ['flag' => '🇬🇪', 'calling_code' => '+995'],
    'DE' => ['flag' => '🇩🇪', 'calling_code' => '+49'],
    'GH' => ['flag' => '🇬🇭', 'calling_code' => '+233'],
    'GR' => ['flag' => '🇬🇷', 'calling_code' => '+30'],
    'GD' => ['flag' => '🇬🇩', 'calling_code' => '+1'],
    'GT' => ['flag' => '🇬🇹', 'calling_code' => '+502'],
    'GN' => ['flag' => '🇬🇳', 'calling_code' => '+224'],
    'GW' => ['flag' => '🇬🇼', 'calling_code' => '+245'],
    'GY' => ['flag' => '🇬🇾', 'calling_code' => '+592'],
    'HT' => ['flag' => '🇭🇹', 'calling_code' => '+509'],
    'HN' => ['flag' => '🇭🇳', 'calling_code' => '+504'],
    'HU' => ['flag' => '🇭🇺', 'calling_code' => '+36'],
    'IS' => ['flag' => '🇮🇸', 'calling_code' => '+354'],
    'IN' => ['flag' => '🇮🇳', 'calling_code' => '+91'],
    'ID' => ['flag' => '🇮🇩', 'calling_code' => '+62'],
    'IR' => ['flag' => '🇮🇷', 'calling_code' => '+98'],
    'IQ' => ['flag' => '🇮🇶', 'calling_code' => '+964'],
    'IE' => ['flag' => '🇮🇪', 'calling_code' => '+353'],
    'IL' => ['flag' => '🇮🇱', 'calling_code' => '+972'],
    'IT' => ['flag' => '🇮🇹', 'calling_code' => '+39'],
    'JM' => ['flag' => '🇯🇲', 'calling_code' => '+1'],
    'JP' => ['flag' => '🇯🇵', 'calling_code' => '+81'],
    'JO' => ['flag' => '🇯🇴', 'calling_code' => '+962'],
    'KZ' => ['flag' => '🇰🇿', 'calling_code' => '+7'],
    'KE' => ['flag' => '🇰🇪', 'calling_code' => '+254'],
    'KI' => ['flag' => '🇰🇮', 'calling_code' => '+686'],
    'KP' => ['flag' => '🇰🇵', 'calling_code' => '+850'],
    'KR' => ['flag' => '🇰🇷', 'calling_code' => '+82'],
    'KW' => ['flag' => '🇰🇼', 'calling_code' => '+965'],
    'KG' => ['flag' => '🇰🇬', 'calling_code' => '+996'],
    'LA' => ['flag' => '🇱🇦', 'calling_code' => '+856'],
    'LV' => ['flag' => '🇱🇻', 'calling_code' => '+371'],
    'LB' => ['flag' => '🇱🇧', 'calling_code' => '+961'],
    'LS' => ['flag' => '🇱🇸', 'calling_code' => '+266'],
    'LR' => ['flag' => '🇱🇷', 'calling_code' => '+231'],
    'LY' => ['flag' => '🇱🇾', 'calling_code' => '+218'],
    'LI' => ['flag' => '🇱🇮', 'calling_code' => '+423'],
    'LT' => ['flag' => '🇱🇹', 'calling_code' => '+370'],
    'LU' => ['flag' => '🇱🇺', 'calling_code' => '+352'],
    'MG' => ['flag' => '🇲🇬', 'calling_code' => '+261'],
    'MW' => ['flag' => '🇲🇼', 'calling_code' => '+265'],
    'MY' => ['flag' => '🇲🇾', 'calling_code' => '+60'],
    'MV' => ['flag' => '🇲🇻', 'calling_code' => '+960'],
    'ML' => ['flag' => '🇲🇱', 'calling_code' => '+223'],
    'MT' => ['flag' => '🇲🇹', 'calling_code' => '+356'],
    'MH' => ['flag' => '🇲🇭', 'calling_code' => '+692'],
    'MR' => ['flag' => '🇲🇷', 'calling_code' => '+222'],
    'MU' => ['flag' => '🇲🇺', 'calling_code' => '+230'],
    'MX' => ['flag' => '🇲🇽', 'calling_code' => '+52'],
    'FM' => ['flag' => '🇫🇲', 'calling_code' => '+691'],
    'MD' => ['flag' => '🇲🇩', 'calling_code' => '+373'],
    'MC' => ['flag' => '🇲🇨', 'calling_code' => '+377'],
    'MN' => ['flag' => '🇲🇳', 'calling_code' => '+976'],
    'ME' => ['flag' => '🇲🇪', 'calling_code' => '+382'],
    'MA' => ['flag' => '🇲🇦', 'calling_code' => '+212'],
    'MZ' => ['flag' => '🇲🇿', 'calling_code' => '+258'],
    'MM' => ['flag' => '🇲🇲', 'calling_code' => '+95'],
    'NA' => ['flag' => '🇳🇦', 'calling_code' => '+264'],
    'NR' => ['flag' => '🇳🇷', 'calling_code' => '+674'],
    'NP' => ['flag' => '🇳🇵', 'calling_code' => '+977'],
    'NL' => ['flag' => '🇳🇱', 'calling_code' => '+31'],
    'NZ' => ['flag' => '🇳🇿', 'calling_code' => '+64'],
    'NI' => ['flag' => '🇳🇮', 'calling_code' => '+505'],
    'NE' => ['flag' => '🇳🇪', 'calling_code' => '+227'],
    'NG' => ['flag' => '🇳🇬', 'calling_code' => '+234'],
    'MK' => ['flag' => '🇲🇰', 'calling_code' => '+389'],
    'NO' => ['flag' => '🇳🇴', 'calling_code' => '+47'],
    'OM' => ['flag' => '🇴🇲', 'calling_code' => '+968'],
    'PK' => ['flag' => '🇵🇰', 'calling_code' => '+92'],
    'PW' => ['flag' => '🇵🇼', 'calling_code' => '+680'],
    'PA' => ['flag' => '🇵🇦', 'calling_code' => '+507'],
    'PG' => ['flag' => '🇵🇬', 'calling_code' => '+675'],
    'PY' => ['flag' => '🇵🇾', 'calling_code' => '+595'],
    'PE' => ['flag' => '🇵🇪', 'calling_code' => '+51'],
    'PH' => ['flag' => '🇵🇭', 'calling_code' => '+63'],
    'PL' => ['flag' => '🇵🇱', 'calling_code' => '+48'],
    'PT' => ['flag' => '🇵🇹', 'calling_code' => '+351'],
    'QA' => ['flag' => '🇶🇦', 'calling_code' => '+974'],
    'RO' => ['flag' => '🇷🇴', 'calling_code' => '+40'],
    'RU' => ['flag' => '🇷🇺', 'calling_code' => '+7'],
    'RW' => ['flag' => '🇷🇼', 'calling_code' => '+250'],
    'KN' => ['flag' => '🇰🇳', 'calling_code' => '+1'],
    'LC' => ['flag' => '🇱🇨', 'calling_code' => '+1'],
    'VC' => ['flag' => '🇻🇨', 'calling_code' => '+1'],
    'WS' => ['flag' => '🇼🇸', 'calling_code' => '+685'],
    'SM' => ['flag' => '🇸🇲', 'calling_code' => '+378'],
    'ST' => ['flag' => '🇸🇹', 'calling_code' => '+239'],
    'SA' => ['flag' => '🇸🇦', 'calling_code' => '+966'],
    'SN' => ['flag' => '🇸🇳', 'calling_code' => '+221'],
    'RS' => ['flag' => '🇷🇸', 'calling_code' => '+381'],
    'SC' => ['flag' => '🇸🇨', 'calling_code' => '+248'],
    'SL' => ['flag' => '🇸🇱', 'calling_code' => '+232'],
    'SG' => ['flag' => '🇸🇬', 'calling_code' => '+65'],
    'SK' => ['flag' => '🇸🇰', 'calling_code' => '+421'],
    'SI' => ['flag' => '🇸🇮', 'calling_code' => '+386'],
    'SB' => ['flag' => '🇸🇧', 'calling_code' => '+677'],
    'SO' => ['flag' => '🇸🇴', 'calling_code' => '+252'],
    'ZA' => ['flag' => '🇿🇦', 'calling_code' => '+27'],
    'ES' => ['flag' => '🇪🇸', 'calling_code' => '+34'],
    'LK' => ['flag' => '🇱🇰', 'calling_code' => '+94'],
    'SD' => ['flag' => '🇸🇩', 'calling_code' => '+249'],
    'SR' => ['flag' => '🇸🇷', 'calling_code' => '+597'],
    'SZ' => ['flag' => '🇸🇿', 'calling_code' => '+268'],
    'SE' => ['flag' => '🇸🇪', 'calling_code' => '+46'],
    'CH' => ['flag' => '🇨🇭', 'calling_code' => '+41'],
    'SY' => ['flag' => '🇸🇾', 'calling_code' => '+963'],
    'TW' => ['flag' => '🇹🇼', 'calling_code' => '+886'],
    'TJ' => ['flag' => '🇹🇯', 'calling_code' => '+992'],
    'TZ' => ['flag' => '🇹🇿', 'calling_code' => '+255'],
    'TH' => ['flag' => '🇹🇭', 'calling_code' => '+66'],
    'TL' => ['flag' => '🇹🇱', 'calling_code' => '+670'],
    'TG' => ['flag' => '🇹🇬', 'calling_code' => '+228'],
    'TO' => ['flag' => '🇹🇴', 'calling_code' => '+676'],
    'TT' => ['flag' => '🇹🇹', 'calling_code' => '+1'],
    'TN' => ['flag' => '🇹🇳', 'calling_code' => '+216'],
    'TR' => ['flag' => '🇹🇷', 'calling_code' => '+90'],
    'TM' => ['flag' => '🇹🇲', 'calling_code' => '+993'],
    'TV' => ['flag' => '🇹🇻', 'calling_code' => '+688'],
    'UG' => ['flag' => '🇺🇬', 'calling_code' => '+256'],
    'UA' => ['flag' => '🇺🇦', 'calling_code' => '+380'],
    'AE' => ['flag' => '🇦🇪', 'calling_code' => '+971'],
    'GB' => ['flag' => '🇬🇧', 'calling_code' => '+44'],
    'US' => ['flag' => '🇺🇸', 'calling_code' => '+1'],
    'UY' => ['flag' => '🇺🇾', 'calling_code' => '+598'],
    'UZ' => ['flag' => '🇺🇿', 'calling_code' => '+998'],
    'VU' => ['flag' => '🇻🇺', 'calling_code' => '+678'],
    'VA' => ['flag' => '🇻🇦', 'calling_code' => '+379'],
    'VE' => ['flag' => '🇻🇪', 'calling_code' => '+58'],
    'VN' => ['flag' => '🇻🇳', 'calling_code' => '+84'],
    'YE' => ['flag' => '🇾🇪', 'calling_code' => '+967'],
    'ZM' => ['flag' => '🇿🇲', 'calling_code' => '+260'],
    'ZW' => ['flag' => '🇿🇼', 'calling_code' => '+263'],
];
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
$hiringType = findElementByTitle('Hiring Type', $columns, $trackdata, 'label');
$nationality = json_decode(findElementByTitle('Country of Residency', $columns, $trackdata, 'value'), true) ?? '';
$nationality = ($nationality['countryName'] ?? '') . ' ' . ($countryInfo[$nationality['countryCode']]['flag'] ?? '');
$countryOfResidence = json_decode(findElementByTitle('Nationality', $columns, $trackdata, 'value'), true);
$countryOfResidence = ($countryOfResidence['countryName'] ?? '') . ' ' . ($countryInfo[$countryOfResidence['countryCode']]['flag'] ?? '');
$updatesMsg = json_decode(findElementByTitle('Updates', $columns, $trackdata, 'value'), true) ?? '';
$whatsappNumber = findElementByTitle('Candidate Contact Number (Whatsapp Number)', $columns, $trackdata, 'value');
$VisaIssuanceValue = json_decode(findElementByTitle('Visa Issuance', $columns, $trackdata, 'value'), true);
$VisaIssuancestatus = findElementByTitle('Visa Issuance', $columns, $trackdata, 'label');

if ($whatsappNumber !== null) {
    $number_details = json_decode($whatsappNumber, true);
    $whatsappNumber = $countryInfo[$number_details['countryShortName']]['calling_code'] ?? '';
    if ($whatsappNumber !== '') {
        $whatsappNumber = '(' . $whatsappNumber . ') ';
    }
    $whatsappNumber = $whatsappNumber . ($number_details['phone'] ?? '');
}
$joiningDate = findElementByTitle('Joining Date', $columns, $trackdata, 'value');
if ($joiningDate !== null) {
    $joiningDate = dateFormater(json_decode($joiningDate, true)['date']);
}
$onboardings = ['Visa Issuance', 'Visa / E-wakala', 'Degree Attestation', 'Police Clearance'];
?>
<main class="px-3 pt-5">
    <div class="w-100 mt-3">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"> <a class="inactive link-secondary text-decoration-none"
                        href="/monday/form"><u> {{ ucwords('Command Center') }}</u></a></li>
                <li class="breadcrumb-item active"> <a class="inactive link-secondary text-decoration-none"
                        href="/monday/form/track-request"><u> {{ $requestTracking }}</u></a></li>
                <li class="breadcrumb-item active"> <a class="inactive link-primary text-decoration-none"
                        href="">
                        {{ $name }}</a></li>


            </ol>
        </nav>
    </div>
    <div class="w-100">
        <div class="d-flex mt-5 w-100" style="gap:20px">
            <div class="col-6 d-flex flex-column" style="gap:30px">
                <div class="d-flex mb-2" style="gap:16px">
                    <div class="rounded-circle bg-{{ getClass($profileStatus) }} p-4">
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
                        <h5 class="text-start m-0">{{ $name }}</h5>
                        <p class="profession m-0 text-start text-secondary" style="font-weight: 400">
                            <?php
                            $str = [];
                            for ($cnt = 0; $cnt < count($sub_headings_column); $cnt++) {
                                $el = $sub_headings_column[$cnt];
                            }
                            $txt = getValueById($columns_val, $el['id'], 'text');
                            echo($txt);
                            if ($txt) {
                                array_push($str, $txt);
                                if($cnt > 0 && $cnt !== count($sub_headings_column) - 1 && $txt !== '')
                                array_push($str, '|');
                              }
                            ?>

{{ implode(" ", $str)}}
                        </p>
                        <h6 class="status m-0 text-start text-{{ getClass($profileStatus) }} fw-bold">
                            {{ $profileStatus }}</h6>
                    </div>
                </div>
                <div class="w-100">
                    <div class="card border-0 border-1 p-4">
                        <h4 class="text-start head-color fw-bold pb-4 border-bottom">Candidate Information</h4>
                        <ul class="list-group list-group-flush">
                            @foreach ($candidate_coulmns as $col)
                                <li class="list-group-item d-flex align-items-center border-0 text-start"
                                    style="background: inherit;gap:16px"><span>
                                        <i class="bi {{ $col['icon'] }}"></i>
                                    </span><span><strong>{{ $col['custom_title'] }}&nbsp;:&nbsp;</strong>{{ getValueById($columns_val, $col['id'], 'text') ?? $countryOfResidence }}</span>
                                </li>
                            @endforeach
                            {{-- <li class="list-group-item d-flex align-items-center border-0 text-start"
                                style="background: inherit;gap:16px"><span><svg xmlns="http://www.w3.org/2000/svg"
                                        width="16" height="16" fill="currentColor" class="bi bi-flag-fill"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M14.778.085A.5.5 0 0 1 15 .5V8a.5.5 0 0 1-.314.464L14.5 8l.186.464-.003.001-.006.003-.023.009a12 12 0 0 1-.397.15c-.264.095-.631.223-1.047.35-.816.252-1.879.523-2.71.523-.847 0-1.548-.28-2.158-.525l-.028-.01C7.68 8.71 7.14 8.5 6.5 8.5c-.7 0-1.638.23-2.437.477A20 20 0 0 0 3 9.342V15.5a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 1 0v.282c.226-.079.496-.17.79-.26C4.606.272 5.67 0 6.5 0c.84 0 1.524.277 2.121.519l.043.018C9.286.788 9.828 1 10.5 1c.7 0 1.638-.23 2.437-.477a20 20 0 0 0 1.349-.476l.019-.007.004-.002h.001" />
                                    </svg></span><span>{{ $countryOfResidence }}</span></li>
                            <li class="list-group-item d-flex align-items-center border-0 text-start"
                                style="background: inherit;gap:16px"><span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z" />
                                        <path
                                            d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" />
                                    </svg></span>{{ $nationality }} <span></span></li>
                            <li class="list-group-item d-flex align-items-center border-0 text-start"
                                style="background: inherit;gap:16px"><span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                                    </svg></span><span>{{ $whatsappNumber }}</span></li>
                            <li class="list-group-item d-flex align-items-center border-0 text-start"
                                style="background: inherit;gap:16px"><span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-calendar4-range" viewBox="0 0 16 16">
                                        <path
                                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1z" />
                                        <path
                                            d="M9 7.5a.5.5 0 0 1 .5-.5H15v2H9.5a.5.5 0 0 1-.5-.5zm-2 3v1a.5.5 0 0 1-.5.5H1v-2h5.5a.5.5 0 0 1 .5.5" />
                                    </svg></span><span>Joining Date | {{ $joiningDate }}</span></li> --}}

                        </ul>
                    </div>
                    <div class="card border-0 border-1 p-4">
                        <h4 class="text-start head-color fw-bold pb-4 border-bottom">Onboarding Status</h4>
                        <ul class="list-group list-group-flush">
                            @foreach ($onboarding_columns as $step)
                                <?php
                                
                                $valued = json_decode(getValueById($columns_val, $step['id'], 'value'), true);
                                // dd($valued);
                                // json_decode(getValueById($step, $columns, $trackdata, 'value'), true);
                                $status = getValueById($columns_val, $step['id'], 'text');
                                ?>
                                <li class="list-group-item d-flex align-items-start border-0 text-start mb-1"
                                    style="background: inherit;gap:10px">
                                    <span style="width: 20px;height:20px"
                                        class="text-{{ getClass(Str::upper($status)) }} mt-1">
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
                                                {{ $valued !== null ? dateFormater($valued['changed_at']) : 'Pending' }}
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 border-1 p-4" style="background: rgba(111, 116, 144, 0.06)">
                    <h5 class="text-start head-color fw-bold pb-4 border-bottom">Onboarding Updates</h5>
                    <h6 class="text-start mt-2 mb-4 fw-bold text-secondary">
                        {{ dateFormater($created_at) ?? '' }}</h6>
                    <p class="text-start text-secondary">
                        {{ getValueById($columns_val, $onboarding_updates_columns, 'text') ?? '' }}</p>

                </div>
            </div>
        </div>
    </div>
</main>
@include('includes.footer')
