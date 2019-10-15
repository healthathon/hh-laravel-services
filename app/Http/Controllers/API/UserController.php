<?php

namespace App\Http\Controllers\API;

use App\Constants;
use App\Events\SendRegistrationMail;
use App\Exceptions\UserNotFoundException;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Model\User;
use App\Respositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $successStatus = 200;
    private $userService, $userRepo;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->userRepo = new UserRepository();
    }


    /**
     * login api
     * Modifications done by @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author  Mayank Jariwala
     * [ User Few Information  are sent to client as response after successful login]
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required'
        ]);
        if ($validator->fails()) {
            return Helpers::getResponse(400, $validator->getMessageBag()->first());
        }
        if ($request->type === Constants::SOCIAL_LOGIN)
            return $this->socialLogin($request);
        else
            return $this->healthAppLogin($request);
    }

    //Social Login
    private function socialLogin($request)
    {
        $socialId = $request->social_id;
        $user = User::where('social_id', $socialId)->first(['id', 'first_name', 'last_name', 'email']);
        if ($user == null)
            return ['status' => false];
        // added device token for mobile notification       :: JEET DUMS - 20-04-2019
        $this->updateUserDeviceToken($request->device_token, $user->id);
        $token = $user->createToken('MyApp')->accessToken;
        return response()->json(
            [
                'status' => true,
                'userInfo' => $user,
                'token' => 'Bearer ' . $token
            ]);
    }

    // Normal Health App Login with username and password
    private function updateUserDeviceToken($deviceToken, $userId)
    {
        if (isset($deviceToken) && !empty($deviceToken)) {
            User::where("device_token", $deviceToken)->update([
                "device_token" => ""
            ]);
            User::whereId($userId)->update([
                "device_token" => $deviceToken
            ]);
        }
    }

    private function healthAppLogin($request)
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            // added device token for mobile notification       :: JEET DUMS - 20-04-2019
            $this->updateUserDeviceToken($request->device_token, $user->id);
            $token = $user->createToken('MyApp')->accessToken;
            return response()->json(
                [
                    'status' => true,
                    'userInfo' => $user->only(['id', 'first_name', 'last_name', 'email']),
                    'token' => 'Bearer ' . $token
                ]);
        } else {
            return response()->json(['status' => false], 401);
        }
    }

    /**
     * Register api
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'city' => 'required',
            'birthday' => 'required',
            'contact_no' => 'unique:users'
        ], $this->registerValidationMessage());
        if ($validator->fails())
            return Helpers::getResponse(401, "Validation Errors", $validator->getMessageBag()->first());
        try {
            $user = new User();
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->name = $request->has('name') ? $request->input('name') :
                $request->input('first_name') . " " . $request->input('last_name');
            $user->city = $request->input('city');
            $user->birthday = $request->input('birthday');
            // added device token for mobile notification       :: JEET DUMS - 20-04-2019
            if (isset($request->device_token) && !empty($request->device_token)) {
                $user->device_token = $request->device_token;
            }
            $user->password = bcrypt($request->input('password'));
            if ($request->get('type') == "social") {
                $user->social_id = $request->get('social_id');
                $user->platform = $request->get('platform');
            }
            if ($user->save()) {
                event(new SendRegistrationMail($user));
                $token = $user->createToken('MyApp')->accessToken;
                $user_id = $user->id;
                $data = [
                    'userId' => $user_id,
                    'token' => 'Bearer ' . $token
                ];
                return Helpers::getResponse(200, "Registered Successfully", $data);
            } else {
                return Helpers::getResponse(400, "Registered failed");
            }
        } catch (\Exception $e) {
            return Helpers::getResponse(500, "Exception Occurred", $e->getMessage());
        }
    }

    private function registerValidationMessage()
    {
        return [
            'type.required' => 'Social Type is require',
            'first_name.required' => 'First Name is require',
            'last_name.required' => 'Last Name is require',
            'email.required' => 'User Email is require',
            'password.required' => 'User Password is require',
            'city.required' => 'City is require',
            'birthday.required' => 'Birthday is require',
            'contact_no.unique' => 'Contact Number is already taken',
        ];
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */

    public function logout()
    {
        $user = auth::user();
        $user->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    // Mayank Jariwala

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }

    public function getUserDetails($userId)
    {
        try {
            $response = [];
            $user = $this->userRepo->getUser($userId);
            $response["about"] = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "birthday" => $user->birthday,
                "contact_no" => $user->contact_no,
                "city" => $user->city,
                "ethnicity" => $user->ethnicity,
                "gender" => $user->gender,
                "height" => $user->height,
                "weight" => $user->weight,
                "image" => $user->profile_image_data
            ];
            $response["bmi"] = [
                "value" => $user->BMI,
                "state" => $user->BMI_state,
                "score" => $user->BMI_score
            ];
            $response["score"] = $this->getUserScoreInfo($userId);
            return Helpers::getResponse(200, "User Information", $response);
        } catch (UserNotFoundException $e) {
            $e->setMessage("No user found with given id");
            return $e->sendUserNotFoundExceptionResponse();
        }
    }

    /**
     * @param $userId
     * @return array
     * @throws UserNotFoundException
     */
    private function getUserScoreInfo($userId)
    {
        $user = $this->userRepo->getUser($userId);
        $userTaskInfo = $user->taskInformation;
        return [
            "physical" => empty($userTaskInfo) ? 0 : $userTaskInfo->physical_score,
            "mental" => empty($userTaskInfo) ? 0 : $userTaskInfo->lifestyle_score,
            "lifestyle" => empty($userTaskInfo) ? 0 : $userTaskInfo->nutrition_score,
            "nutrition" => empty($userTaskInfo) ? 0 : $userTaskInfo->mental_score,
            "overall" => empty($userTaskInfo) ? 0 : $userTaskInfo->overall_score
        ];
    }

    public function getUserAchievements($userId)
    {
        try {
            $userObj = $this->userRepo->getUser($userId, ['id', 'first_name', 'last_name']);
            $userObj->getAchievements;
            return Helpers::getResponse(200, "User Achievements", $userObj);
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        }
    }

    /**
     * Updating User Profile ( No email and  password is accepted)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author  Mayank Jariwala
     */
    public function updateProfile(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "user_id" => "required",
            'contact_no' => 'unique:users'
        ], [
            'contact_no.unique' => 'Contact Number is already taken',
        ]);
        if ($validate->fails()) {
            return $this->sendResponseMessage(406, "Validation Error", $validate->getMessageBag()->all());
        }
        return $this->userService->updateProfile($request);
    }

    /**
     * Simply sends a json response
     *
     * @param $statusCode
     * @param $statusMessage
     * @param null $response
     * @return \Illuminate\Http\JsonResponse
     * @author Mayank Jariwala
     */
    private function sendResponseMessage($statusCode, $statusMessage, $response = null)
    {
        return response()->json([
            'statusCode' => $statusCode,
            'statusMessage' => $statusMessage,
            'response' => $response
        ]);
    }

    /**
     * Updating User BMI  Information
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author  Mayank Jariwala
     */
    public function updateBMI(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "user_id" => "required",
            "height" => "required",
            "weight" => "required"
        ]);
        if ($validate->fails()) {
            return $this->sendResponseMessage(406, "Validation Error", $validate->getMessageBag()->all());
        }
        return $this->userService->updateBMI($request);
    }

    /**
     * Update Profile Picture of User
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhoto(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "user_id" => "required",
            "file_ext" => "required",
            "image_data" => "required"
        ]);
        if ($validate->fails()) {
            return $this->sendResponseMessage(406, "Validation Error", $validate->getMessageBag()->all());
        }
        return $this->userService->updatePhoto($request);
    }

    /**
     * Get User Profile Image Data
     * NOTE: Using Lazy Loading Techniques
     *
     *
     * @param $userId : Id of User
     * @return \Illuminate\Http\JsonResponse
     * @author Mayank Jariwala
     */
    public function getUserProfileImage($userId)
    {
        $user = User::getUserProfileImageInformation($userId);
        if (is_null($user))
            return response()->make("")->header('Content-Type', $this->mapImageFromExt($user->profile_image_filename));
        return response()->make($user->profile_image_data)->header('Content-Type', $this->mapImageFromExt($user->profile_image_filename));
    }

    private function mapImageFromExt($fileName)
    {
        $fileArr = explode(".", $fileName);
        $ext = end($fileArr);
        switch ($ext) {
            case "jpg":
                return "image/jpeg";
            case "png":
                return "image/png";
            case "gif":
                return "image/gif";
            case "jpeg":
                return "image/jpg";
            default:
                return "image/jpg";
        }
    }

    /**
     *  Change Password Controller Function
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author   Mayank Jariwala
     */
    public function changePassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'user_id' => 'required',
            'current_password' => 'required',
            'new_password' => 'required',
            'confirm_new_password' => 'required|same:new_password',
        ]);
        // Validation fails send error
        if ($validate->fails()) {
            return response()->json([
                'statusCode' => 400,
                'statusMessage' => "Validation Fails",
                // Send all error messages found
                'response' => $validate->getMessageBag()->all()
            ]);
        }
        return $this->userService->changeUserPassword($request);
    }

    public function forgotPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required'
        ]);
        if ($validation->fails())
            return Helpers::getResponse(400, "Validation Error", $validation->getMessageBag()->all());
        return $this->userService->updatePassword($request->email);
    }
}
