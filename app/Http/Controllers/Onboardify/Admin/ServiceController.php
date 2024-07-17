<?php

namespace App\Http\Controllers\Onboardify\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoardColumnMappings;
use App\Models\ColourMappings;
use App\Models\GovernifyServiceCategorie;
use App\Models\MondayUsers;
use App\Models\SiteSettings;
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

class ServiceController extends Controller
{
    use MondayApis;

    public function index()
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                // $dataToRender =  GovernifyServiceRequest::with(['serviceCategorie','form'])->whereNull('deleted_at')->orderBy('service_categories_request_index')->get();
                $dataToRender =  OnboardifyService::whereNull('deleted_at')->get();
                if (!empty($dataToRender)) {
                    return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Onboardify Service Request Data.")));
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Service Request Data Not Found.")));
                }
                
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createServiceRequests(Request $request)
    {
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $input = $request->json()->all();

                $this->validate($request, [
                    'title'       => "required|string|unique:onboardify_service",
                    // 'title'       => "required|string",
                    'description' => "required|string",
                    'image_name'  => "required",
                    'image'       => "required",
                    'form_embed_code'    => "required",
                    'board_id' => 'required',
                ], $this->getErrorMessages());

                // Check if the validation fails
                // if ($validator->fails()) {
                //     return response(json_encode(array('response' => true, 'status' => false, 'message' => $validator->errors())));
                // }

                 // Additional validation for base64 image
                if (!$this->isValidBase64Image($request->image)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid image format. Please re-upload the image (jpeg|jpg|png).")));
                }

                $insert_array = array(
                    "title"       => $request->title,
                    "description" => $request->description,
                    "form_embed_code" => $request->form_embed_code,
                    "board_id" => $request->board_id,
                    "created_at" => date("Y-m-d H:i:s"),
                    "updated_at" => date("Y-m-d H:i:s")
                );

                if ($request->image) {
                // if ($request->input('image')) {

                    $imageData = $request->input('image');
                    list($type, $data) = explode(';', $imageData);
                    list(, $data)      = explode(',', $data);
                    $data      = base64_decode($data);
                    $extension = explode('/', mime_content_type($imageData))[1];
                    $timestamp = now()->timestamp;

                    $updateFileName = $timestamp. '_'.$request->input('image_name');
                    File::put(public_path('uploads/onboardify/' . $updateFileName), $data);
                    $insert_array['image']         = $updateFileName;
                    $imagePath = '/uploads/onboardify/' . $updateFileName;
                    $insert_array['file_location'] =  URL::to("/") . $imagePath;

                    /*
                    $timestamp    = now()->timestamp;
                    $imageContent = file_get_contents($request->image);
                    $updateFileName = $timestamp. '_'.$request->image_name;
                    File::put(public_path('uploads/onboardify/' . $updateFileName), $imageContent);
                    $insert_array['image']         = $updateFileName;
                    $imagePath = '/uploads/onboardify/' . $updateFileName;
                    $insert_array['file_location'] =  URL::to("/") . $imagePath;
                    */
                }
                // else {
                //     return response(json_encode(array('response' => true, 'status' => false, 'message' => 'Image upload failed')));
                // }
                // $insert = GovernifyServiceCategorie::insertTableData("governify_service_categories", $insert_array);
                $insert = OnboardifyService::create($insert_array);
                if ($insert) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => "Onboardify Service Created Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Service Not Created.")));
                }
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
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

    public function updateServiceRequests(Request $request, $id)
    {
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $input = $request->json()->all();

                $serviceRequest = OnboardifyService::find($id);

                if (!empty($serviceRequest)) {
                    $this->validate($request, [
                        'title'       => "required|string|unique:onboardify_service",
                        // 'title'       => "required|string",
                        'description' => "required|string",
                        'form_embed_code'    => "required",
                        'board_id' => 'required',
                    ], $this->getErrorMessages());

                    $insert_array = array(
                        "title" => $request->title,
                        "description" => $request->description,
                        "form_embed_code" => $request->form_embed_code,
                        "board_id" => $request->board_id,
                        "updated_at" => date("Y-m-d H:i:s")
                    );


                    if ($request->image) {
                        $timestamp    = now()->timestamp;
                        $imageContent = file_get_contents($request->image);
                        $updateFileName = $timestamp. '_'.$request->image_name;
                        File::put(public_path('uploads/onboardify/' . $updateFileName), $imageContent);
                        $insert_array['image']         = $updateFileName;
                        $imagePath = '/uploads/onboardify/' . $updateFileName;
                        $insert_array['file_location'] =  URL::to("/") . $imagePath;

                        $uploadedImagePath = public_path('uploads/onboardify/'. $serviceRequest->image);
                        if (File::exists($uploadedImagePath)) {
                            File::delete($uploadedImagePath);
                        }
                    }

                    $update = $serviceRequest->update($insert_array);
                    if ($update) {
                        return response(json_encode(array('response' => [], 'status' => true, 'message' => "Onboardify Service Request Updated Successfully.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Service Request Not Updated.")));
                    }
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Service Request Data Not Found. Invalid Service Request Id Provided.")));
                }
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
        }
    }

    public function destroy(string $id)
    {
        $userId = $this->verifyToken()->getData()->id;
        if (!empty($userId)) {
            $serviceRequestData = OnboardifyService::find($id);

            if (!empty($serviceRequestData)) {
                $params = array(
                    'id' => $id
                );
                $dataToUpdate = array(
                    'deleted_at' => date('Y-m-d H:i:s')
                );

                // Hard Delete
                $deleteServiceRequest = OnboardifyService::where('id', $id)->delete();
                if ($deleteServiceRequest > 0) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Onboardify Service Request Deleted Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Onboardify Service Request Not Deleted.")));
                }

                //Soft Delete
                /*
                $deleteServiceRequest = GovernifyServiceRequest::where('id', $id)->update($dataToUpdate);
                if ($deleteServiceRequest) {
                    // $responseData = array(
                    //     'access_token' => $this->refreshToken()->original[ 'access_token' ]
                    // );
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Service Request Deleted Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Service Request Not Deleted.")));
                }
                */
            }
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Onboardify Service Request Data Not Found. Invalid Service Request Id Provided.")));
        }
        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
    }
}