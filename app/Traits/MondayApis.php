<?php
namespace App\Traits;

trait MondayApis
{
    /**
     * Base url of monday api.
     */
    private $baseUrl = "https://api.monday.com/v2";


    public function _get($post_params)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->baseUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 30,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        // CURLOPT_POSTFIELDS =>json_encode($post_params),
        CURLOPT_POSTFIELDS =>'{"query":'.json_encode($post_params).'}',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '. env('MONDAY_API_KEY'),
            'Content-Type: application/json',
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_errors = curl_error($curl);
        return ['status_code' => $status_code, 'response' => json_decode($response, true), 'errors' => $curl_errors];
    }

    public function _getMondayData ($post_params){

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->baseUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 30,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        // CURLOPT_POSTFIELDS =>json_encode($post_params),
        CURLOPT_POSTFIELDS =>'{"query":'.json_encode($post_params).'}',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '. env('MONDAY_API_KEY'),
            'Content-Type: application/json',
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_errors = curl_error($curl);
        return ['status_code' => $status_code, 'response' => json_decode($response, true), 'errors' => $curl_errors];
    }

    public function verifyToken() {
        return response()->json(auth()->user());
    }

    public function refreshToken() {
        return $this->respondWithToken(auth()->refresh());
    }
}
