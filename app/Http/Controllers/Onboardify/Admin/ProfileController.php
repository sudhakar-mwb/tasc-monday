<?php

namespace App\Http\Controllers\Onboardify\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardColumnMappings;
use App\Models\ColourMappings;
use App\Models\GovernifyServiceCategorie;
use App\Models\MondayUsers;
use App\Models\SiteSettings;
use App\Models\OnboardifyProfiles;
use App\Models\OnboardifyService;
use Illuminate\Http\Request;
use App\Traits\MondayApis;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use App\Models\CategoryServiceFormMapping;
use Illuminate\Support\Str;
use App\Models\GovernifyServiceRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use MondayApis;

    public function getOnboardifyProfile (){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $dataToRender =  OnboardifyProfiles::get();
                if (!empty($dataToRender)) {
                    return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Onboardify Profile Data Found.")));
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Profile Data Not Found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function onboardifyProfile (Request $request){

        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $input = $request->json()->all();
                $this->validate($request, [
                    'title'  => 'required|string|unique:onboardify_profiles',
                    'users'  => 'required',
                ], $this->getErrorMessages());
                $insert_array = array(
                    "title"      => $input['title'],
                    "users"      => $input['users'],
                    "created_at" => date("Y-m-d H:i:s"),
                    "updated_at" => date("Y-m-d H:i:s")
                );
                $insert = GovernifyServiceCategorie::insertTableData("onboardify_profiles", $insert_array);
                if ($insert['status'] == 'success') {
                    return response(json_encode(array('response' => [$insert['data']], 'status' => true, 'message' => "Onboardify Profile Created Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Profile Not Created.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function updateProfileSetting (Request $request){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $input = $request->json()->all();
                $this->validate($request, [
                    'profile_id'  => 'required',
                    'value'  => 'required',
                ], $this->getErrorMessages());
                $update_array = array(
                    "make_default" => $input['value'],
                    "updated_at"   => date("Y-m-d H:i:s")
                );
                $OnboardifyProfilesData = OnboardifyProfiles::find($input['profile_id']);
                if (!empty($OnboardifyProfilesData)) {
                    if ($input['value'] == 0) {
                        $update =  OnboardifyProfiles::where('id', $input['profile_id'])->update($update_array);
                        if ($update) {
                            return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Profile Updated Successfully.")));
                        } else {
                            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Profile Not Updated.")));
                        }
                    }
                    $profiles = OnboardifyProfiles::where('id', '!=', $input['profile_id'])->get()->toArray();
                    foreach ($profiles as $key => $value) {
                        if ($value['make_default'] == 1) {
                            return response(json_encode(array('response' => [], 'status' => false, 'message' =>$value['title']. ' already set as default profile. First remove from default association.' )));
                        }
                    }
                    
                    // $allOnboardifyProfilesData =   OnboardifyProfiles::all()->toArray();
                    // foreach ($allOnboardifyProfilesData as $key => $value) {
                    //     OnboardifyProfiles::where('id', $value['id'])->update(["make_default" => 0,
                    //     "updated_at"   => date("Y-m-d H:i:s")]);
                    // }
                    $update =  OnboardifyProfiles::where('id', $input['profile_id'])->update($update_array);
                if ($update) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Profile Updated Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Profile Not Updated.")));
                }
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "This profile details not found in database.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function destroy(string $id)
    {
        $userId = $this->verifyToken()->getData()->id;
        if (!empty($userId)) {
            $OnboardifyProfilesData = OnboardifyProfiles::find($id);

            if (!empty($OnboardifyProfilesData)) {
                if ($OnboardifyProfilesData['make_default'] == 1) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Default profile can not be deleted.")));
                }else{
                    $params = array(
                        'id' => $id
                    );
    
                    $deleteOnboardifyProfiles = OnboardifyProfiles::where('id', $id)->delete();
                    if ($deleteOnboardifyProfiles > 0) {
                        return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Onboardify Profile Deleted Successfully.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Onboardify Profile Not Deleted.")));
                    }
                }
            }
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Profiles Data Not Found. Invalid Onboardify Profiles Id Provided.")));
        }
        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
    }

    public function editOnboardifyProfile (Request $request, $id){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $input = $request->json()->all();
                $OnboardifyProfilesData = OnboardifyProfiles::find($id);

                if (!empty($OnboardifyProfilesData)) {
                    $this->validate($request, [
                        'title'  => 'required|string|unique:onboardify_profiles,title,' . $OnboardifyProfilesData['title'],
                        'users'  => 'required',
                    ], $this->getErrorMessages());
                    $insert_array = array(
                        "title" => $request->title,
                        "users" => $request->users,
                        "updated_at" => date("Y-m-d H:i:s")
                    );
                    // $update = $OnboardifyProfilesData->update($insert_array);
                    // if ($update) {
                    //     return response(json_encode(array('response' => [], 'status' => true, 'message' => "Onboardify Profile Updated Successfully.")));
                    // } else {
                    //     return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Profile Not Updated.")));
                    // }

                    $update = GovernifyServiceCategorie::updateTableData("onboardify_profiles", array("id" => $OnboardifyProfilesData['id']), $insert_array);
                    if ($update['status'] == 'success') {
                        return response(json_encode(array('response' => [$update['data']], 'status' => true, 'message' =>  "Service Category Updated Successfully.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Category Not Updated.")));
                    }
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Profiles Data Not Found. Invalid Onboardify Profiles Id Provided.")));
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
}