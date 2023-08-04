<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InstallationController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\magicaiUpdaterController;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]], function() {
    Route::get('/', [IndexController::class, 'index'])->name('index');
});

Route::get('/activate', [IndexController::class, 'activate']);

Route::get('/confirm/email/{email_confirmation_code}', [MailController::class, 'emailConfirmationMail']);
Route::get('/confirm/email/{password_reset_code}', [MailController::class, 'emailPasswordResetEmail']);


//Route::get('/install-script-env-editor', [InstallationController::class, 'envFileEditor'])->name('installer.envEditor');
//Route::post('/install-script-env-editor/save', [InstallationController::class, 'envFileEditorSave'])->name('installer.envEditor.save');
//Route::get('/install-script', [InstallationController::class, 'install'])->name('installer.install');
Route::get('/upgrade-script', [InstallationController::class, 'upgrade']);
Route::get('/update-manual', [InstallationController::class, 'updateManual']);

Route::get('/page/{slug}', [PageController::class, 'pageContent']);
Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{slug}', [BlogController::class, 'post']);
Route::get('/blog/tag/{slug}', [BlogController::class, 'tags']);
Route::get('/blog/category/{slug}', [BlogController::class, 'categories']);
Route::get('/blog/author/{slug}', [BlogController::class, 'author']);

Route::get('/privacy-policy', [PageController::class, 'pagePrivacy']);
Route::get('/terms', [PageController::class, 'pageTerms']);


// Clear log file
Route::get('/clear-log', function () {
    $logFile = storage_path('logs/laravel.log');

    if (file_exists($logFile)) {
        unlink($logFile);
    }

    return response()->json(['success' => true]);
});

//Updater

Route::get('magicai.updater.check',[magicaiUpdaterController::class, 'check']);
Route::get('magicai.updater.currentVersion', [magicaiUpdaterController::class, 'getCurrentVersion']);
Route::get('magicai.updater.update', [magicaiUpdaterController::class, 'update'])->middleware('admin');




require __DIR__.'/auth.php';
require __DIR__.'/panel.php';
require __DIR__.'/webhooks.php';


