<?php

namespace App\Http\Controllers\Governify\Admin;

use App\Http\Controllers\Controller;
use App\Models\GovernifyServiceRequestForms;
use App\Traits\MondayApis;
use Illuminate\Http\Request;

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
                $dataToRender =  GovernifyServiceRequestForms::whereNull('deleted_at')->orderByDesc('id')->get();
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Request Form Data.")));
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
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $input = $request->json()->all();
                $this->validate($request, [
                    'name'       => "required|string|unique:governify_service_request_forms",
                    'description' => "required|string",
                    'form_data'  => "required",
                ], $this->getErrorMessages());
                $insert_array = array(
                    "name"        => $request->name,
                    "form_data"   => $request->form_data,
                    "description" => $request->description,
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
