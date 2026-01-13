<?php

namespace Accelade\Actions\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'actions:install
                            {--force : Overwrite existing files}';

    protected $description = 'Install the Actions package';

    public function handle(): int
    {
        $this->info('Installing Actions...');

        // Publish config
        $this->call('vendor:publish', [
            '--tag' => 'actions-config',
            '--force' => $this->option('force'),
        ]);

        // Publish assets
        $this->call('vendor:publish', [
            '--tag' => 'actions-assets',
            '--force' => $this->option('force'),
        ]);

        $this->info('Actions installed successfully!');
        $this->newLine();
        $this->info('Add @actionsScripts to your layout to start using actions.');

        return self::SUCCESS;
    }
}
