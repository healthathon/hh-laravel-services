<?php

/**
 * This  Service Class represents the thyrocare related services like booking  test, fetching
 * test and insert ben information.
 *
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  $nameSpace
 * @version  v.1.1
 */

namespace App\Services;

use App\Events\SendMMGBookingMailToUser;
use App\Exceptions\ThyrocareResponseException;
use App\Helpers;
use App\Http\Controllers\Admin\AssessmentController;
use App\Http\Controllers\MailController;
use App\Jobs\SendMMGBookingEmail;
use App\Model\DiagnosticLabInformation;
use App\Model\LabsTest;
use App\Model\MMGBookingDetails;
use App\Model\ThyrocareBeanDetails;
use App\Model\ThyrocareUserData;
use App\Respositories\UserRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class ThyroCareServices : Execute All Services developed by thyrocare lab
 *
 * @author  Mayank Jariwala
 * @package App\Services
 */
class ThyroCareServices
{
    use DispatchesJobs;
    private $testsStoragePath, $apiKey, $guzzleClient, $accessController, $userRepo, $labService;

    public function __construct()
    {
        $this->accessController = new AssessmentController();
        $this->guzzleClient = new Client([
            'verify' => false
        ]);
        $this->testsStoragePath = url("/") . '/storage/thyrocare';
        $this->apiKey = "Hhjkis5QSeHy@1NZV1qodTN7w4qlmXd78hNQeD7zmbNkeO8WObutCH9Lp4fH6UDWHCi1r18hcxM=";
        $this->userRepo = new UserRepository();
        $this->labService = new LabService();
    }

    /**
     * Invoke by saveAllThyrocareProducts() function
     *
     * @return \Illuminate\Http\JsonResponse
     * @author  Mayank Jariwala
     */
    public function saveAllThyrocareProducts()
    {
        $this->clearCache();
        try {
            $getThyroCareProductUrl = Config::get('constants.api.thyrocare.get_products');
            // By Passing SSL
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $data = file_get_contents($getThyroCareProductUrl, false, stream_context_create($arrContextOptions));
            $data = json_decode($data, true);
            $response['offer'] = $data['MASTERS']['OFFER'];
            $response['tests'] = $data['MASTERS']['TESTS'];
            $response['profile'] = $data['MASTERS']['PROFILE'];
            Storage::disk('public')->put('thyrocare/offer.json', json_encode($data['MASTERS']['OFFER']));
            Storage::disk('public')->put('thyrocare/tests.json', json_encode($data['MASTERS']['TESTS']));
            Storage::disk('public')->put('thyrocare/profile.json', json_encode($data['MASTERS']['PROFILE']));
            $diagnosticModel = DiagnosticLabInformation::where('id', 1)->first();
            $diagnosticModel->offer_data = $this->testsStoragePath . "/offer.json";
            $diagnosticModel->test_data = $this->testsStoragePath . "/tests.json";
            $diagnosticModel->profile_data = $this->testsStoragePath . "/profile.json";
            if ($diagnosticModel->save()) {
                Cache::rememberForever('offerValue', function () use ($data) {
                    Log::info("Storing Information of Offer Value into Cache");
                    return $data['MASTERS']['OFFER'];
                });
                Cache::rememberForever('profileValue', function () use ($data) {
                    Log::info("Storing Information of Offer Value into Cache");
                    return $data['MASTERS']['PROFILE'];
                });
                Cache::rememberForever('testValue', function () use ($data) {
                    Log::info("Storing Information of Offer Value into Cache");
                    return $data['MASTERS']['TESTS'];
                });
                return response()->json($response);
            }
        } catch (\Exception $e) {
            Log::error("Something went wrong {}" . $e->getMessage());
            $response['statusCode'] = 500;
            $response['statusMessage'] = "Something went wrong. Please Try again";
        }
        return response()->json($response);
    }

    private function clearCache()
    {
        // Clear all caches regarding thyrocare test information
        Cache::forget('offerValue');
        Cache::forget('profileValue');
        Cache::forget('testValue');
    }

    /**
     * This function is responsible to send thyrocare test data in json
     * format to client or any server who requested for this service
     *
     * This data is getting refresh after every 15 days
     * @author  Mayank Jariwala
     */
    public function getThyroCareTests()
    {
        $response = [];
        if (Cache::has('testValue')) {
            $data = Cache::get('testValue');
        } else {
            $data = json_decode(Storage::disk('public')->get('thyrocare/tests.json'));
        }
        $response['tests'] = $data;
        return $response;
    }

