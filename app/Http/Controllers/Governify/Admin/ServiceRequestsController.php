<?php

namespace App\Http\Controllers\Governify\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryServiceFormMapping;
use Illuminate\Http\Request;
use App\Traits\MondayApis;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\GovernifyServiceRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
class ServiceRequestsController extends Controller
{
    use MondayApis;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                // $dataToRender =  GovernifyServiceRequest::with(['serviceCategorie','form'])->whereNull('deleted_at')->orderBy('service_categories_request_index')->get();
                $dataToRender =  GovernifyServiceRequest::with(['serviceCategorie','form'])->whereNull('deleted_at')->orderByRaw('CASE WHEN service_categories_request_index IS NULL THEN 1 ELSE 0 END, service_categories_request_index')->get();
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Request Data.")));
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
                    // 'title'       => "required|string|unique:governify_service_requests",
                    'title'       => "required|string",
                    'description' => "required|string",
                    'image'       => "required",
                    'image_name'  => "required",
                    // 'form'        => "required|integer",
                    // 'service_categorie_id' => 'required|exists:governify_service_categories,id',
                    'service_categorie_id' => [
                        'required',
                        Rule::exists('governify_service_categories', 'id'),
                        Rule::unique('governify_service_requests')->where(function ($query) use ($request) {
                            return $query->where('title', $request->title);
                        }),
                    ],
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
                    // "form"        => $request->form,
                    "service_categorie_id" => $request->service_categorie_id,
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
                    File::put(public_path('uploads/governify/' . $updateFileName), $data);
                    $insert_array['image']         = $updateFileName;
                    $imagePath = '/uploads/governify/' . $updateFileName;
                    $insert_array['file_location'] =  URL::to("/") . $imagePath;

