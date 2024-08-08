<?php

namespace App\Http\Controllers\Incorpify;

use App\Http\Controllers\Controller;
use App\Models\OnboardifyProfiles;
use Exception;
use Illuminate\Http\Request;
use App\Traits\MondayApis;
use Illuminate\Support\Facades\Validator;
use CURLFile;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use App\Models\IncorpifySiteSettings;
use App\Models\GovernifySiteSetting;
use App\Models\MondayUsers;
use App\Models\Incorpify_likes;
use Illuminate\Support\Facades\DB;
use App\Models\UpdateNotification;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\BoardColumnMappings;
use App\Models\Tasc360Setting;
use Illuminate\Support\Facades\Storage;
use App\Models\TascPlateform;
use App\Models\TascInvitation;


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
            'type_of_license_column_id' => 'string|min:0',
            'email_column_id' => 'string|min:0',
            'name_column_id' => 'string|min:0',
        ]);

        // Custom error messages
        $validator->setAttributeNames([
            'email' => 'Email',
            'your_company_name' => 'Company Name',
            'type_of_license' => 'Type of License',
            'type_of_license_column_id' => 'Type of License Column ID',
            'email_column_id' => 'Email Column ID',
            'name_column_id' => 'name Column ID',
        ]);

        if ($validator->fails()) {
            return $this->returnData($validator->errors(), false);
        }

        $column_values = json_encode(
            json_encode(
                [
                    $payload['email_column_id'] => [
                        "email" => $payload['email'],
                        "text" => $payload['email']
                    ],
                    $payload['type_of_license_column_id'] => $payload['type_of_license'],
                    $payload['name_column_id'] => $payload['your_company_name']
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
            'status_to_update' => 'required|string|min:0',
            'status_column_id' => 'required|string|min:0'
        ]);

        // Custom error messages
        $validator->setAttributeNames([
            'subitem_id' => 'Subitem Id ',
            'status_to_update' => 'Update Status',
            'status_column_id' => 'Status Column Id'
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
              column_id: "'.$payload['status_column_id'].'",
              value: "{\"label\": \"In Progress\"}") {
            id
            }
        }';


        $response = $this->_getMondayData($update_query);

        if (isset($response['response']['data']['change_column_value']['id'])) {
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

        // $description = "text";
        // $required_action = "dup__of_description__1";
        // $assignee = "assigness__1";
        // $overall_status = "status__1";

        $query = '{
            items(ids: '.$request->id.') {
              id
              name
              created_at
              updated_at
              column_values {
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
    

    public function getAllDomain(Request $request)
    {
        try {
            // Payload 
            $payload = $request->json()->all();

            $requiredKeys = ['incorpify', 'governify', 'onboardify'];
            $allowedStatuses = ["in progress", "completed", "pending", "canceled", "awaiting action"];
            
            foreach ($requiredKeys as $key) {
                if (!isset($payload[$key]['emailColumnId']) || empty($payload[$key]['emailColumnId']) ||
                    !isset($payload[$key]['statusColumnId']) || empty($payload[$key]['statusColumnId'])) {
                    return $this->returnData("$key: emailColumnId and statusColumnId are required fields", false);
                }
            
                // Additional check for governify
                if ($key === 'governify' && (!isset($payload[$key]['catagoryColumnId']) || empty($payload[$key]['catagoryColumnId']))) {
                    return $this->returnData("$key: catagoryColumnId is a required field", false);
                }
            }
            
            if (!empty($payload['status'])) {
                if (!isset($payload['status']) || !in_array(strtolower($payload['status']), $allowedStatuses)) {
                    return $this->returnData("Status is required and must be one of the following: " . implode(", ", $allowedStatuses), false);
                }
            }

            // Parse and verify the token
            $user = JWTAuth::parseToken()->authenticate();

            // Get the email from the authenticated user
            $email = $user->email;

            // Search for the email in the three tables and get the data
            $incorpifyData = $this->getBoardId();
            $siteSettingsGovernify = GovernifySiteSetting::first()->toArray();
            $siteSettingsOnboardify = MondayUsers::where('email', '=', $email)->first()->toArray();
            $siteSettingsOnboardifyData = [];
            
            //Left to work 
            //Left to work 
            //Left to work 
            //Left to work 
            //Left to work 
            //Left to work 
            //Left to work 
            //Left to work 
            //Left to work 
            //Left to work 
            //Left to work 

            // $dataToRender = OnboardifyProfiles::with('services')->whereRaw('FIND_IN_SET(?, users)', [$email])->get()->first();
            
            if(isset($siteSettingsOnboardify['board_id'])){

                $siteSettingsOnboardifyData = BoardColumnMappings::where('board_id', (int)$siteSettingsOnboardify['board_id'])
                ->first();

                $siteSettingsOnboardifyData = json_decode(json_encode($siteSettingsOnboardifyData, true), true);
                

                if(empty($siteSettingsOnboardifyData['board_id'])){
                 
                    $siteSettingsOnboardifyData = BoardColumnMappings::where('board_id', $siteSettingsOnboardify['board_id'])
                    ->first();

                    $siteSettingsOnboardifyData = json_decode(json_encode($siteSettingsOnboardifyData, true), true);


                }
            }

            // Check if board_id is present and construct the GraphQL query
            $graphqlQuery = [];
            $graphqlResults = [];

            if (isset($incorpifyData['board_id'])) {
                $incorpifyQuery = $this->constructGraphQLQuery($incorpifyData['board_id'], 'subitems', $email, $payload['incorpify']['emailColumnId'], $payload['incorpify']['statusColumnId']);
                $response = $this->_getMondayData($incorpifyQuery);
                if(!isset($payload['status']) || empty($payload['status'])){
                    
                    $graphqlResults['Incorpify'] = $this->filterSubitemsByStatus($response);
                } else {
                    $filterResult = $this->filterSubitemsByStatus($response, $payload['status']);
                    $graphqlResults['Incorpify'] = $filterResult;
                }


            } else {
                $graphqlResults['Incorpify'] = [];
            }

            if (isset($siteSettingsGovernify['board_id'])) {

                $governifyQuery = $this->constructGraphQLQuery($siteSettingsGovernify['board_id'], 'items', $email, $payload['governify']['emailColumnId'], $payload['governify']['statusColumnId'], $payload['status']??null, $payload['governify']['catagoryColumnId']);
                
                $graphqlResults['Governify'] = $this->_getMondayData($governifyQuery);
            } else {
                $graphqlResults['Governify'] = [];
            }


            //----------------------
            //Comment Untill the conformation
            //----------------------

            // if (isset($siteSettingsOnboardifyData['board_id'])) {

            //     //onboardify

            //     $jsonData = json_decode($siteSettingsOnboardifyData['columns'], true);

            //     $emailColumnId = $jsonData['email_key'];
            //     $statusColumnId = null;

            //     foreach ($jsonData['onboarding_columns'] as $column) {
            //         if ($column['name'] === 'Email Address') {
            //             $emailColumnId = $column['id'];
            //         }
            //         if (strpos($column['name'], 'Status') !== false) {
            //             $statusColumnId = $column['id'];
            //         }
            //     }

            //     $onboardifyQuery = $this->constructGraphQLQuery($siteSettingsOnboardify['board_id'], 'items', $emailColumnId, $statusColumnId);

            //     echo '<pre>';
            //     print_r($emailColumnId);
            //     print_r($statusColumnId);
            //     print_r($onboardifyQuery);
            //     echo '[Line]:     ' . __LINE__ . "\n";
            //     echo '[Function]: ' . __FUNCTION__ . "\n";
            //     echo '[Class]:    ' . (__CLASS__ ? __CLASS__ : 'N/A') . "\n";
            //     echo '[Method]:   ' . (__METHOD__ ? __METHOD__ : 'N/A') . "\n";
            //     echo '[File]:     ' . __FILE__ . "\n";
            //     die;
                
                
            //     $graphqlResults['Onboardify'] = $this->_getMondayData($onboardifyQuery);
            // } else {
            //     $graphqlResults['Onboardify'] = [];
            // }

            // Return the combined data as a JSON response
            return response()->json($graphqlResults);

        } catch (JWTException $e) {
            // Something went wrong whilst decoding the token
            return response()->json(['error' => 'Token is invalid'], 400);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    //filter out the status 
    function filterSubitemsByStatus($array, $status=null) {  
        if (isset($array['response']['data']['boards'][0]['items_page']['items'])) {  
            foreach ($array['response']['data']['boards'][0]['items_page']['items'] as &$item) {  
                if (isset($item['subitems'])) {  
                    if($status!=null) {

                        $filteredSubitems = array_filter($item['subitems'], function($subitem) use ($status) {  
                            foreach ($subitem['column_values'] as $columnValue) {  
                                if (strcasecmp($columnValue['text'], $status) === 0) {  
                                    return true;  
                                }  
                            }  
                            return false;  
                        });  
                        $item['subitems'] = array_values($filteredSubitems);  
                    }
                    
                    
                    foreach ($item['subitems'] as $index => &$subitem) {  
                        $subitem['step'] = 'step ' . ($index + 1);  
                    }  
                }  
            }  
        }  
        
        return $array;  
    }



    public function constructGraphQLQuery($boardId, $type, $email = null, $emailColumnId = null, $statusColumnId=null, $status=null, $serviceCatagoryId = null)
    {

        $allowedStatus = [
            "in progress"=>"0",
            "completed"=>"1",
            "pending"=>"2",
            "canceled"=>"3",
            "awaiting action"=>"5"
        ];

        if($status==null){
            $allowedStatus = [0,1,2,3,4,5];
        }

        if($type=='items') {


            if($status==null){
                return 'query {
                    boards(ids: '.$boardId.') {
                      items_page(query_params: {
                        rules: [
                          {column_id: "'.$emailColumnId.'", compare_value: ["'.$email.'"], operator: any_of}
                        ]
                      }) {
                        items {
                          id
                          name
                          created_at
                          updated_at
    
                          column_values(ids: ["'.$statusColumnId.'", "'.$serviceCatagoryId.'"]){
                            id text
                          }
                        }
                      }
                    }
                  }';
            } else {

                $statusValue = strtolower($status);
                return 'query {
                    boards(ids: '.$boardId.') {
                      items_page(query_params: {
                        rules: [
                          {column_id: "'.$emailColumnId.'", compare_value: ["'.$email.'"], operator: any_of},
                          {column_id: "'.$statusColumnId.'", compare_value: ['.$allowedStatus[$statusValue].'], operator: any_of}
                        ]
                      }) {
                        items {
                          id
                          name
                          created_at
                          updated_at
    
                          column_values(ids: ["'.$statusColumnId.'"]){
                            id text
                          }
                        }
                      }
                    }
                  }';
            }
           
        } elseif ($type=='subitems'){
            return '{
                boards(ids: '.$boardId.') {
                  items_page(
                    query_params: {rules: [{column_id: "' . $emailColumnId . '", compare_value: ["' . $email . '"], operator: contains_text}]}
                  ) {
                    items {
                        subitems {
                          name
                          id
                          created_at
                          updated_at

                          column_values(ids: ["'.$statusColumnId.'"]){
                            id text
                          }
                        }
                      }
                  }
                }
              }
            ';
        }
    }

    public function saveTascSiteSettings(Request $request){

        // Validate the incoming request data
        $validatedData = $request->validate([
            'ui_settings' => 'required|array',
            'quick_access' => 'required|array',
            'slider_images' => 'required|array',
        ]);

        // Assuming there's only one record you want to update repeatedly, you can use a fixed identifier or find the first record
        $tasc360Setting = Tasc360Setting::first();

        // If the record doesn't exist, create it
        if (!$tasc360Setting) {
            $tasc360Setting = new Tasc360Setting();
        }

        // Update the settings
        $tasc360Setting->ui_settings = $validatedData['ui_settings'];
        $tasc360Setting->quick_access = $validatedData['quick_access'];
        $tasc360Setting->slider_images = $validatedData['slider_images'];

        // Save the record
        $tasc360Setting->save();

        // Return a JSON response with the created or updated instance
        return $this->returnData($tasc360Setting);
    }

    public function getTascSiteSettings(Request $request){


         // Retrieve all records from the Tasc360Setting model
        $tasc360Settings = Tasc360Setting::all();

        // Return a JSON response with all the settings
        return $this->returnData($tasc360Settings);

    }

    public function uploadTask360Images(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'image_key' => 'required|string',
            'image' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($this->returnData('Invalid image format. Please re-upload the image (jpeg|jpg|png|svg).', false), 400);
        }

        $input = $request->all();

        if (isset($input['image']) && $input['image']) {
            // Extract and decode the image data
            $imageData = $input['image'];
            list($type, $data) = explode(';', $imageData);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
        
            // Determine the file extension manually
            $mimeToExtension = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/svg+xml' => 'svg',
                'image/gif' => 'gif'
            ];
            
            $mimeType = explode(':', substr($imageData, 0, strpos($imageData, ';')))[1];
            $extension = isset($mimeToExtension[$mimeType]) ? $mimeToExtension[$mimeType] : 'bin';

            // Use the image_key as the base file name
            $updateFileName = $input['image_key'] . '.' . $extension;
        
            // Define the path where the image will be saved
            $directory = public_path('uploads/tasc360');
            $filePath = $directory . '/' . $updateFileName;

            // Ensure the directory exists
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
        
            // Save the image to the specified path
            $response = file_put_contents($filePath, $data);

            if ($response !== false) {
                // Return the URL as a response
                $imageUrl = url('uploads/tasc360/' . $updateFileName);
                return response()->json($this->returnData(['url' => $imageUrl, 'status' => true]));
            } else {
                return response()->json($this->returnData('Failed to save the image.', false), 500);
            }
        }

        return response()->json($this->returnData('No image data found.', false), 400);
    }

    public function deleteUploadedImage(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'image_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->returnData('Invalid request.', false);
        }

        $input = $request->all();
        $imageKey = $input['image_key'];

        // Define the path where the image is saved
        $directory = public_path('uploads/tasc360');
        
        $filePath = $directory . '/' . $imageKey;

        // Check if the file exists and delete it
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                return $this->returnData(['status' => true, 'message' => 'Image deleted successfully.']);
            } else {
                return $this->returnData('Failed to delete the image.', false);
            }
        } else {
            return $this->returnData('Image not found.', false);
        }
    }

    public function getCommonSiteSettings(Request $request){
        
        
        try {
            $incorpifySettings = IncorpifySiteSettings::all()->toArray();
            $governifySettings = GovernifySiteSetting::all()->toArray();

            $incorpify = (json_decode($incorpifySettings[0]['ui_settings'], true));
            $governify = (json_decode($governifySettings[0]['ui_settings'], true));

            $allSettings = [
                'incorpify' => $incorpify['selectedColumn'],
                'governify' => $governify['selectedColumn']
            ];

            return $this->returnData($allSettings);

        } catch (Exception $e) {
            
            return $this->returnData($e->getMessage(), false);
        }
                
    }

    public function getPlateformData(Request $request) {
    
        // Retrieve all records from the TascPlateform table
        $platforms = TascPlateform::all();

        // Return the results as a JSON response
        return response()->json([
            'status' => 'success',
            'data' => $platforms
        ]);
    }

    public function sendInviteUserMail(Request $request) {
        $payload = $request->all();
    
        // Define the validation rules
        $rules = [
            'plateformId' => 'required|array|min:1',
            'plateformId.*' => 'required|integer',
            'user_email_id' => 'required|email',
            'invited_user_email' => 'required|email',
        ];
    
        // Define custom validation messages if necessary
        $messages = [
            'plateformId.required' => 'The platform ID is required.',
            'plateformId.array' => 'The platform ID must be an array.',
            'plateformId.min' => 'The platform ID array must contain at least one ID.',
            'plateformId.*.required' => 'Each platform ID is required.',
            'plateformId.*.integer' => 'Each platform ID must be an integer.',
            'user_email_id.required' => 'The user email ID is required.',
            'user_email_id.email' => 'The user email ID must be a valid email address.',
            'invited_user_email.required' => 'The invited user email is required.',
            'invited_user_email.email' => 'The invited user email must be a valid email address.',
        ];
    
        // Perform the validation
        $validator = Validator::make($request->all(), $rules, $messages);
    
        // Check if validation fails
        if ($validator->fails()) {
            return $this->returnData(['errors' => $validator->errors()], false);
        }

        // Fetch the platform data by ids
        $platformIds = $payload['plateformId'];
        $platformData = TascPlateform::whereIn('id', $platformIds)->get();
    
        if ($platformData->isEmpty()) {
            return $this->returnData("Invalid platform ID(s) found", false);
        }

        $userData = json_decode(json_encode($this->verifyTOken()->getData(), true), true);
       
        // Apply json_encode and json_decode on the platform data
        $platformData = json_decode(json_encode($platformData, true), true);
        
        $dataToRender = json_decode(json_encode(OnboardifyProfiles::with('services')->whereRaw('FIND_IN_SET(?, users)', [$userData['email']])->get()->first(), true), true);

        if(!isset($dataToRender['id']) || empty($dataToRender['id'])){
            return $this->returnData("Profile Id is not available", false);
        }

        //prepare url
        $plateForms = [];
        $plateFormsSignup = "";
        foreach ($platformData as $platform) {
            $plateForms[] = $platform['plateform_name'];
            if(count($payload['plateformId'])==1){
                $plateFormsSignup = $platform["plateform_signuplink"];
            } 
        }
        if(empty($plateFormsSignup)){
            $baseUrl = "https://login.tasc360.com/invite";
        } else {
            $baseUrl = $plateFormsSignup;
        }

        $parameters = [
            "user_email"=> $userData['email'],
            "invited_user_email"=> $payload['invited_user_email'],
            "company_name"=> $userData['company_name'],
            "plateform"=> $plateForms,
            "profile_id"=> $dataToRender['id']
        ];

        $encodedParams = http_build_query($parameters);
        $finalUrl = $baseUrl."?".$encodedParams;

        $verificationData = [
           "invited_user_email"=> $payload['invited_user_email'],
           "url"=> $finalUrl 
        ];


        //save the invitation
        $inviteData = [
            'inviter_id' => $userData['id'],
            'invitee_email' => $payload['invited_user_email'],
            'invitation_status' => 'pending',
            'onboardify_status' => false,
            'incorpify_status' => false,
            'governify_status' => false,
        ];

        //check where the invite user has already invited or not

        $getInvitedDataObj = TascInvitation::where("invitee_email", $payload['invited_user_email'])->first();
        $getInvitedData = json_decode(json_encode($getInvitedDataObj, true), true);

        if(!empty($getInvitedData)){

            $status = $getInvitedData['invitation_status'];

            //for the done case and the pending case db getting updated

            foreach ($plateForms as $key => $value) {
                $value = $value."_status";
                $getInvitedDataObj->$value = true;

            }

            $responseDbUpdate = $getInvitedDataObj->save();


            if($status == "done") {
                
                $response = $this->sendInvitationEmail($verificationData, true);
                
                return [
                    "success"=> true,
                    "message"=> "Access granted for the provided domain as the invited user is already exist",
                    "data"=> []
                ];

            } else { //pending status condition

                //a regular email has been send to the invited users 
                $response = $this->sendInvitationEmail($verificationData);

                return [
                    "success"=> true,
                    "message"=> "Email successfully send",
                    "data"=> []
                ];

            }
            
        } else {

            $response = $this->sendInvitationEmail($verificationData);

            $responseData = $this->updateInviteData($inviteData, $plateForms);

            $save = TascInvitation::create($responseData);

            return [
                "success"=> true,
                "message"=> "Email successfully send",
                "data"=> []
            ];
        }

    }

    function updateInviteData(array $inviteData, array $plateForms): array
    {
        // Define the platform status keys
        $statusKeys = ['onboardify_status', 'governify_status', 'incorpify_status'];
        
        // Iterate over each status key
        foreach ($statusKeys as $statusKey) {
            // Extract the platform name from the status key (e.g., 'onboardify' from 'onboardify_status')
            $platformName = str_replace('_status', '', $statusKey);
            
            // Check if the platform name is in the plateForms array
            if (in_array($platformName, $plateForms)) {
                // Set the corresponding status to true
                $inviteData[$statusKey] = true;
            } else {
                // Set the corresponding status to false
                $inviteData[$statusKey] = false;
            }
        }
    
        return $inviteData;
    }


    public function sendInvitationEmail($invitationData, $userExists = false) {

        $sitelogo = "https://login.tasc360.com/assets/1.png"; // Replace with the actual path to your logo
        $email = $invitationData['invited_user_email'];
    
        if ($userExists) {
            $subject = "Access Granted to TASC360";
            $mail_body = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>TASC360 | Access Granted</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                        background-color: #f2f2f2;
                    }
                    .container {
                        max-width: 600px;
                        margin: 30px auto;
                        padding: 20px;
                        background-color: #ffffff;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        text-align: center;
                    }
                    .logo img {
                        width: 120px;
                        margin-bottom: 20px;
                    }
                    .header {
                        background-color: #007BFF;
                        color: #ffffff;
                        padding: 20px;
                        border-radius: 10px 10px 0 0;
                        font-size: 24px;
                        font-weight: bold;
                    }
                    .message {
                        margin: 20px 0;
                        font-size: 16px;
                        color: #333333;
                        line-height: 1.5;
                    }
                    .button {
                        display: inline-block;
                        padding: 12px 24px;
                        background-color: #007BFF;
                        color: #ffffff;
                        text-decoration: none;
                        border-radius: 5px;
                        font-size: 16px;
                        font-weight: bold;
                        margin-top: 20px;
                    }
                    .button:hover {
                        background-color: #0056b3;
                    }
                    .footer {
                        margin-top: 20px;
                        font-size: 14px;
                        color: #777777;
                    }
                    .footer a {
                        color: #007BFF;
                        text-decoration: none;
                    }
                    .footer a:hover {
                        text-decoration: underline;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        Access Granted to TASC360
                    </div>
                    <div class="logo">
                        <img src="' . $sitelogo . '" alt="TASC Logo">
                    </div>
                    <div class="message">
                        <p>Hello ' . $invitationData['invited_user_email'] . ',</p>
                        <p>Your account has been granted access to TASC360. You can now log in to your account using your existing credentials.</p>
                        <p><a href="' . $invitationData['url'] . '" class="button">Go to TASC360</a></p>
                        <p>If you cannot click the button, please copy and paste the following URL into your browser:</p>
                        <a href="' . $invitationData['url'] . '">' . $invitationData['url'] . '</a>
                    </div>
                    <div class="footer">
                        <p>If you have any questions, please contact us at <a href="mailto:KSAAutomation@tascoutsourcing.com">KSAAutomation@tascoutsourcing.com</a></p>
                    </div>
                </div>
            </body>
            </html>';
        } else {
            $subject = "You're Invited to Join TASC360";
            $mail_body = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>TASC360 | Invitation</title>
                <style>
                    /* (Keep the styles same as before) */
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        You\'re Invited to Join TASC360
                    </div>
                    <div class="logo">
                        <img src="' . $sitelogo . '" alt="TASC Logo">
                    </div>
                    <div class="message">
                        <p>Hello ' . $invitationData['invited_user_email'] . ',</p>
                        <p>You have been invited to join our platform. To get started, please click the button below to accept the invitation and complete your registration:</p>
                        <p><a href="' . $invitationData['url'] . '" class="button">Accept Invitation</a></p>
                        <p>If you cannot click the button, please copy and paste the following URL into your browser:</p>
                        <a href ="'.$invitationData['url'].'">' . $invitationData['url'] . '</a>
                    </div>
                    <div class="footer">
                        <p>If you have any questions, please contact us at <a href="mailto:KSAAutomation@tascoutsourcing.com">KSAAutomation@tascoutsourcing.com</a></p>
                    </div>
                </div>
            </body>
            </html>';
        }
    
        try {
            $data = array(
                "personalizations" => array(
                    array(
                        "to" => array(
                            array(
                                "email" => $email,
                                "name" => $invitationData['invited_user_email']
                            )
                        )
                    )
                ),
                "from" => array(
                    "email" => "KSAAutomation@tascoutsourcing.com"
                ),
                "subject" => $subject,
                "content" => array(
                    array(
                        "type" => "text/html",
                        "value" => $mail_body
                    )
                )
            );
    
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.sendgrid.com/v3/mail/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('SENDGRID_API_KEY'),
                    'Content-Type: application/json'
                ),
            ));
    
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
    
            if ($err) {
                throw new \Exception("cURL Error #: " . $err);
            }
    
            return $response;
        } catch (\Exception $e) {
            // Log the error or handle it as necessary
            throw $e;
        }
    }
    
    
}