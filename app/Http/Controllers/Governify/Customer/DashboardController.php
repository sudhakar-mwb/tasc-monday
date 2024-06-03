<?php

namespace App\Http\Controllers\Governify\Customer;

use App\Http\Controllers\Controller;
use App\Traits\MondayApis;
use Illuminate\Http\Request;
use App\Models\GovernifyServiceRequest;
use App\Models\GovernifyServiceCategorie;

class DashboardController extends Controller
{
    use MondayApis;
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $dataToRender =  GovernifyServiceCategorie::with([
                    'service_category_request' => function ($query) {
                        $query->where([['governify_service_requests.deleted_at', '=', null]])->orderBy('service_categories_request_index')->get();
                    },
                    'service_category_request.form' => function ($query) {
                        $query->where([['governify_service_request_forms.deleted_at', '=', null]]);
                    },
                ])->whereNull('deleted_at')->orderBy('service_categories_index')->get();

                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Request Data.")));
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}
