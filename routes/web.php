<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Monday\RequestOnboardingController;
use App\Http\Controllers\Monday\TrackOnboardingController;
use App\Http\Controllers\Monday\StatusOnboardingController;
use App\Http\Controllers\Monday\DashboardController;
use App\Http\Controllers\Monday\AuthController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//Monday.com

Route::group(['prefix' => "monday"], function () {
    // Route::middleware('monday.auth')->group(function () {
    // Route::group(['middleware' => 'monday.auth'], function(){
    // Track Onboarding
    // Route::get('/', [DashboardController::class, 'dashboard'])->name('monday.dashboard');
    Route::group(['prefix' => "form", 'middleware'=>['web','isUser']], function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('monday.dashboard');
        Route::get('/track-request', [DashboardController::class, 'trackRequest'])->name('monday.track_request');
        Route::post('/track-request', [DashboardController::class, 'trackRequest'])->name('monday.track_request');
        Route::get('/candidate-form', [DashboardController::class, 'mobilityform'])->name('monday.mobilityform'); // monday-form
        Route::get('/candidate-stats', [DashboardController::class, 'stats'])->name('monday.stats'); // chart
        Route::get('/track-request/{id}/{userName}', [DashboardController::class, 'manageById'])->name('user.show');


    });

    Route::group(['prefix' => "admin", 'middleware'=>['web','isAdmin']], function () {
        Route::get('/users', [DashboardController::class, 'userslist'])->name('admin.users');
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

    });
    Route::get('/login', [AuthController::class, 'login'])->name('monday.get.login');
    Route::post('/login', [AuthController::class, 'login'])->name('monday.post.login');
    Route::get('/signup', [AuthController::class, 'signup'])->name('monday.get.signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('monday.post.signup');
    Route::get('/logout', [AuthController::class, 'logout'])->name('monday.get.logout');

    Route::get('/forgot', [AuthController::class, 'forgot'])->name('monday.forgot');
    Route::post('/forgot', [AuthController::class, 'forgot'])->name('monday.post.forgot');
    Route::get('/create-password/{token}', [AuthController::class, 'createNewPassword'])->name('monday.createNewPassword');
    Route::post('/create-password', [AuthController::class, 'createNewPasswordPost'])->name('monday.createNewPasswordPost');
    Route::get('/thanks', [AuthController::class, 'thankssignup'])->name('monday.thankssignup');




    Route::post('/track-onboarding', [TrackOnboardingController::class, 'trackOnboarding'])->name('monday.trackOnboarding');

    Route::post('/track-onboarding-byid', [TrackOnboardingController::class, 'trackOnboardingById'])->name('monday.trackOnboardingById');

    Route::post('/status-onboarding-hiring-type', [StatusOnboardingController::class, 'statusOnboardingHiringType'])->name('monday.statusOnboardingHiringType');
    // });
});

require __DIR__ . '/auth.php';
