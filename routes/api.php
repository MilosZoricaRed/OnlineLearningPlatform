<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/**/ 
// REGISTRATION
Route::post('/register', 'RegisterController@register');
Route::post('/login', 'RegisterController@login');
Route::get('companys/{id}/sectors', 'CompanyController@companysSector');
Route::get('companys', 'CompanyController@index');
Route::post('users/icon', 'UserController@saveUserIcon');
 
// Password reset
Route::post('/forgot-password', 'ApiAuth\PasswordResetController@create');
Route::get('/reset-password/{token}', 'ApiAuth\PasswordResetController@find');
Route::post('/reset-password', 'ApiAuth\PasswordResetController@reset');


Route::group(['middleware' => ['admin', 'auth:api']], function () {
    // ANALYTICS
    Route::get('admin/companies/users', 'AnalyticController@usersAnalytics');
    Route::get('admin/companies/sectors/users', 'AnalyticController@usersSectors');
    Route::post('admin/company/analytics/training', 'AnalyticController@postTrainingGet');
    Route::post('admin/company/analytics/quizz', 'AnalyticController@postQuizzesGet');
});

Route::group(['middleware' => ['superadmin', 'auth:api']], function () {
    // TEST INTRO
    // Route::get('admin/intro/{id}', 'IntroController@getIntroSuperAdmin');

    // SUPERADMIN GETS
    Route::get('superadmin/companys', 'CompanyController@adminCompanys');
    Route::get('superadmin/companys/{id}/sectors', 'CompanyController@adminCompanysSector');
    Route::get('superadmin/companys/{id}/categories', 'CompanyController@adminCompanysCategories');
    Route::get('superadmin/company/{id}/trainings', 'TrainingController@adminTrainings');
    Route::get('superadmin/companys/{company_id}/categories/{category_id}/quizzes', 'CategoryController@adminCategorysQuizzes');
    Route::get('superadmin/companys/{company_id}/trainings/{training_id}/slides', 'TrainingController@adminTrainingSlides');
    Route::get('superadmin/quizzes/{id}/questions', 'QuestionController@adminQuizzesQuestions');
    Route::get('superadmin/question/{id}/allanswers', 'AnswerController@adminGetAllAnswers');
    Route::get('superadmin/question/{id}/exactanswer', 'AnswerController@adminExactAnswer');
    Route::get('superadmin/company/{company_id}/strategy/slides', 'SlideController@adminStrategySlides');
    Route::get('superadmin/company/{company_id}/intro/slides', 'SlideController@adminIntroSlides');
    Route::get('superadmin/company/{company_id}/categories/{id}/types', 'CategoryController@adminCategoriesTypes');
    Route::get('superadmin/companys/{company_id}/trainings/{id}', 'TrainingController@superadminTrainings');
    Route::get('superadmin/company/{company_id}/category/{id}/trainings', 'CategoryController@superadminCategoryTrainings');

    Route::post('superadmin/companys/create', 'CompanyController@store');
    Route::get('superadmin/companys/{id}/users', 'UserController@getUsers');
    // Edits
    Route::post('superadmin/answers/{id}/edit', 'AnswerController@edit');
    Route::post('superadmin/company/{company_id}/sectors/{sectors_id}/edit', 'CompanyController@editSectors');
    Route::post('superadmin/categories/{id}/edit', 'CategoryController@edit');
    Route::post('superadmin/companys/{id}/edit', 'CompanyController@edit');
    Route::post('superadmin/questions/{id}/edit', 'QuestionController@edit');
    Route::post('superadmin/quizzes/{id}/edit', 'QuizController@edit');
    Route::post('superadmin/slides/{id}/edit', 'SlideController@edit');
    Route::post('superadmin/trainings/{id}/edit', 'TrainingController@edit');
    Route::post('superadmin/intros/{id}/edit', 'IntroController@edit');
    Route::post('superadmin/strategys/{id}/edit', 'StrategyController@edit');
    Route::post('superadmin/user/{id}/update', 'UserController@editUser');
    Route::post('superadmin/training/{id}/published', 'TrainingController@changePublished');

    //DELETE
    Route::delete('superadmin/training/{id}', 'TrainingController@destroy');
    Route::delete('superadmin/sector/{id}', 'CompanyController@destroySector');
    Route::delete('superadmin/company/{id}', 'CompanyController@destroy');
    Route::delete('superadmin/user/delete/{id}', 'UserController@deleteUser');
    Route::delete('superadmin/slides/delete/{id}', 'SlideController@destroy');
    Route::delete('superadmin/strategy/delete/{id}', 'StrategyController@deleteStrategy');
    Route::delete('superadmin/intro/delete/{id}', 'IntroController@deleteIntro');
    Route::delete('superadmin/category/delete/{id}', 'CategoryController@destroy');
    Route::delete('superadmin/answer/delete/{id}', 'AnswerController@destroy');
    Route::delete('superadmin/question/delete/{id}', 'QuestionController@destroy');


    //Route::delete('superadmin/quizz/delete/{id}', 'QuizController@destroy');
    // CREATE
    Route::post('superadmin/intro/create', 'IntroController@createIntro');
    Route::post('superadmin/strategy/create', 'StrategyController@createStrategy');
    Route::post('superadmin/categories', 'CategoryController@store');
    Route::post('superadmin/answers', 'AnswerController@create');
    Route::post('superadmin/questions', 'QuestionController@store');
    Route::post('superadmin/quizzes', 'QuizController@store');
    Route::post('superadmin/slides/training', 'SlideController@storeTrainingSlides');
    Route::post('superadmin/slides/intro', 'SlideController@storeIntroSlides');
    Route::post('superadmin/slides/strategy', 'SlideController@storeStrategySlides');
    Route::post('superadmin/trainings', 'TrainingController@store');

    // MEDIA UPLOAD
    Route::post('superadmin/slides/image', 'SlideController@saveSlideImage');
    Route::post('superadmin/trainings/detail', 'TrainingController@saveTrainingImageDetail');
    Route::post('superadmin/trainings/list', 'TrainingController@saveTrainingImageList');
    Route::post('superadmin/slides/video', 'SlideController@saveSlideVideo');
    Route::post('superadmin/slides/audio', 'SlideController@saveSlideAudio');
});



