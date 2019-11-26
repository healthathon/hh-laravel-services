<?php

namespace App\Http\Controllers\API;

use App\Services\HealthAppKitService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Helpers;

class HealthAppKitController extends Controller
{

    private $healthAppKitService;

    public function __construct()
    {
        $this->healthAppKitService = new HealthAppKitService();
    }

    // Delete User Entry from table on reset demand
    private function validationMessageForsaveUserHealthData()
    {
        return [
            'userid.required' => "Please provide user identity",
        ];
    }

    public function saveUserHealthData(Request $request)
    {

    	$validator = Validator::make($request->all(), [
            'userid' => 'required',
            'steps' => 'required',
            'walks' => 'required',
            'sleep' => 'required',
            'heartrate' => 'required',
            'bmi' => 'required',
            'temp' => 'required',
            'fate' => 'required',
        ], $this->validationMessageForsaveUserHealthData());
        if ($validator->fails())
            return Helpers::getResponse(400, "Validation Error", $validator->getMessageBag()->first());

        return $this->healthAppKitService->saveUserHealthData($request);
    }
}
