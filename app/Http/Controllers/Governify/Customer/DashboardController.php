<?php

namespace App\Http\Controllers\Governify\Customer;

use App\Http\Controllers\Controller;
use App\Traits\MondayApis;
use Illuminate\Http\Request;
use App\Models\GovernifyServiceRequest;
use App\Models\GovernifyServiceCategorie;
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
                ])->whereNull('deleted_at')->orderBy('service_categories_index')->get();

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
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $boardId = 1493464821;
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
                $query = 'mutation {
                    create_item(
                      board_id: ' . $boardId . '
                      group_id: "topics"
                      item_name: "' . $request->service_request . '"
                      column_values: "{\"form_infomation__1\":\"' . $formDataString . '\",\"people0__1\":{\"email\":\"' . $getUser->email . '\" ,\"text\":\"' . $getUser->email . '\"}}"
                    ) {
                      id
                    }
                }';

                $boardsData = $this->_getMondayData($query);
                if (isset($boardsData['response']['error_message'])) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => $boardsData['response']['error_message'])));
                }

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
                        }
                    }
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => 'Column Updated Successfully')));
                }
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
}