Route::middleware('auth:api')->group(function () {
    Route::get('token/{token}/verify', 'RegisterController@verifyToken');
    Route::get('me', 'UserController@getMe');
    Route::get('training/{id}/likes', 'LikeController@index');
    Route::post('training/{id}/likes', 'LikeController@store');
    Route::post('user/update', 'UserController@edit');
    Route::get('questions/{id}/getuseranswer', 'AnswerController@answer');
    // Intro get
    Route::get('intro/{id}/slides', 'SlideController@introSlides');
    Route::get('intros', 'IntroController@getIntro');
    // Strategy get
    Route::get('strategy/{id}/slides', 'SlideController@strategySlides');
    Route::get('strategys', 'StrategyController@getStrategy');
    // Slides get
    Route::get('training/{id}/slides', 'SlideController@index');
    //Route::get('quizz/{id}/slides', 'SlideController@quizzIndex');
    Route::get('slides/{id}', 'SlideController@show');
    // Companys get
    Route::get('trainings', 'TrainingController@index');
    Route::get('companys/{id}/trainings', 'CompanyController@show');
    Route::get('companys/{id}/categories', 'CompanyController@companysCategories');
    Route::get('companys/{id}/categories/type', 'CompanyController@companysCategoriesType');
    Route::get('companys/{id}/quizzes', 'CompanyController@companysQuizzes');

    // Trainings get

    Route::get('trainings/{id}', 'TrainingController@show');
    Route::get('trainings-default', 'TrainingController@showDefaults');
    Route::post('trainings/{id}/custom-default', 'TrainingController@showCustomDefault');
    Route::get('trainings/{id}/slides', 'TrainingController@trainingSlides');
    // Quizzes get
    Route::get('quizzes', 'QuizController@index');
    Route::get('quizzes/{id}', 'QuizController@show');
    // Questions get
    Route::get('questions', 'QuestionController@all');
    Route::get('questions/{id}', 'QuestionController@show');
    Route::get('quizzes/{id}/questions', 'QuestionController@index');
    Route::get('question/{id}/exactanswer', 'AnswerController@exactanswer');
    // Answers get
    Route::get('answers', 'AnswerController@index');
    Route::get('answers/{id}', 'AnswerController@show');
    Route::get('question/{id}/allanswers', 'AnswerController@getallanswers');
    // USER RANK
    Route::get('user/trainingrank', 'UserRankController@index');
    Route::get('user/{id}/quizzesrank', 'UserRankController@show');
    Route::get('user/{id}/quizzrank/{category_id}', 'UserRankController@quizzRank');
    //Route::get('users', 'UserController@getUsers');
    // Category get
    Route::get('categories', 'CategoryController@index');
    Route::get('categories/{id}/types', 'CategoryController@show');
    Route::get('category/{id}/trainings', 'CategoryController@categoryTrainings');
    Route::get('categories/{id}/trainings', 'TrainingController@categorysTraining'); // izuzetak funkcija je sa razlogom u trening controlleru
    Route::get('categories/{id}/quizzes', 'CategoryController@categorysQuizzes');
    // Sectors get
    Route::get('sectors', 'CompanyController@sectors');
    // Messages get and post
    Route::get('messages', 'MessageController@index');
    Route::post('messages/{id}', 'MessageController@edit');

    // Home page for every user
    Route::get('learning', 'AnalyticController@homeLearning');

    // Delete Completed
    Route::delete('completed/quizz/{id}', 'QuizController@deleteCompletedQuizz');

    //Inserts
    Route::post('quizzes/{id}/starttime', 'QuizController@startTime');
    Route::post('quizzes/{id}/endtime', 'QuizController@endTime');
    Route::post('slides/{id}/completed', 'SlideController@completed');
    Route::post('trainings/{id}/completed', 'TrainingController@completed');
    Route::post('quizzes/{id}/completed', 'QuizController@completed');
    Route::post('answers/useranswered', 'AnswerController@useranswered');
    Route::post('answers/draganddrop', 'AnswerController@userAnsweredDragAndDrop');
    //Route::post('users/icon', 'UserController@saveUserIcon');
    //Route::post('user/image', 'UserController@saveProfileImage');
    Route::post('provera', 'AnswerController@checkAnswer');
   
});
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/
