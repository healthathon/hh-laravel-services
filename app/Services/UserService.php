<?php

namespace App\Services;


use App\Constants;
use App\Events\SendForgotPasswordMail;
use App\Exceptions\UserNotFoundException;
use App\Helpers;
use App\Http\Controllers\UserController;
use App\Model\Assess\assesHistory;
use App\Model\Assess\AssessmentQuestionsTagOrder;
use App\Model\Assess\queryTag;
use App\Model\BmiScore;
use App\Model\SHAAnswerBasedLevelRestriction;
use App\Model\SHAQuestionAnswers;
use App\Model\SHATaskRecommendation;
use App\Model\SHATestRecommendation;
use App\Model\ShortHealthAssessment;
use App\Model\User;
use App\Model\UserHealthHistory;
use App\Model\UsersTestsRecommendation;
use App\Model\UserTaskRecommendation;
use App\Respositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{

    private $cloudinaryService, $userRepo, $assessmentService;

    public function __construct()
    {
        $this->cloudinaryService = new CloudinaryService();
        $this->userRepo = new UserRepository();
        $this->assessmentService = new AssessmentService();
    }

    /**
     * Change User Password Service
     *
     * @author  Mayank Jariwala
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeUserPassword($request)
    {
        $user = User::where('id', '=', $request->get('user_id'))->first();
        if (!is_null($user)) {
            $current_password = $request->get('current_password');
            if (Hash::check($current_password, $user->password)) {
                try {
                    if ($user->update(['password' => bcrypt($request->get('new_password'))])) {
                        return $this->sendResponseMessage(200, "Password Changed Successfully");
                    }
                } catch (\Exception $e) {
                    Log::error(" Something went wrong while updating password" . $e->getMessage());
                    return $this->sendResponseMessage(500, "Something went wrong. Please try again");
                }
            }
            return $this->sendResponseMessage(406, "User current password not match");
        }
        return $this->sendResponseMessage(404, "No User Found with such id");
    }

    /**
     * Simply sends a json response
     *
     * @author Mayank Jariwala
     * @param $statusCode
     * @param $statusMessage
     * @param null $response
     * @return \Illuminate\Http\JsonResponse
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
     * Update Profile Service
     *
     * @author  Mayank Jariwala
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile($request)
    {
        $user = User::where('id', $request->get('user_id'))->first();
        if (!is_null($user)) {
            try {
                if ($user->update($request->all())) {
                    return $this->sendResponseMessage(200,
                        "User Profile Updated Successfully",
                        $user->only(['id', 'first_name', 'last_name', 'name', 'city', 'birthday', 'gender', 'ethnicity']));
                }
            } catch (\Exception $e) {
                Log::error(" Something went wrong while updating profile" . $e->getMessage());
                return $this->sendResponseMessage(500, "Something went wrong. Please try again");
            }
        }
        return $this->sendResponseMessage(404, "No User Found with such id");
    }

    /**
     *  Uploading User Profile Image Service
     *
     * @author  Mayank Jariwala
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePhoto(\Illuminate\Http\Request $request)
    {
        try {
            $user = $this->userRepo->getUser($request->get("user_id"));
            if (!is_null($request->get("image_data"))) {
                try {
                    $user->profile_image_filename = $user->name . "." . $request->get('file_ext');
                    $imageData = $request->get('image_data');
                    $this->cloudinaryService->upload("data:image/png;base64," . $imageData, $user->name, [
                        "folder" => "userimage"
                    ]);
                    $url = $this->cloudinaryService->getUrl();
                    $user->profile_image_data = $url;
                    if ($user->save()) {
                        return $this->sendResponseMessage(200, "User Profile Updated");
                    }
                } catch (\Exception $e) {
                    Log::error(" Something went wrong while updating Image profile" . $e->getMessage());
                    return $this->sendResponseMessage(500, "Something went wrong. Please try again", $e->getMessage());
                }
            }
            return $this->sendResponseMessage(404, "Received empty image file");
        } catch (UserNotFoundException $e) {
            return $e->sendUserNotFoundExceptionResponse();
        }
    }

    /**
     *  Updating  User BMI Information
     *
     * @author  Mayank Jariwala
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBMI(\Illuminate\Http\Request $request)
    {
        try {
            $user = User::getUser($request->get('user_id'));
            $user = $this->processBMI($request->get('height'), $request->get('weight'), $user);
            if ($user->save()) {
                return Helpers::getResponse(200, "User Information save successfully", $user->only(['height', 'weight', 'BMI', 'BMI_state', 'BMI_score']));
            } else {
                return Helpers::getResponse(500, "Something went wrong");
            }
        } catch (UserNotFoundException $e) {
            $e->setMessage(Constants::NO_USER_FOUND);
            return $e->sendUserNotFoundExceptionResponse();
        }
    }

    //Mayank Jariwala ( Separated from saveBMI function so other independent function can call it)

    /**
     * @param $height
     * @param $weight
     * @param $user
     * @return mixed
     * @throws \Exception
     */
    public function processBMI($height, $weight, $user)
    {
        $dev = 0;
        $bmi_score = 0;
        $bmi_state = '';
        $bmi = $weight / $height / $height;
        $low_bmi = 18.5;
        $high_bmi = 24.9;

        if ($bmi >= 18.5 && $bmi <= 24.9)
            $bmi_state = 'Normal';
        if ($bmi < 18.5)
            $bmi_state = 'UnderWeight';
        if ($bmi > 25 && $bmi < 29.9)
            $bmi_state = 'OverWeight';
        if ($bmi > 30)
            $bmi_state = 'Obese';


        if ($bmi < $low_bmi) {
            $dev = abs($bmi - $low_bmi) / $low_bmi * 100;
        }
        if ($bmi > $high_bmi)
            $dev = abs($bmi - $high_bmi) / $high_bmi * 100;

        $bmiDeviationBaseScore = BmiScore::all();
        $recommendTestIds = [];
        foreach ($bmiDeviationBaseScore as $value) {
            $deviationRange = str_replace("val", $dev, $value->deviation_range);
            if (eval("return $deviationRange;")) {
                $bmi_score = $value->score;
                $recommendTestIds = $value->recommend_test == null ? [] : array_column($value->recommend_test->toArray(), "test_id");
                break;
            }
        }
        $user->height = $height;
        $user->weight = $weight;
        $user->BMI = $bmi;
        $user->BMI_state = $bmi_state;
        $user->BMI_score = $bmi_score;
        $bmiObject = queryTag::where('tag_name', strtoupper(Constants::BMI))->first([
            'id', 'excellent_marks', 'good_marks', 'bad_marks'
        ]);
        $tagStateColumn = "tag" . $bmiObject->id . "_state";
        $tagScoreColumn = "tag" . $bmiObject->id . "_score";
        if ($bmi_score >= $bmiObject->excellent_marks) {
            $tagState = Constants::EXCELLENT;
        } else if ($bmi_score < $bmiObject->excellent_marks && $bmi_score >= $bmiObject->good_marks) {
            $tagState = Constants::GOOD;
        } else {
            $tagState = Constants::BAD;
        }
        try {
            DB::beginTransaction();
            if ($user->assessmentRecord == null) {
                $userAssessmentRecord = new assesHistory();
                $userAssessmentRecord->user_id = $user->id;
                $userAssessmentRecord->tags_completed = [];
                $userAssessmentRecord->$tagScoreColumn = $bmi_score;
                $userAssessmentRecord->$tagStateColumn = $tagState;
                $userAssessmentRecord->save();
            } else {
                $user->assessmentRecord->$tagScoreColumn = $bmi_score;
                $user->assessmentRecord->$tagStateColumn = $tagState;
                $user->assessmentRecord->save();
            }
            foreach ($recommendTestIds as $recommendedTestId) {
                $user->recommendedTest()->updateOrCreate([
                    'user_id' => $user->id,
                    'test_id' => $recommendedTestId
                ], [
                    'user_id' => $user->id,
                    'test_id' => $recommendedTestId
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return $user;
    }

    /**
     *  Generate a random Password and send password to user registered email address
     *
     * @author  Mayank Jariwala
     * @param $email
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword($email)
    {
        $userObj = User::where('email', $email)->first(['id', 'email', 'password']);
        if ($userObj == null)
            return Helpers::getResponse(404, "Email address not found");
        $newPassword = $this->generateRandomPassword();
        $userObj->password = bcrypt($newPassword);
        try {
            if ($userObj->save()) {
                event(new SendForgotPasswordMail($userObj, $newPassword));
                return Helpers::getResponse(200, "Password sent to your registered email address");
            }
        } catch (\Exception $e) {
            Log::error("Exception Occurred " . $e);
        }
        return Helpers::getResponse(500, "Server Side issues");
    }

    // Reference Code: https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
    private function generateRandomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * This function execute saving or updating user about short history data
     * @param $id
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function putAboutUserShortHealthData($id, $request)
    {
        try {
            $user = User::getUser($id);
            $collectUserAnswer = [];
            $answerObject = $request["answers"];
            $objectToSave = [];
            $recommendedTaskIds = [];
            $recommendedTestIds = [];
            $calculatedScore = 0;
            foreach ($answerObject as $key => $answers) {
                $shaObject = ShortHealthAssessment::where('id', $answers["questionId"])->first();
                $calculatedScore += $this->isSHAQuestionMultipleAndScoreable($shaObject, $answers["answers"]);
                foreach ($answers["answers"] as $answer) {
                    if ($this->isSHAQuestionNotMultipleAndScoreable($shaObject))
                        $calculatedScore += $this->getShaAnswerScore($shaObject, $answers["questionId"], $answer);
                    if ($shaObject->is_scoreable) {
                        array_push($collectUserAnswer, $answer);
                        $taskIds = SHATaskRecommendation::where('answer_id', $answer)->pluck("task_id");
                        $testIds = SHATestRecommendation::where('answer_id', $answer)->pluck("test_id");
                        $recommendedTaskIds = array_merge($recommendedTaskIds, $taskIds->toArray());
                        $recommendedTestIds = array_merge($recommendedTestIds, $testIds->toArray());
                    }
                    $objectToSave[] = [
                        'user_id' => $id,
                        'question_id' => $answers["questionId"],
                        'answer_id' => $answer
                    ];
                }
            }
            $recommendedTaskIds = array_unique($recommendedTaskIds);
            $recommendedTestIds = array_unique($recommendedTestIds);
            DB::beginTransaction();
            DB::transaction(function () use ($user, $objectToSave, $calculatedScore, $recommendedTaskIds, $recommendedTestIds, $collectUserAnswer) {
                // Reset User Restriction Level
                $user->restriction()->delete();
                if ($user->assessmentRecord == null) {
                    $assessmentRecord = new assesHistory();
                    $tagId = queryTag::where("tag_name", ucfirst("history"))->first()->id;
                    $tagColumn = "tag" . $tagId . "_score";
                    $tagStateColumn = "tag" . $tagId . "_state";
                    $assessmentRecord->user_id = $user->id;
                    $assessmentRecord->tags_completed = [$tagId];
                    $assessmentRecord->order_seq_id = AssessmentQuestionsTagOrder::where("is_active", 1)->first()->id;
                    $assessmentRecord->$tagColumn = $calculatedScore;
                    $assessmentRecord->$tagStateColumn = $this->assessmentService->getTagState($tagId, $calculatedScore);
                } else {
                    $tagId = queryTag::where("tag_name", ucfirst("history"))->first()->id;
                    $tagColumn = "tag" . $tagId . "_score";
                    $tagStateColumn = "tag" . $tagId . "_state";
                    $assessmentRecord = $user->assessmentRecord;
                    $assessmentRecord->user_id = $user->id;
                    $completedTags = $assessmentRecord->tags_completed;
                    if (!in_array($tagId, $completedTags))
                        array_push($completedTags, $tagId);
                    $assessmentRecord->tags_completed = $completedTags;
                    $assessmentRecord->order_seq_id = AssessmentQuestionsTagOrder::where("is_active", 1)->first()->id;
                    $assessmentRecord->$tagColumn = $calculatedScore;
                    $assessmentRecord->$tagStateColumn = $this->assessmentService->getTagState($tagId, $calculatedScore);
                }
                $assessmentRecord->save();
                $user->getShortHealthData()->delete();
                UserHealthHistory::insert($objectToSave);
                //Insert Task Recommendation
                $recommendedTaskIds = array_unique($recommendedTaskIds);
                $recommendedTestIds = array_unique($recommendedTestIds);
                foreach ($recommendedTaskIds as $key => $recommendedTaskId) {
                    $user->recommendedTask()->updateOrCreate([
                        'user_id' => $user->id,
                        'regimen_id' => $recommendedTaskId
                    ], [
                        'user_id' => $user->id,
                        'regimen_id' => $recommendedTaskId
                    ]);
                }
                //Insert Test Recommendation
                foreach ($recommendedTestIds as $recommendedTestId) {
                    $user->recommendedTest()->updateOrCreate([
                        'user_id' => $user->id,
                        'test_id' => $recommendedTestId
                    ], [
                        'user_id' => $user->id,
                        'test_id' => $recommendedTestId
                    ]);
                }
                // Update Restriction Level
                $restrictionLevelForUser = $this->getRestrictionLevel($collectUserAnswer);
                if ($restrictionLevelForUser !== -1) {
                    $user->restriction()->updateOrCreate([
                        "user_id" => $user->id
                    ], [
                        "restriction_level" => $restrictionLevelForUser
                    ]);
                }
            });
            DB::commit();
            return Helpers::getResponse(200, "Data Saved");
        } catch (UserNotFoundException $e) {
            $e->setMessage("User not found");
            return Helpers::getResponse(500, "User not found", $e->getMessage());
        } catch
        (\Exception $e) {
            DB::rollBack();
            return Helpers::getResponse(500, "Server Error", $e->getMessage());
        }
    }

    private function isSHAQuestionMultipleAndScoreable($shaObject, $answers)
    {
        $score = 0;
        if ($shaObject->is_scoreable && $shaObject->multiple) {
            $totalAnswers = $shaObject->answers()->count();
            $givenAnswers = count($answers);
            $score = $totalAnswers - $givenAnswers;
        }
        return $score;
    }

    private function isSHAQuestionNotMultipleAndScoreable($shaObject)
    {
        return $shaObject->is_scoreable && !$shaObject->multiple;
    }

    private function getShaAnswerScore($shaObject, $questionId, $answerId)
    {
        $shaAnswersObj = $shaObject->answers()->where("question_id", $questionId)
            ->where("id", $answerId)
            ->first();
        return $shaAnswersObj->score;
    }

    private function getRestrictionLevel($collectionOfAnsweredByUser)
    {
        $restrictedLevels = SHAAnswerBasedLevelRestriction::whereIn("sha_answer_id", $collectionOfAnsweredByUser)->pluck("restriction_level");
        if (count($restrictedLevels) > 0) {
            $minLevel = min($restrictedLevels->toArray());
            return $minLevel;
        }
        return -1;
    }
}