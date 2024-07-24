<?php

namespace App\Http\Controllers\Governify\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryServiceFormMapping;
use Illuminate\Http\Request;
use App\Traits\MondayApis;
use App\Models\GovernifyServiceCategorie;
use App\Models\GovernifyServiceRequest;
use App\Models\GovernifyServiceRequestForms;
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
                    // 'subtitle'    => "required|string",
                    'description' => "required|string",
                ], $this->getErrorMessages());

                $insert_array = array(
                    "icon"       => $input['icon'],
                    "title"      => $input['title'],
                    // "subtitle"   => $input['subtitle'],
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
                    // 'subtitle'    => "required|string",
                    'description' => "required|string",
                ], $this->getErrorMessages());

                if (empty($checkStoreExits)) {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Service Categorie Data Not Found. Invalid Service Categorie Id Provided.")));
                }
                $update_array = array(
                    'icon'        => $input['icon'],
                    'title'       => $input['title'],
                    // 'subtitle'    => $input['subtitle'],
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
                // $deleteServiceCategorie = GovernifyServiceCategorie::updateTableData('governify_service_categories', $params, $dataToUpdate);
                // Delete dependent records
                // Update the service_categorie_id to null for the given id
                GovernifyServiceRequest::where('service_categorie_id', $id)->update(['service_categorie_id' => null]);
                CategoryServiceFormMapping::where('categorie_id', $id)->delete();
                // Delete the parent record
                // Hard Delete
                $deleteServiceCategorie = GovernifyServiceCategorie::where('id', $id)->delete();
                if ($deleteServiceCategorie > 0) {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Service Category Deleted Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Service Category Not Deleted.")));
                }

                //Soft Delete
                /*
                $deleteServiceCategorie = GovernifyServiceCategorie::updateTableData('governify_service_categories', $params, $dataToUpdate);
                if ($deleteServiceCategorie['status'] == "success") {
                    // $responseData = array(
                    //     'access_token' => $this->refreshToken()->original[ 'access_token' ]
                    // );
                    return response(json_encode(array('response' => [], 'status' => true, 'message' =>  "Service Category Deleted Successfully.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' =>  "Service Category Not Deleted.")));
                }
                */
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

                $this->validate($request, [
                    'board_id' => 'required',
                ], $this->getErrorMessages());

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
                $datatoUpdate['board_id'] = $request['board_id'];

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

    public function getCategoriesWithAllService (){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                // $dataToRender = GovernifyServiceCategorie::whereNull('deleted_at')->orderBy('service_categories_index')->get();
                $dataToRender = GovernifyServiceCategorie::with('serviceRequests')->whereNull('deleted_at')->orderByRaw('CASE WHEN service_categories_index IS NULL THEN 1 ELSE 0 END, service_categories_index')->get();
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Categorie Data.")));
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function rejectServiceCategoryMapping (Request $request){

        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $input = $request->json()->all();
                $this->validate($request, [
                    'category_id'        => 'required',
                    'service_id'        => 'required',
                ], $this->getErrorMessages());
                $CategoryServiceFormMappingData = CategoryServiceFormMapping::where(['categorie_id'=> $input['category_id'], 'service_id' => $input['service_id']])->get();
                if ($CategoryServiceFormMappingData->isNotEmpty()) {
                    $firstItem = $CategoryServiceFormMappingData->first();
                    $GovernifyServiceRequestFormsResponse = GovernifyServiceRequestForms::where(['id'=> $firstItem->service_form_id])->get();
                    $decodedData = json_decode($GovernifyServiceRequestFormsResponse, true);
                    $formName = $decodedData[0]['name'];
                   
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => $formName." already assigned to the same service and category.")));
                } else {
                    return response(json_encode(array('response' => [], 'status' => true, 'message' => 'No Mapping found')));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }

    public function listOfOverallStatus(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            $GovernifySiteSettingData = GovernifySiteSetting::where('id', '=', 1)->first();
            $boardId = !empty($GovernifySiteSettingData['board_id']) ? $GovernifySiteSettingData['board_id'] : 1493464821;
            if ($userId) {
                $query = 'query {
                    boards( ids: '.$boardId.') {
                    id
                    name
                    state
                    permissions
                    board_kind
                    columns {
                              title
                              id
                              archived
                              description
                              settings_str
                              title
                              type
                              width
                          }
                        }
                        }';

                $boardsData = $this->_getMondayData($query);

                if (!empty($boardsData['response']['data']) && !empty($boardsData['response']['data']['boards'] && $boardsData['response']['data']['boards'][0]['columns'])) {
                    foreach ($boardsData['response']['data']['boards'][0]['columns'] as $item) {
                        if ($item['id'] === 'status__1') {
                            $settings_str = json_decode($item['settings_str'], true);
                            $manipulatedData = [];
                            foreach ($settings_str['labels'] as $value => $label) {
                                $manipulatedData[] = ['label' => $label, 'value' => $value];
                            }
                            break;
                        }
                    }
                    if (!empty($manipulatedData)) {
                        return response(json_encode(array('response' => $manipulatedData, 'status' => true, 'message' => "Governify overall status data.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Governify overall status data not fetch.")));
                    }
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Governify overall status data not found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
    public function fetchAllBoards(Request $request)
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $page      = 1;
                $tolalData  = 1000;
                $after     = 'null';
                $mondayData = [];
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
                return $newResponse;
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
    
    public function fetchBoardWiseColumn($id)
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
                    $boardsData = $this->_getMondayData($query);
                    if (!empty($boardsData['response']['data']) && !empty($boardsData['response']['data']['boards']) && !empty($boardsData['response']['data']['boards'][0]['columns'])) {
                        return response(json_encode(array('response' => $boardsData['response']['data']['boards'][0]['columns'], 'status' => true, 'message' => "Board Columns Data Found.")));
                    } else {
                        return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Columns Not Data Found.")));
                    }
                } else {
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Board Id Not Found")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}
