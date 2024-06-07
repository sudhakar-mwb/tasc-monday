<?php

namespace App\Http\Controllers\Governify\Customer;

use App\Http\Controllers\Controller;
use App\Traits\MondayApis;
use Illuminate\Http\Request;

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
                    'from'       => 'required',
                    'to'         => 'required',
                    'column_id'  => 'required',
                ]);

                $query = '{
                    boards(limit: 500, ids: ' . $request->board_id . ') {
                             id
                             name
                             state
                             permissions
                             board_kind
                             activity_logs (from: "' . $request->from . '", to: "' . $request->to . '", column_ids:["' . $request->column_id . '"], item_ids : [' . $request->pulse_id . ']) {
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

                return $this->fetchMondayData($boardId);
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
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function fetchMondayData($boardId)
    {
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
        $totalMondayData = count($mondayData);

        unset($newResponse['response']['data']['boards'][0]['items_page']['items']);
        $newResponse['response']['data']['boards'][0]['items_page']['items'] = $mondayData;
        return $newResponse;
    }
}
