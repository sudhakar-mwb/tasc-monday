<?php

namespace App\Http\Controllers\Monday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\MondayApis;

class TrackOnboardingController extends Controller
{
    use  MondayApis;

    public function trackOnboarding(Request $request)
    {
        $query = request()->all();
        return $response = $this->_get($query);
    }

    public function trackOnboardingById (Request $request){
        $query = request()->all();
        return $response = $this->_get($query);
    }
}
