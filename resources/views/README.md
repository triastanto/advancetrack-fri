# Views Directory Structure

This directory contains all the Blade views for the application. The views are organized following Laravel and Livewire best practices.

## Directory Structure

- `layouts/` - Contains the main layout templates
- `livewire/` - Contains Livewire component views
- `components/` - Contains reusable Blade components
- `auth/` - Contains authentication-related views
- `partials/` - Contains reusable view partials

## Livewire Component Pattern

The application follows a consistent pattern for Livewire components:

1. **Main Blade File** (e.g., `verification.blade.php`):
   - Located in the root of the views directory
   - Extends the main layout
   - Only contains the Livewire component inclusion
   ```php
   @extends('layouts.app')
   @section('content')
   <livewire:component-name />
   @endsection
   ```

2. **Livewire Component View** (e.g., `livewire/verification.blade.php`):
   - Located in the `livewire/` directory
   - Contains the actual component markup and logic
   - Follows Livewire's component structure

## Reusable Components

The application includes several reusable components to maintain consistent styling:

1. **Page Container** (`components/page-container.blade.php`):
   - Provides consistent page padding and title styling
   - Usage:
   ```php
   <x-page-container title="Page Title">
       <!-- Content -->
   </x-page-container>
   ```

2. **Card** (`components/card.blade.php`):
   - Provides consistent card styling with variants
   - Props:
     - `variant`: 'default' | 'primary' | 'table'
     - `padding`: 'p-6' | 'p-8' | etc.
     - `class`: Additional classes
   - Usage:
   ```php
   <x-card variant="default" padding="p-6">
       <!-- Card content -->
   </x-card>
   ```

## Best Practices

1. Keep main blade files minimal and focused on layout inheritance
2. Place all component-specific markup in the Livewire component views
3. Use consistent naming between component classes and views
4. Follow the established directory structure for new components
5. Use the provided reusable components for consistent styling
6. Maintain consistent spacing and layout patterns across views 