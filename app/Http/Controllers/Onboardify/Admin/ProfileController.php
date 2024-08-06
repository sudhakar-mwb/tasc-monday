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
use DB;
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

                // Parse the users from the request
                $users = explode(',', $request->users);

                // Fetch all profiles and remove specified users
                $profiles = OnboardifyProfiles::all();

                foreach ($profiles as $profile) {
                    $profileUsers = explode(',', $profile->users);
                    $updatedUsers = array_diff($profileUsers, $users);

                    if (count($updatedUsers) != count($profileUsers)) {
                        $profile->users = implode(',', $updatedUsers);
                        $profile->save();
                    }
                }

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
                    // Delete related services
                    OnboardifyService::where('profile_id', $id)->delete();
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
                        'title'  => 'required|string|unique:onboardify_profiles,title,' . $OnboardifyProfilesData['id'],
                        'users'  => 'required',
                    ], $this->getErrorMessages());

                    // Parse the users from the request
                    $users = explode(',', $request->users);

                    // Fetch all profiles and remove specified users
                    $profiles = OnboardifyProfiles::all();

                    foreach ($profiles as $profile) {
                        $profileUsers = explode(',', $profile->users);
                        $updatedUsers = array_diff($profileUsers, $users);

                        if (count($updatedUsers) != count($profileUsers)) {
                            $profile->users = implode(',', $updatedUsers);
                            $profile->save();
                        }
                    }

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
                        return response(json_encode(array('response' => [$update['data']], 'status' => true, 'message' =>  "Onboardify Profile Updated Successfully.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Profile Not Updated.")));
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

    public function allProfileWithServices (){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $dataToRender =  OnboardifyProfiles::with('services')->get();
                if (!empty($dataToRender)) {
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

    public function getProfileWithServicesById ($id){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $dataToRender =  OnboardifyProfiles::with('services')->where('id', $id)->get();
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

    public function cloneOnboardifyProfile ($profileId){

        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                // Fetch the original profile
                $originalProfile = OnboardifyProfiles::findOrFail($profileId);

                if (empty($originalProfile)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Something went wrong profile information not retrieved.")));
                }
                // Clone the profile
                $newProfile = $originalProfile->replicate();
                $newProfile->title = $originalProfile->title . ' - Clone';
                $newProfile->users = '';
                $newProfile->save();

                // Fetch associated services
                $originalServices = OnboardifyService::where('profile_id', $profileId)->get();

                // Clone each service and associate it with the new profile
                foreach ($originalServices as $originalService) {
                    $newService = $originalService->replicate();
                    $newService->profile_id = $newProfile->id;
                    $newService->save();
                }

                DB::commit();
                return response(json_encode(array('response' => [$newProfile->id], 'status' => tue, 'message' => "Profile cloned successfully.")));
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}