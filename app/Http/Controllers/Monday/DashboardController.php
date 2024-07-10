<?php

namespace App\Http\Controllers\Monday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Traits\MondayApis;
use Carbon\Carbon;
use App\Models\MondayUsers;
use App\Models\BoardColumnMappings;
// use App\Models\ColourMappings;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Session;
use App\Models\ColourMappings;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use App\Models\SiteSettings;

class DashboardController extends Controller
{
  use MondayApis;

  public function info()
  {
    return view('auth.info');
  }

  public function dashboard()
  {
    $heading = 'Onboardify';
    $cards = [
      [
        'title' => 'Request Onboarding ',
        'btn_text' => 'Request',
        'icon' =>
        '<span class="iconElement__inner_2iN"><svg xmlns="http://www.w3.org/2000/svg" data-name="Слой 1" viewBox="0 0 128 128" x="0px" y="0px" style="max-width:100%" height="100%"><title style="" fill="currentColor">ic_request_group</title><path d="M90.16,92.23a16.15,16.15,0,1,0-17.45,0A27.35,27.35,0,0,0,64,97,38.59,38.59,0,0,0,49,88.83a22.26,22.26,0,1,0-20.75,0A38.66,38.66,0,0,0,0,126a2,2,0,0,0,4,0,34.6,34.6,0,0,1,69.19,0,2,2,0,0,0,4,0A38.46,38.46,0,0,0,66.92,99.82,23.56,23.56,0,0,1,105,118.37a2,2,0,0,0,4,0A27.61,27.61,0,0,0,90.16,92.23ZM20.34,69.15A18.26,18.26,0,1,1,38.59,87.41,18.28,18.28,0,0,1,20.34,69.15Zm49,9.51A12.15,12.15,0,1,1,81.44,90.81,12.16,12.16,0,0,1,69.29,78.66Z" style="" fill="currentColor"></path><path d="M128,44.66c-.18-2.75-2.34-3.79-3.92-4.55l-.82-.41h0a8,8,0,0,1-4.12-6.65V20.41a20.63,20.63,0,0,0-41.26,0V33.05a8,8,0,0,1-4.12,6.65c-.24.13-.49.25-.74.36-1.52.74-3.82,1.84-4,4.6A5.92,5.92,0,0,0,70.7,49,6,6,0,0,0,75,51H88.18a10.42,10.42,0,0,0,20.64,0H122a6,6,0,0,0,4.31-2A5.92,5.92,0,0,0,128,44.66ZM98.5,56a6.43,6.43,0,0,1-6.26-5h12.52A6.43,6.43,0,0,1,98.5,56Zm24.89-9.73A2.16,2.16,0,0,1,122,47H75a2.16,2.16,0,0,1-1.38-.75A2,2,0,0,1,73,44.92c0-.37.46-.63,1.74-1.25.29-.14.57-.27.85-.42a12,12,0,0,0,6.28-10.2V20.41a16.63,16.63,0,0,1,33.26,0V33.05a12,12,0,0,0,6.28,10.2l.92.46c1.51.73,1.65.89,1.67,1.21A2,2,0,0,1,123.39,46.27Z" style="" fill="currentColor"></path></svg></span>',
        'description' =>
        'Streamline your employee onboarding with TASC Outsourcing. Request here for a hassle-free experience, letting us handle the rest with care and efficiency.',
        'link' => URL::to('/') . '/onboardify/form/candidate-form'
      ],
      [
        'title' => 'Track Onboarding',
        'btn_text' => 'Track',
        'icon' =>
        '<span class="iconElement__inner_2iN"><svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" style="max-width:100%" height="100%"><g transform="translate(0,-952.36218)" style="" fill="currentColor"><path style="text-indent:0;text-transform:none;direction:ltr;block-progression:tb;baseline-shift:baseline;color:#000000;enable-background:accumulate" d="m 37.012126,960.36576 c -16.0043,0 -29,12.99558 -29,29.00001 0,16.00443 12.9957,29.00003 29,29.00003 7.6449,0 14.5979,-2.9705 19.7813,-7.8125 l 4.5313,4.5312 -2.125,2.125 c -0.3655,0.3697 -0.3655,1.0366 0,1.4063 l 25.437398,25.4687 c 0.3697,0.3655 1.0366,0.3655 1.4063,0 l 5.6563,-5.6562 c 0.3842,-0.3718 0.3842,-1.0657 0,-1.4375 l -25.437598,-25.4375 c -0.3697,-0.3655 -1.0365,-0.3655 -1.4062,0 l -2.125,2.125 -4.5,-4.5313 c 4.8333,-5.1817 7.7812,-12.14413 7.7812,-19.78123 0,-16.00443 -12.9956,-29.00001 -29,-29.00001 z m 0,2 c 14.9235,0 27,12.07646 27,27.00001 0,14.92353 -12.0765,27.00003 -27,27.00003 -14.9236,0 -27,-12.0765 -27,-27.00003 0,-14.92355 12.0764,-27.00001 27,-27.00001 z m 0,5 c -5.3158,0 -10.6381,2.08089 -14.6874,6.25 -0.4228,0.59717 -0.4657,1.05369 -0.026,1.47395 0.6635,0.40454 0.9533,0.31438 1.4325,-0.0365 7.3454,-7.56266 19.217,-7.56269 26.5625,0 0.3127,0.34071 0.8672,0.41003 1.25,0.15625 0.5062,-0.32717 0.5888,-1.169 0.1563,-1.59375 -4.0494,-4.16911 -9.3717,-6.25 -14.6876,-6.25 z m 28.5313,46.31254 24.031298,24.0312 -4.2188,4.25 -24.062498,-24.0625 z" fill="currentColor" fill-opacity="1" stroke="currentColor" marker="none" visibility="visible" display="inline" overflow="visible"></path></g></svg></span>',
        'description' =>
        'Track your onboarding requests seamlessly with us. Stay updated on the progress of your employee onboarding journey. Effortless tracking for a smoother onboarding experience.',
        'link' => URL::to('/') . '/onboardify/form/track-request'
      ],
      [
        'title' => 'Overall Status',
        'btn_text' => 'Check',
        'icon' =>
        '<span class="iconElement__inner_2iN"><svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" style="max-width:100%" height="100%"><g transform="translate(0,-952.36218)" style="" fill="currentColor"><path style="font-size:medium;font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;text-indent:0;text-align:start;text-decoration:none;line-height:normal;letter-spacing:normal;word-spacing:normal;text-transform:none;direction:ltr;block-progression:tb;writing-mode:lr-tb;text-anchor:start;baseline-shift:baseline;opacity:1;color:#000000;fill-opacity:1;stroke:none;stroke-width:4;marker:none;visibility:visible;display:inline;overflow:visible;enable-background:accumulate;font-family:Sans;-inkscape-font-specification:Sans" d="M 40.8125 13 A 2.0002 2.0002 0 0 0 39 15 L 39 78 A 2.0002 2.0002 0 0 0 41 80 L 59 80 A 2.0002 2.0002 0 0 0 61 78 L 61 15 A 2.0002 2.0002 0 0 0 59 13 L 41 13 A 2.0002 2.0002 0 0 0 40.8125 13 z M 43 17 L 57 17 L 57 76 L 43 76 L 43 17 z M 67.8125 36 A 2.0002 2.0002 0 0 0 66 38 L 66 78 A 2.0002 2.0002 0 0 0 68 80 L 86 80 A 2.0002 2.0002 0 0 0 88 78 L 88 38 A 2.0002 2.0002 0 0 0 86 36 L 68 36 A 2.0002 2.0002 0 0 0 67.8125 36 z M 70 40 L 84 40 L 84 76 L 70 76 L 70 40 z M 13.8125 50 A 2.0002 2.0002 0 0 0 12 52 L 12 78 A 2.0002 2.0002 0 0 0 14 80 L 32 80 A 2.0002 2.0002 0 0 0 34 78 L 34 52 A 2.0002 2.0002 0 0 0 32 50 L 14 50 A 2.0002 2.0002 0 0 0 13.8125 50 z M 16 54 L 30 54 L 30 76 L 16 76 L 16 54 z M 7.8125 83 A 2.0021961 2.0021961 0 1 0 8 87 L 92 87 A 2.0002 2.0002 0 1 0 92 83 L 8 83 A 2.0002 2.0002 0 0 0 7.8125 83 z " transform="translate(0,952.36218)" fill="currentColor"></path></g></svg></span>',
        'description' =>
        'Stay in the loop with ease! Check the overall status of your onboarding requests and keep tabs on your employee onboarding progress for a comprehensive overview of the entire process.',
        'link' => URL::to('/') . '/onboardify/form/candidate-stats'
      ],
      // Add more card data as needed
    ];
    return view('admin.dashboard', compact('heading', 'cards'));
  }

