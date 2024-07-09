<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Monday\RequestOnboardingController;
use App\Http\Controllers\Monday\TrackOnboardingController;
use App\Http\Controllers\Monday\StatusOnboardingController;
use App\Http\Controllers\Monday\DashboardController;
use App\Http\Controllers\Monday\AuthController;
use App\Http\Controllers\Incorpify\DashboardController as IncorpifyDashboard;
use App\Http\Controllers\Governify\Admin\ServiceCategoriesController;
use App\Http\Controllers\Governify\Admin\ServiceRequestFormsController;
use App\Http\Controllers\Governify\Admin\ServiceRequestsController;
use App\Http\Controllers\Governify\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Governify\Customer\GovernifyRequestTrackingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::get('/', [AuthController::class, 'login'])->name('monday.get.login');
//Monday.com

Route::post('/', [AuthController::class, 'login'])->name('monday.post.login');

Route::post('/commom-login', [AuthController::class, 'userLogin']);
Route::post('/commom-forgot', [AuthController::class, 'commomForgot']);
// Route::post('/commom-create-password/{token}', [AuthController::class, 'commomCreateNewPasswordPost']);

Route::get('/loginUserDetails/{id}', [AuthController::class, 'loginUserDetails'])->name('monday.get.loginUserDetails')->middleware('auth:api');

//loginUserDetails

Route::group(['prefix' => "common"], function () {
    Route::post('/updateNewPassword', [AuthController::class, 'commonUpdateNewPassword']);
    Route::post('/verifyUser', [AuthController::class, 'verifyUser']);
});
Route::group(['prefix' => "onboardify", 'middleware' => ['web', 'setSession']], function () {
    // Route::middleware('monday.auth')->group(function () {
    // Route::group(['middleware' => 'monday.auth'], function(){
    // Track Onboarding
    // Route::get('/', [DashboardController::class, 'dashboard'])->name('monday.dashboard');
    Route::get('/info', [AuthController::class, 'info'])->name('monday.get.info');
   
    Route::group(['prefix' => "form", 'middleware'=>['web','isUser']], function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('monday.dashboard');
        Route::get('/track-request', [DashboardController::class, 'trackRequest'])->name('monday.get.track_request');
        Route::post('/track-request', [DashboardController::class, 'trackRequest'])->name('monday.post.track_request');
        Route::get('/candidate-form', [DashboardController::class, 'mobilityform'])->name('monday.mobilityform'); // monday-form
        Route::get('/candidate-stats', [DashboardController::class, 'stats'])->name('monday.stats'); // chart
        Route::get('/track-request/{id}/{userName}', [DashboardController::class, 'manageById'])->name('user.show');


    });

    Route::group(['prefix' => "admin", 'middleware'=>['web','isAdmin']], function () {
        Route::get('/users', [DashboardController::class, 'userslist'])->name('admin.users');
        Route::post('/users', [DashboardController::class, 'userslist'])->name('admin.post.users');
        Route::get('/usersDelete/{id}', [DashboardController::class, 'usersDelete'])->name('admin.delete.users');
        // Route::get('/create-admin', [DashboardController::class, 'createAdmin'])->name('admin.get.createAdmin');
        // Route::post('/create-admin', [DashboardController::class, 'storeAdmin'])->name('admin.post.storeAdmin');
      Route::get('/board-visiblilty', [DashboardController::class, 'columnAllowed'])->name('admin.boardvisibility');
      Route::post('/board-visiblilty', [DashboardController::class, 'boardColumnMapping'])->name('admin.post.boardvisibility');
      Route::get('/get-board-columns/{id}', [DashboardController::class, 'getBoardColumns'])->name('admin.get.getBoardColumns');
      Route::get('/get-board-columns-data', [DashboardController::class, 'getBoardColumnsData'])->name('admin.getBoardColumnsData');
      Route::get('/get-board-columns-data/{id}', [DashboardController::class, 'getBoardColumnsDataById'])->name('admin.getBoardColumnsDataById');
      Route::get('/colour-mapping', [DashboardController::class, 'getColourMapping'])->name('admin.getColourMapping');
      Route::post('/colour-mapping', [DashboardController::class, 'postColourMapping'])->name('admin.postColourMapping');

    });
    Route::group(['prefix' => "admin", 'middleware'=>['web','isSuperAdmin']], function () {
        Route::get('/create-admin', [DashboardController::class, 'createAdmin'])->name('admin.get.createAdmin');
        Route::post('/create-admin', [DashboardController::class, 'storeAdmin'])->name('admin.post.storeAdmin');
        Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.get.settings');
        Route::post('/settings', [DashboardController::class, 'settings'])->name('admin.post.settings');

    });
//     Route::get('/login', [AuthController::class, 'login'])->name('monday.get.login');
//     Route::post('/login', [AuthController::class, 'login'])->name('monday.post.login');
    Route::get('/signup', [AuthController::class, 'signup'])->name('monday.get.signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('monday.post.signup');
    Route::post('/newSignup', [AuthController::class, 'newSignup'])->name('monday.post.newSignup');
    Route::get('/logout', [AuthController::class, 'logout'])->name('monday.get.logout');

    Route::get('/forgot', [AuthController::class, 'forgot'])->name('monday.forgot');
    Route::post('/forgot', [AuthController::class, 'forgot'])->name('monday.post.forgot');
    Route::get('/verify/{token}', [AuthController::class, 'verify'])->name('monday.get.verify');
    Route::get('/create-password/{token}', [AuthController::class, 'createNewPassword'])->name('monday.createNewPassword');
    Route::post('/create-password/{token}', [AuthController::class, 'createNewPasswordPost'])->name('monday.createNewPasswordPost');
    Route::get('/info', [AuthController::class, 'thankssignup'])->name('monday.thankssignup');


    // Route::get('/test', [AuthController::class, 'test'])->name('monday.forgot');

    Route::post('/track-onboarding', [TrackOnboardingController::class, 'trackOnboarding'])->name('monday.trackOnboarding');

    Route::post('/track-onboarding-byid', [TrackOnboardingController::class, 'trackOnboardingById'])->name('monday.trackOnboardingById');

    Route::post('/status-onboarding-hiring-type', [StatusOnboardingController::class, 'statusOnboardingHiringType'])->name('monday.statusOnboardingHiringType');
    // });

});

