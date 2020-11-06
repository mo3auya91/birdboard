<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvitationsController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::resource('projects', ProjectsController::class);
    Route::resource('projects.tasks', TaskController::class);
    Route::post('projects/{project}/invitations', [InvitationsController::class, 'store'])->name('project.invitations');
});
Route::match(['get', 'post'], '/locale/change', function () {
    $HTTP_REFERER = str_replace(url('/'), '', request()->server->get('HTTP_REFERER'));
    $HTTP_REFERER = LaravelLocalization::localizeURL($HTTP_REFERER, request('lang'));
    //$HTTP_REFERER = str_replace(url('/' . app()->getLocale()), url('/' . request('lang')), request()->server->get('HTTP_REFERER'));
    return redirect()->to($HTTP_REFERER);
})->name('locale.change');
