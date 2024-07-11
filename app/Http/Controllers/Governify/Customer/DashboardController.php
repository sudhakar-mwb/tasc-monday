<?php

namespace App\Http\Controllers\Governify\Customer;

use App\Http\Controllers\Controller;
use App\Traits\MondayApis;
use Illuminate\Http\Request;
use App\Models\GovernifyServiceRequest;
use App\Models\GovernifyServiceCategorie;
use App\Models\GovernifySiteSetting;
use App\Models\MondayUsers;
use Illuminate\Support\Facades\Validator;
use CURLFile;
use DateTime;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DashboardController extends Controller
{
    use MondayApis;
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $dataToRender =  GovernifyServiceCategorie::with([
                    'service_category_request' => function ($query) {
                        $query->where([['governify_service_requests.deleted_at', '=', null]])->orderBy('service_categories_request_index')->get();
                    },
                    'service_category_request.form' => function ($query) {
                        $query->where([['governify_service_request_forms.deleted_at', '=', null]]);
                    },
                ])->whereNull('deleted_at')->orderByRaw('CASE WHEN service_categories_index IS NULL THEN 1 ELSE 0 END, service_categories_index')->get();

                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Request Data.")));
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function createRequestDashboard(Request $request)
    {
        // ini_set('post_max_size', '100M');
        // ini_set('upload_max_filesize', '100M');
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $GovernifySiteSettingData = GovernifySiteSetting::where('id', '=', 1)->first();
                $boardId = !empty($GovernifySiteSettingData['board_id']) ? $GovernifySiteSettingData['board_id'] : 1493464821;
                $ui_settings = json_decode($GovernifySiteSettingData['ui_settings'], true);
                
                $category_name_key = !empty($ui_settings['submitRequestKey']['category_name_key']) ? $ui_settings['submitRequestKey']['category_name_key'] : 'service_category__1';
                
                $form_information_key = !empty($ui_settings['submitRequestKey']['form_information_key']) ? $ui_settings['submitRequestKey']['form_information_key'] : 'form_infomation__1';
                
                $email = !empty($ui_settings['submitRequestKey']['email']) ? $ui_settings['submitRequestKey']['email'] : 'people0__1';
                
                $getUser = MondayUsers::getUser(['id' => $userId]);
                $formDataStrings = [];
                if (!empty($request->form_data)) {
                    foreach ($request->form_data as $index => $formData) {
                        foreach ($formData as $key => $value) {
                            $formDataStrings[] = "$key: $value";
                        }
                    }
                    $formDataString = implode(', ', $formDataStrings);
                }
                // need to dynamic mutation query
                $query = 'mutation {
                    create_item(
                      board_id: ' . $boardId . '
                      group_id: "topics"
                      item_name: "' . $request->service_request . '"
                      column_values: "{\"'.$category_name_key.'\":\"' . $request->service_category . '\",\"'.$form_information_key.'\":\"' . $formDataString . '\",\"'.$email.'\":{\"email\":\"' . $getUser->email . '\" ,\"text\":\"' . $getUser->email . '\"}}"
                    ) {
                      id
                    }
                }';

                $boardsData = $this->_getMondayData($query);
                if (isset($boardsData['response']['error_message'])) {
                    return response(json_encode(array('response' => $boardsData, 'status' => false, 'message' => $boardsData['response']['error_message'])));
                }

                if (isset($boardsData['response']['data']['create_item']['id'])) {
                    return response(json_encode(array('response' => $boardsData, 'status' => true, 'message' => "Item created successfully.")));
                }else{
                    return response(json_encode(array('response' => $boardsData, 'status' => false, 'message' => "Item creation failed.")));
                }
                /*
                if (isset($boardsData['response']['data']['create_item']['id'])) {
                    if (!empty($request->file_data)) {
                        $fileDataStrings = [];
                        foreach ($request->file_data as $index => $fileData) {

                            $imageData = $fileData['file_image'];
                            list($type, $data) = explode(';', $imageData);
                            list(, $data)      = explode(',', $data);
                            $data      = base64_decode($data);
                            $extension = explode('/', mime_content_type($imageData))[1];
                            $timestamp = now()->timestamp;

                            $updateFileName = $timestamp . '_' . $fileData['file_name'];
                            // File::put(public_path('uploads/governify/' . $updateFileName), $data);
                            // $imagePath = '/uploads/governify/' . $updateFileName;
                            File::put($_SERVER['DOCUMENT_ROOT'] . '/uploads/governify/' . $updateFileName, $data);
                            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/governify/' . $updateFileName;
                            // $filePath =  URL::to("/") . $imagePath;
                            $filePath =   $imagePath;

                            $query = 'mutation add_file($file: File!) { add_file_to_column (item_id: ' . $boardsData['response']['data']['create_item']['id'] . ', column_id: "files__1", file: $file) { id } }';
                            $map = '{"image": "variables.file"}';
                            $postFields = [
                                'query' => $query,
                                'map'   => $map,
                                'image' => new CURLFile($filePath)
                            ];
                            $fileUploadResponse = $this->imageUpload($postFields);

                            // // Get validated data
                            // $itemId   = $boardsData['response']['data']['create_item']['id'];
                            // $columnId = 'files__1';
                            // $fileData = $fileData['file_image'];
                            // $fileName = $fileData['file_name'];

                            // // Upload file to Monday.com
                            // $response = $this->uploadFileToMonday($itemId, $columnId, $fileData, $fileName);
                        }

                        // // After foreach loop
                        // if (!isset($response['data']['add_file_to_column']['id'])) {
                        //     return $this->returnData($response, false);
                        // }
                        // return $this->returnData($response);
                    }
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => 'Column Updated Successfully')));
                }
                */
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function imageUpload($postFields)
    {
        $endpoint = 'https://api.monday.com/v2/file';
        $apiKey   = env('MONDAY_API_KEY');

        // Initialize cURL
        $ch = curl_init();
        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiKey,
            'Content-Type: multipart/form-data'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

        $response = curl_exec($ch);
        // Close the cURL session
        curl_close($ch);

        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errors = curl_error($ch);
        return ['status_code' => $status_code, 'response' => json_decode($response, true), 'errors' => $curl_errors];
    }

    //upload file to the monday.com function 
    public function uploadFileToMonday($itemId, $columnId, $fileData, $fileName)
    {
        // $fileContent = base64_decode($fileData);
        $fileContent = file_get_contents($fileData);
        $client = new Client();

        $response = $client->post('https://api.monday.com/v2/file', [
            'headers' => [
                'Authorization' => env('MONDAY_API_KEY')
            ],
            'multipart' => [
                [
                    'name'     => 'query',
                    'contents' => "mutation (\$file: File!) {
                            add_file_to_column (item_id: $itemId, column_id: \"$columnId\", file: \$file) {
                                id
                                name
                                url
                            }
                        }"
                ],
                [
                    'name'     => 'variables[file]',
                    'contents' => $fileContent,
                    'filename' => $fileName
                ]
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function returnData($data, $success = true)
    {

        if ($success) {
            return [
                "success" => $success,
                "data" => $data
            ];
        }

        return [
            "success" => $success,
            "message" => $data
        ];
    }
    public function newResponseDashboard()
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $categories = GovernifyServiceCategorie::with(['serviceRequests', 'serviceFormMappings.serviceForm'])->whereNull('deleted_at')->orderByRaw('CASE WHEN service_categories_index IS NULL THEN 1 ELSE 0 END, service_categories_index')->get();

                $dataToRender = $categories->map(function ($category) {
                    return [
                        'category' => $category,
                        'service_requests' => $category->serviceRequests->whereNull('deleted_at')->map(function ($request) use ($category) {
                            $formMappings = $category->serviceFormMappings->where('service_id', $request->id);
                            return [
                                'service_request' => $request,
                                'service_forms' => $formMappings->map(function ($mapping) {
                                    return $mapping->serviceForm;
                                })
                            ];
                        })
                    ];
                });

                // $dataToRender =  $categories = GovernifyServiceCategorie::with([
                //     'serviceRequests.serviceFormMappings.serviceForm'
                // ])->get();

                // $dataToRender =  $categories = GovernifyServiceCategorie::with([
                //     'serviceRequests.serviceFormMappings.serviceForm',
                //     'serviceFormMappings.serviceRequest',
                //     'serviceFormMappings.serviceCategory'
                // ])->get();

                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Request Data.")));
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}
