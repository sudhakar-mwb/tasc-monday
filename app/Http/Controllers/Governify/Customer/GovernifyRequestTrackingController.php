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
            'query_params' => 'required|array',
            'query_params.order_by' => 'nullable|array',
            'query_params.order_by.*.direction' => 'in:asc,desc',
            'query_params.order_by.*.column_id' => 'required_with:query_params.order_by|string',
            'query_params.rules' => 'nullable|array',
            'query_params.rules.*.column_id' => 'required_with:query_params.rules|string',
            'query_params.rules.*.compare_value' => 'required_with:query_params.rules|array',
            'query_params.operator' => 'nullable|string|in:and,or'
        ]);

        // Prepare the query
        $queryParams = json_encode($validatedData['query_params']);

        // Remove the surrounding double quotes from the JSON string
        $queryParams = str_replace(['"{', '}"'], ['{', '}'], $queryParams);
        $queryParams = str_replace(['"direction":', '"column_id":', '"compare_value":', '"operator":', '"rules":', '"order_by":'], ['direction:', 'column_id:', 'compare_value:', 'operator:', 'rules:', 'order_by:'], $queryParams);


        // Manually format the query parameters string to match the required format
        // $queryParams = str_replace(['"{', '}"'], ['{', '}'], $queryParams);
        // $queryParams = preg_replace('/"(\w+)":/u', '$1:', $queryParams);
        // Specifically replace the values for direction to be unquoted
        $queryParams = str_replace(['"asc"', '"desc"', '"and"'], ['asc', 'desc', 'and'], $queryParams);



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
                  items_page (limit: 3, cursor:null,  query_params: '.$queryParams.'  ){
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
}
