<?php

namespace App\Http\Controllers\Governify\Customer;

use App\Http\Controllers\Controller;
use App\Models\MondayUsers;
use App\Traits\MondayApis;
use Illuminate\Http\Request;
use CURLFile;
use Illuminate\Support\Facades\File;

class GovernifyRequestTrackingController extends Controller
{
    use MondayApis;

    public function requestTracking(Request $request)
    {

        // Validate the input
        $validatedData = $request->validate([
            'query_params'          => 'array',
            'query_params.order_by' => 'nullable|array',
            'query_params.order_by.*.direction' => 'required_with:in:asc,desc',
            'query_params.order_by.*.column_id' => 'required_with:query_params.order_by|string',
            'query_params.rules' => 'nullable|array',
            'query_params.rules.*.column_id' => 'required_with:query_params.rules|string',
            'query_params.rules.*.compare_value' => 'required_with:query_params.rules|array',
            'query_params.operator' => 'nullable|string|in:and,or'
        ]);

        if (!empty($validatedData['query_params'])) {
            $queryParams = json_encode($validatedData['query_params']);
            // Remove the surrounding double quotes from the JSON string
            $queryParams = str_replace(['"{', '}"'], ['{', '}'], $queryParams);
            $queryParams = str_replace(['"direction":', '"column_id":', '"compare_value":', '"operator":', '"rules":', '"order_by":'], ['direction:', 'column_id:', 'compare_value:', 'operator:', 'rules:', 'order_by:'], $queryParams);


            // Manually format the query parameters string to match the required format
            // $queryParams = str_replace(['"{', '}"'], ['{', '}'], $queryParams);
            // $queryParams = preg_replace('/"(\w+)":/u', '$1:', $queryParams);
            // Specifically replace the values for direction to be unquoted
            $queryParams = str_replace(['"asc"', '"desc"', '"and"'], ['asc', 'desc', 'and'], $queryParams);
        }



        $query = 'query {
            boards(limit: 500, ids: 1493464821) {
            id
            name
            state
            permissions
            board_kind
            columns {
                      title
                      id
                      archived
                      description
                      settings_str
                      title
                      type
                      width
                  }
                  items_page (limit: 3, cursor:null,  query_params: ' . (!empty($queryParams) ? $queryParams : '{}') . '  ){
                      cursor,
                      items {
                          created_at
                          creator_id
                          email
                          id
                          name
                          relative_link
                          state
                          updated_at
                          url
                          column_values {
                             id
                             value
                             type
                             text
                             ... on StatusValue  {
                                label
                                update_id
                             }
                         }updates (limit: 5000) {
                            assets {
                                created_at
                                file_extension
                                file_size
                                id
                                name
                                original_geometry
                                public_url
                                url
                                url_thumbnail 
                                }
                            body
                            text_body
                            created_at
                            creator_id
                            id
                            item_id
                            replies {
                                body
                                created_at
                                creator_id
                                id
                                text_body
                                updated_at
                            }
                            updated_at
                            text_body
                            creator {
                              name
                              id
                              email
                            }
                          } 
                         subitems {
                          created_at
                          creator_id
                          email
                          id
                          name
                          relative_link
                          state
                          updated_at
                          url
                          column_values {
                          id
                          value
                          type
                          text
                          ... on StatusValue  {
                             label
                             update_id
                                  }
                              }
                          }
                      }
                  }
          }
        }';