                    /*
                    $timestamp    = now()->timestamp;
                    $imageContent = file_get_contents($request->image);
                    $updateFileName = $timestamp. '_'.$request->image_name;
                    File::put(public_path('uploads/governify/' . $updateFileName), $imageContent);
                    $insert_array['image']         = $updateFileName;
                    $imagePath = '/uploads/governify/' . $updateFileName;
                    $insert_array['file_location'] =  URL::to("/") . $imagePath;
                    */
                }
                // else {
                //     return response(json_encode(array('response' => true, 'status' => false, 'message' => 'Image upload failed')));
                // }
                // $insert = GovernifyServiceCategorie::insertTableData("governify_service_categories", $insert_array);
                $insert = GovernifyServiceRequest::create($insert_array);
                if ($insert) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => "Service Request Created Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request Not Created.")));
                }
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function showServiceRequestsById( $id )
    {
        $userId = $this->verifyToken()->getData()->id;
        if (!empty($userId)) {
            $dataToRender = GovernifyServiceRequest::with(['serviceCategorie', 'form'])->whereNull('deleted_at')->find($id);

            if (!empty($dataToRender)) {
                // $responseData = array(
                //     'access_token' => $this->refreshToken()->original[ 'access_token' ]
                // );
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Request Data.")));
            }
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request Data Not Found. Invalid Service Request Id Provided.")));
        }
        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function updateServiceRequests(Request $request, $id)
    {
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $input = $request->json()->all();

                $serviceRequest = GovernifyServiceRequest::find($id);

                if (!empty($serviceRequest)) {
                    $this->validate($request, [
                        // 'title'       => ['required', 'string', Rule::unique('governify_service_requests')->ignore($id)],
                        'title'       => ['required', 'string'],
                        'description' => "required|string",
                        // 'image'       => "required|string",
                        // 'image_name'  => "required|string",
                        // 'form'        => "required|integer",
                        // 'service_categorie_id' => 'required|exists:governify_service_categories,id',
                        'service_categorie_id' => [
                            'required',
                            Rule::exists('governify_service_categories', 'id'),
                            Rule::unique('governify_service_requests')->where(function ($query) use ($request) {
                                return $query->where('title', $request->title);
                            }),
                        ],
                    ], $this->getErrorMessages());

                    // Check if the validation fails
                    // if ($validator->fails()) {
                    //     return response(json_encode(array('response' => true, 'status' => false, 'message' => $validator->errors())));
                    // }

                    $insert_array = array(
                        "title"       => $request->title,
                        "description" => $request->description,
                        // "form"        => $request->form,
                        "service_categorie_id" => $request->service_categorie_id,
                        "updated_at" => date("Y-m-d H:i:s")
                    );



                    /*
                    if ($request->hasFile('image')) {
                        $file      = $request->file('image');
                        $timestamp = now()->timestamp;
                        $fileName  = $file->getClientOriginalName();
                        $updateFileName  = $timestamp . '_' . $fileName;
                        $file->move(public_path('uploads/governify'), $updateFileName);
                        $imagePath = '/uploads/governify/' . $updateFileName;

                        $insert_array['image']         = $updateFileName;
                        $insert_array['file_location'] =  URL::to("/") . $imagePath;
                    }
                    */
                    if ($request->image) {
                    // if ($request->input('image')) {

                        // Additional validation for base64 image
                        // if (!$this->isValidBase64Image($request->image)) {
                        //     return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid image format. Please re-upload the image (jpeg|jpg|png).")));
                        // }

                        /*
                        $imageData = $request->input('image');
                        list($type, $data) = explode(';', $imageData);
                        list(, $data)      = explode(',', $data);
                        $data      = base64_decode($data);
                        $extension = explode('/', mime_content_type($imageData))[1];
                        $timestamp = now()->timestamp;
    
                        $updateFileName = $timestamp. '_'.$request->input('image_name');
                        File::put(public_path('uploads/governify/' . $updateFileName), $data);
                        $insert_array['image']         = $updateFileName;
                        $imagePath = '/uploads/governify/' . $updateFileName;
                        $insert_array['file_location'] =  URL::to("/") . $imagePath;
                        */

                        $timestamp    = now()->timestamp;
                        $imageContent = file_get_contents($request->image);
                        $updateFileName = $timestamp. '_'.$request->image_name;
                        File::put(public_path('uploads/governify/' . $updateFileName), $imageContent);
                        $insert_array['image']         = $updateFileName;
                        $imagePath = '/uploads/governify/' . $updateFileName;
                        $insert_array['file_location'] =  URL::to("/") . $imagePath;

                        // $uploadedImagePath = $serviceRequest->file_location;
                        $uploadedImagePath = public_path('uploads/governify/'. $serviceRequest->image);
                        // Check if the image file exists
                        if (File::exists($uploadedImagePath)) {
                            // Delete the image file
                            File::delete($uploadedImagePath);
                        }
                    }

                    // else {
                    //     return response(json_encode(array('response' => true, 'status' => false, 'message' => 'Image upload failed')));
                    // }
                    $update = $serviceRequest->update($insert_array);
                    if ($update) {
                        return response(json_encode(array('response' => [], 'status' => true, 'message' => "Service Request Updated Successfully.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request Not Updated.")));
                    }
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request Data Not Found. Invalid Service Request Id Provided.")));
                }
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userId = $this->verifyToken()->getData()->id;
        if (!empty($userId)) {
            $serviceRequestData = GovernifyServiceRequest::find($id);

            if (!empty($serviceRequestData)) {
                $params = array(
                    'id' => $id
                );
                $dataToUpdate = array(
                    'deleted_at' => date('Y-m-d H:i:s')
                );
                
                // $deleteServiceRequest = GovernifyServiceRequest::where('id', $id)->update($dataToUpdate);
                // Delete dependent records
                CategoryServiceFormMapping::where('service_id', $id)->delete();
                // Delete the parent record
                $deleteServiceRequest = GovernifyServiceRequest::where('id', $id)->delete();
                if ($deleteServiceRequest > 0) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Service Request Deleted Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Service Request Not Deleted.")));
                }
                if ($deleteServiceRequest) {
                    // $responseData = array(
                    //     'access_token' => $this->refreshToken()->original[ 'access_token' ]
                    // );
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Service Request Deleted Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Service Request Not Deleted.")));
                }
            }
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request Data Not Found. Invalid Service Request Id Provided.")));
        }
        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
    }

    function getErrorMessages()
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

    public function swapServiceRequests (Request $request ){

        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $request->validate([
                    'service_request'        => 'required|array',
                    'service_request.*.from' => 'required|integer|exists:governify_service_requests,id',
                    'service_request.*.to'   => 'required|integer',
                ]);

                // Extract the array of IDs from the request
                $fromIds     = array_column($request->input('service_request'), 'from');
                $toPositions = array_column($request->input('service_request'), 'to');

                // Check for duplicate `to` positions in the input
                if (count($toPositions) !== count(array_unique($toPositions))) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Duplicate `to` IDs found in the input.")));
                }

                if (count($fromIds) !== count(array_unique($fromIds))) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Duplicate `from` IDs found in the input.")));
                }

                // Check if all `from` IDs exist in the service_categorie table
                $existingCategoryIds = GovernifyServiceRequest::whereIn('id', $fromIds)->pluck('id')->toArray();

                if (count($fromIds) !== count($existingCategoryIds)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "One or more IDs do not exist in the Service Categorie Request table")));
                }

                // Extract the order array from the request
                $categories = $request->input('service_request');

                // Update the service_categories_request_index for each service_categorie
                foreach ($categories as $category) {
                    GovernifyServiceRequest::where('id', $category['from'])->update(['service_categories_request_index' => $category['to']]);
                }
                $dataToRender =  GovernifyServiceRequest::with(['serviceCategorie','form'])->whereNull('deleted_at')->orderBy('service_categories_request_index')->get();
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Service categories request order updated successfully.")));
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}
