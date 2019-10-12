<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Helpers;
use App\Http\Controllers\API\AssessController;
use App\Model\Assess\queryTag;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\Assess\assesHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public $successStatus = 200;

    /**
     * login api
     * Modifications done by @author  Mayank Jariwala
     * [ User Few Information  are sent to client as response after successful login]
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();

            // added device token for mobile notification       :: JEET DUMS - 20-04-2019
            if (isset($request->device_token) && !empty($request->device_token)) {
                User::where("device_token", $request->device_token)->update([
                    "device_token" => ""
                ]);
                User::whereId($user->id)->update([
                    "device_token" => $request->device_token
                ]);
            }

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
            'first_name' => 'required',
            'last_name' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'city' => 'required',
            'birthday' => 'required'
        ]);
        if ($validator->fails())
            return Helpers::getResponse(401, "Validation Errors", $validator->errors());
        try {
            $user = new User;
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->name = $request->input('name');
            $user->city = $request->input('city');
            $user->birthday = $request->input('birthday');
            // added device token for mobile notification       :: JEET DUMS - 20-04-2019
            if (isset($request->device_token) && !empty($request->device_token)) {
                $user->device_token = $request->device_token;
            }
            $user->password = bcrypt($request->input('password'));
            if ($user->save()) {
                MailController::sendRegistrationMail($user);
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
            return Helpers::getResponse(500, "Exception Occurred", $e->getTraceAsString());
        }
    }

    /**
     * Save Gender
     *
     * @author  Mayank Jariwala
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveGender(Request $request)
    {
        $user = auth::user();
        $gender = $request->input('gender');
        $user->gender = $gender;
        if ($user->save()) {
            return response()->json(['status' => "true"]);
        }
        return response()->json(['status' => "false"]);
    }

    /**
     * details api
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveBMI(Request $request)
    {
        $user = auth::user();
        $height = $request->input('height');
        $weight = $request->input('weight');
        $user = $this->processBMI($height, $weight, $user);
        if ($user->save()) {
            return response()->json([
                'statusCode' => 200,
                'statusMessage' => 'User Information save successfully'
            ]);
        } else {
            return response()->json([
                'statusCode' => 500,
                'statusMessage' => "Something went wrong"
            ]);
        }
    }

    public function logout()
    {
        $user = auth::user();
        $user->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }
}