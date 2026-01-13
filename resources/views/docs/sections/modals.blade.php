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
            <div class="flex flex-wrap gap-4">
                @php
                    $confirmAction = \Accelade\Actions\Action::make('confirm-demo')
                        ->label('Confirm Action')
                        ->icon('alert-circle')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Confirm Action')
                        ->modalDescription('Are you sure you want to proceed?')
                        ->url('#');
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
                        ->modalDescription('This action cannot be undone. Are you sure?')
                        ->url('#');
                @endphp

                <x-accelade::action :action="$deleteAction" />
            </div>
        </div>

        {{-- Custom Modal Content --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Custom Modal Text</h3>
            <div class="flex flex-wrap gap-4">
                @php
                    $customAction = \Accelade\Actions\Action::make('publish')
                        ->label('Publish')
                        ->icon('upload')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Publish to Production')
                        ->modalDescription('This will make your changes visible to all users.')
                        ->modalSubmitActionLabel('Yes, Publish')
                        ->modalCancelActionLabel('Not Yet')
                        ->url('#');
                @endphp

                <x-accelade::action :action="$customAction" />
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
