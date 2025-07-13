<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'create'])->name('login');
    Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'store']);
    Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'create'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'store']);
    Route::get('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'store'])->name('password.email');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('email/verification-notification', function (Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Link verifikasi baru telah dikirim!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

Route::middleware('auth')->post('logout', [App\Http\Controllers\Auth\LoginController::class, 'destroy'])->name('logout');

Route::get('/', fn () => view('welcome'));

// Main Routes - Require email verification
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'pages.dashboard.dashboard')->name('dashboard');
    Route::middleware('lecturer.only')->group(function () {
        Route::view('personal-data', 'pages.personal-data.personal-data')->name('personal-data');
        Route::view('personal-data/education', 'pages.personal-data.education')->name('personal-data.education');
    });
});

// Dokumen Saya Routes - Lecturer Only
Route::prefix('documents')->name('documents.')->middleware(['auth', 'verified', 'lecturer.only'])->group(function () {
    Route::view('study-requirements', 'pages.documents.study-requirements')->name('study-requirements');
    Route::view('semester-reports', 'pages.documents.semester-reports')->name('semester-reports');
    Route::view('final-reports', 'pages.documents.final-reports')->name('final-reports');
    Route::view('study-approvals', 'pages.documents.study-approvals')->name('study-approvals');
});

// Administrasi Dokumen Routes - Non-Lecturer Only
Route::prefix('administrations')->name('administrations.')->middleware(['auth', 'verified', 'non.lecturer.only'])->group(function () {
    Route::view('lecturers', 'pages.administrations.lecturer')->name('lecturers');
    Route::view('upload', 'pages.administrations.upload')->name('upload');
    Route::view('verification', 'pages.administrations.verification')->name('verification');
});

// Administration Routes - Management roles
Route::prefix('administration')->name('administration.')->middleware(['auth', 'verified', 'non.lecturer.only'])->group(function () {
    Route::view('approval', 'pages.administration.approval')
        ->name('approval')
        ->middleware('role:head_of_study_program,head_of_research_group,fri_vice_dean,hr_finance_staff');
});

// Monitoring & Laporan Routes - Non-Lecturer Only
Route::prefix('monitoring')->name('monitoring.')->middleware(['auth', 'verified', 'non.lecturer.only'])->group(function () {
    Route::view('activity', 'pages.monitoring.activity')->name('activity');
    Route::view('document-status', 'pages.monitoring.document-status')->name('document-status');
    Route::view('audit-log', 'pages.monitoring.audit-log')->name('audit-log');
});

// Notifikasi, Help, Settings Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('notifications', 'pages.notifications.notification')->name('notifications');
    Route::view('help', 'pages.help.help')->name('help');
    Route::view('settings', 'pages.settings.settings')->name('settings.index');
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::view('account', 'pages.settings.account')->name('account');
    });
});

// Studi Kalender routes
Route::prefix('study-calendar')->name('study-calendar.')->middleware(['auth', 'verified'])->group(function () {
    Route::view('manage', 'pages.study-calendar.manage')->name('manage');
    Route::view('create', 'pages.study-calendar.create')->name('create');

    Route::view('approval', 'pages.study-calendar.approval')->name('approval');
});