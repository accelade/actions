<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    {{-- Demo Content --}}
    <div class="space-y-8">
        {{-- Basic Confirmation --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic Confirmation</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Click to see a confirmation dialog before the action executes.</p>
            <div class="flex flex-wrap gap-4">
                @php
                    $confirmAction = \Accelade\Actions\Action::make('confirm-demo')
                        ->label('Confirm Action')
                        ->icon('alert-circle')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Confirm Action')
                        ->modalDescription('Are you sure you want to proceed with this action?')
                        ->action(function () {
                            \Accelade\Facades\Notify::success('Confirmed!', 'You confirmed the action successfully.');
                            return 'Action confirmed and executed!';
                        });
                @endphp

                <x-accelade::action :action="$confirmAction" />
            </div>
        </div>

        {{-- Danger Confirmation --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Danger Confirmation</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Danger confirmations use a red color scheme to indicate destructive actions.
            </p>
            <div class="flex flex-wrap gap-4">
                @php
                    $deleteAction = \Accelade\Actions\DeleteAction::make('delete-item')
                        ->modalDescription('This action cannot be undone. Are you sure you want to delete this item?')
                        ->action(function () {
                            \Accelade\Facades\Notify::danger('Deleted', 'The item has been permanently deleted.');
                            return 'Item deleted successfully';
                        });
                @endphp

                <x-accelade::action :action="$deleteAction" />
            </div>
        </div>

        {{-- Custom Modal Content --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Custom Modal Text</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Customize the modal heading, description, and button labels.</p>
            <div class="flex flex-wrap gap-4">
                @php
                    $publishAction = \Accelade\Actions\Action::make('publish')
                        ->label('Publish')
                        ->icon('upload')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Publish to Production')
                        ->modalDescription('This will make your changes visible to all users. Are you ready to publish?')
                        ->modalSubmitActionLabel('Yes, Publish Now')
                        ->modalCancelActionLabel('Not Yet')
                        ->action(function () {
                            \Accelade\Facades\Notify::success('Published!', 'Your changes are now live in production.');
                            return 'Published successfully!';
                        });

                    $archiveAction = \Accelade\Actions\Action::make('archive')
                        ->label('Archive')
                        ->icon('archive')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Item')
                        ->modalDescription('This item will be moved to the archive. You can restore it later.')
                        ->modalSubmitActionLabel('Archive It')
                        ->modalCancelActionLabel('Keep Active')
                        ->action(function () {
                            \Accelade\Facades\Notify::warning('Archived', 'The item has been moved to archive.');
                            return 'Item archived';
                        });
                @endphp

                <x-accelade::action :action="$publishAction" />
                <x-accelade::action :action="$archiveAction" />
            </div>
        </div>

        {{-- Confirmation with URL --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Confirmation Before Navigation</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Show a confirmation dialog before navigating to a URL.</p>
            <div class="flex flex-wrap gap-4">
                @php
                    $logoutAction = \Accelade\Actions\Action::make('logout')
                        ->label('Logout')
                        ->icon('log-out')
                        ->color('secondary')
                        ->outlined()
                        ->requiresConfirmation()
                        ->modalHeading('Sign Out')
                        ->modalDescription('Are you sure you want to sign out? You will need to log in again to access your account.')
                        ->modalSubmitActionLabel('Sign Out')
                        ->url('/');
                @endphp

                <x-accelade::action :action="$logoutAction" />
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