    /**
     * This function is responsible to send thyrocare Profile data in json
     * format to client or any server who requested for this service
     *
     *  This data is getting refresh after every 15 days
     * @author  Mayank Jariwala
     */
    public function getThyroCareProfile()
    {
        $response = [];
        if (Cache::has('profileValue')) {
            $data = Cache::get('profileValue');
        } else {
            $data = json_decode(Storage::disk('public')->get('thyrocare/profile.json'));
        }
        $response['profile'] = $data;
        return $response;
    }

    /**
     * This function is responsible to send thyrocare Offer data in json
     * format to client or any server who requested for this service
     *
     * This data is getting refresh after every 15 days
     * @author  Mayank Jariwala
     */
    public function getThyroCareOffer()
    {
        $response = [];
        if (Cache::has('offerValue')) {
            $data = Cache::get('offerValue');
        } else {
            Log::info(" Getting Information from stored file");
            $data = json_decode(Storage::disk('public')->get('thyrocare/offer.json'));
        }
        $response['offer'] = $data;
        return $response;
    }

    /**
     *  Get Thyrocare Appointment Slots
     * @param $date
     * @param $pincode
     * @return \Illuminate\Http\JsonResponse
     * @author  Mayank Jariwala
     */
    public function getAppointmentSlots($date, $pincode)
    {
        try {
            $getThyroCareAppointmentUrl = Config::get('constants.api.thyrocare.get_appointment_slots');
            // By Passing SSL
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $date = date("Y-m-d", strtotime($date));
            // Appending date and pincode to base url of appointment slots
            $getThyroCareAppointmentUrl .= $pincode . "/" . $date . "/GetAppointmentSlots";
            $data = file_get_contents($getThyroCareAppointmentUrl, false, stream_context_create($arrContextOptions));
            $data = json_decode($data, true);
            return response()->json([
                'statusCode' => 200,
                'statusMessage' => 'Appointment Slots',
                'LSlotDataRes' => $data['LSlotDataRes']
            ]);
        } catch (\Exception $e) {
            Log::error("Something went wrong {}" . $e->getMessage());
            $response['statusCode'] = 500;
            $response['statusMessage'] = "Something went wrong. Please Try again";
            return response()->json($response);
        }
    }

    /**
     * @param $pincode
     * @return \Illuminate\Http\JsonResponse
     * @author  Mayank Jariwala
     */
    public function getPincodeAvailability($pincode)
    {
        try {
            $getThyroCarePincodeAvailableUrl = Config::get('constants.api.thyrocare.get_pincode_availability');
            // By Passing SSL
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $getThyroCarePincodeAvailableUrl .= $pincode . "/PincodeAvailability";
            $data = file_get_contents($getThyroCarePincodeAvailableUrl, false, stream_context_create($arrContextOptions));
            $data = json_decode($data, true);
            return response()->json([
                'statusCode' => 200,
                'statusMessage' => 'Appointment Slots',
                'response' => $data
            ]);
        } catch (\Exception $e) {
            Log::error("Something went wrong {}" . $e->getMessage());
            $response['statusCode'] = 500;
            $response['statusMessage'] = "Something went wrong. Please Try again";
            return response()->json($response);
        }
    }


