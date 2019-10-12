<?php

namespace App\Http\Controllers\API;

use App\Services\HealthAppKitService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HealthAppKitController extends Controller
{

    private $healthAppKitService;

    public function __construct()
    {
        $this->healthAppKitService = new HealthAppKitService();
    }

    public function saveUserHealthData(Request $request)
    {
        return $this->healthAppKitService->saveUserHealthData($request);
    }
}