Route::group(['prefix' => "incorpify", "middleware" => ["auth:api"]], function () {
    Route::get('/', [IncorpifyDashboard::class, 'dashboard'])->name('incorpify.dashboard');
    Route::get('/incorpifyById/{id}', [IncorpifyDashboard::class, 'incorpifyById'])->name('incorpify.incorpifyById');
    Route::get('/getSubItemDetailsById/{id}', [IncorpifyDashboard::class, 'getSubItemDetailsById'])->name('incorpify.getSubItemDetailsById');//getUpdateAndReply
    Route::post('/update', [IncorpifyDashboard::class, 'update'])->name('incorpify.update');
    Route::post('/updateReplyOrLike', [IncorpifyDashboard::class, 'updateReplyOrLike'])->name('incorpify.updateReplyOrLike');
    Route::post('/uploadFiles', [IncorpifyDashboard::class, 'uploadFiles'])->name('incorpify.uploadFiles');
    Route::post('/testwebhooks', [IncorpifyDashboard::class, 'testwebhooks'])->name('incorpify.testwebhooks');
    
    Route::post('/uploadMondayFiles', [IncorpifyDashboard::class, 'uploadMondayFiles'])->name('incorpify.uploadMondayFiles');
    Route::post('/saveSiteSettings', [IncorpifyDashboard::class, 'saveSiteSettings'])->name('incorpify.saveSiteSettings');
    Route::delete('/dislikeUpdateOrReply/{id}', [IncorpifyDashboard::class, 'dislikeUpdateOrReply'])->name('incorpify.dislikeUpdateOrReply');
    Route::get('/listAllLikes', [IncorpifyDashboard::class, 'listAllLikes'])->name('incorpify.listAllLikes');
    Route::get('/saveSiteSettings', [IncorpifyDashboard::class, 'saveSiteSettings']);

    Route::post('/createItem', [IncorpifyDashboard::class, 'createItem'])->name('incorpify.createItem');
    Route::post('/getSubItemByEmail', [IncorpifyDashboard::class, 'getSubItemByEmail'])->name('incorpify.getSubItemByEmail');
    Route::post('/updateSubitemStatus', [IncorpifyDashboard::class, 'updateSubitemStatus'])->name('incorpify.updateSubitemStatus');
    Route::get('/profile', [IncorpifyDashboard::class, 'profile'])->name('incorpify.profile');
    Route::get('/refreshToken', [IncorpifyDashboard::class, 'refreshToken'])->name('incorpify.refreshToken');
    Route::get('/logout', [IncorpifyDashboard::class, 'logout'])->name('incorpify.logout');
});

