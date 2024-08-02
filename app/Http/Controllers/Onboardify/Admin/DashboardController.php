<?php

namespace App\Http\Controllers\Onboardify\Admin;

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

    public function userListing(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $mondayUsers = MondayUsers::get();
                if (!empty($mondayUsers)) {
                    return response(json_encode(array('response' => $mondayUsers, 'status' => true, 'message' => "Users data.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Users data not found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function assignBoard(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $input = $request->json()->all();
                $this->validate($request, [
                    'user_id'   => 'required',
                    'board_id'  => 'required',
                    'email_id'  => 'required',
                ], $this->getErrorMessages());
                $response = MondayUsers::where('id', $input['user_id'])->update(['board_id' => $input['board_id']]);
                if (!empty($response)) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => "Board assign to user successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Something went wrong during assign board to user.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function userDelete($id)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $mondayUsersData =  MondayUsers::find($id);
                if (empty($mondayUsersData)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "User Not Found.")));
                }
                $deleteMondayUsers = MondayUsers::where('id', $id)->delete();
                if ($deleteMondayUsers > 0) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "User Deleted Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "User Not Deleted.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function getErrorMessages()
    {
        return [
            "required" => ":attribute is required.",
            "max"   => ":attribute should not be more then :max characters.",
            "min"   => ":attribute should not be less then :min characters."
        ];
    }

    public function getboardVisibilityMapping(Request $request)
    {
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

    public function boardVisibilityMapping(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $data  = $request->getContent();
                if (!empty($data)) {
                    $dataArray = json_decode($data, true);

                    $datatoUpdate = [
                        'columns' => $data,
                    ];

                    if (!empty($dataArray['email'])) {
                        // Not Required Issue Generate
                        $criteria = [
                            'board_id' => $dataArray['board'],
                            'email'    => $dataArray['email'],
                        ];
                        $updateData = [
                            'columns' => $datatoUpdate['columns'],
                            'email'   => $dataArray['email'],
                        ];
                        $user     = BoardColumnMappings::where($criteria)->get();
                        $response = BoardColumnMappings::updateOrCreate($criteria, $updateData);

                        if ($user && $response->wasChanged()) {
                            return response(json_encode(array('response' => true, 'status' => true, 'message' => "Board column mapping successfully updated.")));
                        } else {
                            return response(json_encode(array('response' => true, 'status' => true, 'message' => "Board column mapping updated.")));
                        }
                    } else {
                        $criteria = [
                            'board_id' => $dataArray['board']
                        ];
                        $user = BoardColumnMappings::where($criteria)->get();
                        $response = BoardColumnMappings::updateOrCreate($criteria, $datatoUpdate);

                        if ($user && $response->wasChanged()) {
                            return response(json_encode(array('response' => true, 'status' => true, 'message' => "Board column mapping successfully updated.")));
                        } else {
                            return response(json_encode(array('response' => true, 'status' => true, 'message' => "Board column mapping updated.")));
                        }
                    }
                    return response(json_encode(array('response' => true, 'status' => false, 'message' => "Something went wrong during board column mapping.")));
                } else {
                    return response(json_encode(array('response' => true, 'status' => false, 'message' => "Board column mapping data not received.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function boardColourMapping(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
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
                        return response(json_encode(array('response' => [], 'status' => true, 'message' => "Status Colour mapping updated successfully.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => true, 'message' => "Status Colour mapping updated successfully.")));
                    }
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "something went wrong during status colour mapping.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Status Colour mapping data not received.")));
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

    public function generalSettings(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $input = $request->json()->all();
                $criteria = ['status' => 0];
                $get_data = SiteSettings::where('id', '=', 1)->first();

                $insert_array = array(
                    "ui_settings" => $request->ui_settings,
                    "created_at"  => date("Y-m-d H:i:s"),
                    "updated_at"  => date("Y-m-d H:i:s")
                );

                $datatoUpdate = [];
                if ($request->input('logo_image')) {
                    // Additional validation for base64 image
                    if (!$this->isValidBase64Image($request->logo_image)) {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid image format. Please re-upload the image (jpeg|jpg|png).")));
                    }

                    $imageData = $request->input('logo_image');
                    list($type, $data) = explode(';', $imageData);
                    list(, $data)      = explode(',', $data);
                    $data      = base64_decode($data);
                    $extension = explode('/', mime_content_type($imageData))[1];
                    $timestamp = now()->timestamp;

                    $updateFileName = $timestamp . '_' . $request->input('logo_name');
                    File::put(public_path('uploads/onboardify/' . $updateFileName), $data);
                    $datatoUpdate['logo']         = $updateFileName;
                    $imagePath = '/uploads/onboardify/' . $updateFileName;
                    $datatoUpdate['logo_location'] =  URL::to("/") . $imagePath;

                    // $uploadedImagePath = $serviceRequest->file_location;
                    $uploadedImagePath = public_path('uploads/onboardify/' . $get_data->logo);
                    // Check if the image file exists
                    if (File::exists($uploadedImagePath)) {
                        // Delete the image file
                        File::delete($uploadedImagePath);
                    }
                }

                $datatoUpdate['ui_settings'] = json_encode(!empty($insert_array['ui_settings']) ? $insert_array['ui_settings'] : '');
                $datatoUpdate['status'] = 0;

                if (empty($get_data)) {
                    $insert = SiteSettings::where($criteria)->create($datatoUpdate);
                } else {
                    $insert = SiteSettings::where($criteria)->update($datatoUpdate);
                }

                if ($insert) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => "Onboardify Site Setting Updated Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Site Setting Not Created.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    private function isValidBase64Image($base64Image)
    {
        $pattern = '/^data:image\/(jpeg|jpg|png);base64,/';
        if (preg_match($pattern, $base64Image)) {
            $data = substr($base64Image, strpos($base64Image, ',') + 1);
            if (base64_decode($data, true) === false) {
                return false;
            }
            $image = imagecreatefromstring(base64_decode($data));
            if (!$image) {
                return false;
            }
            return true;
        }
        return false;
    }

    public function addAdminOrUser(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $input = $request->json()->all();
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
                    if ($request['role'] == 0) {
                        return response(json_encode(array('response' => [], 'status' => true, 'message' => "Contact Created Successfully.")));
                    }
                    if ($request['role'] == 1) {
                        return response(json_encode(array('response' => [], 'status' => true, 'message' => "Super Admin Created Successfully.")));
                    }
                    if ($request['role'] == 2) {
                        return response(json_encode(array('response' => [], 'status' => true, 'message' => "Admin Created Successfully.")));  
                    }
                  } elseif ($insertUserInDB['status'] == "already") {
                    
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => "User already exist.")));

                  }elseif ($insertUserInDB['status'] == "error") {
                    
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => "something went wrong during user creation.")));

                  } 
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function getBoardColumns($id)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                if (!empty($id)) {
                    $query = 'query
                        {
                            boards(ids: ' . $id . ') {
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
                    if (!empty($boardsData['data']) && !empty($boardsData['data']['boards']) && !empty($boardsData['data']['boards'][0]['columns'])) {
                        return response(json_encode(array('response' => $boardsData['data']['boards'][0]['columns'], 'status' => true, 'message' => "Boards columns data found.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Boards columns data not found.")));
                    }
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Boards id not found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function fetchAllBoards (){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $page      = 1;
                $tolalData  = 1000;
                $after     = 'null';
                do {
                    $query = 'query {
                boards (limit : ' . $tolalData . ', page : ' . $page . '){
                id
                name
                state
                permissions
                board_kind
            }
        }';

                    $boardsData = $this->_getMondayData($query);

                    if (!empty($boardsData['response']['data']['boards'])) {
                        // $page += $page;
                        $page++;
                    } else {
                        $after = '';
                    }
                    $curr_data = isset($boardsData['response']['data']['boards']) ? $boardsData['response']['data']['boards'] : [];
                    if (!empty($curr_data)) {
                        if (count($curr_data))
                            foreach ($curr_data as $item) {
                                $mondayData[] = $item;
                            }
                    }
                    $newResponse = $boardsData;
                } while (!empty($after));
                unset($newResponse['response']['data']['boards']);
                $newResponse['response']['data']['boards'] = $mondayData;
                if (!empty($newResponse['response']['data'])) {
                    return response(json_encode(array('response' => $newResponse['response']['data'], 'status' => true, 'message' => "Fetch all board data.")));
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board data not found.")));
                }
                
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function getAllCustomer(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $mondayUsers = MondayUsers::where('role', '=', 0)->get();
                if (!empty($mondayUsers)) {
                    return response(json_encode(array('response' => $mondayUsers, 'status' => true, 'message' => "Users data.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Users data not found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}
