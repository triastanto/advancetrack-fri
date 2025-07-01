<?php

use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'create'])->name('login');
    Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'store']);
    Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'create'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'store']);
    Route::get('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'store'])->name('password.email');
});

Route::middleware('auth')->post('logout', [App\Http\Controllers\Auth\LoginController::class, 'destroy'])->name('logout');

Route::get('/', fn () => view('welcome'));

// Main Routes
Route::middleware('auth')->group(function () {
    Route::view('dashboard', 'pages.dashboard.dashboard')->name('dashboard');
    Route::middleware('lecturer.only')->group(function () {
        Route::view('personal-data', 'pages.personal-data.personal-data')->name('personal-data');
    });
});

// Dokumen Saya Routes - Lecturer Only
Route::prefix('documents')->name('documents.')->middleware(['auth', 'lecturer.only'])->group(function () {
    Route::view('study-requirements', 'pages.documents.study-requirements')->name('study-requirements');
    Route::view('semester-reports', 'pages.documents.semester-reports')->name('semester-reports');
    Route::view('final-reports', 'pages.documents.final-reports')->name('final-reports');
    Route::view('study-approvals', 'pages.documents.study-approvals')->name('study-approvals');
});

// Administrasi Dokumen Routes - Non-Lecturer Only
Route::prefix('administrations')->name('administrations.')->middleware(['auth', 'non.lecturer.only'])->group(function () {
    Route::view('lecturers', 'pages.administrations.lecturer')->name('lecturers');
    Route::view('upload', 'pages.administrations.upload')->name('upload');
    Route::view('verification', 'pages.administrations.verification')->name('verification');
});

// Monitoring & Laporan Routes - Non-Lecturer Only
Route::prefix('monitoring')->name('monitoring.')->middleware(['auth', 'non.lecturer.only'])->group(function () {
    Route::view('analytics', 'pages.monitoring.analytics')->name('analytics');
    Route::view('activity', 'pages.monitoring.activity')->name('activity');
    Route::view('document-status', 'pages.monitoring.document-status')->name('document-status');
    Route::view('audit-log', 'pages.monitoring.audit-log')->name('audit-log');
});

// Notifikasi, Help, Settings Routes
Route::middleware('auth')->group(function () {
    Route::view('notifications', 'pages.notifications.notification')->name('notifications');
    Route::view('help', 'pages.help.help')->name('help');
    Route::view('settings', 'pages.settings.settings')->name('settings.index');
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::view('account', 'pages.settings.account')->name('account');
    });
});