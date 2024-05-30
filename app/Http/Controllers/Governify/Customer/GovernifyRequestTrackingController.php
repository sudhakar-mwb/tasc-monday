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
        // $query = 'query {
        //     boards(limit: 500, ids: 1493464821) {
        //     id
        //     name
        //     state
        //     permissions
        //     board_kind
        //     columns {
        //               title
        //               id
        //               archived
        //               description
        //               settings_str
        //               title
        //               type
        //               width
        //           }
        //           items_page (limit: 3, cursor:null,  query_params: {} ){
        //               cursor,
        //               items {
        //                   created_at
        //                   creator_id
        //                   email
        //                   id
        //                   name
        //                   relative_link
        //                   state
        //                   updated_at
        //                   url
        //                   column_values {
        //                      id
        //                      value
        //                      type
        //                      text
        //                      ... on StatusValue  {
        //                         label
        //                         update_id
        //                      }
        //                  }
        //                  subitems {
        //                   created_at
        //                   creator_id
        //                   email
        //                   id
        //                   name
        //                   relative_link
        //                   state
        //                   updated_at
        //                   url
        //                   column_values {
        //                   id
        //                   value
        //                   type
        //                   text
        //                   ... on StatusValue  {
        //                      label
        //                      update_id
        //                           }
        //                       }
        //                   }
        //               }
        //           }
        //     owners {
        //       id
        //       name
        //       email
        //     }
  
        //     subscribers {
        //       id,
        //       name,
        //       email
        //       enabled,
        //       is_guest,
        //       is_view_only
        //     }
        //   }
        // }';
echo '<pre>'; print_r( $request ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
        return $boardsData = $this->_getMondayData($query);
    }
}
