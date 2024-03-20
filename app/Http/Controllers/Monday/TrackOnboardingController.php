<?php

namespace App\Http\Controllers\Monday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MondayApis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class TrackOnboardingController extends Controller
{
    use  MondayApis;

    public function trackOnboarding(Request $request)
    {
        $query = request()->all();
        return $response = $this->_get($query);
    }

    public function trackOnboardingById(Request $request)
    {
        $query = "query {
            boards(ids: 1390329031) {
            columns {
               title
               id
            }activity_logs (from: "2023-03-14T00:00:00Z", to: "2024-03-18T00:00:00Z", column_ids:["status4","status7","status54","status6","status1"]) {
              id
              user_id
              account_id
              data
              entity
              event
              created_at
          }}
         items (ids: [1390668025]) {
           id
           name
           email
           column_values {
                   id
                      value
                      type
                      ... on StatusValue  {
                         label
                         update_id
                         index
                         value
                      }
               }
           }
       }";
        $query = request()->all();
        return $response = $this->_get($query);
    }

    public function trackOnboardingExport(Request $request)
    {

        // $query = request()->all();
        // $response = $this->_get($query);
        // if ($response['status_code'] == '200' && !empty($response['response']['data'])) {
        //     # code...
        // }

        // // Decode the JSON response
        // // $data = json_decode($response['response']['data'], true);
        // // $data = $response['response']['data'];

        // // Set headers for CSV download
        // header('Content-Type: text/csv');
        // header('Content-Disposition: attachment; filename="monday_com_data.csv"');

        // // Open file pointer
        // $output = fopen('php://output', 'w');

        // // Write headers
        // $headers = ['Name', 'Overall Status', 'Updates', 'Degree Attestation', 'Police Clearance', 'Medical Test', 'Visa Issuance', 'Visa / E-wakala', 'Nationality', 'Country of Residency', 'Profession', 'Hiring Type', 'Docs Group 1', 'Docs Group 2', 'Docs Group 3', 'Candidate Email Address', 'Is there specified joining date', 'Joining Date', 'Candidate Contact Number (Whatsapp Number)'];
        // fputcsv($output, $headers);

        // // Loop through items and write data
        // foreach ($response['response']['data']['boards'][0]['items_page']['items'] as $item) {
        //     $rowData = [
        //         $item['name'],
        //         $item['column_values'][0]['label'], // Overall Status
        //         json_decode($item['column_values'][1]['value'], true)['text'], // Updates
        //         $item['column_values'][2]['label'], // Degree Attestation
        //         $item['column_values'][3]['label'], // Police Clearance
        //         $item['column_values'][4]['label'], // Medical Test
        //         $item['column_values'][5]['label'], // Visa Issuance
        //         $item['column_values'][6]['label'], // Visa / E-wakala
        //         json_decode($item['column_values'][7]['value'], true)['countryName'], // Nationality
        //         json_decode($item['column_values'][8]['value'], true)['countryName'], // Country of Residency
        //         $item['column_values'][9]['value'], // Profession
        //         $item['column_values'][10]['label'], // Hiring Type
        //         $item['column_values'][11]['value'], // Docs Group 1
        //         ($item['column_values'][12]['value']), // Docs Group 2
        //         $item['column_values'][13]['value'], // Docs Group 3
        //         $item['column_values'][14]['value'], // Candidate Email Address
        //         // json_decode($item['column_values'][14]['value'], true)['email'], // Candidate Email Address
        //         $item['column_values'][15]['label'], // Is there specified joining date
        //         $item['column_values'][16]['value'], // Joining Date
        //         $item['column_values'][16]['value'], // Candidate Contact Number (Whatsapp Number)
        //     ];
        //     fputcsv($output, $rowData);
        //     // Store CSV file in storage folder
        //     // $filePath = storage_path('app/monday_data.csv');
        //     // file_put_contents($filePath, $csvContent);
        // }

        // // Close file pointer
        // Storage::put(storage_path('data.csv'), $output);
        // fclose($output);





            $query = "query {
                boards(ids: 1390329031) {
                   columns {
                      title
                      id
                   }
                   items_page (limit: 20, cursor:null) {
                      cursor
                      items {
                          id
                          name
                          email
                          column_values {
                             id
                             value
                             type
                             ... on StatusValue  {
                                label
                                update_id
                             }
                         }
                      }
                  }
                }
             }";

        // $query = request()->all();
        $response = $this->_get($query);

        if ($response['status_code'] == '200' && !empty($response['response']['data'])) {
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="monday_com_data.csv"');

            // Open file pointer
            $output = fopen('php://output', 'w');

            // Write headers
            $headers = ['Name', 'Overall Status', 'Updates', 'Degree Attestation', 'Police Clearance', 'Medical Test', 'Visa Issuance', 'Visa / E-wakala', 'Nationality', 'Country of Residency', 'Profession', 'Hiring Type', 'Docs Group 1', 'Docs Group 2', 'Docs Group 3', 'Candidate Email Address', 'Is there specified joining date', 'Joining Date', 'Candidate Contact Number (Whatsapp Number)'];
            fputcsv($output, $headers);

            // Loop through items and write data
            foreach ($response['response']['data']['boards'][0]['items_page']['items'] as $item) {

                $docLinkPrepare = 'https://tascksa.monday.com/boards/1390329031/pulses/'.$item['id'].'?asset_id=';

                if (!empty(json_decode($item['column_values'][11]['value'], true))) {
                    $docGroupAssetIds1 = array_column(json_decode($item['column_values'][11]['value'], true)['files'], 'assetId');

                    $docGroupResult1 = array();
                    foreach ($docGroupAssetIds1 as $id) {
                        $docGroupResult1[] = $docLinkPrepare . $id;
                    }

                    $docGroupResultString1 = implode(', ', $docGroupResult1);
                }

                if (!empty(json_decode($item['column_values'][12]['value'], true))) {
                    $docGroupAssetIds2 = array_column(json_decode($item['column_values'][12]['value'], true)['files'], 'assetId');

                    $docGroupResult2 = array();
                    foreach ($docGroupAssetIds2 as $id) {
                        $docGroupResult2[] = $docLinkPrepare . $id;
                    }

                    $docGroupResultString2 = implode(', ', $docGroupResult2);
                }

                if (!empty(json_decode($item['column_values'][13]['value'], true))) {
                    $docGroupAssetIds3 = array_column(json_decode($item['column_values'][13]['value'], true)['files'], 'assetId');

                    $docGroupResult3 = array();
                    foreach ($docGroupAssetIds3 as $id) {
                        $docGroupResult3[] = $docLinkPrepare . $id;
                    }

                    $docGroupResultString3 = implode(', ', $docGroupResult3);
                }

                $rowData = [
                    $item['name'],
                    $item['column_values'][0]['label'], // Overall Status
                    json_decode($item['column_values'][1]['value'], true)['text'], // Updates
                    $item['column_values'][2]['label'], // Degree Attestation
                    $item['column_values'][3]['label'], // Police Clearance
                    $item['column_values'][4]['label'], // Medical Test
                    $item['column_values'][5]['label'], // Visa Issuance
                    $item['column_values'][6]['label'], // Visa / E-wakala
                    json_decode($item['column_values'][7]['value'], true)['countryName'], // Nationality
                    json_decode($item['column_values'][8]['value'], true)['countryName'], // Country of Residency
                    $item['column_values'][9]['value'], // Profession
                    $item['column_values'][10]['label'], // Hiring Type
                    $docGroupResultString1 ?? '', // Docs Group 1
                    $docGroupResultString2 ?? '', // Docs Group 2
                    $docGroupResultString3 ?? '', // Docs Group 3
                    json_decode($item['column_values'][14]['value'], true)['email'], // Candidate Email Address
                    $item['column_values'][15]['label'], // Is there specified joining date
                    $item['column_values'][16]['value'], // Joining Date
                    json_decode($item['column_values'][17]['value'], true)['phone'], // Candidate Contact Number (Whatsapp Number)
                ];
            //    echo '<pre>'; print_r( $rowData ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
                fputcsv($output, $rowData);
            }

            // Close file pointer
            fclose($output);
            return Response::make('', 200, $headers);
            echo '<pre>'; print_r( 'v' ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
            // Save the CSV file to a local storage folder
            $filePath = storage_path('app/monday_com_data.csv');
            file_put_contents($filePath, file_get_contents('php://output'));
        }
    }
}
