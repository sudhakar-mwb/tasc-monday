<?php

namespace App\Http\Controllers\Incorpify;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MondayApis;
use Illuminate\Support\Facades\Validator;
use CURLFile;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use App\Models\IncorpifySiteSettings;
use App\Models\Incorpify_likes;
use Illuminate\Support\Facades\DB;
use App\Models\UpdateNotification;


class DashboardController extends Controller
{
    protected static $BOARD_ID_INCORPIFY = 1472103835;

    use MondayApis;
    public function dashboard()
    {
        $query = 'query {
              boards(limit: 500, ids: 1472103835) {
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
                    items_page (limit: 3, cursor:null,  query_params: {} ){
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
              owners {
                id
                name
                email
              }

              subscribers {
                id,
                name,
                email
                enabled,
                is_guest,
                is_view_only
              }
            }
          }';
        return $boardsData = $this->_getMondayData($query);
    }

    // User Profile (GET)
    public function profile()
    {

        $userdata = auth()->user();
        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "data" => $userdata
        ]);
    }

    // To generate refresh token value
    public function refreshToken()
    {

        $newToken = auth()->refresh();
        return response()->json([
            "status" => true,
            "message" => "New access token",
            "token" => $newToken
        ]);
    }

    // User Logout (GET)
    public function logout()
    {

        auth()->logout();
        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }

    public function incorpifyById($id)
    {
        $query = 'query {
            boards(limit: 500, ids: 1472103835) {
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
                  }}
                  #items_page (limit: 3, cursor:null,  query_params: {} ){
                   #   cursor,
                   items (ids: [' . $id . ']) {
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
                 # }
               # }
            }';
        return $boardsData = $this->_getMondayData($query);
    }

    public function update(Request $request)
    {
        $payload = $request->json()->all();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|filled',
            'text_body' => 'required|filled',
            'email' => 'required|filled|email',
        ]);

        if ($validator->fails()) {
            return $this->returnData($validator->errors(), false);
        }

        // Escape the text body content
        $textBody = json_encode($payload['text_body']);

        // Build the query based on the presence of parent_id
        if (!empty($payload['parent_id'])) {
            $query = 'mutation {
                create_update(item_id: ' . $payload['item_id'] . ', parent_id: ' . $payload['parent_id'] . ', body: ' . $textBody . ') {
                    id
                    body
                }
            }';
        } else {
            $query = 'mutation {
                create_update(item_id: ' . $payload['item_id'] . ', body: ' . $textBody . ') {
                    id
                    body
                }
            }';
        }

        // Run the prepared GraphQL query
        $response = $this->_getMondayData($query);
        if (isset($response['response']['data']['create_update']['id'])) {

            // Save the current response 

            // Search the subitems of the items by email address
            $column_id = "email__1";
            $description = "text";
            $required_action = "dup__of_description__1";
            $assignee = "assigness__1";
            $overall_status = "status__1";

            $query = '{
            boards(ids: 1472103835) {
            items_page(
                query_params: {rules: [{column_id: "' . $column_id . '", compare_value: ["' . $payload['email'] . '"], operator: contains_text}]}
            ) {
                items {
                        id
                        name
                        subitems {
                        name
                        id
                        created_at
                        updated_at
                        column_values(ids: ["' . $description . '", "' . $required_action . '", "' . $assignee . '", "' . $overall_status . '"]) {
                            id
                            text
                        }
                        updates {
                            id body created_at updated_at
                        }
                            }
                        }
                    }
                }
            }
            ';

            $responseOne = $this->_getMondayData($query);

            $subitems = $responseOne['response']['data']['boards'][0]['items_page']['items'][0]['subitems'] ?? [];

            if (!empty($subitems)) {
                UpdateNotification::updateOrCreate(
                    ['email' => $request->email], // Search criteria
                    ['item_data' => json_encode(json_encode($responseOne, true), true)] // Data to be updated or created
                );
            }

            return $this->returnData($response);
        }

        return $this->returnData($response, false);
    }


    public function updateReplyOrLike(Request $request)
    {
        $payload = $request->json()->all();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'mode' => 'required|in:like,reply',
            'update_id' => 'required|filled',
            'item_type' => 'required_if:mode,like|filled',
            'text_body' => 'required_if:mode,reply|filled',
            'item_id' => 'required_if:mode,reply|filled',
        ]);

        // Custom error messages
        $validator->setAttributeNames([
            'mode' => 'Mode',
            'update_id' => 'Update ID',
            'item_id' => 'Item ID',
            'text_body' => 'Text Body',
            'item_type' => 'Item Type',
        ]);

        if ($validator->fails()) {
            return $this->returnData($validator->errors(), false);
        }

        // Prepare the query
        if ($payload['mode'] == 'like') {
            $query = 'mutation {
                like_update (update_id: ' . $payload['update_id'] . ') {
                id
                }
            }';
        } else {
            $textBody = json_encode($payload['text_body']);
            $query = 'mutation {
                create_update(item_id: ' . $payload['item_id'] . ', parent_id: ' . $payload['update_id'] . ', body: ' . $textBody . ') {
                id
                }
            }';
        }

        $response = $this->_getMondayData($query);

        if (isset($response['response']['data']['like_update']['id'])) {
            // Save the liked data to the database
            $data = [
                "item_type" => $payload['item_type'],
                "update_id" => $payload['update_id'],
            ];

            $savedResponse = $this->saveLikeData($data, true);

            return $this->returnData($response);
        }

        if (isset($response['response']['data']['create_update']['id'])) {
            return $this->returnData($response);
        }

        return $this->returnData($response, false);
    }


    public function getSubItemByEmail(Request $request)
    {

        $payload = $request->json()->all();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|min:0',
            'limit' => 'int|min:0',
            'skip' => 'int|min:0',
        ]);

        // Custom error messages
        $validator->setAttributeNames([
            'email' => 'Email',
            'limit' => 'Limit',
            'skip' => 'Skip'
        ]);

        if ($validator->fails()) {
            return $this->returnData($validator->errors(), false);
        }

        //search the subitems of the items by email address
        $column_id = "email__1";
        $description = "text";
        $required_action = "dup__of_description__1";
        $assignee = "assigness__1";
        $overall_status = "status__1";

        $query = '{
            boards(ids: 1472103835) {
              items_page(
                query_params: {rules: [{column_id: "' . $column_id . '", compare_value: ["' . $payload['email'] . '"], operator: contains_text}]}
              ) {
                items {
                    id
                    name
                    subitems {
                      name
                      id
                      created_at
                      updated_at
                      column_values(ids: ["' . $description . '", "' . $required_action . '", "' . $assignee . '", "' . $overall_status . '"]) {
                        id
                        text
                     }
                     updates {
                        id body created_at updated_at
                      }
                    }
                  }
              }
            }
          }
        ';

        $response = $this->_getMondayData($query);

        $subitems = $response['response']['data']['boards'][0]['items_page']['items'][0]['subitems'] ?? [];

        if (empty($subitems)) {
            return $this->returnData("no data found", false);
        }

        $updatedSubitemIDs = [];
        $getLastState = UpdateNotification::where('email', $request->email)->first();
        if (!empty($getLastState)) {

            $getLastStateItemData = json_decode(json_decode($getLastState['item_data'], true), true);
            $updatedSubitemIDs = $this->getSubitemsWithNewUpdates($getLastStateItemData, $response);
        }

        $updateNotification = UpdateNotification::updateOrCreate(
            ['email' => $request->email], // Search criteria
            ['item_data' => json_encode(json_encode($response, true), true)] // Data to be updated or created
        );

        $subitems = $response['response']['data']['boards'][0]['items_page']['items'][0]['subitems'] ?? [];

        if (empty($subitems)) {
            return $this->returnData("no data found", false);
        }

        $total_subitem = count($subitems);
        $limit = $payload['limit'];
        $skip = $payload['skip'];

        $send_response = [];

        if ($limit > $total_subitem) {
            $send_response = array_slice($subitems, $skip, $total_subitem);
        } else {
            $send_response = array_slice($subitems, $skip, $limit);
        }

        $response['response']['data']['boards'][0]['items_page']['items'][0]['subitems'] = $send_response;
        $response['response']['data']['boards'][0]['items_page']['items'][0]['new_updates'] = $updatedSubitemIDs;

        return $this->returnData($response);
    }


    //helper functions 
    function getSubitemsWithNewUpdates($oldResponse, $newResponse)
    {
        // Extract subitems from old and new responses
        $oldSubitems = $oldResponse['response']['data']['boards'][0]['items_page']['items'][0]['subitems'];
        $newSubitems = $newResponse['response']['data']['boards'][0]['items_page']['items'][0]['subitems'];

        // Create an associative array of old subitems with their IDs as keys
        $oldSubitemsById = [];
        foreach ($oldSubitems as $subitem) {
            $oldSubitemsById[$subitem['id']] = $subitem;
        }

        // Array to hold subitems with new updates
        $subitemsWithNewUpdates = [];

        // Iterate over new subitems
        foreach ($newSubitems as $newSubitem) {
            $newSubitemId = $newSubitem['id'];

            // Check if this subitem existed in the old response
            if (isset($oldSubitemsById[$newSubitemId])) {
                $oldUpdates = $oldSubitemsById[$newSubitemId]['updates'];
                $newUpdates = $newSubitem['updates'];

                // Find new updates in the new subitem that are not in the old subitem
                $newOnlyUpdates = array_udiff($newUpdates, $oldUpdates, function ($a, $b) {
                    return strcmp($a['id'], $b['id']);
                });

                // If there are new updates, add them to the result
                if (!empty($newOnlyUpdates)) {
                    $subitemsWithNewUpdates[$newSubitemId] = $newOnlyUpdates;
                }
            } else {
                // If the subitem didn't exist in the old response, consider all its updates as new
                $subitemsWithNewUpdates[$newSubitemId] = $newSubitem['updates'];
            }
        }

        return $subitemsWithNewUpdates;
    }

    public function uploadFiles(Request $request)
    {


        $payload = $request->all();

        $column_id = "files";

        // The ID of the item to which the file will be uploaded
        $itemId = '1873224195';

        // The file you want to upload
        $filePath = '/home/cedcoss/Desktop/notes.gif';
        $fileMimeType = mime_content_type($filePath);
        $fileName = basename($filePath);

        // The GraphQL query
        $query = 'mutation ($file: File!) {
        add_file_to_column (item_id: ' . $itemId . ' column_id: "' . $column_id . '", file: $file) {
            id
        }
        }';

        // Prepare the variables
        $variables = [
            'file' => new CURLFile($filePath, $fileMimeType, $fileName)
        ];

        // Initialize cURL
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, 'https://api.monday.com/v2/file');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . env('MONDAY_API_KEY'),
            'Content-Type: multipart/form-data'
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'query' => $query,
            'variables[file]' => $variables['file']
        ]);


        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            return $error_msg;
        } else {
            return $response;
        }

        curl_close($ch);


    }

    public function createItem(Request $request)
    {


        $payload = $request->json()->all();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|min:0',
            'your_company_name' => 'string|min:0',
            'type_of_license' => 'string|min:0',
        ]);

        // Custom error messages
        $validator->setAttributeNames([
            'email' => 'Email',
            'your_company_name' => 'Company Name',
            'type_of_license' => 'Type of License',
        ]);

        if ($validator->fails()) {
            return $this->returnData($validator->errors(), false);
        }

        $column_values = json_encode(
            json_encode(
                [
                    "email__1" => [
                        "email" => $payload['email'],
                        "text" => $payload['email']
                    ],
                    "single_select3__1" => $payload['type_of_license']
                ],
                true
            ),
            true
        );

        $group_id = "topics";


        $query = 'mutation {
            create_item(
              board_id: 1472103835
              group_id: "' . $group_id . '"
              item_name: "' . $payload['your_company_name'] . '"
              column_values: ' . $column_values . '
            ) {
              id
            }
        }';

        $response = $this->_getMondayData($query);

        if (isset($response['response']['data']['create_item'])) {
            return $this->returnData($response);
        }

        return $this->returnData($response, false);

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

    public function updateSubitemStatus(Request $request)
    {

        $payload = $request->json()->all();

        $validator = Validator::make($request->all(), [
            'subitem_id' => 'required|min:0',
            'status_to_update' => 'required|string|min:0'
        ]);

        // Custom error messages
        $validator->setAttributeNames([
            'subitem_id' => 'Subitem Id ',
            'status_to_update' => 'Update Status'
        ]);

        if ($validator->fails()) {
            return $this->returnData($validator->errors(), false);
        }

        $board_id_query = 'query {items (ids: [' . $payload['subitem_id'] . ']) {
                 board { id }
               }
            }
        ';

        $getBoardId = $this->_getMondayData($board_id_query);
        $getBoardId = $getBoardId['response']['data']['items'][0]['board']['id'] ?? "";

        if (empty($getBoardId)) {
            return [
                "success" => false,
                "message" => "item id is invalid"
            ];
        }

        $update_query = 'mutation{
            change_column_value(
              board_id: ' . $getBoardId . ',
              item_id: ' . $payload['subitem_id'] . ',
              column_id: "status__1",
              value: "{\"label\": \"In Progress\"}") {
            id
            }
        }';


        $response = $this->_getMondayData($update_query);

        if ($response['response']['data']['change_column_value']['id']) {
            return $this->returnData($response);
        }

        return $this->returnData($response, false);
    }

    public function getBoardId()
    {
        try {
            $siteSettings = IncorpifySiteSettings::select('board_id')->where('id', '=', 1)->first();
            if ($siteSettings) {
                return (['board_id' => $siteSettings->board_id, 'status' => true, 'message' => "Board ID fetched successfully."]);
            } else {
                return (['response' => null, 'status' => false, 'message' => "Site settings not found."]);
            }
        } catch (\Exception $e) {
            return response()->json(['response' => null, 'status' => false, 'message' => $e->getMessage()]);
        }



        // $data = $this->getBoardId();
        // $board_id = $data['board_id'] ?? null;

        // if($board_id==null && empty($board_id)) {
        //     return $this->returnData("board id not set", false);
        // }s
    }

    public function getSubItemDetailsById(Request $request)
    {

        

        $data = [
            "id" => $request->id
        ];

        $rules = [
            "id" => "required|min:0|integer"
        ];

        $message = [
            'id.requried' => "id is an required field"
        ];

        $attribute = [
            "id" => "Item id or Subitem id"
        ];


        $validator = Validator::make($data, $rules, $message, $attribute);

        if ($validator->fails()) {
            return $this->returnData($validator->errors(), false);
        }

        $description = "text";
        $required_action = "dup__of_description__1";
        $assignee = "assigness__1";
        $overall_status = "status__1";

        $query = '{
            items(ids: ' . $request->id . ') {
              id
              name
              created_at
              updated_at
              column_values(
                ids: ["' . $description . '", "' . $required_action . '", "' . $assignee . '", "' . $overall_status . '"]
              ) {
                id
                text
              }
              updates {
                id
                text_body
                body
                replies {
                  id
                  text_body
                  body
                }
              }
              assets {
                id
                url
                name
                public_url
              }
              updates {
                id
                body
                created_at
                creator {
                  name
                  email
                }
                replies {
                  id
                  body
                  created_at
                  creator {
                    name
                    email
                  }
                }
              }
            }
          }';

        $response = $this->_getMondayData($query);

        if (!isset($response['response']['data']['items'][0])) {
            return $this->returnData($response, false);
        }

        return $this->returnData($response);

    }

    //upload file to the monday.com function 
    function uploadFileToMonday($itemId, $columnId, $fileData, $fileName)
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
                    'name' => 'query',
                    'contents' => "mutation (\$file: File!) {
                        add_file_to_column (item_id: $itemId, column_id: \"$columnId\", file: \$file) {
                            id
                            name
                            url
                        }
                    }"
                ],
                [
                    'name' => 'variables[file]',
                    'contents' => $fileContent,
                    'filename' => $fileName
                ]
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }


    public function uploadMondayFiles(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
            'file' => 'required',
            'file_name' => 'required',
            'column_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Get validated data
        $itemId = $request->input('item_id');
        $columnId = $request->column_id;
        $base64Data = $request->file;
        $fileName = $request->file_name;

        // Upload file to Monday.com
        $response = $this->uploadFileToMonday($itemId, $columnId, $base64Data, $fileName);

        if (!isset($response['data']['add_file_to_column']['id'])) {
            return $this->returnData($response, false);
        }

        return $this->returnData($response);
    }

    // Helper function to validate base64 image
    private function isValidBase64Image($base64Image)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $data = substr($base64Image, strpos($base64Image, ',') + 1);
            $data = base64_decode($data);
            if ($data === false) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function saveSiteSettings(Request $request)
    {
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                if ($request->isMethod('get')) {
                    // Handle GET request to fetch site settings
                    $get_data = IncorpifySiteSettings::where('id', '=', 1)->first();
                    if ($get_data) {
                        return response()->json(['response' => $get_data, 'status' => true, 'message' => "Incorpify Site Setting Fetched Successfully."]);
                    } else {
                        return response()->json(['response' => [], 'status' => false, 'message' => "Incorpify Site Setting Not Found."]);
                    }
                } else if ($request->isMethod('post')) {
                    // Handle POST request to save site settings
                    $input = $request->json()->all();
                    $get_data = IncorpifySiteSettings::where('id', '=', 1)->first();
    
                    $insert_array = [
                        "ui_settings" => json_encode($input['ui_settings']),
                        "meeting_link" => $input['meeting_link'] ?? null,
                        "created_at" => date("Y-m-d H:i:s"),
                        "board_id" => $input['board_id'] ?? null,
                        "updated_at" => date("Y-m-d H:i:s")
                    ];
    
                    $datatoUpdate = [];
                    if (isset($input['logo_image']) && $input['logo_image']) {
                        // Additional validation for base64 image
                        if (!$this->isValidBase64Image($input['logo_image'])) {
                            return response()->json(['response' => [], 'status' => false, 'message' => "Invalid image format. Please re-upload the image (jpeg|jpg|png)."]);
                        }
    
                        $imageData = $input['logo_image'];
                        list($type, $data) = explode(';', $imageData);
                        list(, $data) = explode(',', $data);
                        $data = base64_decode($data);
                        $extension = explode('/', mime_content_type($imageData))[1];
                        $timestamp = now()->timestamp;
    
                        $updateFileName = $timestamp . '_' . $input['logo_name'];
                        \File::put(public_path('uploads/incorpify/' . $updateFileName), $data);
    
                        $datatoUpdate['logo_name'] = $updateFileName;
                        $imagePath = '/uploads/incorpify/' . $updateFileName;
                        $datatoUpdate['logo_location'] = \URL::to("/") . $imagePath;
    
                        if ($get_data && $get_data->logo_name) {
                            $uploadedImagePath = public_path('uploads/incorpify/' . $get_data->logo_name);
                            // Check if the image file exists
                            if (\File::exists($uploadedImagePath)) {
                                // Delete the image file
                                \File::delete($uploadedImagePath);
                            }
                        }
                    }

                    $datatoUpdate['board_id'] = $input['board_id'];
    
                    $datatoUpdate['ui_settings'] = $insert_array['ui_settings'];
                    $datatoUpdate['meeting_link'] = $insert_array['meeting_link'];
                    $datatoUpdate['status'] = 0;
                    $datatoUpdate['updated_at'] = date("Y-m-d H:i:s");
    
                    if (empty($get_data)) {
                        $datatoUpdate['created_at'] = date("Y-m-d H:i:s");
                        $insert = IncorpifySiteSettings::create($datatoUpdate);
                    } else {
                        $insert = IncorpifySiteSettings::where('id', '=', 1)->update($datatoUpdate);
                    }
    
                    if ($insert) {
                        return response()->json(['response' => [], 'status' => true, 'message' => "Incorpify Site Setting Updated Successfully."]);
                    } else {
                        return response()->json(['response' => [], 'status' => false, 'message' => "Incorpify Site Setting Not Created."]);
                    }
                } else {
                    return response()->json(['response' => [], 'status' => false, 'message' => "Invalid Request Method."]);
                }
            } catch (\Exception $e) {
                return response()->json(['response' => [], 'status' => false, 'message' => $e->getMessage()]);
            }
        } else {
            return response()->json(['response' => [], 'status' => false, 'message' => "Invalid User."]);
        }
    }
    

    public function getLikes($item_type_id, $item_type)
    {

        $likes = MondayLike::where('item_type_id', $item_type_id)
            ->where('item_type', $item_type)
            ->get();

        return response()->json($likes, 200);
    }

    public function saveLikeData($data = [], $like = true)
    {
        try {
            // Validate data
            if (empty($data['update_id']) || empty($data['item_type'])) {
                throw new \InvalidArgumentException('update_id and item_type are required.');
            }

            // Validate user token
            $userId = $this->verifyToken()->getData()->id ?? "";
            if (empty($userId)) {
                throw new \RuntimeException('User authentication failed.');
            }

            // Search for existing like item
            $existingItem = Incorpify_likes::where('user_id', $userId)
                ->where('item_type_id', $data['update_id'])
                ->where('item_type', $data['item_type'])
                ->first();

            if ($existingItem) {
                // Update the existing item
                $existingItem->liked = $like;
                $existingItem->save();
                $item = $existingItem;
            } else {
                // Create a new like item
                $item = Incorpify_likes::create([
                    'user_id' => $userId,
                    'item_type_id' => $data['update_id'],
                    'item_type' => $data['item_type'],
                    'liked' => $like,
                ]);
            }

            return $this->returnData($item, true); // return the created or updated item

        } catch (Exception $e) {
            // Handle the error appropriately
            return ['error' => $e->getMessage()];
        }
    }

    public function listAllLikes()
    {
        try {
            // Fetch all records from the Incorpify_likes table
            $allLikes = Incorpify_likes::all();

            // Return the data, potentially formatting it as needed
            return $this->returnData($allLikes, true);

        } catch (Exception $e) {
            // Handle any errors
            return $this->returnData($e->getMessage(), false);
        }
    }

    public function dislikeUpdateOrReply(Request $request)
    {


        if (empty($request->id)) {
            return $this->returnData("update or reply id is empty", false);
        }

        $response = DB::table('incorpify_likes')->where('item_type_id', $request->id)->first();
        $response = json_decode(json_encode($response, true), true);

        if (empty($response)) {
            return $this->returnData("invalid id found {" . $request->id . "}", false);
        }

        $delResponse = DB::table('incorpify_likes')->where('item_type_id', $request->id)->delete();


        if ($delResponse) {
            return $this->returnData("item " . $request->id . " deleted successfully", true);
        }

        return $this->returnData($delResponse, false);

    }


    //temp data 

    public function testwebhooks(Request $request)
    {


        $data = $request->all();

        return $data;
    }

    public function getBoardIds()
    {
        $query = "query {
            boards(page: 1, limit: 99999) {
              id
              name
            }
        }";

        try {
            $responseData = $this->_getMondayData($query);

            if(isset($responseData['response']['data']['boards'])) {
                $responseData['response']['data']['message'] = "boards ids fetched successfully";
                return $this->returnData($responseData['response']['data']);
            } 

            return $this->returnData($responseData, false);


        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching board IDs'], 500);
        }
    }

    public function getBoardColumnsIds(Request $request) {
        // Validate the request to ensure 'id' is set
        if (!isset($request->id)) {
            return $this->returnData("invalid board id found", false);
        }
    
        // Correcting the variable name to $board_id
        $board_id = $request->id;
    
        // Construct the GraphQL query
        $query = "{
            boards(ids: $board_id) {
                columns {
                    id
                    title
                    description
                    type
                    archived
                    width
                }

                items_page {
                    items {
                      subitems {
                        column_values {
                          column {
                            id
                            title
                            
                          }
                        }
                      }
                    }
                }
            }
        }";
    
        // Fetch data using the _getMondayData method
        $responseData = $this->_getMondayData($query);
    
        // Check if columns data is present in the response
        if (isset($responseData['response']['data']['boards'][0]['columns'])) {

            $subitemsColumnIds = $responseData['response']['data']['boards'][0]['items_page']['items'][0]['subitems'][0]??[];
            $columnValue = $responseData['response']['data']['boards'][0]['columns'];

            $response = array(
                "board"=>$columnValue,
                "subitems"=>$subitemsColumnIds
            );
            
            // $responseData['response']['data']['boards'][0]['items_page'] = $subitemsColumnIds;
            
            return $this->returnData($response);
        } else {
            return $this->returnData($responseData, false);
        }
    }
    


}
