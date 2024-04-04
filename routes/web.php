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
    Route::get('/', [DashboardController::class, 'dashboard'])->name('monday.dashboard');
    Route::group(['prefix' => "form"], function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('monday.dashboard');
        Route::get('/track-request', [DashboardController::class, 'trackRequest'])->name('monday.track_request');
        Route::post('/track-request', [DashboardController::class, 'trackRequest'])->name('monday.track_request');
        Route::get('/candidate-form', [DashboardController::class, 'mobilityform'])->name('monday.mobilityform');
        Route::get('/candidate-stats', [DashboardController::class, 'stats'])->name('monday.stats');
        Route::get('/track-request/{id}/{userName}', [DashboardController::class, 'manageById'])->name('user.show');


    });

    Route::group(['prefix' => "admin"], function () {
        Route::get('/users', [DashboardController::class, 'userslist'])->name('admin.users');
        // Route::post('/users', [DashboardController::class, 'usersBoardAssign'])->name('admin.post.users');
      Route::get('/board-visiblilty', [DashboardController::class, 'columnAllowed'])->name('admin.boardvisibility');
      Route::post('/board-visiblilty', [DashboardController::class, 'boardColumnMapping'])->name('admin.post.boardvisibility');
      Route::get('/get-board-columns/{id}', [DashboardController::class, 'getBoardColumns'])->name('admin.get.getBoardColumns');
      Route::get('/get-board-columns-data', [DashboardController::class, 'getBoardColumnsData'])->name('admin.getBoardColumnsData');
      Route::get('/get-board-columns-data/{id}', [DashboardController::class, 'getBoardColumnsDataById'])->name('admin.getBoardColumnsDataById');
    });
    Route::get('/login', [AuthController::class, 'login'])->name('monday.get.login');
    Route::post('/login', [AuthController::class, 'login'])->name('monday.post.login');
    Route::get('/signup', [AuthController::class, 'signup'])->name('monday.get.signup');
    Route::post('/signup', [AuthController::class, 'signup'])->name('monday.post.signup');

    Route::get('/forgot', [AuthController::class, 'forgot'])->name('monday.forgot');
    Route::get('/thanks', [AuthController::class, 'thankssignup'])->name('monday.thankssignup');




    Route::post('/track-onboarding', [TrackOnboardingController::class, 'trackOnboarding'])->name('monday.trackOnboarding');

    Route::post('/track-onboarding-byid', [TrackOnboardingController::class, 'trackOnboardingById'])->name('monday.trackOnboardingById');

    Route::post('/status-onboarding-hiring-type', [StatusOnboardingController::class, 'statusOnboardingHiringType'])->name('monday.statusOnboardingHiringType');
    // });
});

require __DIR__ . '/auth.php';
