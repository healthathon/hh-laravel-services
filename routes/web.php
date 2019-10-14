<?php

//---------------  Test Routes -----------------

Route::get('/reg', function () {
});

//---------------  Test Routes -----------------

Route::get('/', function () {
    return redirect()->route("admin.loginForm");
});

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.'], function () {

    // Auth Routes
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name("loginForm");
    Route::post('/login', 'Auth\LoginController@login')->name('login.submit');
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

    // Home Route
    Route::get('home', [
        'uses' => 'HomeController@viewDashboardHomePage',
        'as' => 'home'
    ]);

    // BMI Routes
    Route::group(['prefix' => 'bmi'], function () {
        // BMI Score
        Route::get('/', [
            'uses' => 'BMIController@showBMIScoresPage',
            'as' => 'bmi-get-page'
        ]);
        Route::get('get/scores/', [
            'uses' => 'BMIController@getBMIScores',
            'as' => 'bmi-get-score'
        ]);
        Route::patch('update/score', [
            'uses' => 'BMIController@updateBMIScore',
            'as' => 'bmi-update-score'
        ]);

        Route::group(['prefix' => 'test/recommendation', 'as' => 'bmi.'], function () {
            Route::get('page', [
                'uses' => 'BMIController@testRecommendationPage',
                'as' => 'test.recommend.page'
            ]);
            Route::get('get', [
                'uses' => 'BMIController@fetchRecommendedTests',
                'as' => 'test.recommend.get'
            ]);
            Route::get('{bmiId}/page', [
                'uses' => 'BMIController@updateTestRecommendationPage',
                'as' => 'test.recommend.modify.page'
            ]);
            Route::post('bmi/recommend/test', [
                'uses' => 'BMIController@updateTestRecommendations',
                'as' => 'test.recommend.modify'
            ]);
        });

    });

    //Nutrition Score Bank Routes
    Route::group(["prefix" => "ntr-bank", 'as' => 'ntr_bank.'], function () {
        Route::get("/all", "NutritionScoreBankController@getAll")->name("all");
        Route::get("/page", "NutritionScoreBankController@getPage")->name("page");
        Route::get("/{id}/task/recommend", "NutritionScoreBankController@getRecommendTask")->name("task.recommend");
        Route::patch("{id}/update", "NutritionScoreBankController@updateScoreBank")->name("update");
        Route::post("/recommend", "NutritionScoreBankController@insertRecommendRegimen")->name("insert.recommend");
    });

    //Mental Bnk Recommendation Routes
    Route::group(["prefix" => "mental/recommend", 'as' => 'mntl_bank.'], function () {
        Route::get("/all", "MentalBankRecommendation@getAll")->name("all");
        Route::get("/page", "MentalBankRecommendation@getPage")->name("page");
    });


    // SHA Routes
    Route::group(['prefix' => 'sha', 'as' => 'sha.'], function () {

        Route::group(["prefix" => "restriction"], function () {
            Route::get("/level/page", "ShortHealthAssessmentController@restrictionLevelPage")->name("level.restriction.page");
            Route::get("/level/info", "ShortHealthAssessmentController@restrictionLevelData")->name("level.restriction.info");
            Route::put("/level/answer/{id}/update", "ShortHealthAssessmentController@updateRestrictionLevelData")->name("level.restriction.update");
            Route::delete("/level/answer/{id}/delete", "ShortHealthAssessmentController@deleteRestrictionLevelData")->name("level.restriction.delete");
        });

        Route::get('/', [
            'uses' => 'ShortHealthAssessmentController@showPage',
            'as' => 'page'
        ]);
        Route::get('/questions', [
            'uses' => 'ShortHealthAssessmentController@getQuestions',
            'as' => 'questions'
        ]);
        Route::put('/{id}/update', [
            'uses' => 'ShortHealthAssessmentController@updateQuestionObj',
            'as' => 'update'
        ]);
        Route::post('/insert', [
            'uses' => 'ShortHealthAssessmentController@insertQuestion',
            'as' => 'insert'
        ]);
        Route::delete('/{id}/delete', [
            'uses' => 'ShortHealthAssessmentController@deleteQuestion',
            'as' => 'delete'
        ]);

        // Task Recommendation
        Route::get("recommend/page/questions/get", "ShortHealthAssessmentController@getQuestionForTaskRecommendationPage")
            ->name("task.recommend.questions");
        Route::get("question/{questionId}/task/recommend/get", "ShortHealthAssessmentController@taskRecommendAnswerPage")
            ->name("task.recommend.info.get");
        Route::get("question/{questionId}/answer/{answerId}/task/recommend/get", "ShortHealthAssessmentController@fetchTaskRecommendAnswerInfo")
            ->name("task.recommend.info.select.fetch");
        Route::post("task/recommend/update", "ShortHealthAssessmentController@updateRecommendedTask")
            ->name("task.recommend.info.update");
        Route::get("task/recommend/page", "ShortHealthAssessmentController@taskRecommendPage")
            ->name("task.recommend.page");

        // Test Recommendation
        Route::get("test/recommend/page", "ShortHealthAssessmentController@testRecommendPage")
            ->name("test.recommend.page");
        Route::get("question/{questionId}/answer/{answerId}/test/recommend/get", "ShortHealthAssessmentController@fetchTestRecommendAnswerInfo")
            ->name("test.recommend.info.select.fetch");
        Route::get("recommend/page/questions", "ShortHealthAssessmentController@getQuestionForTestRecommendationPage")
            ->name("test.recommend.question");
        Route::post("test/recommend/update", "ShortHealthAssessmentController@updateRecommendedTest")
            ->name("test.recommend.info.update");
        Route::get("question/{questionId}/test/recommend/get", "ShortHealthAssessmentController@testRecommendAnswerPage")
            ->name("test.recommend.info.get");
    });

    // Blog  Routes
    Route::group(['prefix' => 'blog', 'as' => 'blog.'], function () {
        Route::get('/all', [
            'uses' => 'BlogController@getBlogs',
            'as' => 'fetch'
        ]);
        Route::get('{id}/info', [
            'uses' => 'BlogController@getBlogInfoById',
            'as' => 'detail.page'
        ]);
        Route::get('{action}/{id?}', [
            'uses' => 'BlogController@renderAddOrEditBlogPage',
            'as' => 'add_edit'
        ]);
        Route::post('add', [
            'uses' => 'BlogController@postBlog',
            'as' => 'save'
        ]);;
        Route::put('{id}/update', [
            'uses' => 'BlogController@updateBlog',
            'as' => 'update'
        ]);
        Route::delete('{id}/delete/', [
            'uses' => 'BlogController@deleteBlogById',
            'as' => 'delete'
        ]);
    });

    //Labs Routes
    Route::group(['prefix' => 'labs'], function () {
        Route::get('show/test/{id?}', [
            'uses' => 'DiagnosticLabController@showThyrocareTestsView',
            'as' => 'show_tests'
        ]);
        Route::put('/test/{id}/update', 'DiagnosticLabController@updateTestInfo');
        Route::delete('/test/{id}/delete', [
            'uses' => 'DiagnosticLabController@deleteTest',
            'as' => 'test.delete'
        ]);
        Route::get('/tests', 'DiagnosticLabController@getThyrocareTests');
        Route::get('/test/{id}/info', 'DiagnosticLabController@getSpecificTestInfo');
        Route::get('/mail/receivers', 'DiagnosticLabController@getMMGMailReceiversPage')->name("mmg.mail.receivers");
        Route::get('/mail/receivers/info', 'DiagnosticLabController@fetchMMGMailReceiverMembers')->name("mmg.mail.receivers.info.get");
        Route::post('/mail/receivers/info', 'DiagnosticLabController@storeMMGMailReceiverMembers')->name("mmg.mail.receivers.info.save");
        Route::delete('/mail/receiver/{id}/delete', 'DiagnosticLabController@deleteMMGMailReceiverMembers')->name("mmg.mail.receivers.info.delete");
        Route::put('/mail/receiver/{id}/update', 'DiagnosticLabController@updateMMGMailReceiverMembers')
            ->name("mmg.mail.receivers.info.update");
    });

    // Task Routes
    Route::group(['prefix' => 'regimen', 'as' => 'regimen.'], function () {
        Route::get('{category}/page', [
            'uses' => 'TaskControllerV2@regimenPage',
            'as' => 'page'
        ]);
        Route::get('{category}/get', [
            'uses' => 'TaskControllerV2@getRegimenInfo',
            'as' => 'info'
        ]);
        Route::get('{regimenId}/week-details/page', [
            'uses' => 'TaskControllerV2@getRegimenWeekDetailsPage',
            'as' => 'week_details_page'
        ]);
        Route::get('{regimenCode}/week-details/get', [
            'uses' => 'TaskControllerV2@getRegimenWeekDetailsInfo',
            'as' => 'week_details_info'
        ]);
        Route::get('{regimenCode}/week/{weekNo}/edit/page', [
            'uses' => 'TaskControllerV2@getRegimenWeekInfoPage',
            'as' => 'week_edit_page'
        ]);
        Route::get('{regimenCode}/week/{weekNo}/get', [
            'uses' => 'TaskControllerV2@getRegimenWeeklyTaskInfo',
            'as' => 'week.info.get'
        ]);
        Route::patch('{regimenCode}/week/{weekNo}/update', [
            'uses' => 'TaskControllerV2@updateRegimenWeekObj',
            'as' => 'week.info.update'
        ]);
        Route::post('daily/badge/upload', [
            'uses' => 'TaskControllerV2@uploadDailyBadge',
            'as' => 'daily.badge.upload'
        ]);
        Route::post('/badge/upload', [
            'uses' => 'TaskControllerV2@uploadRegimenImage',
            'as' => 'badge.upload'
        ]);
        Route::post('insert', [
            'uses' => 'TaskControllerV2@insertRegimen',
            'as' => 'insert'
        ]);
        Route::put('update', [
            'uses' => 'TaskControllerV2@updateRegimen',
            'as' => 'update'
        ]);
        Route::delete('{regimenCode}/delete', [
            'uses' => 'TaskControllerV2@deleteRegimen',
            'as' => 'delete'
        ]);
        Route::post('weekly-task/insert', [
            'uses' => 'TaskControllerV2@insertWeeklyRegimen',
            'as' => 'weekly.insert'
        ]);
        Route::delete('weekly-task/week/{week}/code/{code}/delete', [
            'uses' => 'TaskControllerV2@deleteWeeklyRegimen',
            'as' => 'weekly.delete'
        ]);
        Route::get('weekly-task/week/{week}/code/{code}/advise', [
            'uses' => 'TaskControllerV2@getWeeklyTaskAdvisePage',
            'as' => 'weekly.advise.page'
        ]);
        Route::get('weekly-task/week/{week}/code/{code}/advise/info', [
            'uses' => 'TaskControllerV2@getWeeklyTaskAdviseInfo',
            'as' => 'weekly.advise.info'
        ]);
        Route::put('weekly-task/advise/info/update', [
            'uses' => 'TaskControllerV2@updateWeeklyTaskAdviseInfo',
            'as' => 'weekly.advise.info.update'
        ]);
    });

    Route::group(['prefix' => 'assess', 'as' => 'assess.'], function () {
        Route::group(["prefix" => "mental-score-level-mapping"], function () {
            Route::get('/page', "AssessmentController@mentalScoreLevelMappingPage")->name("score-level-map.page");
            Route::get('/info', "AssessmentController@fetchMentalScoreLevelMappingInfo")->name("score-level-map.info");
            Route::post('/insert', "AssessmentController@insertMentalScoreLevelMappingInfo")->name("score-level-map.insert");
            Route::put('/{id}/update', "AssessmentController@updateMentalScoreLevelMappingInfo")->name("score-level-map.update");
            Route::delete('/{id}/delete', "AssessmentController@deleteMentalScoreLevelMappingInfo")->name("score-level-map.delete");
        });
        Route::get('questions/order', 'AssessmentController@getDefinedQuestionsOrder');
        Route::group(['prefix' => 'tag'], function () {
            Route::get('get/order', 'AssessmentController@rearrangeAssessmentTags')->name("tag.order");
            Route::get('sequence', 'AssessmentController@getDefinedTagOrderSequence');
            Route::post('add/order', 'AssessmentController@postNewQuestionsTagOrder')->name("postTagOrder");
            Route::put('update/order', 'AssessmentController@updateTagOrder')->name("update.tag.order");
        });
        Route::group(['prefix' => 'regimen/recommendation'], function () {
            Route::get('page', [
                'uses' => 'AssessmentController@taskRecommendRegimenPage',
                'as' => 'regimen.recommend.page'
            ]);
            Route::get('get', [
                'uses' => 'AssessmentController@fetchRecommendRegimen',
                'as' => 'regimen.recommend.get'
            ]);
            Route::get('question/{queryId}/answer/{answer}/page', [
                'uses' => 'AssessmentController@getQueryAnswersRegimenPage',
                'as' => 'query.answers.page'
            ]);
            Route::get('question/{queryId}/answer/{answer}', [
                'uses' => 'AssessmentController@getRegimensCorrespondingToAnswer',
                'as' => 'regimen.query.answers.get'
            ]);
            Route::post('answers/regimen/update', [
                'uses' => 'AssessmentController@modifyQueryAnswersRegimen',
                'as' => 'query.answers.update'
            ]);
            Route::patch('answer/{id}/level-restriction/update', [
                'uses' => 'AssessmentController@updateRestrictionLevel',
                'as' => 'query.answers.restriction.level.update'
            ]);
        });
        Route::group(['prefix' => 'test/recommendation'], function () {
            Route::get('page', [
                'uses' => 'AssessmentController@testRecommendationPage',
                'as' => 'test.recommend.page'
            ]);
            Route::get('get', [
                'uses' => 'AssessmentController@fetchRecommendedTests',
                'as' => 'test.recommend.get'
            ]);
            Route::get('query/{queryId}/answer/{answer}/page', [
                'uses' => 'AssessmentController@updateTestRecommendationPage',
                'as' => 'test.recommend.modify.page'
            ]);
            Route::get('query/{queryId}/answer/{answer}', [
                'uses' => 'AssessmentController@getTestsCorrespondingToAnswer',
                'as' => 'query.answers.get'
            ]);

            Route::post('assess/modify/answers/test', [
                'uses' => 'AssessmentController@updateTestRecommendations',
                'as' => 'test.recommend.modify'
            ]);
        });
    });

    //TODO: Refactoring needed
    Route::get('assess/view/tests', 'AssessmentController@viewTestsPage');
    Route::get('assess/fetch/tests', 'DiagnosticLabController@fetchTests');
    Route::put('assess/{isEditPage}/update/test', 'AssessmentController@addOrUpdateTest');
    Route::post('assess/{isEditPage}/add/test', 'AssessmentController@addOrUpdateTest');
    Route::delete('assess/test/{id}/delete', 'AssessmentController@deleteTest');

    Route::get('users-tasks', 'UserTasksController@showUsersTasks');
    Route::get('get_users_task', 'UserTasksController@getUsersTasks');
    Route::get('assess/category', 'AssessmentController@showQuestionCategory');
    Route::get('assess/get_category_list', 'AssessmentController@showQuestionCategoryList');
    Route::post('assess/update_category', 'AssessmentController@update_category');

    Route::get('assess/tag', 'AssessmentController@showQuestionTag');
    Route::get('assess/get_tag_list', 'AssessmentController@showQuestionTagList');
    Route::post('assess/update_tag', 'AssessmentController@update_tag');

    Route::get('assess/question', 'AssessmentController@showQuestion');
    Route::get('assess/get_question_list', 'AssessmentController@showQuestionList');
    Route::post('assess/update_question', 'AssessmentController@update_question');
    Route::post('assess/insert_question', 'AssessmentController@insert_question');
    Route::post('assess/delete_question', 'AssessmentController@delete_question');

    Route::get('assess/interpolation', 'AssessmentController@showInterp');
    Route::get('assess/get_interp_list', 'AssessmentController@showInterpList');
    Route::post('assess/insert_interp', 'AssessmentController@insert_interp');
    Route::post('assess/update_interp', 'AssessmentController@update_interp');
    Route::post('assess/delete_interp', 'AssessmentController@delete_interp');

//-------------------------------------------------------------------------------
    // The below routes are  using TaskControllerV2
    Route::post("flushAndUpload/task", 'TaskControllerV2@flushAndUploadNewRegimenData');
    Route::post("taskDone/message", 'TaskControllerV2@writeToFileTaskDoneMessage')->name("task.complete.message");

});