Route::group(['prefix' => "governify/admin", "middleware" => ["auth:api","isSuperAdmin","isAdmin"]], function () {
    // serviceCategories API
    Route::get('/serviceCategories',  [ServiceCategoriesController::class, 'index'])->name('serviceCategories.index');
    Route::get('/serviceCategories/{id}',  [ServiceCategoriesController::class, 'showServiceCategoriesById'])->name('serviceCategories.showServiceCategoriesById');
    Route::post('/serviceCategories/create', [ServiceCategoriesController::class, 'createServiceCategories'])->name('serviceCategories.createServiceCategories');
    Route::put('/serviceCategories/{id}', [ServiceCategoriesController::class, 'updateServiceCategories'])->name('serviceCategories.updateServiceCategories');
    Route::delete('/serviceCategories/{id}', [ServiceCategoriesController::class, 'destroy'])->name('serviceCategories.destroy');
    //swap
    Route::post('/serviceCategories/swap',  [ServiceCategoriesController::class, 'swapServiceCategories']);
    Route::post('/rejectServiceCategoryMapping',  [ServiceCategoriesController::class, 'rejectServiceCategoryMapping']);
    Route::get('/overallStatus',  [ServiceCategoriesController::class, 'listOfOverallStatus']);
    Route::get('/fetchAllBoards',  [ServiceCategoriesController::class, 'fetchAllBoards']);

    Route::post('/governifySiteSetting', [ServiceCategoriesController::class, 'governifySiteSetting'])->name('serviceCategories.governifySiteSetting');
    Route::get('/governifySiteSetting', [ServiceCategoriesController::class, 'getGovernifySiteSetting']);
    Route::get('/getCategoriesWithAllService', [ServiceCategoriesController::class, 'getCategoriesWithAllService']);

    //  serviceRequests API
    Route::get('/serviceRequests',  [ServiceRequestsController::class, 'index'])->name('serviceRequests.index');
    Route::get('/serviceRequests/{id}',  [ServiceRequestsController::class, 'showServiceRequestsById'])->name('serviceRequests.showServiceRequestsById');
    Route::post('/serviceRequests/create', [ServiceRequestsController::class, 'createServiceRequests'])->name('serviceRequests.createServiceRequests');
    Route::put('/serviceRequests/{id}', [ServiceRequestsController::class, 'updateServiceRequests'])->name('serviceRequests.updateServiceRequests');
    Route::delete('/serviceRequests/{id}', [ServiceRequestsController::class, 'destroy'])->name('serviceRequests.destroy');
    //swap
    Route::post('/serviceRequests/swap',  [ServiceRequestsController::class, 'swapServiceRequests']);

    //  serviceRequests Form API
    Route::get('/serviceRequestForms',   [ServiceRequestFormsController::class, 'index'])->name('serviceRequestForms.index');
    Route::get('/fetchServiceRequestForms',   [ServiceRequestFormsController::class, 'fetchServiceRequestFormSchema'])->name('serviceRequestForms.fetchServiceRequestFormSchema');
    Route::post('/serviceRequestForms',  [ServiceRequestFormsController::class, 'createServiceRequestForms'])->name('serviceRequestForms.createServiceRequestForms');
    Route::put('/serviceRequestForms/{id}', [ServiceRequestFormsController::class, 'updateServiceRequestForms'])->name('serviceRequestForms.updateServiceRequestForms');
    Route::delete('/serviceRequestForms/{id}',   [ServiceRequestFormsController::class, 'destroy'])->name('serviceRequestForms.destroy');
});

Route::group(['prefix' => "governify/customer", "middleware" => ["auth:api","isUser"]], function () {
    Route::get('/dashboard',   [CustomerDashboardController::class, 'dashboard']);
    Route::get('/dashboardata',   [CustomerDashboardController::class, 'newResponseDashboard']);
    Route::post('/createRequestDashboard',   [CustomerDashboardController::class, 'createRequestDashboard']);
    Route::post('/requestTracking',   [GovernifyRequestTrackingController::class, 'requestTracking']);
    Route::post('/cancelRequest',   [GovernifyRequestTrackingController::class, 'cancelRequest']);
    Route::post('/reverseCancelRequest',   [GovernifyRequestTrackingController::class, 'reverseCancelRequest']);
    Route::get('/exportGovernifyData',   [GovernifyRequestTrackingController::class, 'exportGovernifyData']);
    Route::get('/addGovernifyComment',   [GovernifyRequestTrackingController::class, 'addGovernifyComment']);
    Route::post('/addGovernifyLike',   [GovernifyRequestTrackingController::class, 'addGovernifyLike']);
    Route::get('/uploadGovernifyDocument',   [GovernifyRequestTrackingController::class, 'uploadGovernifyDocument']);

    Route::get('/governifySiteSetting', [ServiceCategoriesController::class, 'getGovernifySiteSetting']);
    Route::get('/serviceRequests',  [ServiceRequestsController::class, 'index']);
});

require __DIR__ . '/auth.php';
