<?php

use Accelade\Actions\Http\Controllers\ActionController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('actions.middleware', ['web']))
    ->prefix(config('actions.prefix', 'actions'))
    ->group(function () {
        // Action execution endpoint
        Route::post('/execute', [ActionController::class, 'execute'])
            ->name('actions.execute');

        // Asset routes (when asset_mode is 'route')
        if (config('actions.asset_mode', 'route') === 'route') {
            Route::get('/js/actions.js', function () {
                $path = __DIR__.'/../dist/actions.js';
                if (! file_exists($path)) {
                    abort(404, 'Actions JS not found. Run: npm run build');
                }

                return response()->file($path, [
                    'Content-Type' => 'application/javascript',
                    'Cache-Control' => 'public, max-age=31536000',
                ]);
            })->name('actions.js');

            Route::get('/js/actions.esm.js', function () {
                $path = __DIR__.'/../dist/actions.esm.js';
                if (! file_exists($path)) {
                    abort(404, 'Actions ESM JS not found. Run: npm run build');
                }

                return response()->file($path, [
                    'Content-Type' => 'application/javascript',
                    'Cache-Control' => 'public, max-age=31536000',
                ]);
            })->name('actions.esm.js');
        }
    });

// Demo routes
if (config('actions.demo.enabled', false)) {
    Route::middleware(config('actions.demo.middleware', ['web']))
        ->prefix(config('actions.demo.prefix', 'actions-demo'))
        ->group(function () {
            Route::get('/', function () {
                return view('actions::demo.index');
            })->name('actions.demo');
        });
}
