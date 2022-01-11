<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Staff\StaffController;
use App\Http\Controllers\Api\User\UserController;

use App\Http\Controllers\Api\User\ForgotPasswordController;
use App\Http\Controllers\Api\User\ResetPasswordController;
use App\Http\Controllers\Api\Staff\SendOfferController;
use App\Http\Controllers\Api\Staff\UserSearchController;
use App\Http\Controllers\Api\User\Friku\FrikuJobsController;
use App\Http\Controllers\Api\User\JobSearchesController;
use App\Http\Controllers\Api\User\StripeController;
use App\Http\Controllers\Api\User\VerifyEmailController;

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
//ユーザー側ルート

//Note: Route::prefix('user')内のグループにはURIにuserが付きます。
Route::prefix('user')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::get('/show/{user}', [UserController::class, 'show'])->name('user.show');



    //パスワードリセット
    Route::post('/password/request', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');
    //Route::prefix('joboffer')内のグループにはjobofferがURIに付きます。


});

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();

//     return redirect('localhost:3000');
// })->middleware(['auth', 'signed'])->name('verification.verify');
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed',  'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');


Route::prefix('admins')->group(function () {
    Route::post('/user/export', [AdminUserController::class, 'csvExport'])->name('user.export');
    Route::middleware(['auth:admins'])->group(function(){

    });
});
