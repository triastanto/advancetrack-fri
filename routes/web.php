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

Route::middleware('auth')->group(function () {
    Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'destroy'])->name('logout');
});

Route::get('/', function () {
    return view('welcome');
});

// Main Routes
Route::middleware('auth')->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('lecturer', 'lecturer')->name('lecturer');
    Route::view('verification', 'pages.administrations.verification')->name('verification');

    // Data Pribadi Routes
    Route::view('profile','profile.index')->name('profile.index');
    Route::view('profile/education', 'profile.education')->name('profile.education');
    Route::view('profile/contact', 'profile.contact')->name('profile.contact');
});

// Dokumen Saya Routes
Route::prefix('documents')->name('documents.')->middleware('auth')->group(function () {
    Route::view('study-requirements', 'pages.documents.study-requirements')->name('study-requirements');
    Route::view('semester-reports', 'pages.documents.semester-reports')->name('semester-reports');
    Route::view('final-reports', 'pages.documents.final-reports')->name('final-reports');
    Route::view('fsdp-documents', 'pages.documents.fsdp-documents')->name('fsdp-documents');
});

// Administrasi Dokumen Routes
Route::prefix('administrations')->name('administrations.')->middleware('auth')->group(function () {
    Route::view('lecturers', 'pages.administrations.lecturers')->name('lecturers');
    Route::view('upload', 'pages.administrations.upload')->name('upload');
    Route::view('verification', 'pages.administrations.verification')->name('verification');
});

// Monitoring & Laporan Routes
Route::prefix('reports')->name('reports.')->middleware('auth')->group(function () {
    Route::view('/', 'reports.index')->name('index');
    Route::view('verification-status', 'reports.verification-status')->name('verification-status');
    Route::view('activity-logs', 'reports.activity-logs')->name('activity-logs');
});

// Notifikasi Route
Route::view('notifications', 'notifications.index')->name('notifications')->middleware('auth');

// Pengaturan Routes
Route::prefix('settings')->name('settings.')->middleware('auth')->group(function() {
    Route::view('/', 'settings.index')->name('index');
    Route::view('account', 'settings.account')->name('account');
    Route::view('password', 'settings.password')->name('password');
    Route::view('notifications', 'settings.notifications')->name('notifications');
});