<?php

namespace App\Http\Controllers\Onboardify\Admin;

use App\Http\Controllers\Controller;
use App\Models\MondayUsers;
use Illuminate\Http\Request;
use App\Traits\MondayApis;

class DashboardController extends Controller
{
    use MondayApis;

    public function userListing (Request $request){
        try {
            $userId = $this->verifyToken()->getData()->id;
            if ($userId) {
                $mondayUsers = MondayUsers::latest()->get();
                if (!empty($mondayUsers)) {
                    return response(json_encode(array('response' => $mondayUsers, 'status' => true, 'message' => "Users data.")));
                }else{
                    return response(json_encode(array('response' => [], 'status' => false, 'message' => "Users data not found.")));
                }
            } else {
                return response(json_encode(array('response' => [], 'status' => false, 'message' => "Invalid User.")));
            }
        } catch (\Exception $e) {
            return response(json_encode(array('response' => [], 'status' => false, 'message' => $e->getMessage())));
        }
    }
}   