        return $boardsData = $this->_getMondayData($query);
    }

    public function reverseCancelRequest(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                // Validate the input
                $validatedData = $request->validate([
                    'board_id'   => 'required',
                    'pulse_id'   => 'required',
                    // 'from'       => 'required',
                    // 'to'         => 'required',
                    'column_id'  => 'required',
                ]);

                $query = '{
                    boards(limit: 500, ids: ' . $request->board_id . ') {
                             id
                             name
                             state
                             permissions
                             board_kind
                             activity_logs (limit: 10,column_ids:["' . $request->column_id . '"], item_ids : [' . $request->pulse_id . ']) {
                                account_id
                                id
                                data
                                entity
                                event
                                user_id
                                created_at                        
                             }
                    }
                 }';

                $boardsData = $this->_getMondayData($query);

                if (isset($boardsData['response']['errors']) || empty($boardsData['response']['data']['boards'][0]['activity_logs'])) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => 'Something Went Wrong. Last Status Not Found')));
                }
                if (!empty($boardsData['response']['data']['boards'][0]['activity_logs'])) {
                    foreach ($boardsData['response']['data']['boards'][0]['activity_logs'] as $key => $value) {
                        $data = json_decode($value['data'], true);
                        $previous_value = $data['previous_value']['label']['text'];

                        $query = 'mutation {
                            change_simple_column_value (board_id: ' . $request->board_id . ', item_id: ' . $request->pulse_id . ', column_id: "' . $request->column_id . '", value: "' . $previous_value . '") {
                              id
                            }
                          }';
                        $boardsData = $this->_getMondayData($query);
                        if (isset($boardsData['response']['errors'])) {
                            return response(json_encode(array('response' => [], 'status' => false, 'message' => $boardsData['response']['errors'][0]['message'])));
                        }
                        if (isset($boardsData['response']['data']['change_simple_column_value']['id'])) {
                            return response(json_encode(array('response' => [], 'status' => true, 'message' => 'Column Updated Successfully')));
                        }
                        break;
                    }
                }
                return $boardsData;
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function cancelRequest(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {

                $validatedData = $request->validate([
                    'board_id'   => 'required',
                    'request_id' => 'required',
                    'column_id'  => 'required',
                    'value'      => 'required',
                ]);

                $query = 'mutation {
                    change_simple_column_value (board_id: ' . $request->board_id . ', item_id: ' . $request->request_id . ', column_id: "' . $request->column_id . '", value: "' . $request->value . '") {
                      id
                    }
                  }';
                $boardsData = $this->_getMondayData($query);
                if (isset($boardsData['response']['errors'])) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => $boardsData['response']['errors'][0]['message'])));
                }
                if (isset($boardsData['response']['data']['change_simple_column_value']['id'])) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => 'Column Updated Successfully')));
                }
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function exportGovernifyData(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $boardId = 1493464821;

                $after      = 'ddd';
                $tolalData  = 500;
                $cursor     = 'null';
                do {
                    $query = 'query {
                        boards( ids: ' . $boardId . ') {
                        id
                        name
                        state
                        permissions
                        board_kind
                        columns {
                                  title
                                  id
                                  archived
                                  description
                                  settings_str
                                  title
                                  type
                                  width
                              }
                              items_page (limit: ' . $tolalData . ', cursor:' . $cursor . '){
                                  cursor,
                                  items {
                                      created_at
                                      creator_id
                                      email
                                      id
                                      name
                                      relative_link
                                      state
                                      updated_at
                                      url
                                      column_values {
                                         id
                                         value
                                         type
                                         text
                                         ... on StatusValue  {
                                            label
                                            update_id
                                         }
                                     }
                                  }
                              }
                      }
                    }';
                    $boardsData = $this->_getMondayData($query);

                    if (!empty($boardsData['response']['data']['boards'][0]['items_page']['cursor'])) {
                        $cursor =  "\"" . $boardsData['response']['data']['boards'][0]['items_page']['cursor'] . "\"";
                    } else {
                        $after = '';
                    }
                    $curr_data = isset($boardsData['response']['data']['boards'][0]['items_page']['items']) ? $boardsData['response']['data']['boards'][0]['items_page']['items'] : [];
                    if (!empty($curr_data)) {
                        if (count($curr_data))
                            foreach ($curr_data as $item) {
                                $mondayData[] = $item;
                            }
                    }
                    $newResponse = $boardsData;
                } while (!empty($after));

                unset($newResponse['response']['data']['boards'][0]['items_page']['items']);
                $newResponse['response']['data']['boards'][0]['items_page']['items'] = $mondayData;

                if (!empty($newResponse['response']['data']['boards'][0]['items_page']['items'])) {

                    // Set headers for CSV download
                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="governify_data.csv"');
                    // Open file pointer
                    $output = fopen('php://output', 'w');

                    $csvHeader = ["Name Of Service", "Created Date", "Status", "Form Information"];
                    fputcsv($output, $csvHeader);

                    $rowData = [];
                    foreach ($newResponse['response']['data']['boards'][0]['items_page']['items'] as $item) {
                        if (!empty($item['column_values'])) {
                            $rowData = [
                                !empty($item['name']) ? $item['name'] : '',
                                !empty($item['created_at']) ? $item['created_at'] : '',
                                !empty($item['column_values'][1]['label']) ? $item['column_values'][1]['label'] : '',
                                !empty($item['column_values'][3]['text']) ? $item['column_values'][3]['text'] : '',
                            ];
                        }

                        fputcsv($output, $rowData);
                    }
                    fclose($output);
                    return true;
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => 'Governify Board Items Not Found.')));
                }
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function addGovernifyComment(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $getUser = MondayUsers::getUser(['id' => $userId]);
                $validatedData = $request->validate([
                    'item_id'  => 'required',
                    'message'  => 'required',
                ]);

                $query = 'mutation {
                    create_update (item_id: ' . $request->item_id . ', body: "' . $getUser->email . ' : ' . $request->message . '") {
                      id
                    }
                  }';

                $boardsData = $this->_getMondayData($query);

                if (isset($boardsData['response']['errors'])) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => $boardsData['response']['errors'][0]['message'])));
                }
                if (isset($boardsData['response']['data']['create_update']['id'])) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => 'Comment Added Successfully')));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => 'Something Went Wrong. Comment Not Added. Please Try To Re-Comment')));
                }
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function addGovernifyLike(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $getUser = MondayUsers::getUser(['id' => $userId]);
                $validatedData = $request->validate([
                    'item_id'  => 'required'
                ]);

                $query = 'mutation {
                    like_update (update_id: ' . $request->item_id . ') {
                      id
                    }
                  }';

                $boardsData = $this->_getMondayData($query);

                if (isset($boardsData['response']['errors'])) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => $boardsData['response']['errors'][0]['message'])));
                }
                if (isset($boardsData['response']['data']['like_update']['id'])) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => 'Thank Your For Like.')));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => 'Something Went Wrong. Not Liked. Please Try To Re-Like')));
                }
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function uploadGovernifyDocument(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {

                $validatedData = $request->validate([
                    'item_id'     => 'required',
                    'file_name'   => 'required',
                    'file_image'  => 'required',
                ]);

                $imageData = $request->file_image;
                list($type, $data) = explode(';', $imageData);
                list(, $data)      = explode(',', $data);
                $data      = base64_decode($data);
                $extension = explode('/', mime_content_type($imageData))[1];
                $timestamp = now()->timestamp;

                $updateFileName = $timestamp . '_' . $request->file_name;

                // The file you want to upload
                // $imagePath = '/home/cedcoss/Desktop/brevo.png';

                // File::put(public_path('uploads/governify/' . $updateFileName), $data);
                // $imagePath = '/uploads/governify/' . $updateFileName;
                File::put($_SERVER['DOCUMENT_ROOT'] . '/uploads/governify/' . $updateFileName, $data);
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/uploads/governify/' . $updateFileName;
                // $filePath =  URL::to("/") . $imagePath;
                $filePath =   $imagePath;

                $query = 'mutation add_file($file: File!) { add_file_to_column (item_id: ' . $request->item_id . ', column_id: "files__1", file: $file) { id } }';
                $map = '{"image": "variables.file"}';
                $postFields = [
                    'query' => $query,
                    'map'   => $map,
                    'image' => new CURLFile($filePath)
                ];
                $fileUploadResponse = $this->imageUpload($postFields);

                if (isset($fileUploadResponse['response']['errors'])) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => $fileUploadResponse['response']['errors'])));
                }

                if ($fileUploadResponse['response']['data']['id']['add_file_to_column']) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => 'Image Updated Successfully')));
                } else {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => 'Something Went Wrong. Image Not Uploaded. Please Try To Re-Upload')));
                }
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
