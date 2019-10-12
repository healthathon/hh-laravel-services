<?php

## TODO: After login, for each request user will pass the token

Route::post('saveGender', 'UserController@saveGender')->middleware('auth:api');
Route::post('saveBMI', 'UserController@saveBMI')->middleware('auth:api');
Route::post('logout', 'UserController@logout')->middleware('auth:api');
Route::post('query_category', 'QueryController@getQueryCategory');
Route::post('query', 'QueryController@getQuery');
Route::post('saveQueryResult', 'QueryController@saveQueryResult');

Route::group(['namespace' => 'API'], function () {

    Route::post('auth/register', 'UserController@register');
    Route::post('auth/login', 'UserController@login');
    Route::post('user/change-password', 'UserController@changePassword')->middleware('auth:api');
    Route::put('user/update-profile', 'UserController@updateProfile')->middleware('auth:api');
    Route::put('user/update-photo', 'UserController@updatePhoto')->middleware('auth:api');
    Route::put('user/update-bmi', 'UserController@updateBMI')->middleware('auth:api');

    //Forgot Password
    Route::patch('user/forgot-password', 'UserController@forgotPassword');

    // Achievements Api
    Route::get('user/{userId}/achievements', 'UserController@getUserAchievements');
    Route::get('task/{taskBankId}/week/{weekNo}/image', 'TaskController@displayWeeklyBadgeImage');
    Route::get('task/{taskBankId}/week/{weekNo}/day/{day}/image', 'TaskControllerV2@displayDailyBadgeImage');

    // SHA API
    Route::group(['prefix' => "sha"], function () {
        Route::get("questions", 'ShortHealthAssessmentController@getQuestions');
        Route::put('user/{id}/about', 'ShortHealthAssessmentController@putAboutUserShortHealthHistory')->middleware('auth:api');
        Route::get('user/{id}/about', 'ShortHealthAssessmentController@getAboutUserShortHealthHistory');
    });

    // Blogs  Api
    Route::group(['prefix' => "blog"], function () {
        Route::get("categories", "BlogController@getBlogCategories");
        Route::get('{categoryName}/get', 'BlogController@getAllBlog');
    });

    Route::post('save_health_data', 'HealthAppKitController@saveUserHealthData');

    // name : Thyrocare profile/Test/offer [ Data from Storage/thyrocare/files]
    Route::get('get-thyrocare/{name}', [
        'uses' => 'DiagnosticLabController@getThyrocareInformation',
        'as' => 'thyrocare_information'
    ]);
    // Thyrocare Tests DATA : NEW TEST DATA [REF: thyrocare_tests table]
    Route::group(['prefix' => 'labs'], function () {
        Route::get('{labName}/tests/', 'DiagnosticLabController@getLabTests');
        Route::get('thyrocare/test/{testId}/info', 'DiagnosticLabController@getTestDetailInformation');
    });

    Route::post('book-order', 'DiagnosticLabController@bookLabOrder');
    Route::get('get-appointment-slots/{date}/{pincode}', [
        'uses' => 'DiagnosticLabController@getAppointmentSlots',
        'as' => 'appointment_slots'
    ]);
    Route::get('user/{id}/thyrocare/order', 'DiagnosticLabController@getUserThyrocareOrders');
    Route::get('thyrocare/order/{orderNo}/ben-details', 'DiagnosticLabController@getThyrocareOrderBenDetails');

    Route::get('get-pincode-availability/{pincode}', [
        'uses' => 'DiagnosticLabController@getPincodeAvailability',
        'as' => 'pincode_availability'
    ]);
    Route::get('user/{userId}/task-count', 'TaskControllerV2@getUserTasksCount');
    Route::get('get_all_task/{userId}/{category}', 'TaskController@getAllTasks');

    // Recommendation and Popular
//    Route::get('user/{userId}/task/{category}/recommend', 'TaskController@getRecommendedTask');
    Route::get('user/{userId}/task/{category}/recommend', 'TaskControllerV2@getRecommendedTask');
    Route::get('user/{userId}/task/{category}/popular', 'TaskControllerV2@getPopularTasks');

    // Non-authenticated get routes [ Should be Authenticated Routes  - Still Pending]

    ## For Rendering Image API
    Route::get('user/{userId}/get-profile-image', 'UserController@getUserProfileImage');
    Route::get('regimen/{id}/image', 'TaskController@displayRegimenBadgeImage');


    Route::get('{userId}/get-feeds', 'FeedsController@getFriendsFeeds');
    Route::get('{userId}/get-details', 'UserController@getUserDetails');
    Route::get('{userId}/get_task/', 'TaskController@getTask');
    Route::get('assess/{userId}/reset/', 'AssessController@resetUserAssessResults');
    Route::get('assess/{userId}/get-results', 'AssessController@getUserAssessResults');
    Route::get('assess/resume/{userId}/get-questions', 'AssessController@getAssessmentQuestionsForUser');
    Route::get('top-leaders', 'LeaderBoardController@getTop20Users');
    Route::get('user/{userId}/test/recommended', 'DiagnosticLabController@getRecommendedTestForUser');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::group(['prefix' => 'task'], function () {
            Route::post('register', 'TaskControllerV2@subscribeTask');
            Route::put('complete', 'TaskControllerV2@dailyTaskCompleted');
            Route::post('unregister', 'TaskControllerV2@unsubscribeTask');
        });

    });

    // POST routes should be authenticated [ For iOS developer this rule is broken]
    Route::post('save/assessment', 'AssessController@recordUserTagQuestionsAnswers');
});

