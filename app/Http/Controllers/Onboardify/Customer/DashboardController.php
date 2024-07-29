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
use Carbon\Carbon;
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
                $mondayData = [];
                // limit: ' . $tolalData . ', cursor:' . $cursor . ',
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
                // if (empty($emailKeyForFilter)) {
                //     return response(json_encode(array('response' => [], 'status' => false, 'message' => "The required key for Onboardify board visibility is missing.")));
                // }
                // items_page (query_params: {rules: [{column_id: "'.$emailKeyForFilter.'", compare_value: ["' . $userEmail . '"], operator: contains_text}]}){
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
                      items_page {
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
                if (!empty( $newResponse['response']['data']['boards'][0]['items_page']['items'])) {
                    return response(json_encode(array('response' => $newResponse['response'], 'status' => true, 'message' => "Board Items Data Found.")));
                }else{
                    return response(json_encode(array('response' => $newResponse['response'], 'status' => false, 'message' => "Board Items Data Not Found.")));
                }
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

    public function getboardVisibilityMapping(Request $request){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $board_id = $request->query('board_id');
                $email    = $request->query('email');
                if (!empty($board_id) && !empty($email)) {
                    $BoardColumnMappingData = BoardColumnMappings::where(['board_id' => $board_id, 'email' => $email])->get();
                    if (!empty($BoardColumnMappingData)) {
                        return response(json_encode(array('response' => $BoardColumnMappingData, 'status' => true, 'message' => "Board Column Mppaing Data.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Column Mppaing Data Not Found.")));
                    }
                } elseif (!empty($board_id) && empty($email)) {
                    $BoardColumnMappingData = BoardColumnMappings::where(['board_id' => $board_id, 'email' => ""])->get();
                    if (!empty($BoardColumnMappingData)) {
                        return response(json_encode(array('response' => $BoardColumnMappingData, 'status' => true, 'message' => "Board Column Mppaing Data.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Column Mppaing Data Not Found.")));
                    }
                } elseif (empty($board_id) && !empty($email)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Currently board not selected first select the board.")));
                } elseif (empty($board_id) && empty($email)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Currently board not selected first select the board.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function requestTrackingActivity ($itemID){
        try {
            $userId = $this->verifyToken()->getData()->id;
            
            if ($userId) {

                $getUser = MondayUsers::getUser(['id' => $userId]);
            if (!empty($getUser) && !empty($getUser->email)) {
                $userEmail = $getUser->email;
            }else{
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Login User Details Not Found")));
            }

                $BoardColumnMappingsData = BoardColumnMappings::where(['board_id' => $getUser->board_id, 'email'=>$userEmail])->first();
                if (!empty($BoardColumnMappingsData['columns'])) {
                    $onboardingColumnsKeys = json_decode($BoardColumnMappingsData['columns'], true);
                    if (!empty($onboardingColumnsKeys['onboarding_columns'])) {
                        $onboardingColumnsKeyForFilter = $onboardingColumnsKeys['onboarding_columns'];
                    }
                }else{
                    $BoardColumnMappingsData = BoardColumnMappings::where(['board_id' => $getUser->board_id, 'email'=>""])->first();
                    if (!empty($BoardColumnMappingsData['columns'])) {
                        $onboardingColumnsKeys = json_decode($BoardColumnMappingsData['columns'], true);
                        if (!empty($onboardingColumnsKeys['onboarding_columns'])) {
                            $onboardingColumnsKeyForFilter = $onboardingColumnsKeys['onboarding_columns'];
                        }
                    }else{
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Column Mapping Data Not Found.")));
                    }
                }
                if (empty($onboardingColumnsKeyForFilter)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "The required key for Onboardify board visibility is missing.")));
                }

                $idArray = [];

                foreach ($onboardingColumnsKeyForFilter as $item) {
                    $idArray[] = '"' . $item['id'] . '"';
                }

                $idString = '[' . implode(',', $idArray) . ']';
                // activity_logs (from: "' . Carbon::now()->subWeek()->startOfDay()->toIso8601String() . '", to: "' . Carbon::now()->toIso8601String() . '", column_ids:'.$idString.', item_ids:'.$itemID.')
                if (!empty($idString)) {
                    $query = '{
                        boards(ids: ' . $getUser->board_id . ') {
                        columns {
                            title
                            id
                            archived
                            description
                            settings_str
                            title
                            type
                            width
                        }activity_logs ( limit:500 column_ids:'.$idString.', item_ids:'.$itemID.') {
                          id
                          user_id
                          account_id
                          data
                          entity
                          event
                          created_at
                      }}
                     items (ids: [' . $itemID . ']) {
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
                   }';

                    $boardsItemActivityData = $this->_get($query)['response'];
                    if (!empty($boardsItemActivityData['data']['boards']) && !empty($boardsItemActivityData['data']['items'])) {
                        return response(json_encode(array('response' => $boardsItemActivityData, 'status' => true, 'message' => "Borda items data found.")));
                    }else{
                        return response(json_encode(array('response' => $boardsItemActivityData, 'status' => false, 'message' => "Borda items data not found.")));
                    }
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Borda items activity column not prepared.")));
                }
               
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }

    }

    public function getBoardColourMapping()
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $colourMappingsData = ColourMappings::get();
                if (!empty($colourMappingsData)) {
                    $data = json_decode($colourMappingsData, true);

                    $coloursData = array();
                    foreach ($data as $record) {
                        $coloursData[] = [
                            $record['colour_name'] =>  json_decode($record['colour_value'], true),
                        ];
                    }
                    return response(json_encode(array('response' => $coloursData, 'status' => true, 'message' => "Status Colour Mapping data.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Status Colour mapping data not found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function getGeneralSettings(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $get_data = SiteSettings::where('id', '=', 1)->first()->toArray();
                if (!empty($get_data)) {
                    return response(json_encode(array('response' => $get_data, 'status' => true, 'message' => "General Settings Data Fetch.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "General Settings Data Not Found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}