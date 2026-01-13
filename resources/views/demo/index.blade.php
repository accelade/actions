<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Actions Demo - Accelade</title>

    {{-- Tailwind CSS via CDN for demo --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    @acceladeStyles
    @actionsStyles
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900">
    {{-- Notifications container --}}
    <x-accelade::notifications position="top-right" />

    <div class="min-h-full">
        {{-- Header --}}
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Actions Demo - Complete API Testing
                    </h1>
                    <div class="flex items-center gap-4">
                        <x-accelade::link href="/" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400">
                            ← Back to Home
                        </x-accelade::link>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-12">

            {{-- 1. Basic Action Creation --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">1. Basic Action Creation (make, label, name)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-wrap gap-4">
                        @php
                            $basicAction = \Accelade\Actions\Action::make('basic')
                                ->label('Basic Action')
                                ->action(fn() => \Accelade\Facades\Notify::success('Basic action executed!'));

                            $namedAction = \Accelade\Actions\Action::make('my-custom-name')
                                ->label('Named Action (my-custom-name)')
                                ->action(fn() => \Accelade\Facades\Notify::info('getName() = my-custom-name'));
                        @endphp
                        <x-accelade::action :action="$basicAction" />
                        <x-accelade::action :action="$namedAction" />
                    </div>
                </div>
            </section>

            {{-- 2. Colors --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">2. Colors (color)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
                    <div class="flex flex-wrap gap-3">
                        @foreach(['primary', 'secondary', 'success', 'danger', 'warning', 'info'] as $color)
                            @php
                                $colorAction = \Accelade\Actions\Action::make("color-{$color}")
                                    ->label(ucfirst($color))
                                    ->color($color)
                                    ->action(fn() => \Accelade\Facades\Notify::info("Color: {$color}"));
                            @endphp
                            <x-accelade::action :action="$colorAction" />
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- 3. Icons --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">3. Icons (icon, iconPosition)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
                    <div class="flex flex-wrap gap-4">
                        @php
                            $iconBefore = \Accelade\Actions\Action::make('icon-before')
                                ->label('Icon Before')
                                ->icon('check')
                                ->iconPosition('before')
                                ->action(fn() => \Accelade\Facades\Notify::info('Icon position: before'));

                            $iconAfter = \Accelade\Actions\Action::make('icon-after')
                                ->label('Icon After')
                                ->icon('arrow-right')
                                ->iconPosition('after')
                                ->action(fn() => \Accelade\Facades\Notify::info('Icon position: after'));

                            $multipleIcons = \Accelade\Actions\Action::make('save-icon')
                                ->label('Save')
                                ->icon('save')
                                ->color('success')
                                ->action(fn() => \Accelade\Facades\Notify::success('Saved!'));
                        @endphp
                        <x-accelade::action :action="$iconBefore" />
                        <x-accelade::action :action="$iconAfter" />
                        <x-accelade::action :action="$multipleIcons" />
                    </div>
                </div>
            </section>

            {{-- 4. Sizes --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">4. Sizes (size: xs, sm, md, lg, xl)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-wrap items-center gap-4">
                        @foreach(['xs', 'sm', 'md', 'lg', 'xl'] as $size)
                            @php
                                $sizeAction = \Accelade\Actions\Action::make("size-{$size}")
                                    ->label(strtoupper($size))
                                    ->icon('check')
                                    ->size($size)
                                    ->action(fn() => \Accelade\Facades\Notify::info("Size: {$size}"));
                            @endphp
                            <x-accelade::action :action="$sizeAction" />
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- 5. Variants --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">5. Variants (outlined, link, iconButton)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-wrap items-center gap-4">
                        @php
                            $solidBtn = \Accelade\Actions\Action::make('solid')
                                ->label('Solid Button')
                                ->icon('check')
                                ->action(fn() => \Accelade\Facades\Notify::info('Solid variant'));

                            $outlinedBtn = \Accelade\Actions\Action::make('outlined')
                                ->label('Outlined')
                                ->icon('check')
                                ->outlined()
                                ->action(fn() => \Accelade\Facades\Notify::info('Outlined variant'));

                            $linkBtn = \Accelade\Actions\Action::make('link-variant')
                                ->label('Link Style')
                                ->icon('external-link')
                                ->link()
                                ->action(fn() => \Accelade\Facades\Notify::info('Link variant'));

                            $iconBtn = \Accelade\Actions\Action::make('icon-only')
                                ->icon('settings')
                                ->iconButton()
                                ->tooltip('Icon Button')
                                ->action(fn() => \Accelade\Facades\Notify::info('Icon button variant'));
                        @endphp
                        <x-accelade::action :action="$solidBtn" />
                        <x-accelade::action :action="$outlinedBtn" />
                        <x-accelade::action :action="$linkBtn" />
                        <x-accelade::action :action="$iconBtn" />
                    </div>
                </div>
            </section>

            {{-- 6. URLs and Navigation --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">6. URLs and Navigation (url, openUrlInNewTab)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-wrap gap-4">
                        @php
                            $spaNav = \Accelade\Actions\Action::make('spa-nav')
                                ->label('SPA Navigate Home')
                                ->icon('home')
                                ->url('/');

                            $externalLink = \Accelade\Actions\Action::make('external')
                                ->label('Open GitHub')
                                ->icon('external-link')
                                ->url('https://github.com', true);

                            $methodAction = \Accelade\Actions\Action::make('post-action')
                                ->label('POST Request')
                                ->icon('send')
                                ->color('warning')
                                ->url('/')
                                ->method('POST');
                        @endphp
                        <x-accelade::action :action="$spaNav" />
                        <x-accelade::action :action="$externalLink" />
                        <x-accelade::action :action="$methodAction" />
                    </div>
                </div>
            </section>

            {{-- 7. Confirmation Modals --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">7. Confirmation Modals (requiresConfirmation, modal*)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-wrap gap-4">
                        @php
                            $basicConfirm = \Accelade\Actions\Action::make('basic-confirm')
                                ->label('Basic Confirm')
                                ->icon('alert-circle')
                                ->requiresConfirmation()
                                ->action(fn() => \Accelade\Facades\Notify::success('Confirmed!'));

                            $customConfirm = \Accelade\Actions\Action::make('custom-confirm')
                                ->label('Custom Modal')
                                ->icon('help-circle')
                                ->color('warning')
                                ->requiresConfirmation()
                                ->modalHeading('Custom Heading')
                                ->modalDescription('This is a custom description for the confirmation modal.')
                                ->modalSubmitActionLabel('Yes, Continue')
                                ->modalCancelActionLabel('No, Go Back')
                                ->action(fn() => \Accelade\Facades\Notify::success('Custom confirm executed!'));

                            $dangerConfirm = \Accelade\Actions\Action::make('danger-confirm')
                                ->label('Danger Confirm')
                                ->icon('trash-2')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->confirmDanger()
                                ->modalHeading('Delete Item')
                                ->modalDescription('This action cannot be undone. Are you sure?')
                                ->action(fn() => \Accelade\Facades\Notify::error('Item deleted!'));
                        @endphp
                        <x-accelade::action :action="$basicConfirm" />
                        <x-accelade::action :action="$customConfirm" />
                        <x-accelade::action :action="$dangerConfirm" />
                    </div>
                </div>
            </section>

            {{-- 8. Server-Side Actions --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">8. Server-Side Actions (action closure)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-wrap gap-4">
                        @php
                            $serverAction = \Accelade\Actions\Action::make('server-action')
                                ->label('Server Action')
                                ->icon('server')
                                ->color('success')
                                ->action(function () {
                                    \Accelade\Facades\Notify::success('Server time: ' . now()->format('H:i:s'));
                                });

                            $dataAction = \Accelade\Actions\Action::make('data-action')
                                ->label('Return Data')
                                ->icon('database')
                                ->color('info')
                                ->action(function ($record, $data) {
                                    return [
                                        'message' => 'Data returned from server',
                                        'timestamp' => now()->toISOString(),
                                    ];
                                });
                        @endphp
                        <x-accelade::action :action="$serverAction" />
                        <x-accelade::action :action="$dataAction" />
                    </div>
                </div>
            </section>

            {{-- 9. Pre-configured Actions --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">9. Pre-configured Actions (ViewAction, EditAction, CreateAction, DeleteAction)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-wrap gap-4">
                        @php
                            $viewAction = \Accelade\Actions\ViewAction::make()
                                ->action(fn() => \Accelade\Facades\Notify::info('View action - pre-configured with eye icon'));

                            $editAction = \Accelade\Actions\EditAction::make()
                                ->action(fn() => \Accelade\Facades\Notify::info('Edit action - pre-configured with edit icon'));

                            $createAction = \Accelade\Actions\CreateAction::make()
                                ->action(fn() => \Accelade\Facades\Notify::success('Create action - pre-configured with plus icon'));

                            $deleteAction = \Accelade\Actions\DeleteAction::make()
                                ->action(fn() => \Accelade\Facades\Notify::error('Delete action - pre-configured with trash icon & confirmation'));
                        @endphp
                        <x-accelade::action :action="$viewAction" />
                        <x-accelade::action :action="$editAction" />
                        <x-accelade::action :action="$createAction" />
                        <x-accelade::action :action="$deleteAction" />
                    </div>
                </div>
            </section>

            {{-- 10. Disabled and Hidden --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">10. States (disabled, hidden)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-wrap gap-4">
                        @php
                            $disabledAction = \Accelade\Actions\Action::make('disabled')
                                ->label('Disabled Button')
                                ->icon('x')
                                ->disabled();

                            $disabledOutlined = \Accelade\Actions\Action::make('disabled-outlined')
                                ->label('Disabled Outlined')
                                ->icon('x')
                                ->outlined()
                                ->disabled();

                            $hiddenAction = \Accelade\Actions\Action::make('hidden')
                                ->label('Hidden (you should not see this)')
                                ->hidden();

                            $conditionalHidden = \Accelade\Actions\Action::make('conditional')
                                ->label('Conditionally Visible')
                                ->icon('eye')
                                ->color('success')
                                ->hidden(fn() => false)
                                ->action(fn() => \Accelade\Facades\Notify::info('Condition was false, so visible!'));
                        @endphp
                        <x-accelade::action :action="$disabledAction" />
                        <x-accelade::action :action="$disabledOutlined" />
                        <x-accelade::action :action="$hiddenAction" />
                        <x-accelade::action :action="$conditionalHidden" />
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Note: The "Hidden" button should not appear above.</p>
                </div>
            </section>

            {{-- 11. Tooltip --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">11. Tooltip (tooltip)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-wrap gap-4">
                        @php
                            $tooltipAction = \Accelade\Actions\Action::make('tooltip')
                                ->label('Hover Me')
                                ->icon('info')
                                ->tooltip('This is a helpful tooltip!')
                                ->action(fn() => \Accelade\Facades\Notify::info('Button with tooltip'));

                            $iconTooltip = \Accelade\Actions\Action::make('icon-tooltip')
                                ->icon('settings')
                                ->iconButton()
                                ->tooltip('Open Settings')
                                ->action(fn() => \Accelade\Facades\Notify::info('Settings clicked'));
                        @endphp
                        <x-accelade::action :action="$tooltipAction" />
                        <x-accelade::action :action="$iconTooltip" />
                    </div>
                </div>
            </section>

            {{-- 12. Extra Attributes --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">12. Extra Attributes (extraAttributes)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex flex-wrap gap-4">
                        @php
                            $extraAttrsAction = \Accelade\Actions\Action::make('extra-attrs')
                                ->label('Custom Data Attrs')
                                ->icon('code')
                                ->extraAttributes([
                                    'data-custom' => 'my-value',
                                    'data-testid' => 'action-button',
                                    'aria-describedby' => 'action-help',
                                ])
                                ->action(fn() => \Accelade\Facades\Notify::info('Check the HTML for custom attributes!'));
                        @endphp
                        <x-accelade::action :action="$extraAttrsAction" />
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Inspect the button element to see data-custom, data-testid, and aria-describedby attributes.</p>
                </div>
            </section>

            {{-- 13. Action Groups --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">13. Action Groups (ActionGroup)</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-6">
                    {{-- Dropdown Group --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Dropdown Group</h3>
                        <div class="flex justify-end">
                            @php
                                $dropdownGroup = \Accelade\Actions\ActionGroup::make([
                                    \Accelade\Actions\Action::make('view')
                                        ->label('View')
                                        ->icon('eye')
                                        ->action(fn() => \Accelade\Facades\Notify::info('View from dropdown')),
                                    \Accelade\Actions\Action::make('edit')
                                        ->label('Edit')
                                        ->icon('edit')
                                        ->action(fn() => \Accelade\Facades\Notify::info('Edit from dropdown')),
                                    \Accelade\Actions\Action::make('duplicate')
                                        ->label('Duplicate')
                                        ->icon('copy')
                                        ->action(fn() => \Accelade\Facades\Notify::success('Duplicated!')),
                                    \Accelade\Actions\Action::make('delete')
                                        ->label('Delete')
                                        ->icon('trash-2')
                                        ->color('danger')
                                        ->requiresConfirmation()
                                        ->confirmDanger()
                                        ->modalHeading('Delete Item')
                                        ->modalDescription('Are you sure you want to delete this item?')
                                        ->action(fn() => \Accelade\Facades\Notify::error('Deleted!')),
                                ])->tooltip('More actions');
                            @endphp
                            <x-accelade::action-group :group="$dropdownGroup" />
                        </div>
                    </div>

                    {{-- Custom Styled Group --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Custom Styled Group</h3>
                        <div class="flex justify-end">
                            @php
                                $customGroup = \Accelade\Actions\ActionGroup::make([
                                    \Accelade\Actions\Action::make('export-pdf')
                                        ->label('Export PDF')
                                        ->icon('file-text')
                                        ->action(fn() => \Accelade\Facades\Notify::success('PDF exported!')),
                                    \Accelade\Actions\Action::make('export-csv')
                                        ->label('Export CSV')
                                        ->icon('file-spreadsheet')
                                        ->action(fn() => \Accelade\Facades\Notify::success('CSV exported!')),
                                    \Accelade\Actions\Action::make('print')
                                        ->label('Print')
                                        ->icon('printer')
                                        ->action(fn() => \Accelade\Facades\Notify::info('Printing...')),
                                ])
                                    ->label('Export')
                                    ->icon('download')
                                    ->color('primary');
                            @endphp
                            <x-accelade::action-group :group="$customGroup" />
                        </div>
                    </div>

                    {{-- Inline Group (no dropdown) --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Inline Group (dropdown: false)</h3>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $inlineGroup = \Accelade\Actions\ActionGroup::make([
                                    \Accelade\Actions\Action::make('approve')
                                        ->label('Approve')
                                        ->icon('check')
                                        ->color('success')
                                        ->size('sm')
                                        ->action(fn() => \Accelade\Facades\Notify::success('Approved!')),
                                    \Accelade\Actions\Action::make('reject')
                                        ->label('Reject')
                                        ->icon('x')
                                        ->color('danger')
                                        ->size('sm')
                                        ->requiresConfirmation()
                                        ->confirmDanger()
                                        ->action(fn() => \Accelade\Facades\Notify::error('Rejected!')),
                                    \Accelade\Actions\Action::make('pending')
                                        ->label('Mark Pending')
                                        ->icon('clock')
                                        ->color('warning')
                                        ->size('sm')
                                        ->action(fn() => \Accelade\Facades\Notify::warning('Marked as pending')),
                                ])->dropdown(false);
                            @endphp
                            @foreach($inlineGroup->getVisibleActions() as $action)
                                <x-accelade::action :action="$action" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- 14. Records --}}
            <section>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">14. Actions with Records</h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    @php
                        $record = (object) ['id' => 42, 'name' => 'John Doe', 'email' => 'john@example.com'];

                        $recordAction = \Accelade\Actions\Action::make('record-action')
                            ->label('Action with Record')
                            ->icon('user')
                            ->color('info')
                            ->action(function ($record) {
                                \Accelade\Facades\Notify::success("Record ID: {$record->id}, Name: {$record->name}");
                            });
                    @endphp
                    <div class="flex flex-wrap gap-4">
                        <x-accelade::action :action="$recordAction" :record="$record" />
                    </div>
                    <p class="text-sm text-gray-500 mt-2">The action receives the record: ID={{ $record->id }}, Name={{ $record->name }}</p>
                </div>
            </section>

        </main>

        {{-- Footer --}}
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    Accelade Actions - All APIs demonstrated above. Click each button to test!
                </p>
            </div>
        </footer>
    </div>

    @acceladeScripts
    @actionsScripts
</body>
</html>
