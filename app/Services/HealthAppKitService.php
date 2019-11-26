<?php
/**
 * Created by PhpStorm.
 * User: MayankJariwala
 * Date: 21-Mar-19
 * Time: 12:44 AM
 */

namespace App\Services;

use App\Model\HealthAppKit;
use App\Respositories\HealthAppKitRepository;
use Illuminate\Support\Facades\Log;

class HealthAppKitService
{
    private $user_id, $responseArr, $healthAppKitRepo;

    public function __construct()
    {
        $this->user_id = -1;
        $this->responseArr = array();
        $this->healthAppKitRepo = new HealthAppKitRepository();
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveUserHealthData($request)
    {
        $this->responseArr = array();
        $this->user_id = $request->userid;
        $stepsArray = $request->steps;
        $walkArray = $request->walks;
        $sleepArray = $request->sleep;
        $heartRate = $request->heartrate;
        $bmi = $request->bmi;
        $temp = $request->temp;
        $fate = $request->fate;
        if (!empty($sleepArray)) {
            foreach ($sleepArray as $sleep)
                $this->createOrUpdateUserSleepValue($sleep['value'], $sleep['startdate'], $sleep['enddate']);
        }
        if (!empty($stepsArray)) {
            $this->createOrUpdateUserStepsOrWalkValue("steps", $stepsArray);
        }
        if (!empty($walkArray)) {
            $this->createOrUpdateUserStepsOrWalkValue("walk", $walkArray);
        }
        $this->createOrUpdateUserSpecificValue($fate['date'], $fate['value'], "fate");
        $this->createOrUpdateUserSpecificValue($heartRate['date'], $heartRate['value'], "heart_rate");
        $this->createOrUpdateUserSpecificValue($temp['date'], $temp['value'], "temp");
        $this->createOrUpdateUserSpecificValue($bmi['date'], $bmi['value'], "bmi");
        return response()->json([
            'statusCode' => 200,
            'statusMessage' => $this->responseArr['statusMessage']
        ]);
    }

    /**
     *  For Updating or Creating Sleep value field only since  it has different form of
     * receiving data.
     * TODO: Can be merged with createOrUpdateUserSpecificValue function
     * but client want immediately so temporary function creation is a solution
     * This function is responsible to create or update value of specific column
     * date and value field  which is received as parameter and based on date
     * it checks whether such date already exists then it updates the value or
     * create new value with that date
     *
     * @author  Mayank Jariwala
     * @param $value
     * @param $startdate
     * @param $enddate
     */
    private function createOrUpdateUserSleepValue($value, $startdate, $enddate)
    {
        $localDate = date("Y-m-d", strtotime($startdate));
        $columnValue = "Sleep";
        $columnValueDate = "sleep_date";
        Log::info(" Column Value Date Column " . $columnValueDate);
        $healthAppUser = $this->healthAppKitRepo->where('user_id', $this->user_id)
            ->whereDate('created_at', '=', $localDate)->first();
        if (!$healthAppUser) {
            Log::debug(" Creating New Value for Sleep");
            $healthAppUser = $this->healthAppKitRepo->create([
                'user_id' => $this->user_id,
                'sleep' => $value,
                'start_date' => $startdate,
                'end_date' => $enddate,
                'created_at' => $startdate
            ]);
            if (!$healthAppUser) {
                $this->responseArr['statusMessage'][$columnValue] = "Unable to save $columnValue value";
            }
        } else {
            $healthAppUser->update([
                'sleep' => $value,
                'start_date' => $startdate,
                'end_date' => $enddate,
                'created_at' => $startdate
            ]);
            Log::debug(" Updating Value for $columnValue");
            if (!$healthAppUser) {
                $this->responseArr['statusMessage'][$columnValue] = "Unable to save $columnValue value";
            }
            $this->responseArr['statusMessage'] = "Success";
        }
    }

    /**
     *  This function get each value of walks  and steps array an
     * pass value to createOrUpdateUserSpecificValue function
     *
     * @author  Mayank Jariwala
     * @param $tag :  Column Value to Update
     * @param $arrayDateValue
     */
    private function createOrUpdateUserStepsOrWalkValue($tag, $arrayDateValue)
    {
        $key = $tag == "steps" ? "step" : "dist";
        foreach ($arrayDateValue as $data) {
            $this->createOrUpdateUserSpecificValue($data['date'], $data[$key], $tag);
        }
    }

    /**
     *  This function is responsible to create or update value of specific column
     * date and value field  which is received as parameter and based on date
     * it checks whether such date already exists then it updates the value or
     * create new value with that date
     *
     * @author  Mayank Jariwala
     * @param $date
     * @param $value
     * @param $columnValue
     */
    private function createOrUpdateUserSpecificValue($date, $value, $columnValue)
    {
        $localDate = date("Y-m-d", strtotime($date));
        $columnValueDate = $columnValue . "_date";
        Log::info(" Column Value Date Column " . $columnValueDate);
        $healthAppUser = $this->healthAppKitRepo->where('user_id', $this->user_id)
            ->whereDate('created_at', '=', $localDate)->first();
        if (!$healthAppUser) {
            Log::debug(" Creating New Value for $columnValue");
            $healthAppUser = $this->healthAppKitRepo->create([
                'user_id' => $this->user_id,
                $columnValue => $value,
                $columnValueDate => $date,
                'created_at' => $date
            ]);
            if (!$healthAppUser) {
                $this->responseArr['statusMessage'][$columnValue] = "Unable to save $columnValue value";
            }
        } else {
            $healthAppUser->update([$columnValue => $value, $columnValueDate => $date, 'created_at' => $date]);
            Log::debug(" Updating Value for $columnValue");
            if (!$healthAppUser) {
                $this->responseArr['statusMessage'][$columnValue] = "Unable to save $columnValue value";
            }
            $this->responseArr['statusMessage'] = "Success";
        }
    }
}