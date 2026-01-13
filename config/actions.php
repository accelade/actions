<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Asset Serving Mode
    |--------------------------------------------------------------------------
    |
    | Determines how Actions serves its JavaScript assets.
    | 'route' - Serve via Laravel route (default, no publishing needed)
    | 'published' - Serve from published assets in public/vendor/actions
    |
    */
    'asset_mode' => env('ACTIONS_ASSET_MODE', 'route'),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The URL prefix for Actions routes.
    |
    */
    'prefix' => 'actions',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware to apply to Actions routes.
    |
    */
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Default Colors (Light & Dark Mode Support)
    |--------------------------------------------------------------------------
    |
    | Default colors for action buttons. These map to Tailwind CSS classes
    | with full support for light and dark modes.
    |
    */
    'colors' => [
        'primary' => [
            'bg' => 'bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600',
            'text' => 'text-white',
            'border' => 'border-indigo-600 dark:border-indigo-500',
            'outline' => 'border-indigo-600 text-indigo-600 hover:bg-indigo-50 dark:border-indigo-400 dark:text-indigo-400 dark:hover:bg-indigo-950',
            'link' => 'text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300',
            'focus' => 'focus:ring-indigo-500 dark:focus:ring-indigo-400',
        ],
        'secondary' => [
            'bg' => 'bg-gray-600 hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600',
            'text' => 'text-white',
            'border' => 'border-gray-600 dark:border-gray-500',
            'outline' => 'border-gray-600 text-gray-600 hover:bg-gray-50 dark:border-gray-400 dark:text-gray-400 dark:hover:bg-gray-800',
            'link' => 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300',
            'focus' => 'focus:ring-gray-500 dark:focus:ring-gray-400',
        ],
        'success' => [
            'bg' => 'bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600',
            'text' => 'text-white',
            'border' => 'border-green-600 dark:border-green-500',
            'outline' => 'border-green-600 text-green-600 hover:bg-green-50 dark:border-green-400 dark:text-green-400 dark:hover:bg-green-950',
            'link' => 'text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300',
            'focus' => 'focus:ring-green-500 dark:focus:ring-green-400',
        ],
        'danger' => [
            'bg' => 'bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600',
            'text' => 'text-white',
            'border' => 'border-red-600 dark:border-red-500',
            'outline' => 'border-red-600 text-red-600 hover:bg-red-50 dark:border-red-400 dark:text-red-400 dark:hover:bg-red-950',
            'link' => 'text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300',
            'focus' => 'focus:ring-red-500 dark:focus:ring-red-400',
        ],
        'warning' => [
            'bg' => 'bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-400 dark:hover:bg-yellow-500',
            'text' => 'text-white dark:text-gray-900',
            'border' => 'border-yellow-500 dark:border-yellow-400',
            'outline' => 'border-yellow-500 text-yellow-600 hover:bg-yellow-50 dark:border-yellow-400 dark:text-yellow-400 dark:hover:bg-yellow-950',
            'link' => 'text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300',
            'focus' => 'focus:ring-yellow-500 dark:focus:ring-yellow-400',
        ],
        'info' => [
            'bg' => 'bg-cyan-500 hover:bg-cyan-600 dark:bg-cyan-400 dark:hover:bg-cyan-500',
            'text' => 'text-white dark:text-gray-900',
            'border' => 'border-cyan-500 dark:border-cyan-400',
            'outline' => 'border-cyan-500 text-cyan-600 hover:bg-cyan-50 dark:border-cyan-400 dark:text-cyan-400 dark:hover:bg-cyan-950',
            'link' => 'text-cyan-600 hover:text-cyan-800 dark:text-cyan-400 dark:hover:text-cyan-300',
            'focus' => 'focus:ring-cyan-500 dark:focus:ring-cyan-400',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Icon Set
    |--------------------------------------------------------------------------
    |
    | The default icon set to use. Currently supports 'lucide'.
    | Icons can be overridden per-action using the icon() method.
    |
    */
    'icon_set' => 'lucide',

    /*
    |--------------------------------------------------------------------------
    | Modal Configuration
    |--------------------------------------------------------------------------
    |
    | Default modal settings for confirmation dialogs.
    |
    */
    'modal' => [
        'default_width' => 'md',
        'backdrop_close' => true,
        'animation' => 'fade',
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo Routes
    |--------------------------------------------------------------------------
    |
    | Enable demo routes for testing actions.
    |
    */
    'demo' => [
        'enabled' => env('ACTIONS_DEMO_ENABLED', env('APP_ENV') !== 'production'),
        'prefix' => 'actions-demo',
        'middleware' => ['web'],
    ],
];
