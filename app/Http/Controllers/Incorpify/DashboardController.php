<?php

namespace App\Http\Controllers\Incorpify;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MondayApis;

class DashboardController extends Controller
{
    use MondayApis;
    public function dashboard () {
        $query = 'query {
              boards(limit: 500, ids: 1472103835) {
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
            boards(limit: 500, ids: 1472103835) {
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
}
