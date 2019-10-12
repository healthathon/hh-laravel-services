<?php

/**
 * This Model Class represents User Model
 * @author  Mayank Jariwala <menickwa@gmail.com>
 * @package  $nameSpace
 * @version  v.1.1
 */

namespace App\Model;

use App\Exceptions\NoAssessmentFoundException;
use App\Exceptions\UserNotFoundException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 *
 * This user model represents application user information
 * @package App\Model
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * @var array Fillable Values into table
     */
    protected $fillable = [
        'first_name', 'last_name', 'name', 'email', 'password', 'city', 'birthday', 'gender',
        'profile_image_filename', 'profile_image_data', 'height', 'weight', 'BMI', 'BMI_state',
        'BMI_score', 'ethnicity', 'overall_score', 'social_id', 'platform', 'contact_no'
    ];

    /**
     * @var array TypeCasting of Few Parameters
     */
    protected $casts = [
        'overall_score' => 'integer'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
        'created_at', 'updated_at',
        'profile_image_filename', 'profile_image_data'
    ];

    /**
     * This function is responsible for fetching entire user_object
     * @param $id : User ID
     * @param array $requiredFields
     * @return mixed: UserObject
     * @throws UserNotFoundException
     */
    public static function getUser($id, $requiredFields = [])
    {
        $user = empty($requiredFields) ? User::where('id', $id)->first() : User::where('id', $id)->first($requiredFields);
        if ($user == null) {
            throw new UserNotFoundException();
        } else {
            return $user;
        }
    }

    /**
     * This function returns user profile image data
     *
     * @param $id : User ID
     * @return mixed
     */
    public static function getUserProfileImageInformation($id)
    {
        return User::where('id', $id)->first(['profile_image_filename', 'profile_image_data']);
    }

    public function taskInformation()
    {
        return $this->hasOne('App\Model\UsersTaskInformations', 'user_id', 'id');
    }

    public function recommendedTest()
    {
        return $this->hasMany('App\Model\UsersTestsRecommendation', 'user_id', 'id');
    }

    public function doingTask()
    {
        return $this->hasMany('App\Model\UserTask', 'user_id', 'id');
    }

    public function recommendedTask()
    {
        return $this->hasMany('App\Model\UserTaskRecommendation', 'user_id', 'id');
    }

    /**
     * @throws NoAssessmentFoundException
     */
    public function hasAssessmentRecord()
    {
        if ($this->assessmentRecord == null)
            throw new NoAssessmentFoundException();
        return $this->assessmentRecord;
    }

    public function assessmentRecord()
    {
        return $this->hasOne('App\Model\Assess\assesHistory', 'user_id', 'id');
    }

    /**
     * This function fetch all tests orders of user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getThyrocareTestOrders()
    {
        return $this->hasMany('App\Model\ThyrocareUserData', 'user_id', 'id');
    }

    /**
     * This relationship fetch all achievements of User
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getAchievements()
    {
        return $this->hasMany('App\Model\UserAchievements', 'user_id', 'id');
    }

    /**
     *  This function will get user health history
     *  About You Section in iOS App -  During Registration Phase this step comes
     */
    public function getUserHealthHistory()
    {
        return $this->hasMany('App\Model\UserHealthHistory', 'user_id', 'id');
    }

    /**
     * This function fetches all friends of  User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany Arrays of Friends
     */
    public function getFriends()
    {
        return $this->hasMany('App\Model\UserFriends', 'user_id', 'id');
    }

    /**
     * This function holds information of user about you section which is ask while registration
     * time by application name as "About You"
     *
     * @author  Mayank Jariwala
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getShortHealthData()
    {
        return $this->hasMany('App\Model\UserHealthHistory', 'user_id', 'id');
    }

    public function regimenScore()
    {
        return $this->hasOne('App\Model\UserRegimenScore', 'user_id', 'id');
    }

    public function getFullName()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function mmgTests()
    {
        return $this->hasMany("App\Model\MMGBookingDetails", "user_id", "id");
    }

    public function restriction()
    {
        return $this->hasOne("App\Model\SHABasedUserLevelRestriction", "user_id", "id");
    }

    public function long_assess_restriction()
    {
        return $this->hasOne("App\Model\LongAssessUserLevelRestriction", "user_id", "id");
    }

    public function physicalLevelTaskTracker()
    {
        return $this->hasMany("App\Model\UserPhysicalTaskTrackingLevelWise", "user_id", "id");
    }
}
