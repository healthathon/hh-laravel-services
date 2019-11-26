<?php

namespace App\Http\Controllers\Api;

use App\Helpers;
use App\Respositories\MixedBagUserHistoryRepository;
use App\Services\MixedBagService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class MixedBagController : This controller is responsible to call an register,reset and
 * complete mixed bag task.
 * @package App\Http\Controllers\Api
 * @author  Mayank Jariwala
 */
class MixedBagController extends Controller
{

    private $mixedBagService,$MixedBagUserHistoryRepo;

    public function __construct()
    {
        $this->mixedBagService = new MixedBagService();
        $this->MixedBagUserHistoryRepo = new MixedBagUserHistoryRepository();
    }

    // Register Task of User
    public function register(Request $request)
    {
        return $this->mixedBagService->register($request);
    }

    // Initialize User Object with Mixed Bag
    public function taskComplete(Request $request)
    {
        $userMbObject = $this->MixedBagUserHistoryRepo->getUserMbObject($request->userId, $request->taskId);
        if (is_null($userMbObject))
            return Helpers::getResponse(404, "User not doing this regimen");
        return $this->mixedBagService->updateUserMixedBagRegimenObject($userMbObject);
    }

    //Reset Mixed Bag Task for user
    public function resetMixedBagRegimen(Request $request)
    {
        $userMbObject = $this->MixedBagUserHistoryRepo->getUserMbObject($request->userId, $request->taskId);
        if (is_null($userMbObject))
            return Helpers::getResponse(404, "User not doing this regimen");
        return $this->mixedBagService->resetUserRegimen($userMbObject);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unregisterMixedTask(Request $request)
    {
        try {
            $userMbObject = $this->MixedBagUserHistoryRepo->removeUser($request->userId, $request->taskId);
            if ($userMbObject)
                return Helpers::getResponse(200, "User removed from regimen");
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Something went wrong " . $e->getMessage());
        }
        return;
    }
}
