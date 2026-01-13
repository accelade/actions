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
            <div class="flex justify-end">
                @php
                    $actionGroup = \Accelade\Actions\ActionGroup::make([
                        \Accelade\Actions\ViewAction::make()->url('#'),
                        \Accelade\Actions\EditAction::make()->url('#'),
                        \Accelade\Actions\DeleteAction::make()->url('#'),
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
                            ->icon('check')
                            ->color('success')
                            ->size('sm')
                            ->url('#'),
                        \Accelade\Actions\Action::make('reject')
                            ->label('Reject')
                            ->icon('x')
                            ->color('danger')
                            ->size('sm')
                            ->url('#'),
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
            <div class="flex justify-end">
                @php
                    $customGroup = \Accelade\Actions\ActionGroup::make([
                        \Accelade\Actions\Action::make('duplicate')
                            ->label('Duplicate')
                            ->icon('copy')
                            ->url('#'),
                        \Accelade\Actions\Action::make('archive')
                            ->label('Archive')
                            ->icon('archive')
                            ->url('#'),
                        \Accelade\Actions\Action::make('export')
                            ->label('Export')
                            ->icon('download')
                            ->url('#'),
                    ])
                        ->label('Actions')
                        ->icon('settings')
                        ->color('primary');
                @endphp

                <x-accelade::action-group :group="$customGroup" />
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
