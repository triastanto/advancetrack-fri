<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Main Routes
Route::view('dashboard', 'dashboard')->name('dashboard');
Route::view('lecturer', 'lecturer')->name('lecturer');
Route::view('verification', 'verification')->name('verification');

// Data Pribadi Routes
Route::view('profile','profile.index')->name('lecturer.profile');
Route::view('profile/education', 'profile.education')->name('profile.education');
Route::view('profile/contact', 'profile.contact')->name('profile.contact');

// Dokumen Saya Routes
Route::prefix('documents')->name('documents.')->group(function () {
    Route::view('study-requirements', 'documents.study-requirements')->name('study-requirements');
    Route::view('semester-reports', 'documents.semester-reports')->name('semester-reports');
    Route::view('final-reports', 'documents.final-reports')->name('final-reports');
    Route::view('additional', 'documents.additional')->name('additional');
    Route::view('service-bond', 'documents.service-bond')->name('service-bond');
});

// Administrasi Dokumen Routes
Route::prefix('admin/documents')->name('admin.documents.')->group(function () {
    Route::view('lecturers', 'admin.documents.lecturers')->name('lecturers');
    Route::view('upload', 'admin.documents.upload')->name('upload');
});

// Monitoring & Laporan Routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::view('/', 'reports.index')->name('index');
    Route::view('verification-status', 'reports.verification-status')->name('verification-status');
    Route::view('activity-logs', 'reports.activity-logs')->name('activity-logs');
});

// Notifikasi Route
Route::view('notifications', 'notifications.index')->name('notifications');

// Pengaturan Routes
Route::prefix('settings')->name('settings.')->group(function() {
    Route::view('/', 'settings.index')->name('index');
    Route::view('account', 'settings.account')->name('account');
    Route::view('password', 'settings.password')->name('password');
    Route::view('notifications', 'settings.notifications')->name('notifications');
});