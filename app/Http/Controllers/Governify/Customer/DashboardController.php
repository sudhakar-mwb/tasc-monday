<?php

namespace App\Http\Controllers\Governify\Customer;

use App\Http\Controllers\Controller;
use App\Traits\MondayApis;
use Illuminate\Http\Request;
use App\Models\GovernifyServiceRequest;

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
                $dataToRender =  GovernifyServiceRequest::with(['serviceCategorie','form'])->whereNull('deleted_at')->orderByDesc('id')->get();
                return response(json_encode(array('response' => $dataToRender, 'status' => true, 'message' => "Governify Service Request Data.")));
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}
