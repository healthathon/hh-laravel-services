<?php

/**
 * This Controller Class represents Labs  Services Controller
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  $nameSpace
 * @version  v.1.1
 */

namespace App\Http\Controllers\API;


use App\Codes\SystemResponseCodes;
use App\Constants;
use App\Exceptions\NoAssessmentFoundException;
use App\Exceptions\UserNotFoundException;
use App\Helpers;
use App\Model\DiagnosticLabInformation;
use App\Model\LabsTest;
use App\Model\ShortHealthAssessment;
use App\Model\ThyrocareUserData;
use App\Model\User;
use App\Respositories\DiagnosticLabInformationRepository;
use App\Respositories\LabRepository;
use App\Respositories\ThyrocareUserDataRepository;
use App\Respositories\UserRepository;
use App\Services\ThyroCareServices;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Class DiagnosticLabController
 *
 * This controller invoke the services request by the client.
 * @package App\Http\Controllers\API
 */
class DiagnosticLabController
{

    private $thyrocareLabService;
    private $userRepo;

    public function __construct()
    {
        $this->thyrocareLabService = new ThyroCareServices();
        $this->userRepo = new UserRepository();
    }

    /**
     * This method is executed by Job Scheduler after every 15 days
     * @author  Mayank Jariwala
     * @return null
     */
    public function saveAllThyrocareProducts()
    {
        return $this->thyrocareLabService->saveAllThyrocareProducts();
    }

    /**
     * This method is executed by Job Scheduler
     * @author  Mayank Jariwala
     * @param $name : thyrocare Params Test/Profile/Offer
     * @return  null
     */
    public function getThyrocareInformation($name)
    {
        $name = strtolower($name);
        switch ($name) {
            case "tests":
                return $this->thyrocareLabService->getThyroCareTests();
                break;
            case "profile":
                return $this->thyrocareLabService->getThyroCareProfile();
                break;
            case "offer":
                return $this->thyrocareLabService->getThyroCareOffer();
                break;
            default:
                return response()->json(['statusCode' => 400, 'statusMessage' => 'No such code found']);
                break;
        }
    }

    /**
     * Based on date and pincode appointment slots are provided by thyrocare services
     *
     * @author  Mayank Jariwala
     * @param $date
     * @param $pincode
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAppointmentSlots($date, $pincode)
    {
        return $this->thyrocareLabService->getAppointmentSlots($date, $pincode);
    }

    /**
     * To check whether services are available  on given pincode
     * @author  Mayank Jariwala
     * @param $pincode
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPincodeAvailability($pincode)
    {
        return $this->thyrocareLabService->getPincodeAvailability($pincode);
    }

    public function bookLabOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "lab" => "required"
        ]);
        if ($validator->fails())
            return Helpers::getResponse(400, $validator->getMessageBag()->first());
        $labName = strtolower($request->get("lab"));
        if ($labName === Constants::THYROCARE)
            return $this->bookThyrocareServiceOrder($request);
        else if ($labName === Constants::MAPMYGENOME)
            return $this->bookMMGTestOrder($request);
        else
            return Helpers::getResponse(400, "Please Enter Valid Lab Name");
    }

    /**
     * Booking Functionality of Thyrocare Services
     *
     * @author  Mayank Jariwala
     * @param Request $request
     * @return mixed
     */
    public function bookThyrocareServiceOrder(Request $request)
    {
        $user_id = $request->user_id;
        $request = $request->except('user_id');
        return $this->thyrocareLabService->bookThyrocareServiceOrder($user_id, $request);
    }

    /**
     * Booking Functionality of Thyrocare Services
     *
     * @author  Mayank Jariwala
     * @param Request $request
     * @return mixed
     */
    public function bookMMGTestOrder(Request $request)
    {
        $user_id = $request->user_id;
        $request = $request->except('user_id');
        try {
            $this->thyrocareLabService->bookMMGTestForUser($user_id, $request);
            return Helpers::getResponse(200, "Your order is booked");
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Exception Occurred", $e->getMessage());
        }
    }

    public function getLabTests($labName)
    {
        $labObject = (new DiagnosticLabInformationRepository())->where('name', ucfirst($labName))->first();
        $testCollection = new Collection($labObject->tests);
        $response['lab_name'] = ucfirst($labName);
        foreach ($testCollection->groupBy('profile') as $key => $groupTests) {
            $response['lab_tests'][] = [
                'category' => empty($key) ? ucfirst($labName) : $key,
                'category_tests' => $groupTests
            ];
        }
        return Helpers::getResponse(200, ucfirst($labName) . " Tests", $response);
    }

    /**
     * Fetch Specific Test Detail Information
     * @param $testId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTestDetailInformation($testId)
    {
        $testDetailInfo = (new LabRepository())->where('id', $testId)->first(['test_name', 'about', 'reason_to_do', 'preparation', 'process_duration', 'result_duration', 'results', 'age_group', 'good_range',
            'parameters_tested', 'parameters_tested_unit', 'test_suggestions']);
        if ($testDetailInfo == null)
            // This case will never happen from application side, unless user/developer externally fire some weird query
            return Helpers::getResponse(404, "No Test Found");
        return Helpers::getResponse(200, "$testDetailInfo->test_name Test Information", $testDetailInfo);
    }

    /**
     * This function shows orders of user and the status is initially the one sent by
     * server of Thyrocare. Te status will be updated by Cron Job Scheduler which will keep check
     * of All users order.
     *
     * @param $id : User ID
     * @return User|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getUserThyrocareOrders($id)
    {
        try {
            $user = $this->userRepo->getUser($id, ['id', 'first_name', 'last_name']);
            $user->getThyrocareTestOrders;
            return Helpers::getResponse(200, "Thyrocare Orders", $user);
        } catch (\Exception $e) {
            return Helpers::getResponse(500, SystemResponseCodes::UNF);
        }
    }

    /**
     * This function provide details of benificiary details.
     *
     * @param $orderNo : Thyrocare Order No
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThyrocareOrderBenDetails($orderNo)
    {
        $benData = (new ThyrocareUserDataRepository())->with('getUserBenDetails')
            ->where('order_id', $orderNo)
            ->get(['order_id', 'ref_order_id']);
        return Helpers::getResponse(200, "Ben Details for order no. $orderNo", $benData);
    }


    public function getRecommendedTestForUser($userId)
    {
        try {
            $user = $this->userRepo->getUser($userId);
            $user->hasAssessmentRecord();

//            return $user;

            return $this->thyrocareLabService->getRecommendedTestForUser($user);
        } catch (UserNotFoundException $e) {
            $e->setMessage(Constants::NO_USER_FOUND);
            return $e->sendUserNotFoundExceptionResponse();
        } catch (NoAssessmentFoundException $e) {
            $e->setMessage(Constants::NO_ASSESSMENT_FOUND);
            return $e->sendNoAssessmentFoundResponse();
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Server Error", $e->getMessage());
        }
    }
}