<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Inspection\InspectionController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Template\TemplateController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Vehicle\VehicleController;
use Illuminate\Support\Facades\Route;

// Language Switch
Route::get('lang/{locale}', function (string $locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.submit');
});
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ========= PUBLIC SHARE (throttled — 30 requests/minute) =========
Route::middleware('throttle:30,1')->group(function () {
    Route::get('share/{token}', [ReportController::class, 'publicView'])->name('share.view');
    Route::get('share/{token}/pdf', [ReportController::class, 'publicPdf'])->name('share.pdf');
});

// PWA offline page
Route::get('offline', fn() => view('errors.offline'))->name('offline');

// ========= AUTHENTICATED ROUTES =========
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Vehicles
    Route::resource('vehicles', VehicleController::class);

    // Inspections
    Route::prefix('inspections')->name('inspections.')->group(function () {
        Route::get('/', [InspectionController::class, 'index'])->name('index');
        Route::get('create', [InspectionController::class, 'create'])->name('create');
        Route::post('/', [InspectionController::class, 'store'])->name('store');
        Route::get('{id}', [InspectionController::class, 'show'])->name('show');
        Route::get('{id}/conduct', [InspectionController::class, 'conduct'])->name('conduct');
        Route::post('{id}/submit', [InspectionController::class, 'submit'])->name('submit');
        Route::post('{id}/cancel', [InspectionController::class, 'cancel'])->name('cancel');
        Route::delete('{id}', [InspectionController::class, 'destroy'])->name('destroy');
        Route::post('{id}/media', [InspectionController::class, 'uploadMedia'])->name('uploadMedia');
        Route::delete('media/{mediaId}', [InspectionController::class, 'deleteMedia'])->name('deleteMedia');
        Route::post('{id}/toggle-hidden', [InspectionController::class, 'toggleHidden'])->name('toggleHidden');
    });

    // Templates
    Route::resource('templates', TemplateController::class);
    Route::post('templates/{id}/duplicate', [TemplateController::class, 'duplicate'])->name('templates.duplicate');
    Route::post('templates/{templateId}/sections', [TemplateController::class, 'addSection'])->name('templates.sections.store');
    Route::put('templates/sections/{sectionId}', [TemplateController::class, 'updateSection'])->name('templates.sections.update');
    Route::delete('templates/sections/{sectionId}', [TemplateController::class, 'deleteSection'])->name('templates.sections.destroy');
    Route::post('templates/sections/{sectionId}/questions', [TemplateController::class, 'addQuestion'])->name('templates.questions.store');
    Route::put('templates/questions/{questionId}', [TemplateController::class, 'updateQuestion'])->name('templates.questions.update');
    Route::delete('templates/questions/{questionId}', [TemplateController::class, 'deleteQuestion'])->name('templates.questions.destroy');

    // Users
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggleActive');

    // Reports
    Route::get('reports/{id}/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
    Route::get('reports/{id}/view', [ReportController::class, 'viewPdf'])->name('reports.view');

    // Customers
    Route::resource('customers', \App\Http\Controllers\Customer\CustomerController::class);
    Route::post('customers/{customer}/link-vehicle', [\App\Http\Controllers\Customer\CustomerController::class, 'linkVehicle'])->name('customers.link-vehicle');
    Route::post('customers/{customer}/unlink-vehicle/{vehicle}', [\App\Http\Controllers\Customer\CustomerController::class, 'unlinkVehicle'])->name('customers.unlink-vehicle');

    // Settings
    Route::get('settings', [\App\Http\Controllers\Settings\SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\Settings\SettingsController::class, 'update'])->name('settings.update');

    // Audit Logs
    Route::get('audit-logs', [\App\Http\Controllers\AuditLog\AuditLogController::class, 'index'])->name('audit-logs.index');

    // Finance / Revenue
    Route::get('finance', [\App\Http\Controllers\Finance\RevenueController::class, 'index'])->name('finance.index');
    Route::post('finance/{id}/paid', [\App\Http\Controllers\Finance\RevenueController::class, 'markPaid'])->name('finance.markPaid');
    Route::post('finance/{id}/unpaid', [\App\Http\Controllers\Finance\RevenueController::class, 'markUnpaid'])->name('finance.markUnpaid');
});