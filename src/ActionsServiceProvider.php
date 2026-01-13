<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Accelade\Docs\DocsRegistry;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ActionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/actions.php',
            'actions'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'actions');

        // Load views under 'accelade' namespace to extend the main Accelade package
        // This allows components to be used as <x-accelade::action />
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accelade');

        // Also load under 'actions' namespace for scripts/styles views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'actions');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Register Blade directives
        $this->registerBladeDirectives();

        // Inject scripts/styles into Accelade (if available)
        $this->injectAcceladeAssets();

        // Register documentation sections
        $this->registerDocumentation();

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__.'/../config/actions.php' => config_path('actions.php'),
            ], 'actions-config');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/accelade'),
            ], 'actions-views');

            // Publish assets
            $this->publishes([
                __DIR__.'/../dist' => public_path('vendor/actions'),
            ], 'actions-assets');

            // Publish translations
            $this->publishes([
                __DIR__.'/../lang' => lang_path('vendor/actions'),
            ], 'actions-lang');

            // Register commands
            $this->commands([
                Console\InstallCommand::class,
                Console\MakeActionCommand::class,
            ]);
        }
    }

    /**
     * Register Blade directives for actions.
     */
    protected function registerBladeDirectives(): void
    {
        // @actionsScripts - Include the actions JavaScript (fallback if not using @acceladeScripts)
        Blade::directive('actionsScripts', function () {
            return "<?php echo view('actions::scripts')->render(); ?>";
        });

        // @actionsStyles - Include the actions CSS (fallback if not using @acceladeStyles)
        Blade::directive('actionsStyles', function () {
            return "<?php echo view('actions::styles')->render(); ?>";
        });
    }

    /**
     * Inject Actions scripts and styles into Accelade.
     * This allows Actions assets to be included automatically with @acceladeScripts/@acceladeStyles.
     */
    protected function injectAcceladeAssets(): void
    {
        // Only inject if Accelade is available
        if (! $this->app->bound('accelade')) {
            return;
        }

        /** @var \Accelade\Accelade $accelade */
        $accelade = $this->app->make('accelade');

        // Inject Actions JavaScript
        $accelade->registerScript('actions', function () {
            return view('actions::scripts')->render();
        });

        // Inject Actions CSS
        $accelade->registerStyle('actions', function () {
            return view('actions::styles')->render();
        });
    }

    /**
     * Register documentation sections with Accelade's DocsRegistry.
     */
    protected function registerDocumentation(): void
    {
        // Only register if Accelade's DocsRegistry is available
        if (! $this->app->bound('accelade.docs')) {
            return;
        }

        /** @var DocsRegistry $registry */
        $registry = $this->app->make('accelade.docs');

        // Register the actions package docs path
        $registry->registerPackage('actions', __DIR__.'/../docs');

        // Register navigation group for Actions
        $registry->registerGroup('actions', 'Actions', '⚡', 30);

        // Register documentation sections
        $registry->section('actions')
            ->label('Actions')
            ->icon('👆')
            ->markdown('actions.md')
            ->description('Filament-style action buttons for Blade')
            ->keywords(['action', 'button', 'click', 'execute', 'filament'])
            ->demo()
            ->view('actions::docs.sections.actions')
            ->package('actions')
            ->inGroup('actions')
            ->register();

        $registry->section('action-modals')
            ->label('Confirmation Modals')
            ->icon('⚠️')
            ->markdown('modals.md')
            ->description('Confirmation dialogs and modals for actions')
            ->keywords(['modal', 'confirmation', 'dialog', 'confirm', 'danger'])
            ->demo()
            ->view('actions::docs.sections.modals')
            ->package('actions')
            ->inGroup('actions')
            ->register();

        $registry->section('action-groups')
            ->label('Action Groups')
            ->icon('📚')
            ->markdown('action-groups.md')
            ->description('Dropdown menus of multiple actions')
            ->keywords(['group', 'dropdown', 'menu', 'multiple'])
            ->demo()
            ->view('actions::docs.sections.action-groups')
            ->package('actions')
            ->inGroup('actions')
            ->register();

        $registry->section('action-api')
            ->label('API Reference')
            ->icon('📖')
            ->markdown('api-reference.md')
            ->description('Complete API reference for Actions')
            ->keywords(['api', 'reference', 'methods', 'properties'])
            ->package('actions')
            ->inGroup('actions')
            ->register();
    }
}
