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
use App\Models\OnboardifyProfiles;
use App\Models\OnboardifyService;
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
                    $BoardColumnMappingData = BoardColumnMappings::where(['board_id' => $board_id])->get();
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

                $BoardColumnMappingsData = BoardColumnMappings::where(['board_id' => $getUser->board_id])->first();
                if (!empty($BoardColumnMappingsData['columns'])) {
                    $onboardingColumnsKeys = json_decode($BoardColumnMappingsData['columns'], true);
                    if (!empty($onboardingColumnsKeys['onboarding_columns'])) {
                        $onboardingColumnsKeyForFilter = $onboardingColumnsKeys['onboarding_columns'];
                    }
                }else{
                    $BoardColumnMappingsData = BoardColumnMappings::where(['board_id' => $getUser->board_id])->first();
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

    public function allProfileWithServicesByUser (){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $userId = $this->verifyToken()->getData()->id;
                $getUser = MondayUsers::getUser(['id' => $userId]);
                if (!empty($getUser) && !empty($getUser->email)) {
                    $userEmail = $getUser->email;
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Login User Details Not Found")));
                }
                // $userEmail = '1524185';
                // Fetch profiles with associated services
                $dataToRender = OnboardifyProfiles::with('services')->whereRaw('FIND_IN_SET(?, users)', [$userEmail])->get();
                if (!empty($dataToRender->isNotEmpty())) {
                    return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Onboardify Profile And Services Data Found.")));
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Profile And Services Data Not Found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function getAllRequestTrackingByUserServices (){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $userId = $this->verifyToken()->getData()->id;
                $getUser = MondayUsers::getUser(['id' => $userId]);
                if (!empty($getUser) && !empty($getUser->email)) {
                    $userEmail = $getUser->email;
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Login User Details Not Found")));
                }
                // Fetch profiles with associated services
                $dataToRender = OnboardifyProfiles::with('services')->whereRaw('FIND_IN_SET(?, users)', [$userEmail])->get()->first();
                if (!empty($dataToRender)) {
                    if (!empty($dataToRender['services'])) {
                        $boardId = [];
                        foreach ($dataToRender['services'] as $services) {
                            $boardId[] = $services['board_id'];
                        }
                        if (!empty($boardId)) {
                            // $boardId = array_map('intval', $boardId);
                            // Convert the array to a string.
                            $boardIdsString = '[' . implode(',', $boardId) . ']';
                        }else{
                            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Id not found from onboardify service.")));
                        }
                        $after      = 'ddd';
                        $tolalData  = 200;
                        $cursor     = 'null';
                        $mondayData = [];
                        // limit: ' . $tolalData . ', cursor:' . $cursor . ',
                        // items_page (query_params: {rules: [{column_id: "'.$emailKeyForFilter.'", compare_value: ["' . $userEmail . '"], operator: contains_text}]}){
                        do {
                            $query = 'query {
                        boards( ids: '.$boardIdsString.') {
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
                    }else{
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Currently onboardify service not assign to this user.")));
                    }
                    
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Currently onboardify profile not assign to this user.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function requestTrackingByBoardId (Request $request,$boardId){
        try {
            $userId = $this->verifyToken()->getData()->id;
            $getUser = MondayUsers::getUser(['id' => $userId]);
            if (!empty($getUser) && !empty($getUser->email)) {
                $userEmail = $getUser->email;
            }else{
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Login User Details Not Found")));
            }
            if ($userId) {
                $limit      = !empty($request->limit) ? $request->limit : 10;
                $cursor     = !empty($request->cursor) ? 'cursor:'.'"'.$request->cursor.'"' : 'cursor:'.'null';
                // limit: ' . $tolalData . ', cursor:' . $cursor . ',
                if (empty($boardId)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Id not found.")));
                }
                // if (empty($emailKeyForFilter)) {
                //     return response(json_encode(array('response' => [], 'status' => false, 'message' => "The required key for Onboardify board visibility is missing.")));
                // }
                // items_page (query_params: {rules: [{column_id: "'.$emailKeyForFilter.'", compare_value: ["' . $userEmail . '"], operator: contains_text}]}){
                // do {
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
                      items_page (limit: ' . $limit . ', '.$cursor.' ){
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
                            }updates (limit: 500) {
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
                if (!empty( $boardsData['response']['data']['boards'][0]['items_page']['items'])) {
                    return response(json_encode(array('response' => $boardsData['response'], 'status' => true, 'message' => "Board Items Data Found.")));
                }else{
                    return response(json_encode(array('response' => $boardsData['response'], 'status' => false, 'message' => "Board Items Data Not Found.")));
                }
            }else{
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function requestTrackingByBoardIdAndSearch (Request $request,$boardId){
        try {
            $userId = $this->verifyToken()->getData()->id;
            $getUser = MondayUsers::getUser(['id' => $userId]);
            if (!empty($getUser) && !empty($getUser->email)) {
                $userEmail = $getUser->email;
            }else{
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Login User Details Not Found")));
            }
            if ($userId) {

                // Validate the input
            $validatedData = $request->validate([
                'query_params'          => 'array',
                'query_params.order_by' => 'nullable|array',
                'limit'                 => 'nullable|string',
                'cursor'                => 'nullable|string',
                'query_params.order_by.*.direction' => 'required_with:in:asc,desc',
                'query_params.order_by.*.column_id' => 'required_with:query_params.order_by|string',
                'query_params.rules'                => 'nullable|array',
                'query_params.rules.*.column_id'    => 'required_with:query_params.rules|string',
                'query_params.rules.*.compare_value' => 'required_with:query_params.rules|array',
                'query_params.rules.*.operator'      => 'nullable|string',
                'query_params.operator'              => 'nullable|string|in:and,or'
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
                $queryParams = str_replace(['"asc"', '"desc"', '"and"', '"or"', '"contains_text"'], ['asc', 'desc', 'and','or', 'contains_text'], $queryParams);
            }

            if (!empty($queryParams)) {
                $queryParamsData = 'query_params: '. $queryParams;
            }
            if (!empty($request->cursor)) {
                $cursorData = !empty($request->cursor) ? 'cursor:'.'"'.$request->cursor.'"' : 'cursor:'.'null';
            }
    
            $limit  = !empty($request->limit)  ? $request->limit  : 200;
            $cursor = !empty($cursorData) ? $cursorData : 'cursor:'.'null';
                // limit: ' . $tolalData . ', cursor:' . $cursor . ',
                if (empty($boardId)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Id not found.")));
                }
                // if (empty($emailKeyForFilter)) {
                //     return response(json_encode(array('response' => [], 'status' => false, 'message' => "The required key for Onboardify board visibility is missing.")));
                // }
                // items_page (query_params: {rules: [{column_id: "'.$emailKeyForFilter.'", compare_value: ["' . $userEmail . '"], operator: contains_text}]}){
                // do {
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
                      items_page (limit: ' . $limit . ', '.(!empty($queryParamsData) ? $queryParamsData : $cursor).'  ){
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
                            }updates (limit: 500) {
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
                if (!empty( $boardsData['response']['data']['boards'][0]['items_page']['items'])) {
                    return response(json_encode(array('response' => $boardsData['response'], 'status' => true, 'message' => "Board Items Data Found.")));
                }else{
                    return response(json_encode(array('response' => $boardsData['response'], 'status' => true, 'message' => "Board Items Data Not Found.")));
                }
            }else{
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function getFilterColumnByBoardId ($boardId){

        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $BoardColumnMappingsData = BoardColumnMappings::where(['board_id' => $boardId])->first();
                if (!empty($BoardColumnMappingsData)) {
                   $mappingData = json_decode($BoardColumnMappingsData['columns'], true);
                   if (!empty($mappingData['required_columns']) && !empty($mappingData['required_columns']['overall_status'])) {
                        $statusKey = $mappingData['required_columns']['overall_status'];

                        $query = 'query {
                            boards( ids: '.$boardId.') {
                            id
                            name
                            state
                            permissions
                            board_kind
                            columns (ids:"'.$statusKey.'"){
                                      title
                                      id
                                      archived
                                      description
                                      settings_str
                                      title
                                      type
                                      width
                                    }
                                }
                            }';
            
                        $boardsData = $this->_getMondayData($query);
                        if (!empty( $boardsData['response']['data']['boards'][0]['columns'])) {
                            return response(json_encode(array('response' => $boardsData['response'], 'status' => true, 'message' => "Board Columns Data Found.")));
                        }else{
                            return response(json_encode(array('response' => $boardsData['response'], 'status' => false, 'message' => "Board Columns Data Not Found.")));
                        }
                   }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Column Mappings Data Not Found For Filter column.")));
                   }
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Column Mappings Data Not Found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function getBoardIdByUser (){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $mondayUsers = MondayUsers::where(['id'=>$userId])->first();
                if (!empty($mondayUsers) && !empty($mondayUsers['board_id']) ) {
                    return response(json_encode(array('response' => $mondayUsers['board_id'] , 'status' => true, 'message' => "User Board Id Found.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Users data or Board Id not found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}