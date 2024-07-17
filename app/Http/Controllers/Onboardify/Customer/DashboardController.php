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
                if (!empty($getUser->board_id)) {
                   $boardId = $getUser->board_id;
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify board not assign to this user.")));
                }
                $BoardColumnMappingsData = BoardColumnMappings::where(['board_id' => $boardId, 'email'=>$userEmail])->first();
                if (!empty($BoardColumnMappingsData)) {

                }
                $boardId = !empty($GovernifySiteSettingData['board_id']) ? $GovernifySiteSettingData['board_id'] : 1493464821;
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
                      items_page (limit: ' . $tolalData . ', cursor:' . $cursor . ',query_params: {rules: [{column_id: "people0__1", compare_value: ["' . $userEmail . '"], operator: contains_text}]}){
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
echo '<pre>'; print_r( $boardsData ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
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
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}