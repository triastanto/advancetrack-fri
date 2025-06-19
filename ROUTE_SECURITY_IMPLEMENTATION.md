# Route Security & Authorization Implementation

## Overview
This document outlines the comprehensive security measures implemented to protect unauthorized users from accessing hidden URLs and restricted functionality.

## Security Layers Implemented

### 1. **Frontend Authorization (Sidebar)**
- **Purpose**: Hide menu items based on user roles
- **Location**: `resources/views/components/layouts/sidebar.blade.php`
- **Mechanism**: Blade conditionals using `Auth::user()->employee->role`

### 2. **Middleware Protection (Backend)**
- **Purpose**: Server-side route protection with automatic redirection/blocking
- **Files Created**:
  - `app/Http/Middleware/RoleBasedAccess.php` - Generic role-based middleware
  - `app/Http/Middleware/LecturerOnly.php` - Lecturer-specific access
  - `app/Http/Middleware/NonLecturerOnly.php` - Administrative access

### 3. **Route-Level Security**
- **Purpose**: Apply middleware protection to route groups
- **Location**: `routes/web.php`
- **Implementation**: Middleware applied to route groups based on access requirements

### 4. **Gate-Based Authorization**
- **Purpose**: Fine-grained permission checking
- **Location**: `app/Providers/AuthServiceProvider.php`
- **Features**: Role-based gates for specific functionality

### 5. **Automated Testing**
- **Purpose**: Verify security measures work correctly
- **Location**: `tests/Feature/RouteSecurityTest.php`
- **Coverage**: All role combinations and access scenarios

## Security Implementation Details

### Middleware Configuration

#### Generic Role Middleware (`RoleBasedAccess`)
```php
// Usage: middleware('role:hr_finance_staff,fri_vice_dean')
// Allows multiple roles to access a route
```

#### Specific Role Middleware
```php
// LecturerOnly - Only lecturers can access
middleware('lecturer.only')

// NonLecturerOnly - Only administrative roles can access
middleware('non.lecturer.only')
```

### Route Protection Examples

#### Lecturer-Only Routes
```php
Route::middleware(['auth', 'lecturer.only'])->group(function () {
    Route::view('profile', 'profile.index')->name('profile.index');
    Route::prefix('documents')->group(function () {
        Route::view('study-requirements', '...')->name('documents.study-requirements');
        // ... other lecturer routes
    });
});
```

#### Administrative Routes
```php
Route::middleware(['auth', 'non.lecturer.only'])->group(function () {
    Route::prefix('administrations')->group(function () {
        Route::view('upload', '...')->name('administrations.upload');
        // ... other admin routes
    });
});
```

### Authorization Gates

#### Role-Based Gates
```php
Gate::define('lecturer-access', function (User $user) {
    return $user->employee && $user->employee->role === 'lecturer';
});

Gate::define('administrative-access', function (User $user) {
    // Check for administrative roles
});
```

#### Usage in Controllers/Views
```php
// In controllers
if (!Gate::allows('lecturer-access')) {
    abort(403);
}

// In Blade templates
@can('administrative-access')
    <!-- Administrative content -->
@endcan
```

## Security Response Behaviors

### Unauthorized Access Attempts

#### 1. **Unauthenticated Users**
- **Response**: Redirect to login page
- **Message**: "Silakan login terlebih dahulu."
- **HTTP Status**: 302 (Redirect)

#### 2. **Users Without Employee Data**
- **Response**: 403 Forbidden
- **Message**: "Data karyawan tidak ditemukan. Hubungi administrator."
- **HTTP Status**: 403

#### 3. **Users With Wrong Role**
- **Response**: 403 Forbidden
- **Messages**:
  - Lecturer trying admin routes: "Akses terbatas untuk staf administrasi saja."
  - Admin trying lecturer routes: "Akses terbatas untuk dosen saja."
- **HTTP Status**: 403

### Error Handling Best Practices

#### Graceful Error Messages
- Clear, user-friendly messages in Indonesian
- No technical details exposed to users
- Appropriate HTTP status codes
- Logging for security monitoring

#### Fallback Mechanisms
- Default menu items for users without proper roles
- Graceful degradation of functionality
- Help links for confused users

## Role-Based Access Matrix

| Route Group | Lecturer | HR Staff | HR Head | Study Head | Research Head | Vice Dean |
|-------------|----------|----------|---------|------------|---------------|-----------|
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Notifications | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Profile (Data Pribadi) | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Documents (Dokumen Saya) | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Administration | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Reports & Monitoring | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |

## Security Testing

### Automated Test Coverage
```php
// Test cases included:
✅ Lecturer can access personal routes
✅ Lecturer cannot access administrative routes
✅ HR staff can access administrative routes
✅ HR staff cannot access lecturer routes
✅ Unauthenticated users redirected to login
✅ Users without employee data get 403
✅ Vice dean has proper administrative access
```

### Manual Testing Checklist
- [ ] Test direct URL access for each role
- [ ] Verify 403 errors display properly
- [ ] Check login redirects work correctly
- [ ] Validate sidebar items match permissions
- [ ] Test edge cases (no employee data, etc.)

## Security Monitoring

### Logging Strategy
- Failed authorization attempts logged with user details
- Security events tracked for audit purposes
- Unusual access patterns detected and reported

### Recommended Monitoring
```php
// Log unauthorized access attempts
Log::warning('Unauthorized access attempt', [
    'user_id' => auth()->id(),
    'role' => auth()->user()->employee?->role,
    'attempted_route' => request()->route()->getName(),
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]);
```

## Best Practices Applied

### 1. **Defense in Depth**
- Multiple security layers (frontend + backend)
- Route-level protection with middleware
- Gate-based fine-grained control

### 2. **Principle of Least Privilege**
- Users only see/access what they need
- Role-based access restrictions
- Clear separation of concerns

### 3. **Fail Secure**
- Default deny approach
- Explicit role checking
- Graceful error handling

### 4. **Auditability**
- Comprehensive logging
- Test coverage for security scenarios
- Clear documentation of access rules

## Future Security Enhancements

### Recommended Additions
1. **Rate Limiting**: Prevent brute force attacks on restricted routes
2. **IP Whitelisting**: Additional restrictions for sensitive areas
3. **Two-Factor Authentication**: Enhanced security for administrative roles
4. **Session Management**: Automatic logout for inactive sessions
5. **CSRF Protection**: Additional protection for state-changing operations

### Security Headers
```php
// Additional security headers to implement:
'X-Content-Type-Options' => 'nosniff',
'X-Frame-Options' => 'DENY',
'X-XSS-Protection' => '1; mode=block',
'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
```

## Conclusion

The implemented security measures provide comprehensive protection against unauthorized access through:

1. **Multi-layered approach** - Frontend hiding + Backend enforcement
2. **Role-based granular control** - Specific access rules per role
3. **Comprehensive testing** - Automated verification of security measures
4. **Clear error handling** - User-friendly messages and proper HTTP codes
5. **Audit capabilities** - Logging and monitoring for security events

Users attempting to access restricted URLs will be properly blocked with appropriate error messages, ensuring the system remains secure while providing a good user experience.
