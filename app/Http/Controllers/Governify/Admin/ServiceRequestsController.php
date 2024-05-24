<?php

namespace App\Http\Controllers\Governify\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MondayApis;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\GovernifyServiceRequest;

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
                $dataToRender =  GovernifyServiceRequest::with('serviceCategorie')->whereNull('deleted_at')->orderByDesc('id')->get();
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
                    'title'       => "required|string",
                    'description' => "required|string",
                    'image'       => "required|image|mimes:jpeg,jpg,png|max:10000",
                    'form'        => "required|integer",
                    'service_categorie_id' => 'required|exists:governify_service_categories,id',
                ], $this->getErrorMessages());

                // Check if the validation fails
                // if ($validator->fails()) {
                //     return response(json_encode(array('response' => true, 'status' => false, 'message' => $validator->errors())));
                // }

                $insert_array = array(
                    "title"       => $request->title,
                    "description" => $request->description,
                    "form"        => $request->form,
                    "service_categorie_id" => $request->service_categorie_id,
                    "created_at" => date("Y-m-d H:i:s"),
                    "updated_at" => date("Y-m-d H:i:s")
                );

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
            $dataToRender = GovernifyServiceRequest::with('serviceCategorie')->whereNull('deleted_at')->find($id);

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

                $this->validate($request, [
                    'title'       => "required|string",
                    'description' => "required|string",
                    'image'       => "required|image|mimes:jpeg,jpg,png|max:10000",
                    'form'        => "required|integer",
                    'service_categorie_id' => 'required|exists:governify_service_categories,id',
                ], $this->getErrorMessages());

                // Check if the validation fails
                // if ($validator->fails()) {
                //     return response(json_encode(array('response' => true, 'status' => false, 'message' => $validator->errors())));
                // }

                $insert_array = array(
                    "title"       => $request->title,
                    "description" => $request->description,
                    "form"        => $request->form,
                    "service_categorie_id" => $request->service_categorie_id,
                    "updated_at" => date("Y-m-d H:i:s")
                );

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
                
                $deleteServiceRequest = GovernifyServiceRequest::where('id', $id)->update($dataToUpdate);
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
}
