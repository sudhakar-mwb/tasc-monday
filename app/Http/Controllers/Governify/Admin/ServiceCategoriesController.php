<?php

namespace App\Http\Controllers\Governify\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MondayApis;
use App\Models\GovernifyServiceCategorie;
use App\Models\GovernifySiteSetting;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class ServiceCategoriesController extends Controller
{
    use MondayApis;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                // $dataToRender = GovernifyServiceCategorie::whereNull('deleted_at')->orderBy('service_categories_index')->get();
                $dataToRender = GovernifyServiceCategorie::whereNull('deleted_at')->orderByRaw('CASE WHEN service_categories_index IS NULL THEN 1 ELSE 0 END, service_categories_index')->get();
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Categorie Data.")));
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
    public function createServiceCategories(Request $request)
    {
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $input = $request->json()->all();
                $this->validate($request, [
                    'icon'        => "required|string",
                    'title'       => "required|string|unique:governify_service_categories",
                    'subtitle'    => "required|string",
                    'description' => "required|string",
                ], $this->getErrorMessages());

                $insert_array = array(
                    "icon"       => $input['icon'],
                    "title"      => $input['title'],
                    "subtitle"   => $input['subtitle'],
                    "description" => $input['description'],
                    "created_at" => date("Y-m-d H:i:s"),
                    "updated_at" => date("Y-m-d H:i:s")
                );
                $insert = GovernifyServiceCategorie::insertTableData("governify_service_categories", $insert_array);
                // $responseData = array(
                //     'access_token' => $this->refreshToken()->original[ 'access_token' ]
                // );
                if ($insert['status'] == 'success') {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => "Service Category Created Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Category Not Created.")));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function showServiceCategoriesById($id)
    {
        $userId = $this->verifyToken()->getData()->id;
        if (!empty($userId)) {
            $dataToRender = GovernifyServiceCategorie::whereNull('deleted_at')->find($id);
            if (!empty($dataToRender)) {
                // $responseData = array(
                //     'access_token' => $this->refreshToken()->original[ 'access_token' ]
                // );
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Categorie Data.")));
            }
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Categorie Data Not Found. Invalid Service Categorie Id Provided.")));
        }
        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function updateServiceCategories(Request $request, $id)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $input = $request->json()->all();
                $checkStoreExits = GovernifyServiceCategorie::getTableData("governify_service_categories", array('id' => $id));

                $this->validate($request, [
                    'icon'        => "required|string",
                    // 'title'       => [ 'required','string',Rule::unique('governify_service_categories')->ignore($checkStoreExits[0]->id)],
                    'title'       => "required|string|unique:governify_service_categories,title," . $checkStoreExits[0]->id,
                    'subtitle'    => "required|string",
                    'description' => "required|string",
                ], $this->getErrorMessages());

                if (empty($checkStoreExits)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Categorie Data Not Found. Invalid Service Categorie Id Provided.")));
                }
                $update_array = array(
                    'icon'        => $input['icon'],
                    'title'       => $input['title'],
                    'subtitle'    => $input['subtitle'],
                    'description' => $input['description'],
                    "updated_at"  => date("Y-m-d H:i:s")
                );
                $update = GovernifyServiceCategorie::updateTableData("governify_service_categories", array("id" => $id), $update_array);

                // $responseData = array(
                //     'access_token' => $this->refreshToken()->original['access_token']
                // );

                if ($update['status'] == 'success') {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Service Category Updated Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Category Not Updated.")));
                }
            }
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
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
    public function destroy($id)
    {
        $userId = $this->verifyToken()->getData()->id;
        if (!empty($userId)) {
            $serviceCategorieData = GovernifyServiceCategorie::getData("governify_service_categories", array("id" => $id));

            if (!empty($serviceCategorieData)) {
                $params = array(
                    'id' => $id
                );
                $dataToUpdate = array(
                    'deleted_at' => date('Y-m-d H:i:s')
                );
                $deleteServiceCategorie = GovernifyServiceCategorie::updateTableData('governify_service_categories', $params, $dataToUpdate);
                if ($deleteServiceCategorie['status'] == "success") {
                    // $responseData = array(
                    //     'access_token' => $this->refreshToken()->original[ 'access_token' ]
                    // );
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Service Category Deleted Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Service Category Not Deleted.")));
                }
            }
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Categorie Data Not Found. Invalid Service Categorie Id Provided.")));
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


    public function governifySiteSetting(Request $request)
    {
        $userId = $this->verifyToken()->getData()->id;
        if ($userId) {
            try {
                $input = $request->json()->all();
                $criteria = ['status' => 0];
                $get_data = GovernifySiteSetting::where('id', '=', 1)->first();

                // $this->validate($request, [
                //     'ui_settings' => 'required|json',
                // ], $this->getErrorMessages());

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
                    File::put(public_path('uploads/governify/' . $updateFileName), $data);
                    $datatoUpdate['logo_name']         = $updateFileName;
                    $imagePath = '/uploads/governify/' . $updateFileName;
                    $datatoUpdate['logo_location'] =  URL::to("/") . $imagePath;

                    // $uploadedImagePath = $serviceRequest->file_location;
                    $uploadedImagePath = public_path('uploads/governify/' . $get_data->logo_name);
                    // Check if the image file exists
                    if (File::exists($uploadedImagePath)) {
                        // Delete the image file
                        File::delete($uploadedImagePath);
                    }
                }

                $datatoUpdate['ui_settings'] = json_encode(!empty($insert_array['ui_settings']) ? $insert_array['ui_settings'] : '');
                $datatoUpdate['status'] = 0;

                if (empty($get_data)) {
                    $insert = GovernifySiteSetting::where($criteria)->create($datatoUpdate);
                } else {
                    $insert = GovernifySiteSetting::where($criteria)->update($datatoUpdate);
                }

                if ($insert) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => "Governify Site Setting Updated Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Governify Site Setting Not Created.")));
                }
            } catch (\Exception $e) {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
            }
        } else {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
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

    public function getGovernifySiteSetting () {

        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $dataToRender = GovernifySiteSetting::where('id', '=', 1)->first();
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Site Setting Data.")));
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function swapServiceCategories (Request $request){

        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $request->validate([
                    'service_categorie'        => 'required|array',
                    'service_categorie.*.from' => 'required|integer|exists:governify_service_categories,id',
                    'service_categorie.*.to'   => 'required|integer',
                ]);

                // Extract the array of IDs from the request
                $fromIds     = array_column($request->input('service_categorie'), 'from');
                $toPositions = array_column($request->input('service_categorie'), 'to');

                // Check for duplicate `to` positions in the input
                if (count($toPositions) !== count(array_unique($toPositions))) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Duplicate `to` IDs found in the input.")));
                }

                if (count($fromIds) !== count(array_unique($fromIds))) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Duplicate `from` IDs found in the input.")));
                }

                // Check if all `from` IDs exist in the service_categorie table
                $existingCategoryIds = GovernifyServiceCategorie::whereIn('id', $fromIds)->pluck('id')->toArray();

                if (count($fromIds) !== count($existingCategoryIds)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "One or more IDs do not exist in the Service Categorie table")));
                }

                // Extract the order array from the request
                $categories = $request->input('service_categorie');

                // Update the service_categories_index for each service_categorie
                foreach ($categories as $category) {
                    GovernifyServiceCategorie::where('id', $category['from'])->update(['service_categories_index' => $category['to']]);
                }
                $dataToRender = GovernifyServiceCategorie::whereNull('deleted_at')->orderBy('service_categories_index')->get();
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Service categories order updated successfully.")));
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}
