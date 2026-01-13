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
    | Default Colors
    |--------------------------------------------------------------------------
    |
    | Default colors for action buttons. These map to Tailwind CSS classes.
    |
    */
    'colors' => [
        'primary' => [
            'bg' => 'bg-indigo-600 hover:bg-indigo-700',
            'text' => 'text-white',
            'border' => 'border-indigo-600',
            'outline' => 'border-indigo-600 text-indigo-600 hover:bg-indigo-50',
        ],
        'secondary' => [
            'bg' => 'bg-gray-600 hover:bg-gray-700',
            'text' => 'text-white',
            'border' => 'border-gray-600',
            'outline' => 'border-gray-600 text-gray-600 hover:bg-gray-50',
        ],
        'success' => [
            'bg' => 'bg-green-600 hover:bg-green-700',
            'text' => 'text-white',
            'border' => 'border-green-600',
            'outline' => 'border-green-600 text-green-600 hover:bg-green-50',
        ],
        'danger' => [
            'bg' => 'bg-red-600 hover:bg-red-700',
            'text' => 'text-white',
            'border' => 'border-red-600',
            'outline' => 'border-red-600 text-red-600 hover:bg-red-50',
        ],
        'warning' => [
            'bg' => 'bg-yellow-500 hover:bg-yellow-600',
            'text' => 'text-white',
            'border' => 'border-yellow-500',
            'outline' => 'border-yellow-500 text-yellow-600 hover:bg-yellow-50',
        ],
        'info' => [
            'bg' => 'bg-cyan-500 hover:bg-cyan-600',
            'text' => 'text-white',
            'border' => 'border-cyan-500',
            'outline' => 'border-cyan-500 text-cyan-600 hover:bg-cyan-50',
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
