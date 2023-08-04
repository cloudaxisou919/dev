<?php
use Illuminate\Support\Facades\Route;
use TheNextLeg\Controllers\TheNextLegController;
use TheNextLeg\Controllers\UpscaleImageController;

//thenextleg API
Route::middleware('auth','localeSessionRedirect', 'localizationRedirect', 'localeViewPath',)->group(function () {
Route::get('dashboard/user/thenextleg/generator/{slug}',[TheNextLegController::class, 'index'])->name('dashboard.user.thenextleg.generator');
Route::post('dashboard/user/thenextleg/generate',[TheNextLegController::class, 'generateAIRequest']);
Route::get('dashboard/user/thenextleg/generate/{messageId}',[TheNextLegController::class, 'checkAIProgress']);
Route::get('dashboard/user/thenextleg/image/delete/{id}',[TheNextLegController::class, 'imageDelete'])->name('dashboard.user.thenextleg.image.delete');
Route::post('dashboard/user/thenextleg/button',[TheNextLegController::class, 'upscaleImages']);
Route::post('dashboard/user/thenextleg/upscale/{messageId}',[UpscaleImageController::class, 'checkAIProgress']);
Route::post('dashboard/user/thenextleg/bad-words-filter',[TheNextLegController::class, 'badWordsFilter']);
});
?>