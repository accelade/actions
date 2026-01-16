<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    {{-- Demo Content --}}
    <div class="space-y-8">
        {{-- Basic Action Group --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Dropdown Action Group</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Click the button to see a dropdown menu of actions.</p>
            <div class="flex justify-end">
                @php
                    $actionGroup = \Accelade\Actions\ActionGroup::make([
                        \Accelade\Actions\ViewAction::make()
                            ->action(fn () => \Accelade\Facades\Notify::info('View', 'Viewing item...')),
                        \Accelade\Actions\EditAction::make()
                            ->action(fn () => \Accelade\Facades\Notify::info('Edit', 'Opening editor...')),
                        \Accelade\Actions\DeleteAction::make()
                            ->action(fn () => \Accelade\Facades\Notify::danger('Deleted', 'Item deleted.')),
                    ])->tooltip('More actions');
                @endphp

                <x-accelade::action-group :group="$actionGroup" />
            </div>
        </div>

        {{-- Inline Action Group --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Inline Action Group</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Actions displayed inline without a dropdown.
            </p>
            <div class="flex flex-wrap gap-2">
                @php
                    $inlineGroup = \Accelade\Actions\ActionGroup::make([
                        \Accelade\Actions\Action::make('approve')
                            ->label('Approve')
                            ->icon('heroicon-o-check')
                            ->color('success')
                            ->size('sm')
                            ->action(fn () => \Accelade\Facades\Notify::success('Approved', 'Item approved!')),
                        \Accelade\Actions\Action::make('reject')
                            ->label('Reject')
                            ->icon('heroicon-o-x-mark')
                            ->color('danger')
                            ->size('sm')
                            ->action(fn () => \Accelade\Facades\Notify::danger('Rejected', 'Item rejected.')),
                    ])->dropdown(false);
                @endphp

                @foreach($inlineGroup->getVisibleActions() as $action)
                    <x-accelade::action :action="$action" />
                @endforeach
            </div>
        </div>

        {{-- Custom Styled Group --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Custom Styled Group</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Customize the trigger button with label, icon, and color.</p>
            <div class="flex justify-end">
                @php
                    $customGroup = \Accelade\Actions\ActionGroup::make([
                        \Accelade\Actions\Action::make('duplicate')
                            ->label('Duplicate')
                            ->icon('heroicon-o-document-duplicate')
                            ->action(fn () => \Accelade\Facades\Notify::success('Duplicated', 'Item duplicated!')),
                        \Accelade\Actions\Action::make('archive')
                            ->label('Archive')
                            ->icon('heroicon-o-archive-box')
                            ->action(fn () => \Accelade\Facades\Notify::warning('Archived', 'Item archived.')),
                        \Accelade\Actions\Action::make('export')
                            ->label('Export')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->action(fn () => \Accelade\Facades\Notify::info('Exporting', 'Export started...')),
                    ])
                        ->label('Actions')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('primary');
                @endphp

                <x-accelade::action-group :group="$customGroup" />
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
