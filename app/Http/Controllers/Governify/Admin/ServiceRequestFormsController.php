<?php

namespace App\Http\Controllers\Governify\Admin;

use App\Http\Controllers\Controller;
use App\Models\GovernifyServiceRequestForms;
use App\Models\CategoryServiceFormMapping;
use App\Traits\MondayApis;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class ServiceRequestFormsController extends Controller
{
    use MondayApis;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['']]);
    }

    public function index()
    {
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                 $dataToRender = GovernifyServiceRequestForms::with(['categoryServiceFormMappings.serviceRequest','categoryServiceFormMappings.serviceCategory'])->get();
                // $dataToRender =  GovernifyServiceRequestForms::whereNull('deleted_at')->orderByDesc('id')->get();
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Request Form Data.")));
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
        }
    }

    public function fetchServiceRequestFormSchema()
    {
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $defaultFormData = [
                    [
                        "type" => "textArea",
                        "required" => false,
                        "label" => "Label",
                        "defaultValue" => "This is the defaultValue of text area"
                    ],
                    [
                        "type" => "input",
                        "fieldType" => "email",
                        "required" => false,
                        "label" => "Input Label",
                        "defaultValue" => "This is the defaultValue of input label"
                    ]
                ];
                return response(json_encode(array('response' => $defaultFormData, 'status' => true, 'message' => 'Default Form Data')));
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
        }
    }

    public function createServiceRequestForms(Request $request)
    {
        /*
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $input = $request->json()->all();
                $this->validate($request, [
                    'name'       => "required|string|unique:governify_service_request_forms",
                    // 'description' => "required|string",
                    'form_data'  => "required",
                ], $this->getErrorMessages());
                $insert_array = array(
                    "name"        => $request->name,
                    "form_data"   => $request->form_data,
                    // "description" => $request->description,
                    "created_at"  => date("Y-m-d H:i:s"),
                    "updated_at"  => date("Y-m-d H:i:s")
                );
                $insert = GovernifyServiceRequestForms::create($insert_array);
                if ($insert) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => "Service Request Form Created Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request Form Not Created.")));
                }
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
        }
        */
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $input = $request->json()->all();
                $this->validate($request, [
                    'name' => "required|string|unique:governify_service_request_forms",
                    // 'description' => 'required|string',
                    'form_data'                => 'required|array',
                    // 'form_data.*.type'         => 'required|string',
                    // 'form_data.*.required'     => 'required|boolean',
                    // 'form_data.*.label'        => 'required|string',
                    // 'form_data.*.defaultValue' => 'required|string',
                    'category_services_mapping'               => 'required|array',
                    'category_services_mapping.*.category_id' => 'required|integer',
                    'category_services_mapping.*.services_id' => 'required|integer',
                ], $this->getErrorMessages());
 
                $insert_array = array(
                    "name"        => $request->name,
                    "form_data"   => $request->form_data,
                    // "description" => $request->description,
                    "created_at"  => date("Y-m-d H:i:s"),
                    "updated_at"  => date("Y-m-d H:i:s")
                );
                
                $insert = GovernifyServiceRequestForms::create($insert_array);
                if (isset($insert->id)) {
                    if (!empty($request->category_services_mapping)) {
                        foreach ($request->category_services_mapping as $key => $value) {
                            $categoryServiceFormMappingData = array(
                                "service_form_id"=> $insert->id,
                                "service_id"     => $value['services_id'],
                                "categorie_id"   => $value['category_id'],
                                "created_at"  => date("Y-m-d H:i:s"),
                                "updated_at"  => date("Y-m-d H:i:s")
                            );
                            $categoryServiceFormMappingRes = CategoryServiceFormMapping::create($categoryServiceFormMappingData);
                        }
                    }
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => "Service Request Form Created & Mapping Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request Form Not Created.")));
                }
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
        }
    }

    public function updateServiceRequestForms(Request $request, $id)
    {
        /*
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $input = $request->json()->all();

                $serviceRequestForm = GovernifyServiceRequestForms::find($id);

                if (!empty($serviceRequestForm)) {

                    $this->validate($request, [
                        'name'        => ['required', 'string', Rule::unique('governify_service_request_forms')->ignore($id)],
                        // 'description' => "required|string",
                        'form_data'   => "required",
                    ], $this->getErrorMessages());

                    $insert_array = array(
                        "name"        => $request->name,
                        // "description" => $request->description,
                        "form_data"   => $request->form_data,
                        "updated_at"  => date("Y-m-d H:i:s")
                    );

                    $update = $serviceRequestForm->update($insert_array);
                    if ($update) {
                        return response(json_encode(array('response' => [], 'status' => true, 'message' => "Service Request Form Updated Successfully.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request Form Not Updated.")));
                    }
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request From Data Not Found. Invalid Service Request Form Id Provided.")));
                }
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
        }
        */

        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $input = $request->json()->all();

                $serviceRequestForm = GovernifyServiceRequestForms::find($id);

                if (!empty($serviceRequestForm)) {

                    $this->validate($request, [
                        'name'        => ['required', 'string', Rule::unique('governify_service_request_forms')->ignore($id)],
                        // 'description' => 'required|string',
                        'form_data'                => 'required|array',
                        // 'form_data.*.type'         => 'required|string',
                        // 'form_data.*.required'     => 'required|boolean',
                        // 'form_data.*.label'        => 'required|string',
                        // 'form_data.*.defaultValue' => 'required|string',
                        'category_services_mapping'               => 'required|array',
                        'category_services_mapping.*.category_id' => 'required|integer',
                        'category_services_mapping.*.services_id' => 'required|integer',
                    ], $this->getErrorMessages());

                    $insert_array = array(
                        "name"        => $request->name,
                        // "description" => $request->description,
                        "form_data"   => $request->form_data,
                        "updated_at"  => date("Y-m-d H:i:s")
                    );
                    $categoryServiceFormMappingDeleteRes = CategoryServiceFormMapping::where('service_form_id', $id)->delete();

                    $update = $serviceRequestForm->update($insert_array);
                    if (isset($update)) {
                        if (!empty($request->category_services_mapping)) {
                            foreach ($request->category_services_mapping as $key => $value) {
                                $categoryServiceFormMappingData = array(
                                    "service_form_id"=> $id,
                                    "service_id"     => $value['services_id'],
                                    "categorie_id"   => $value['category_id'],
                                    "created_at"  => date("Y-m-d H:i:s"),
                                    "updated_at"  => date("Y-m-d H:i:s")
                                );
                                $categoryServiceFormMappingRes = CategoryServiceFormMapping::create($categoryServiceFormMappingData);
                            }
                        }
                        return response(json_encode(array('response' => [], 'status' => true, 'message' => "Service Request Form Updated & Mapping Successfully.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request Form Not Updated.")));
                    }
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request From Data Not Found. Invalid Service Request Form Id Provided.")));
                }
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userId = $this->verifyToken()->getData()->id;
        if (!empty($userId)) {
            $serviceRequestFormData = GovernifyServiceRequestForms::find($id);

            if (!empty($serviceRequestFormData)) {
                $params = array(
                    'id' => $id
                );
                $dataToUpdate = array(
                    'deleted_at' => date('Y-m-d H:i:s')
                );

                // $deleteServiceRequestForm = GovernifyServiceRequestForms::where('id', $id)->update($dataToUpdate);
                // Delete dependent records
                CategoryServiceFormMapping::where('service_form_id', $id)->delete();
                // Delete the parent record
                $deleteServiceRequestForm = GovernifyServiceRequestForms::where('id', $id)->delete();
                if ($deleteServiceRequestForm > 0) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Service Request Form Deleted Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Service Request Form Not Deleted.")));
                }
                if ($deleteServiceRequestForm) {
                    // $responseData = array(
                    //     'access_token' => $this->refreshToken()->original[ 'access_token' ]
                    // );
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Service Request Form Deleted Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Service Request Form Not Deleted.")));
                }
            }
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Request Form Data Not Found. Invalid Service Request Form Id Provided.")));
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
