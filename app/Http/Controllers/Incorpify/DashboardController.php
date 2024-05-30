<?php

namespace App\Http\Controllers\Incorpify;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MondayApis;
use Illuminate\Support\Facades\Validator;
use CURLFile;

class DashboardController extends Controller
{
    use MondayApis;
    public function dashboard () {
        $query = 'query {
              boards(limit: 500, ids: '.env("BOARD_ID_INCORPIFY").') {
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
            boards(limit: 500, ids: '.env("BOARD_ID_INCORPIFY").') {
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
            return response()->json(['errors' => $validator->errors()], 422);
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
        return $this->_getMondayData($query);
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
            return response()->json(['errors' => $validator->errors()], 422);
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

        return $this->_getMondayData($query);
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //search the subitems of the items by email address
        $column_id = "contact_email";
        $description = "text";
        $required_action = "text2";
        $assignee = "dropdown7";
        $overall_status = "overall_status";

        $query = '{
            boards(ids: '.env("BOARD_ID_INCORPIFY").') {
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
                       updates {
                        id
                        text_body
                        
                        replies {
                          id text_body
                        }
                     }
                    
                    assets {
                      id
                      url
                    }
                    }
                  }
              }
            }
          }
        ';
        
        $response = $this->_getMondayData($query);
        $subitems = $response['response']['data']['boards'][0]['items_page']['items'][0]['subitems'];
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
        return $response;
    }

    public function uploadFiles(Request $request) { 


        $payload = $request->all();

        echo '<pre>';
        print_r($payload);
        echo '[Line]:     ' . __LINE__ . "\n";
        echo '[Function]: ' . __FUNCTION__ . "\n";
        echo '[Class]:    ' . (__CLASS__ ? __CLASS__ : 'N/A') . "\n";
        echo '[Method]:   ' . (__METHOD__ ? __METHOD__ : 'N/A') . "\n";
        echo '[File]:     ' . __FILE__ . "\n";
        die;
        
        
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $column_values = json_encode(
            json_encode(
                [
                "email" => $payload['email'],
                "single_select3__1" => $payload['type_of_license']  
                ],
            true),
        true);

        $group_id = "topics";


        $query = 'mutation {
            create_item(
              board_id: '.env("BOARD_ID_INCORPIFY").'
              group_id: "'.$group_id.'"
              item_name: "'.$payload['your_company_name'].'"
              column_values: '.$column_values.'
            ) {
              id
            }
        }';

        $response = $this->_getMondayData($query);

        return $response;
        
    }

}
