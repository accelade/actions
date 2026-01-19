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
        if (! $this->app->bound('accelade.docs')) {
            return;
        }

        /** @var DocsRegistry $registry */
        $registry = $this->app->make('accelade.docs');

        $registry->registerPackage('actions', __DIR__.'/../docs');
        $registry->registerGroup('actions', 'Actions', '⚡', 25);

        // Register sub-groups within Actions
        $registry->registerSubgroup('actions', 'core', '🎯 Core', '', 10);
        $registry->registerSubgroup('actions', 'crud', '📝 CRUD', '', 20);
        $registry->registerSubgroup('actions', 'utility', '🔧 Utility', '', 30);
        $registry->registerSubgroup('actions', 'reference', '📚 Reference', '', 40);

        foreach ($this->getDocumentationSections() as $section) {
            $this->registerDocSection($registry, $section);
        }
    }

    /**
     * Register a single documentation section.
     *
     * @param  array<string, mixed>  $section
     */
    protected function registerDocSection(DocsRegistry $registry, array $section): void
    {
        $builder = $registry->section($section['id'])
            ->label($section['label'])
            ->icon($section['icon'])
            ->markdown($section['markdown'])
            ->description($section['description'])
            ->keywords($section['keywords'])
            ->package('actions')
            ->inGroup('actions');

        if (isset($section['subgroup'])) {
            $builder->inSubgroup($section['subgroup']);
        }

        if ($section['demo'] ?? false) {
            $builder->demo()->view($section['view']);
        }

        $builder->register();
    }

    /**
     * Get all documentation section definitions.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getDocumentationSections(): array
    {
        return [
            ...$this->getCoreDocSections(),
            ...$this->getCrudDocSections(),
            ...$this->getUtilityDocSections(),
        ];
    }

    /**
     * Get core documentation sections.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getCoreDocSections(): array
    {
        return [
            // Main entry - no subgroup
            ['id' => 'actions', 'label' => 'Actions', 'icon' => '👆', 'markdown' => 'actions.md', 'description' => 'Filament-style action buttons for Blade', 'keywords' => ['action', 'button', 'click', 'execute', 'filament'], 'demo' => true, 'view' => 'actions::docs.sections.actions'],
            // Core subgroup
            ['id' => 'action-modals', 'label' => 'Confirmation Modals', 'icon' => '⚠️', 'markdown' => 'modals.md', 'description' => 'Confirmation dialogs and modals for actions', 'keywords' => ['modal', 'confirmation', 'dialog', 'confirm', 'danger'], 'subgroup' => 'core', 'demo' => true, 'view' => 'actions::docs.sections.modals'],
            ['id' => 'action-groups', 'label' => 'Action Groups', 'icon' => '📚', 'markdown' => 'action-groups.md', 'description' => 'Dropdown menus of multiple actions', 'keywords' => ['group', 'dropdown', 'menu', 'multiple'], 'subgroup' => 'core', 'demo' => true, 'view' => 'actions::docs.sections.action-groups'],
            ['id' => 'bulk-actions', 'label' => 'Bulk Actions', 'icon' => '📦', 'markdown' => 'bulk-actions.md', 'description' => 'Actions that operate on multiple selected records', 'keywords' => ['bulk', 'batch', 'multiple', 'mass', 'selection'], 'subgroup' => 'core', 'demo' => true, 'view' => 'actions::docs.sections.bulk-actions'],
        ];
    }

    /**
     * Get CRUD documentation sections.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getCrudDocSections(): array
    {
        return [
            ['id' => 'create-action', 'label' => 'Create Action', 'icon' => '➕', 'markdown' => 'create-action.md', 'description' => 'Preset action for creating new records', 'keywords' => ['create', 'add', 'new', 'insert', 'preset'], 'subgroup' => 'crud', 'demo' => true, 'view' => 'actions::docs.sections.create-action'],
            ['id' => 'edit-action', 'label' => 'Edit Action', 'icon' => '✏️', 'markdown' => 'edit-action.md', 'description' => 'Preset action for editing existing records', 'keywords' => ['edit', 'update', 'modify', 'change', 'preset'], 'subgroup' => 'crud', 'demo' => true, 'view' => 'actions::docs.sections.edit-action'],
            ['id' => 'delete-action', 'label' => 'Delete Action', 'icon' => '🗑️', 'markdown' => 'delete-action.md', 'description' => 'Preset action for deleting records with confirmation', 'keywords' => ['delete', 'remove', 'destroy', 'trash', 'preset', 'confirmation'], 'subgroup' => 'crud', 'demo' => true, 'view' => 'actions::docs.sections.delete-action'],
            ['id' => 'view-action', 'label' => 'View Action', 'icon' => '👁️', 'markdown' => 'view-action.md', 'description' => 'Preset action for viewing record details', 'keywords' => ['view', 'show', 'display', 'details', 'preview', 'preset'], 'subgroup' => 'crud', 'demo' => true, 'view' => 'actions::docs.sections.view-action'],
            ['id' => 'force-delete-action', 'label' => 'Force Delete Action', 'icon' => '💀', 'markdown' => 'force-delete-action.md', 'description' => 'Permanently delete soft-deleted records', 'keywords' => ['force', 'delete', 'permanent', 'trash', 'soft-delete'], 'subgroup' => 'crud', 'demo' => true, 'view' => 'actions::docs.sections.force-delete-action'],
            ['id' => 'restore-action', 'label' => 'Restore Action', 'icon' => '♻️', 'markdown' => 'restore-action.md', 'description' => 'Restore soft-deleted records', 'keywords' => ['restore', 'undelete', 'recover', 'trash', 'soft-delete'], 'subgroup' => 'crud', 'demo' => true, 'view' => 'actions::docs.sections.restore-action'],
            ['id' => 'replicate-action', 'label' => 'Replicate Action', 'icon' => '📋', 'markdown' => 'replicate-action.md', 'description' => 'Duplicate existing records', 'keywords' => ['replicate', 'duplicate', 'copy', 'clone'], 'subgroup' => 'crud', 'demo' => true, 'view' => 'actions::docs.sections.replicate-action'],
        ];
    }

    /**
     * Get utility documentation sections.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getUtilityDocSections(): array
    {
        return [
            ['id' => 'export-action', 'label' => 'Export Action', 'icon' => '📤', 'markdown' => 'export-action.md', 'description' => 'Export data to various formats', 'keywords' => ['export', 'download', 'excel', 'csv', 'pdf'], 'subgroup' => 'utility', 'demo' => true, 'view' => 'actions::docs.sections.export-action'],
            ['id' => 'import-action', 'label' => 'Import Action', 'icon' => '📥', 'markdown' => 'import-action.md', 'description' => 'Import data from files', 'keywords' => ['import', 'upload', 'excel', 'csv', 'file', 'plaintext', 'txt'], 'subgroup' => 'utility', 'demo' => true, 'view' => 'actions::docs.sections.import-action'],
            ['id' => 'print-action', 'label' => 'Print Action', 'icon' => '🖨️', 'markdown' => 'print-action.md', 'description' => 'Print the current page, elements, or custom content', 'keywords' => ['print', 'printer', 'page', 'document', 'pdf'], 'subgroup' => 'utility', 'demo' => true, 'view' => 'actions::docs.sections.print-action'],
            ['id' => 'copy-action', 'label' => 'Copy Action', 'icon' => '📋', 'markdown' => 'copy-action.md', 'description' => 'Copy values to clipboard', 'keywords' => ['copy', 'clipboard', 'paste', 'text'], 'subgroup' => 'utility', 'demo' => true, 'view' => 'actions::docs.sections.copy-action'],
            ['id' => 'action-api', 'label' => 'API Reference', 'icon' => '📖', 'markdown' => 'api-reference.md', 'description' => 'Complete API reference for Actions', 'keywords' => ['api', 'reference', 'methods', 'properties'], 'demo' => false, 'view' => 'actions::docs.sections.action-api', 'subgroup' => 'reference'],
        ];
    }
}
