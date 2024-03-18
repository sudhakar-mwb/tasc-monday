<?php

namespace App\Http\Controllers\Monday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MondayApis;

class TrackOnboardingController extends Controller
{
    use  MondayApis;

    public function trackOnboarding(Request $request, $cursor = null)
    {
        echo '<pre>'; print_r($request ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
        $query = "query {
            boards(ids: 1390329031) {
               columns {
                  title
                  id
               }
               items_page (limit: 2, cursor:".$cursor.") {
                  cursor
                  items {
                      id
                      name
                      email
                      column_values {
                         id
                         value
                         type
                         ... on StatusValue  { # will only run for status columns
                            label
                            update_id
                         }
                     }
                  }
              }
            }
        }";
        echo '<pre>'; print_r( $query ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
        return $response = $this->_get($query);
    }
}