  public function trackRequest(Request $request)
  {
    $prev_cursor = null;
    $cs = 1;
    $operation_query = "";
    $searchquery = "";
    $boardColumnMappingDbData = "";
    $sortbyname = request()->input('sort_by_date') ?? '';
    $status_filter = request()->input('status_filter') ?? '';
    $limit = (int)(request()->input('limit') ?? '25');
    if (!empty(auth()->user()) && !empty(auth()->user()->board_id)) {
      $boardId  = auth()->user()->board_id;
      $response = BoardColumnMappings::where('board_id', '=', $boardId)->get();
      $response = json_decode($response, true);
      if ($response['0']['columns'] ?? false) {
        $boardColumnMappingDbData = $response['0']['columns'];
      } else {
        die('board column mapping not exist in db');
      }
    } else {
      $heading = "Pending";
      $subheading = "Currently Board not Assigned!";
      $status = false;
      return view('auth.thankssignup', compact('status', 'heading', 'subheading'));
    }
    if ($request->isMethod('post')) {
      if (request()->has('cursor')) {
        $cs = (int)$request->input('cursor');
      }
      $searchAvailable = (request()->has('search') && trim(request()->input('search')) !== "");
      $sortAvailable = request()->has('sort_by_date') && trim(request()->input('sort_by_date') !== '');
      $isStatusFilterAvailable = request()->has('status_filter') && trim(request()->input('status_filter')) !== '' && request()->has('status_filter') != null;
      if ($searchAvailable || $isStatusFilterAvailable) {
        $column_setting=json_decode($boardColumnMappingDbData);
        $profession=$column_setting->required_columns->profession;
        $overall_status=$column_setting->required_columns->overall_status;
        $operation_query = ', query_params: { groups:[';
        if ($searchAvailable) {
          $searchquery = request()->input('search');
          $operation_query .= ' { rules: [ {
            column_id: "'.$profession.'",
            compare_value: ["' . request()->input('search') . '"],
            operator: starts_with
          },
          {column_id: "name",
             compare_value: ["' . request()->input('search') . '"],
             operator: starts_with
            }], operator: or }';
        }
        if ($isStatusFilterAvailable) {
          $operation_query .= '{ rules: [{column_id: "'.$overall_status.'", compare_value: [' . request()->input('status_filter') . ']}]
            ,operator: and  }';
        }
        $operation_query .= "]
        operator: and }";
      }
    };
    $response = $this->fetchMondayData($limit, $cs, $operation_query);
    $heading = 'Request Tracking';
    $subheading = 'Track your onboarding progress effortlessly by using our request-tracking center';
    if ($request->export == true) {
      $query = "query {
            boards(ids: " . auth()->user()->board_id . ") {
                   columns {
                      title
                      id
                   }
                   items_page (limit: 500, cursor: null) {
                      cursor
                      items {
                          id
                          name
                          email
                          created_at
                          column_values {
                             id
                             value
                             type
                             text
                             ... on StatusValue  {
                                label
                                update_id
                                index
                                value
                             }
                         }
                      }
                  }
                }
             }";
      $exportResponse = $this->_get($query)['response'];
      // Loop through items and write data
      if (!empty($exportResponse['data']['boards'][0]['items_page']['items']) && !empty($exportResponse['data']['boards'][0]['columns'])) {
        $boardId                     = auth()->user()->board_id;
        $BoardColumnMappingsResponse = BoardColumnMappings::where('board_id', '=', $boardId)->get();
        $BoardColumnMappingsResponse = json_decode($BoardColumnMappingsResponse, true);
        if (!empty($BoardColumnMappingsResponse['0']['columns'])) {
          $columnMappingsData = json_decode($BoardColumnMappingsResponse['0']['columns'], true);

          if (!empty($columnMappingsData['onboarding_columns']) && !empty($columnMappingsData['candidate_coulmns']) && !empty($columnMappingsData['sub_headings_column'])) {
            $requiredCSVColumns = array_merge($columnMappingsData['onboarding_columns'], $columnMappingsData['candidate_coulmns'], $columnMappingsData['sub_headings_column']);

            // Remove duplicate arrays based on their values
            $uniqueData = array_map("unserialize", array_unique(array_map("serialize", $requiredCSVColumns)));

            // Reindex the array to reset keys
            // $uniqueData   = array_values($uniqueData);
            // $unique_names = array_column($uniqueData, 'name');

            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="onboardify_data.csv"');
            // Open file pointer
            $output = fopen('php://output', 'w');

            $new_array  = array();
            foreach ($uniqueData as $item) {
              $new_array[$item['id']] = $item['name'];
            }

            // fputcsv($output, $new_array);

            $csvHeader = [];
            foreach ($exportResponse['data']['boards'][0]['items_page']['items'] as $item) {
              if (!empty($item['column_values'])) {
                if (!empty($item['name'])) {
                  $csvHeader['name'] = $item['name'];
                }
                foreach ($item['column_values'] as $itemValue) {
                  if (array_key_exists($itemValue['id'], $new_array)) {
                    $csvHeader[$new_array[$itemValue['id']]] =  $itemValue['text'];
                  }
                }
              }
            }
            fputcsv($output, array_keys($csvHeader));

            $rows    = [];
            $rowData = [];
            foreach ($exportResponse['data']['boards'][0]['items_page']['items'] as $item) {
              if (!empty($item['column_values'])) {
                if (!empty($item['name'])) {
                  $rowData['name'] = $item['name'];
                }
                foreach ($item['column_values'] as $itemValue) {
                  if (array_key_exists($itemValue['id'], $new_array)) {
                    $rowData[$new_array[$itemValue['id']]] =  $itemValue['text'];
                  }
                }
                $rows[] = $rowData;
              }
              // fputcsv($output, array_keys($rowData));
              fputcsv($output, $rowData);
            }
            // Close file pointer
            fclose($output);
            return true;
          } else {
            $heading    = "No Data Found";
            $subheading = "Board column data mapping not found for onboarding_columns or candidate_coulmns or sub_headings_column.";
            $status     = false;
            return view('auth.thankssignup', compact('status', 'heading', 'subheading'));
          }
        } else {
          $heading    = "No Data Found";
          $subheading = "Board column data mapping not found.";
          $status     = false;
          return view('auth.thankssignup', compact('status', 'heading', 'subheading'));
        }
        /* All data export from monday.com commented
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="monday_com_data.csv"');

        // // Open file pointer
        $output = fopen('php://output', 'w');

        $headers      = [];
        foreach ($exportResponse['data']['boards'][0]['columns'] as $key => $value) {
          $headers[$value['id']] = $value['title'];
        }

        fputcsv($output, $headers);
        foreach ($exportResponse['data']['boards'][0]['items_page']['items'] as $item) {
          $rows = [];
          if (!empty($item['column_values'])) {
            $rowData = [];
            if (!empty($item['name'])) {
              $rowData[$headers['name']] = $item['name'];
            }
            foreach ($item['column_values'] as $itemValue) {
              if (array_key_exists($itemValue['id'], $headers)) {
                $rowData[$headers[$itemValue['id']]] =  $itemValue['text'];
              }
            }
            $rows[] = $rowData;
          }
          fputcsv($output, $rowData);
        }

        // Close file pointer
        fclose($output);
        return true;
        // return Response::make('', 200, $headers);

        // Save the CSV file to a local storage folder
        // $filePath = storage_path('app/monday_com_data.csv');
        // file_put_contents($filePath, file_get_contents('php://output'));
      */
      } else {
        $heading    = "No Data Found";
        $subheading = "Something went wrong. Data not found from board.";
        $status     = false;
        return view('auth.thankssignup', compact('status', 'heading', 'subheading'));
      }
    }
    if ($request->isMethod('post') && $sortAvailable) {
      if ($sortbyname == "asc") {
        usort($response['data']['boards'][0]['items_page']['items'], function ($a, $b) {
          return strtotime($a['created_at']) - strtotime($b['created_at']);
        });
      }

      if ($sortbyname == "desc") {
        usort($response['data']['boards'][0]['items_page']['items'], function ($a, $b) {
          return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
      }
      // dd($response['data']['boards'][0]['items_page']['items']);
    }
    $data = json_decode($boardColumnMappingDbData, true);
    $data = $data['card_section'];
    $items = $response['data']['boards'][0]['items_page']['items'];
    if ($request->isMethod('get') && (!$items || count($items) == 0)) {
      $heading = "No Data Found";
      $subheading = "The board lacks sufficient data.";
      $status = false;
      return view('auth.thankssignup', compact('status', 'heading', 'subheading'));
    }
    $colors_status = json_decode($this->getColourMapping());
    $data['status_color'] = $colors_status;
    return view('admin.track_request', compact('heading', 'subheading', 'response', 'searchquery', 'sortbyname', 'status_filter', 'data', 'limit', 'prev_cursor', 'cs'));
  }

  public function manageById(Request $request)
  {
    if (!empty(auth()->user()) && !empty(auth()->user()->board_id)) {
      $boardId  = auth()->user()->board_id;
      $response = BoardColumnMappings::where('board_id', '=', $boardId)->get();
      $response = json_decode($response, true);

      if ($response['0']['columns'] ?? false) {
        $boardColumnMappingDbData = $response['0']['columns'];
      } else {
        die('board column mapping not exist in db');
      }
    } else {
      $heading = "Pending";
      $subheading = "Currently Board not Assigned!";
      $status = false;
      return view('auth.thankssignup', compact('status', 'heading', 'subheading'));
    }
    $id = request()->route('id');
    $userName = request()->route('username');;
    $boardId  = "";
    $data = "{}";
    if (!empty(auth()->user()) && !empty(auth()->user()->board_id)) {
      $boardId  = auth()->user()->board_id;

      $response = BoardColumnMappings::where('board_id', '=', $boardId)->get();
      $response = json_decode($response, true);
      $colors_status = json_decode($this->getColourMapping());

      if ($response['0']['columns'] ?? false) {
        $data = json_decode($response['0']['columns'], true);
        $data['status_color'] = $colors_status;
      } else {
        die('board column mapping not exist in db');
      }
    } else {
      $heading = "Pending";
      $subheading = "Currently Board not Assigned!";
      $status = false;
      return view('auth.thankssignup', compact('status', 'heading', 'subheading'));
    }
    $query = '{
            boards(ids: ' . $boardId . ') {
            columns {
               title
               id
            }activity_logs (from: "' . Carbon::now()->subWeek()->startOfDay()->toIso8601String() . '", to: "' . Carbon::now()->toIso8601String() . '", column_ids:["status4","status7","status54","status6","status1"]) {
              id
              user_id
              account_id
              data
              entity
              event
              created_at
          }}
         items (ids: [' . $id . ']) {
           id
           name
           email
           created_at
           column_values {
                   id
                   text
                      value
                      type
                      ... on StatusValue  {
                         label
                         update_id
                         index
                         value
                      }
               }
           }
       }';
    $response = $this->_get($query)['response'];
    $heading = 'Request Tracking';
    $subheading = 'Track your onboarding progress effortlessly by using our request-tracking center';

    return view('admin.user_details', compact('response', 'data', 'boardColumnMappingDbData'));
  }

  public function mobilityform()
  {
    $heading = "Create Onboarding Request";
    $subheading = "Provide essential details to ensure a smooth and efficient onboarding experience for your new team members. Let's get started on building your workforce seamlessly";
    $boardId  = auth()->user()->board_id;
    if (empty($boardId)) {
      $heading = "Pending";
      $subheading = "Currently Board not Assigned!";
      $status = false;
      return view('auth.thankssignup', compact('status', 'heading', 'subheading'));
    }
    $data = $this->getBoardColumnsEmbedById($boardId);
    $embed_code = "";

    if ($data)
      $embed_code = (array)$data;

    if ($data[0]['columns']['extra_details'])
      $embed_code = $data[0]['columns']['extra_details']['form_embed_code'] ?? "";
    // dd($embed_code);
    return view('admin.mobilityform', compact('heading', 'subheading', 'embed_code'));
    // return view('admin.mobilityform');
  }

  public function stats()
  {
    $heading = "Overall Status";
    $subheading = "Stay informed and in control of the overall status of your onboarding requests";
    $boardId  = auth()->user()->board_id;
    if (empty($boardId)) {
      $heading = "Pending";
      $subheading = "Currently Board not Assigned!";
      $status = false;
      return view('auth.thankssignup', compact('status', 'heading', 'subheading'));
    }
    $data = $this->getBoardColumnsEmbedById($boardId);
    $embed_code = "";

    if ($data)
      $embed_code = (array)$data;

    if ($data[0]['columns']['extra_details'])
      $embed_code = $data[0]['columns']['extra_details']['chart_embed_code'] ?? "";
    return view('admin.stats', compact('heading', 'subheading', 'embed_code'));
  }

  public function columnAllowed()
  {
    $query = 'query
        {
          boards (limit:500){
            id
            name
            state
            permissions
            board_kind
            columns {
              id
              title
              description
              type
              settings_str
              archived
              archived
              width
            }
          }
        }';
    $boardsData = $this->_get($query)['response'];
    if (isset($boardsData['data']) && !empty($boardsData['data'])) {
      $boardsData =  $boardsData['data'];
    }else{
      $heading = "Board Visibility";
      $subheading = "Column restrictions can be set per board by selecting respective column boards.";
      return view('admin.visiblility', compact('heading', 'subheading'));
    }

    if (!empty($boardsData['boards'])) {
      $idArray = array();
      foreach ($boardsData['boards'] as $item) {
        $idArray[] = ['id' => $item['id'], 'name' => $item['name']];
      }
    }

    $colourMappingsData = ColourMappings::get();
    $mondayUsers = MondayUsers::where('role', '=', '0')->whereNotNull('board_id')->latest()->get();
    if (!empty($mondayUsers)) {
      $mondayUsersData = $mondayUsers;
    }else{
      $mondayUsersData = '';
    }

    if (!empty($colourMappingsData)) {
      $data = json_decode($colourMappingsData, true);

      $coloursData = array();
      foreach ($data as $record) {
        $coloursData[$record['colour_name']] = ["val" => json_decode($record['colour_value'], true), "rgb_code" => $record['rgb_code']];
      }
    }
    Session::flash('error', 'Something went wrong during fetch colour mapping data.');
    $heading = "Board Visibility";
    // $boards=['3454','5345','34553','5345','3553','3455','4355','34553','35345'];
    $boards = $idArray;
    $subheading = "Column restrictions can be set per board by selecting respective column boards.";
    return view('admin.visiblility', compact('heading', 'subheading', 'boards', 'coloursData', 'mondayUsersData'));
  }

  public function userslist(Request $request)
  {
    $msg        = '';
    $status     = '';
    $heading    = "Registerd users";
    $subheading = "Stay informed and in control of the overall status of your onboarding requests";
    $mondayUsers = MondayUsers::latest()->get();
    $query = 'query {
              boards(limit: 500) {
                id
                name
                state
                permissions
                board_kind

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
    // dd($this->_get($query)['response']);
    $boardsData = $this->_get($query)['response'];
    if (isset($boardsData['data']) && !empty($boardsData['data'])) {
      $boardsData =  $boardsData['data'];
    }else{
      return view('admin.users', compact('heading', 'subheading', 'mondayUsers', 'boardsData'));
    }
    if ($request->isMethod('post')) {
      if (!empty(auth()->user()) && (auth()->user()->role == 2 || auth()->user()->role == 1)) {
        if (!empty($request->board_id) && !empty($request->user_id)) {
          $boardId  = $request->board_id;
          $id    = $request->user_id;
          $response = MondayUsers::where('id', $id)->update(['board_id' => $boardId]);
          if ($response) {
            $msg    = 'Board ID assign to user successfully.';
            $status = 'success';
            return view('admin.users', compact('heading', 'subheading', 'msg', 'status', 'mondayUsers', 'boardsData'));
          } else {
            $msg    = 'Something went wrong during assign board to user.';
            $status = 'danger';
            return view('admin.users', compact('heading', 'subheading', 'msg', 'status', 'mondayUsers', 'boardsData'));
          }
        } else {
          $msg    = 'Request not received for assign board to the user.';
          $status = 'danger';
          return view('admin.users', compact('heading', 'subheading', 'msg', 'status', 'mondayUsers', 'boardsData'));
        }
      } else {
        $msg    = 'You have not authorized to update user board mapping field.';
        $status = 'danger';
        return view('admin.users', compact('heading', 'subheading', 'msg', 'status', 'mondayUsers', 'boardsData'));
      }
    }
    // $heading="Board Visibility";
    return view('admin.users', compact('heading', 'subheading', 'mondayUsers', 'boardsData'));
  }

  public function usersBoardAssign(Request $request)
  {
    // $input   = $request->all();
    // $id      = trim($input['id']);
    // $boardId = trim($input['board_id']);
    $id      = '3';
    $boardId = '13526074001';

    // Example data for the new record or update
    $data = [
      'board_id' => $boardId,
    ];

    // Criteria to check for existing record
    $criteria = [
      'id' => $id,
      // Add more criteria as needed
    ];

    // Retrieve the existing record
    $user = MondayUsers::find($criteria['id']);

    // Using updateOrCreate
    $response = MondayUsers::updateOrCreate($criteria, $data);

    // Check if the record was updated
    if ($user && $response->wasChanged()) {
      Session::flash('message', 'User successfully assigned to board!');
    } else {
      Session::flash('message', 'User alredy assigned to board!');
    }
    // echo '<pre>'; print_r( $response ); echo '</pre>';die('just_die_here_'.__FILE__.' Function -> '.__FUNCTION__.__LINE__);
    // return response()->json(['status' => 'success', 'data' => 'Successfully updated the deal in hubspot']);
  }

  public function getBoardColumns(Request $request)
  {

    $boardId =  $request->id;
    if (!empty($boardId)) {
      $query = 'query
                        {
                            boards(ids: ' . $boardId . ') {
                                columns {
                                id
                                title
                                description
                                type
                                settings_str
                                archived
                                archived
                                width
                                }
                            }
                        }';
      $boardsData = $this->_get($query)['response']['data'];
      if (!empty($boardsData['boards']) && !empty($boardsData['boards'][0]['columns'])) {
        return $boardsData['boards'][0]['columns'];
      }
    }
  }

  public function boardColumnMapping(Request $request)
  {
    // $data = $request->all('columns');
    $data  = $request->getContent();
    // $data = '{
    //     "board": "8001201",
    //     "onboarding_columns": [
    //         {
    //             "id": "101",
    //             "name": "Women Shoulder Bags 1",
    //             "icon": ""
    //         },
    //         {
    //             "id": "102",
    //             "name": "Women Shoulder Bags 2",
    //             "icon": ""
    //         },
    //         {
    //             "id": "103",
    //             "name": "Women Shoulder Bags 3",
    //             "icon": ""
    //         }
    //     ],
    //     "candidate_coulmns": [
    //         {
    //             "id": "71",
    //             "name": "Women Shoulder Bags 71",
    //             "icon": ""
    //         },
    //         {
    //             "id": "7200",
    //             "name": "Women Shoulder Bags 7200",
    //             "icon": ""
    //         }
    //     ]
    // }';
    if (!empty($data)) {
      $dataArray = json_decode($data, true);

      $datatoUpdate = [
        'columns' => $data,
      ];


      if (!empty($dataArray['email'])) {
        $criteria = [
          'board_id' => $dataArray['board'],
          'email' => $dataArray['email'],
        ];
        $updateData = [
          'columns' => $datatoUpdate['columns'],
          'email'   => $dataArray['email'],
      ];
        $user     = BoardColumnMappings::find($criteria);
        $response = BoardColumnMappings::updateOrCreate($criteria, $updateData);
      }else{
        $criteria = [
          'board_id' => $dataArray['board'],
          'email' => "",
        ];
        $user = BoardColumnMappings::find($criteria['board_id']);
        $response = BoardColumnMappings::updateOrCreate($criteria, $datatoUpdate);
      }
      // $criteria = [
      //   'board_id' => $dataArray['board'],
      // ];

      // Retrieve the existing record
      // $user = BoardColumnMappings::find($criteria['board_id']);

      // $response = BoardColumnMappings::updateOrCreate($criteria, $datatoUpdate);

      // Check if the record was updated
      if ($user && $response->wasChanged()) {
        // Session::flash('message', 'Board column mapping successfully updated.');
        return response(json_encode(array('response' => true, 'status' => true, 'message' => "Board column mapping successfully updated.")));
      } else {
        // Session::flash('message', 'Board column mapping updated.');
        return response(json_encode(array('response' => true, 'status' => true, 'message' => "Board column mapping updated.")));
      }
      // Session::flash('error', 'something went wrong during board column mapping.');
      return response(json_encode(array('response' => true, 'status' => false, 'message' => "Something went wrong during board column mapping.")));
    } else {
      // Session::flash('error', 'Board column mapping data not received.');
      return response(json_encode(array('response' => true, 'status' => false, 'message' => "Board column mapping data not received.")));
    }
  }

  public function getBoardColumnsData(Request $request)
  {
    $BoardColumnMappingData = BoardColumnMappings::all();
    if (!empty($BoardColumnMappingData)) {
      $data = json_decode($BoardColumnMappingData, true);
      $boardsData = array();

      foreach ($data as $record) {
        $boardsData[] = [
          'board_id' => $record['board_id'],
          'columns' =>  json_decode($record['columns'], true),
        ];
      }
      return json_encode($boardsData);
    }
    Session::flash('error', 'Something went wrong during fetch board column mapping data.');
  }

  public function getBoardColumnsDataById(Request $request)
  {
    $boardId =  $request->id;
    $BoardColumnMappingData = BoardColumnMappings::where('board_id', $boardId)->get();
    if (!empty($BoardColumnMappingData)) {
      $data = json_decode($BoardColumnMappingData, true);
      $boardsData = array();
      foreach ($data as $record) {
        $boardsData[] = [
          'board_id' => $record['board_id'],
          'columns' =>  json_decode($record['columns'], true),
        ];
      }
      return json_encode($boardsData);
    }
    Session::flash('error', 'Something went wrong during fetch board column mapping data.');
  }

  public function getBoardColumnsEmbedById($boardId)
  {
    $BoardColumnMappingData = BoardColumnMappings::where('board_id', $boardId)->get();
    if (!empty($BoardColumnMappingData)) {
      $data = json_decode($BoardColumnMappingData, true);
      $boardsData = array();
      foreach ($data as $record) {
        $boardsData[] = [
          'board_id' => $record['board_id'],
          'columns' =>  json_decode($record['columns'], true),
        ];
      }
      return $boardsData;
    }
    Session::flash('error', 'Something went wrong during fetch board column mapping data.');
  }
  public function createAdmin(Request $request)
  {
    $heading = 'Add Admin';
    $subheading = 'To Create User With Admin Role';
    $msg    = '';
    $status = '';
    if ($request->isMethod('post')) {
    }
    return view('admin.addAdmin', compact('heading', 'subheading', 'msg', 'status'));
  }

  function getErrorMessages()
  {
    return [
      "required" => ":attribute is required.",
      "max"   => ":attribute should not be more then :max characters.",
      "min"   => ":attribute should not be less then :min characters."
    ];
  }

  public function storeAdmin(Request $request)
  {

    $heading     = 'Add Admin';
    $subheading  = 'To Create User With Admin Role';
    $msg    = '';
    $status = '';

    $request->validate([
      'name'         => 'required',
      'email'        => 'required|email|unique:monday_users',
      'password'     => 'required|min:6|max:100',
      'role'         => 'required'
    ], $this->getErrorMessages());

    $dataToSave = array(
      'name'         => trim($request['name']),
      'email'        => trim($request['email']),
      'phone'        => trim($request['phone'] ?? ''),
      'company_name' => trim($request['company_name'] ?? ''),
      'role'         => trim($request['role']),
      'created_at'   => date("Y-m-d H:i:s"),
      'updated_at'   => date("Y-m-d H:i:s"),
      // 'password'     => trim($request['password']),
      'password'     => Hash::make(trim($request['password']))
    );
    $insertUserInDB = MondayUsers::createUser($dataToSave);

    if ($insertUserInDB['status'] == "success") {
      MondayUsers::setUser(['email' => $request['email']], ['status' => 1]);
      $msg    = "Admin Created Successfully.";
      if ($request['role'] == 1) {
        $msg    = "Super admin Created Successfully.";
      $status = "success";
      return view('admin.addAdmin', compact('heading', 'subheading',  'msg', 'status'));
    } elseif ($request['role'] == 0) {

      $msg    = "User Created Successfully.";
      $status = "success";
      return view('admin.addAdmin', compact('heading', 'subheading',  'msg', 'status'));
    }
    } elseif ($insertUserInDB['status'] == "already") {
      $msg    = "Admin Already Exists.";
      $status = "success";
      return view('admin.addAdmin', compact('heading', 'subheading',  'msg', 'status'));
    } else {
      $msg    = "Something went wrong. Please try again.";
      $status = "danger";
      return view('admin.addAdmin', compact('heading', 'subheading',  'msg', 'status'));
    }
    return view('admin.addAdmin', compact('heading', 'subheading',  'msg', 'status'));
  }

  public function settings(Request $request)
  {
    $msg    = '';
    $status = '';
    if ($request->isMethod('post')) {
      $data = $request->all();
      $request->validate([
        'site_bg'        => 'required',
        'button_bg'      => 'required',
        'logo_image'     => 'mimes:JPEG,JPG,jpeg,jpg,PNG,png|max:2048',
        'banner_bg'      => 'required',
        'head_title_color' => 'required',
        'header_bg'      => 'required'
      ], $this->getErrorMessages());

      if (!empty($data)) {
        $siteSettings = SiteSettings::find([
          'id' => 1,
        ]);

        // Store the uploaded file
        $value = [
          'site_bg'        => $request->site_bg,
          'button_bg'      => $request->button_bg,
          'banner_bg'      => $request->banner_bg,
          'banner_content' => $request->banner_content,
          'head_title_color' => $request->head_title_color,
          'logo_image'     => !empty($request->file('logo_image')) ? '' : json_decode($siteSettings[0]['ui_settings'])->logo_image,
          'header_bg'      => $request->header_bg
        ];

        if (!empty($request->file('logo_image'))) {
          $file     = $request->file('logo_image');
          $fileName = $file->getClientOriginalName();
          $file->move(public_path('uploads'), $fileName);
          $value['logo_image'] = $fileName;
        }



        $datatoUpdate = [
          'ui_settings' => json_encode($value),
          'status' => 0,
          'logo' => !empty($value['logo_image']) ? $value['logo_image'] : "",
        ];
        $criteria = [
          'status' => 0,
        ];
        $siteSettingsDBData = SiteSettings::find($criteria['status']);
        $response = SiteSettings::where('id', '=', 1)->update($datatoUpdate);
        // Check if the record was updated
        if ($siteSettingsDBData && $response->wasChanged()) {
          $msg    = 'Site setting mapping successfully updated.';
          $status = 'success';
        } else {
          $msg    = 'Site setting mapping successfully updated.';
          $status = 'success';
        }
      } else {
        $msg    = 'Site settings data mapping not received.';
        $status = 'danger';
      }
    }


    $get_data = SiteSettings::where('id', '=', 1)->first()->toArray();
    // Store data in the session
    session(['siteSettingsData' => $get_data]);
    $settings = "";
    if ($get_data)
      session(['settings' => json_decode($get_data['ui_settings'])]);
    return view('admin.settings', compact('msg', 'status'));
  }

  public function getColourMapping()
  {
    $colourMappingsData = ColourMappings::get();

    if (!empty($colourMappingsData)) {
      $data = json_decode($colourMappingsData, true);

      $coloursData = array();
      foreach ($data as $record) {
        $coloursData[] = [
          $record['colour_name'] =>  json_decode($record['colour_value'], true),
        ];
      }

      return json_encode($coloursData);
    }
    Session::flash('error', 'Something went wrong during fetch colour mapping data.');
  }

  public function postColourMapping(Request  $request)
  {
    $data  = $request->getContent();
    if (!empty($data)) {
      $dataArray = json_decode($data, true);
      foreach ($dataArray as $key => $value) {
        $datatoUpdate = [
          'colour_value' => json_encode($value),
        ];
        $criteria = [
          'colour_name' => $key,
        ];
        $colourMappingDBData = ColourMappings::find($criteria['colour_name']);
        $response = ColourMappings::updateOrCreate($criteria, $datatoUpdate);
      }

      // Check if the record was updated
      if ($colourMappingDBData && $response->wasChanged()) {
        return response(json_encode(array('response' => true, 'status' => true, 'message' => "Colour mapping successfully updated.")));

        // Session::flash('message', 'Colour mapping successfully updated.');
      } else {
        return response(json_encode(array('response' => true, 'status' => true, 'message' => "Colour mapping successfully updated.")));
        // Session::flash('message', 'Colour mapping successfully updated.');
      }
      return response(json_encode(array('response' => true, 'status' => false, 'message' => "something went wrong during colour mapping.")));
      // Session::flash('error', 'something went wrong during colour mapping.');
    } else {
      return response(json_encode(array('response' => true, 'status' => false, 'message' => "Colour mapping data not received.")));

      // Session::flash('error', 'Colour mapping data not received.');
    }
  }

  public function fetchMondayData($limit, $cs, $operation_query)
  {
    // $tolalData = $limit * $cs;
    $tolalData  = 500;
    $cursor     = 'null';
    $mondayData = [];
    $after      = 'ddd';
    do {
      $query = "query {
          boards(ids: " . auth()->user()->board_id . ") {
              columns {
                title
                id,
                settings_str
              }
              items_page (limit: $tolalData, cursor:" . $cursor . " {$operation_query}) {
                cursor
                items {
                    id
                    name
                    email
                    created_at
                    column_values {
                        id
                        value
                        type
                        text
                        ... on StatusValue  {
                          label
                          update_id
                          index
                          value
                        }
                    }
                }
            }
          }
        }";


        $response = $this->_get($query)['response'];
        // dd($response);
      if (!empty($response['data']['boards'][0]['items_page']['cursor'])) {
        $cursor =  "\"" . $response['data']['boards'][0]['items_page']['cursor'] . "\"";
      } else {
        $after = '';
      }
      // dd($response);
      $curr_data = isset($response['data']['boards'][0]['items_page']['items']) ? $response['data']['boards'][0]['items_page']['items'] : [];
      if (!empty($curr_data)) {
        if (count($curr_data))
          foreach ($curr_data as $item) {
            $mondayData[] = $item;
          }
      }
      $newResponse = $response;
    } while (!empty($after));
    $totalMondayData = count($mondayData);
    unset($newResponse['data']['boards'][0]['items_page']['items']);
    $newResponse['data']['boards'][0]['items_page']['items'] = $mondayData;

    $newResponse['data']['boards'][0]['items_page']['items'] = array_slice($newResponse['data']['boards'][0]['items_page']['items'], ($limit * ($cs - 1)), $limit);
    $newResponse['data']['boards']['totalMondayData'] = $totalMondayData;
    return $newResponse;
  }

  public function usersDelete(Request $request)
  {
    // Get the ID of the record you want to delete
    $record = MondayUsers::where('id', $request->id)->first();

    if (!empty($record)) {
      // Delete the record based on the retrieved ID
      $deletedCount = MondayUsers::where('id', $request->id)->delete();
      if ($deletedCount > 0) {
        return redirect()->route('admin.users', ['success' => true]);
      } else {
        return redirect()->route('admin.users', ['success' => false]);
      }
    } else {
      return redirect()->route('admin.users', ['success' => false]);
    }
  }

    public function trackRequestUpdates(Request $request)
  {

    if (!empty(auth()->user()) && !empty(auth()->user()->board_id)) {
      $boardId  = auth()->user()->board_id;
      $response = BoardColumnMappings::where('board_id', '=', $boardId)->get();
      $response = json_decode($response, true);
      $colors_status = json_decode($this->getColourMapping());

      if ($response['0']['columns'] ?? false) {
        $boardColumnMappingDbData = $response['0']['columns'];
      } else {
        $msg = "Board Column Mapping Not Exist In Db";
        $status = false;
        return view('auth.updates', compact('status', 'msg'));
      }
    } else {
      $msg = "Currently Board not Assigned!";
      $status = false;
      return view('auth.updates', compact('status', 'msg'));
    }

    $query = 'query {
      boards( ids: ' . $boardId . ') {
      id
      name
      state
      permissions
      board_kind
 }
                items (ids: ' . $request->id . '){
                    updates (limit: 500) {
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
            
    
  }';
  return  $response = $this->_getMondayData($query)['response'];
    if (!empty($response) && !empty($response['data']) && !empty($response['data']['items'])) {
      $status = true;
      $msg = "Updates Found";
      return view('admin.updates', compact('status', 'msg', 'response', 'boardColumnMappingDbData'));
    } else {
      $msg = "Updates Not Found";
      $status = false;
      return view('admin.updates', compact('status', 'msg'));
    }
  }
}
