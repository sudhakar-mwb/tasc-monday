<?php

namespace App\Http\Controllers\Onboardify\Customer;

use App\Http\Controllers\Controller;
use App\Models\BoardColumnMappings;
use App\Models\ColourMappings;
use App\Models\GovernifyServiceCategorie;
use App\Models\MondayUsers;
use App\Models\SiteSettings;
use Illuminate\Http\Request;
use App\Traits\MondayApis;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
class DashboardController extends Controller
{
    use MondayApis;

    public function requestTracking(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            $getUser = MondayUsers::getUser(['id' => $userId]);
            if (!empty($getUser) && !empty($getUser->email)) {
                $userEmail = $getUser->email;
            }else{
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Login User Details Not Found")));
            }
            if ($userId) {
                $after      = 'ddd';
                $tolalData  = 200;
                $cursor     = 'null';
                $mondayData = '';
                if (!empty($getUser->board_id)) {
                   $boardId = $getUser->board_id;
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify board not assign to this user.")));
                }
                $BoardColumnMappingsData = BoardColumnMappings::where(['board_id' => $boardId, 'email'=>$userEmail])->first();
                if (!empty($BoardColumnMappingsData['columns'])) {
                    $checkEmailKey = json_decode($BoardColumnMappingsData['columns'], true);
                    if (!empty($checkEmailKey['email_key'])) {
                        $emailKeyForFilter = $checkEmailKey['email_key'];
                    }
                }else{
                    $BoardColumnMappingsData = BoardColumnMappings::where(['board_id' => $boardId, 'email'=>""])->first();
                    if (!empty($BoardColumnMappingsData['columns'])) {
                        $checkEmailKey = json_decode($BoardColumnMappingsData['columns'], true);
                        if (!empty($checkEmailKey['email_key'])) {
                            $emailKeyForFilter = $checkEmailKey['email_key'];
                        }
                    }else{
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Column Mapping Data Not Found.")));
                    }
                }
                if (empty($emailKeyForFilter)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "The required key for Onboardify board visibility is missing.")));
                }
                do {
                    $query = 'query {
                boards( ids: '.$boardId.') {
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
                      items_page (limit: ' . $tolalData . ', cursor:' . $cursor . ',query_params: {rules: [{column_id: "'.$emailKeyForFilter.'", compare_value: ["' . $userEmail . '"], operator: contains_text}]}){
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
                             }updates (limit: 200) {
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
                return $newResponse;
            }else{
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function getUserFormAndChart (){
        try {
            $userId = $this->verifyToken()->getData()->id;
            $getUser = MondayUsers::getUser(['id' => $userId]);
            if (!empty($getUser) && !empty($getUser->email)) {
                $userEmail = $getUser->email;
            }else{
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Login User Details Not Found")));
            }
            if ($userEmail) {
                if (!empty($getUser->board_id)) {
                    $getBoardColumnMappings = BoardColumnMappings::where(['board_id' => $getUser->board_id, 'email'=>$getUser->email])->first();
                    if(!empty($getBoardColumnMappings)){
                        return response(json_encode(array('response' => $getBoardColumnMappings, 'status' => true, 'message' => "Board Column Mapping Data.")));
                    }
                    else{
                        $getBoardColumnMappings = BoardColumnMappings::where(['board_id' => $getUser->board_id, 'email'=>""])->first();
                        if (!empty($getBoardColumnMappings)) {
                            return response(json_encode(array('response' => $getBoardColumnMappings, 'status' => true, 'message' => "Board Column Mapping Data.")));
                        }else{
                            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Column Mapping Data Not Found.")));
                        }
                    }
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Currently The Board Is Not Assigned To User. First, Assign The Board To The User.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}