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

    
class DashboardController extends Controller
{
    protected static $BOARD_ID_INCORPIFY = 1472103835;

    use MondayApis;
    public function dashboard () {
        $query = 'query {
              boards(limit: 500, ids: '.$this->BOARD_ID_INCORPIFY.') {
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
    public function profile(){

        $userdata = auth()->user();
        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "data" => $userdata
        ]);
    }

    // To generate refresh token value
    public function refreshToken(){
        
        $newToken = auth()->refresh();
        return response()->json([
            "status" => true,
            "message" => "New access token",
            "token" => $newToken
        ]);
    }

    // User Logout (GET)
    public function logout(){
        
        auth()->logout();
        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }

    public function incorpifyById ($id){
        $query = 'query {
            boards(limit: 500, ids: '.$this->BOARD_ID_INCORPIFY.') {
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

    public function update(Request $request){
        
        $payload = $request->json()->all();

        //validate the request
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|filled',
            'text_body' => 'required|filled',
        ]);

        if ($validator->fails()) {
            return $this->returnData($validator->errors(), false);
        }


        $query = [];
        if(!empty($payload['parent_id'])){
            $query = '{mutation {
                create_update(item_id: '.$payload['item_id'].' parent_id: '.$payload['parent_id'].' body: "'.$payload['text_body'].'") {
                  id
                  body
                }
            }}';
        } else {
            $query = 'mutation {
                create_update(item_id: '.$payload['item_id'].' body: "'.$payload['text_body'].'") {
                  id
                  body
                }
            }';
        }

        //run the prepared graphQL query
        $response = $this->_getMondayData($query);
        if(isset($response['response']['data']['create_update']['id']))
        {
            return $this->returnData($response);
        }

        return $this->returnData($response, false);
    }

    public function updateReplyOrLike(Request $request){

        $payload = $request->json()->all();

        //validate the request
        $validator = Validator::make($request->all(), [
            'mode' => 'required|in:like,reply',
            'update_id' => 'required',
            'text_body' => $request->mode == 'reply' ? 'required_if:mode,reply' : '',
            'item_id' => $request->mode == 'reply' ? 'required_if:mode,reply' : '',
        ]);

        // Custom error messages
        $validator->setAttributeNames([
            'mode' => 'Mode',
            'update_id' => 'Update ID',
            'item_id' => 'Item ID',
            'text_body' => 'Text Body',
        ]);

        if ($validator->fails()) {
            return $this->returnData($validator->errors(), false);
        }

        //preparing query
        $query = "";
        if($payload['mode']=='like'){
            $query = 'mutation {
                like_update (update_id: '.$payload['update_id'].') {
                  id
                }
            }';
        } else {
            $query = 'mutation {
                create_update(item_id : '.$payload['item_id'].' parent_id:'.$payload['update_id'].' body: "'.$payload['text_body'].'") {
                  id
                }
            }';
        }

        $response = $this->_getMondayData($query);

        if(isset($response['response']['data']['create_update']['id']) || isset($response['response']['data']['like_update']['id']))
        {
            return $this->returnData($response);
        }

        return $this->returnData($response, false);

    }

    public function getSubItemByEmail(Request $request){
        
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
            boards(ids: '.$this->BOARD_ID_INCORPIFY.') {
              items_page(
                query_params: {rules: [{column_id: "'.$column_id.'", compare_value: ["'.$payload['email'].'"], operator: contains_text}]}
              ) {
                items {
                    id
                    name
                    subitems {
                      name
                      id
                      created_at
                      updated_at
                      column_values(ids: ["'.$description.'", "'.$required_action.'", "'.$assignee.'", "'.$overall_status.'"]) {
                        id
                        text
                     }
                    }
                  }
              }
            }
          }
        ';
        
        $response = $this->_getMondayData($query);
        $subitems = $response['response']['data']['boards'][0]['items_page']['items'][0]['subitems']??[];

        if(empty($subitems)){
            return $this->returnData("no data found", false);
        }

        $total_subitem = count($subitems);
        $limit = $payload['limit'];
        $skip = $payload['skip'];

        $send_response = [];
        
        if($limit> $total_subitem){
            $send_response = array_slice($subitems,$skip, $total_subitem);
        } else {
            $send_response = array_slice($subitems,$skip, $limit);
        }

        $response['response']['data']['boards'][0]['items_page']['items'][0]['subitems'] = $send_response;
        return $this->returnData($response);
    }

    public function uploadFiles(Request $request) { 


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
        add_file_to_column (item_id: ' . $itemId . ' column_id: "'.$column_id.'", file: $file) {
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

    public function createItem(Request $request) { 


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
                        "email"=>$payload['email'],
                        "text"=>$payload['email']
                    ],
                    "single_select3__1" => $payload['type_of_license']
                ],
            true),
        true);

        $group_id = "topics";


        $query = 'mutation {
            create_item(
              board_id: '.$this->BOARD_ID_INCORPIFY.'
              group_id: "'.$group_id.'"
              item_name: "'.$payload['your_company_name'].'"
              column_values: '.$column_values.'
            ) {
              id
            }
        }';

        $response = $this->_getMondayData($query);

        if(isset($response['response']['data']['create_item'])){
            return $this->returnData($response);
        }

        return $this->returnData($response, false);
        
    }

    public function returnData($data, $success = true) {

        if($success){
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
    
    public function updateSubitemStatus(Request $request) {

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

        $board_id_query = 'query {items (ids: ['.$payload['subitem_id'].']) {
                 board { id }
               }
            }
        ';

        $getBoardId = $this->_getMondayData($board_id_query);
        $getBoardId = $getBoardId['response']['data']['items'][0]['board']['id']??"";

        if(empty($getBoardId)){
            return [
                "success"=> false,
                "message"=> "item id is invalid"
            ];
        }

        $update_query = 'mutation{
            change_column_value(
              board_id: '.$getBoardId.',
              item_id: '.$payload['subitem_id'].',
              column_id: "status__1",
              value: "{\"label\": \"In Progress\"}") {
            id
            }
        }';


        $response = $this->_getMondayData($update_query);

        if($response['response']['data']['change_column_value']['id']){
            return $this->returnData($response);
        }

        return $this->returnData($response, false);
    }

    public function getSubItemDetailsById(Request $request) {

        $data = [
            "id"=> $request->id
        ];

        $rules = [
            "id" => "required|min:0|integer"
        ];

        $message = [
            'id.requried'=> "id is an required field"
        ];

        $attribute = [
            "id" => "Item id or Subitem id"
        ];

        
        $validator = Validator::make($data, $rules, $message, $attribute);

        if($validator->fails()){
            return $this->returnData($validator->errors(), false);
        }

        $description = "text";
        $required_action = "dup__of_description__1";
        $assignee = "assigness__1";
        $overall_status = "status__1";

        $query = '{
            items(ids: '.$request->id.') {
              id
              name
              created_at
              updated_at
              column_values(
                ids: ["'.$description.'", "'.$required_action.'", "'.$assignee.'", "'.$overall_status.'"]
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

        if(!isset($response['response']['data']['items'][0]))
        {
            return $this->returnData($response, false);
        }

        return $this->returnData($response);
        
    }

    //upload file to the monday.com function 
    function uploadFileToMonday($itemId, $columnId, $fileData, $fileName)
    {
        $fileContent = base64_decode($fileData);
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

        return json_decode($response->getBody()->getContents(),true);
    }


    public function uploadMondayFiles(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer',
            'file' => 'required|string',
            'file_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Get validated data
        $itemId = $request->input('item_id');
        $columnId = 'files__1';
        $base64Data = $request->input('file');
        $fileName = $request->input('file_name');

        // Upload file to Monday.com
        $response = $this->uploadFileToMonday($itemId, $columnId, $base64Data, $fileName);

        if(!isset($response['data']['add_file_to_column']['id'])){
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
                $input = $request->json()->all();
                $criteria = ['status' => 0];
                $get_data = IncorpifySiteSettings::where('id', '=', 1)->first();
    
                $insert_array = [
                    "ui_settings" => json_encode($input['ui_settings']),
                    "meeting_link" => $input['meeting_link'] ?? null,
                    "created_at"  => date("Y-m-d H:i:s"),
                    "updated_at"  => date("Y-m-d H:i:s")
                ];
    
                $datatoUpdate = [];
                if (isset($input['logo_image']) && $input['logo_image']) {
                    // Additional validation for base64 image
                    if (!$this->isValidBase64Image($input['logo_image'])) {
                        return response()->json(['response' => [], 'status' => false, 'message' => "Invalid image format. Please re-upload the image (jpeg|jpg|png)."]);
                    }
    
                    $imageData = $input['logo_image'];
                    list($type, $data) = explode(';', $imageData);
                    list(, $data)      = explode(',', $data);
                    $data      = base64_decode($data);
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
            } catch (\Exception $e) {
                return response()->json(['response' => [], 'status' => false, 'message' => $e->getMessage()]);
            }
        } else {
            return response()->json(['response' => [], 'status' => false, 'message' => "Invalid User."]);
        }
    }


}