    /**
     *  Booking Functionality of Thyrocare Services
     *
     * @param $user_id
     * @param $request
     * @return mixed
     * @author  Mayank Jariwala
     */
    public function bookThyrocareServiceOrder($user_id, $request)
    {
        $postData = $request;
        $postData['api_key'] = $this->apiKey;
        $postData['orderid'] = "OR" . $this->generateOrderId();
        try {
            $requestContent = array(
                'headers' => array(
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ),
                'json' => $postData
            );
            $getThyroCareBookServiceUrl = Config::get('constants.api.thyrocare.book_order');
            $responseData = $this->guzzleClient->request('POST', $getThyroCareBookServiceUrl, $requestContent);
            $data = json_decode($responseData->getBody(), true);
            if ($data['RES_ID'] != "RES0000")
                throw  new ThyrocareResponseException($data["RESPONSE"]);
            $newBookingInfo = [
                'order_id' => $data['ORDER_NO'],
                'user_id' => $user_id,
                'ref_order_id' => $data['REF_ORDERID'],
                'email' => $data['EMAIL'],
                'fasting' => $data['FASTING'],
                'mobile' => $data['MOBILE'],
                'address' => $data['ADDRESS'],
                'booked_by' => $data['BOOKED_BY'],
                'product' => $data['PRODUCT'],
                'rate' => $data['CUSTOMER_RATE'],
                'service_type' => $data['SERVICE_TYPE'],
                'payment_mode' => $data['MODE'],
                'payment_type' => $data['PAY_TYPE'],
                'order_status' => $data['STATUS'],
                'hard_copy' => $data['REPORT_HARD_COPY'] === "N0" ? 1 : 0
            ];
            $orderBookingStatus = ThyrocareUserData::create($newBookingInfo);
            if ($orderBookingStatus) {
                $beanDatas = $data['ORDERRESPONSE']['PostOrderDataResponse'];
                $beanDataArr = [];
                foreach ($beanDatas as $beanData) {
                    $beanDataArr[] = [
                        'order_id' => $data['ORDER_NO'],
                        'lead_id' => $beanData['LEAD_ID'],
                        'name' => $beanData['NAME'],
                        'gender' => $beanData['GENDER'],
                        'age' => $beanData['AGE'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                }
                // Mast Insert Ben Details (Patient Details)
                ThyrocareBeanDetails::insert($beanDataArr);
                return Helpers::getResponse(200, "Booking Status", $data);
            } else {
                return Helpers::getResponse(500, "Internal Server Error");
            }
        } catch (ThyrocareResponseException $e) {
            return $e->sendThyrocareExceptionResponse();
        } catch (\Exception $e) {
            Log::error("Something went wrong {}" . $e->getMessage());
            return Helpers::getResponse(500, $e->getMessage());
        } catch (GuzzleException $e) {
            Log::error("Something went wrong {}" . $e->getMessage());
            return Helpers::getResponse(500, "Something went wrong. Please Try again" . $e->getMessage());
        }
    }

    /**
     *  Generate Random Order Id of length 6
     * @param int $length
     * @return string
     * @author  Mayank Jariwala
     */
    private function generateOrderId($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // Fetch Recommended Test for User
    public function getRecommendedTestForUser($user)
    {
        $testIdsArr = array_column($user->recommendedTest->toArray(), 'test_id');
        $response = [];
        $labsTestsInfo = LabsTest::with('lab:id,name,address')
            ->whereIn('id', $testIdsArr)
            ->get(['id', 'test_name', 'profile', 'test_code', 'abbr', 'price', 'lab_id', 'sample_type', 'process_duration', 'result_duration']);
        $labTestCollection = new Collection($labsTestsInfo);
        $groupByLabNameCollection = $labTestCollection->groupBy('lab.name');
        $arrayIndex = 0;
        foreach ($groupByLabNameCollection as $labName => $labTest) {
            $labTestResponse = [];
            $testCollection = new Collection($labTest);
            $groupByProfile = $testCollection->groupBy('profile');
            foreach ($groupByProfile as $categoryName => $categoryTest) {
                $labTestResponse[] = [
                    'category' => empty($categoryName) ? $labName : $categoryName,
                    'category_tests' => $categoryTest
                ];
            }
            $response[$arrayIndex] = [
                'lab_name' => $labName,
                'lab_tests' => $labTestResponse
            ];
            $arrayIndex++;
        }
        return Helpers::getResponse(200, "Recommended Test", $response);
    }

    /**
     * @param $user_id
     * @param array $request
     * @return void
     * @throws \App\Exceptions\UserNotFoundException
     * @throws \Exception
     */
    public function bookMMGTestForUser($user_id, array $request)
    {
        $user = $this->userRepo->getUser($user_id);
        $alreadyBookedTestsId = array_column($user->mmgTests->toArray(), "test_id");
        $newTestIds = array_diff($request["tests_id"], $alreadyBookedTestsId);
        $testIdCollection = [];
        $names = $this->labService->getMultipleTestNameFromTestIds($request["tests_id"]);
        foreach ($newTestIds as $testId) {
            $testIdCollection[] = [
                "test_id" => $testId,
                "user_id" => $user_id,
                "created_at" => date("y-m-d h:i:s"),
                "updated_at" => date("y-m-d h:i:s"),
            ];
        }
        DB::beginTransaction();
        try {
            MMGBookingDetails::insert($testIdCollection);
            DB::commit();
            event(new SendMMGBookingMailToUser($user));
            $job = (new SendMMGBookingEmail($user, $names->toArray()))->onQueue('emails');
            $this->dispatch($job);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return;
    }
}