<?php

namespace App\Http\Controllers\Monday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MondayApis;

class StatusOnboardingController extends Controller
{
    use  MondayApis;

    public function statusOnboardingHiringType (Request $request){
        $query = request()->all();
        return $response = $this->_get($query);
    }
}
