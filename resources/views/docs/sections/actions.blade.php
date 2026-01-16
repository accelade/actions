<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    {{-- Demo Content --}}
    <div class="space-y-8">
        @php
            // Initialize fake records in session if not exists
            if (!session()->has('demo_users')) {
                session(['demo_users' => [
                    ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin', 'status' => 'active'],
                    ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'Editor', 'status' => 'active'],
                    ['id' => 3, 'name' => 'Bob Wilson', 'email' => 'bob@example.com', 'role' => 'Viewer', 'status' => 'inactive'],
                ]]);
            }
            $users = collect(session('demo_users', []));
            $nextId = $users->max('id') + 1;
        @endphp

        {{-- CRUD Demo with Fake Model --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">CRUD Demo - Users</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Full Create, Read, Update, Delete demo with session-based records.</p>
                </div>
                @php
                    // Capture the current page URL at render time (not execution time)
                    $currentPageUrl = url()->current() . (request()->getQueryString() ? '?' . request()->getQueryString() : '');

                    // CreateAction - Creates a new fake user
                    $createAction = \Accelade\Actions\CreateAction::make()
                        ->label('Add User')
                        ->action(function () use ($currentPageUrl) {
                            $users = collect(session('demo_users', []));
                            $nextId = $users->max('id') + 1;

                            $names = ['Alice', 'Charlie', 'Diana', 'Edward', 'Fiona', 'George', 'Hannah'];
                            $roles = ['Admin', 'Editor', 'Viewer', 'Manager'];
                            $name = $names[array_rand($names)] . ' ' . chr(rand(65, 90)) . '.';

                            $newUser = [
                                'id' => $nextId,
                                'name' => $name,
                                'email' => strtolower(explode(' ', $name)[0]) . $nextId . '@example.com',
                                'role' => $roles[array_rand($roles)],
                                'status' => 'active',
                            ];

                            $users->push($newUser);
                            session(['demo_users' => $users->toArray()]);

                            \Accelade\Facades\Notify::success('User Created')->body("Created: {$newUser['name']} ({$newUser['email']})");

                            return ['redirect' => $currentPageUrl];
                        });
                @endphp
                <x-accelade::action :action="$createAction" />
            </div>

            {{-- Users Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-end text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($users as $user)
                            @php
                                $userRecord = (object) $user;

                                // ViewAction - Shows user details
                                $viewAction = \Accelade\Actions\ViewAction::make()
                                    ->action(function ($record) {
                                        $info = "ID: {$record->id}\nName: {$record->name}\nEmail: {$record->email}\nRole: {$record->role}\nStatus: {$record->status}";
                                        \Accelade\Facades\Notify::info('User Details')->body("Viewing: {$record->name}");
                                    });

                                // EditAction - Updates with random data
                                $editAction = \Accelade\Actions\EditAction::make()
                                    ->action(function ($record) use ($currentPageUrl) {
                                        $users = collect(session('demo_users', []));
                                        $roles = ['Admin', 'Editor', 'Viewer', 'Manager'];
                                        $statuses = ['active', 'inactive', 'pending'];

                                        $users = $users->map(function ($u) use ($record, $roles, $statuses) {
                                            if ($u['id'] == $record->id) {
                                                $u['role'] = $roles[array_rand($roles)];
                                                $u['status'] = $statuses[array_rand($statuses)];
                                            }
                                            return $u;
                                        });

                                        session(['demo_users' => $users->toArray()]);
                                        $updated = $users->firstWhere('id', $record->id);

                                        \Accelade\Facades\Notify::success('User Updated')->body("{$record->name}: Role={$updated['role']}, Status={$updated['status']}");

                                        return ['redirect' => $currentPageUrl];
                                    });

                                // DeleteAction - Removes user
                                $deleteAction = \Accelade\Actions\DeleteAction::make()
                                    ->action(function ($record) use ($currentPageUrl) {
                                        $users = collect(session('demo_users', []));
                                        $users = $users->reject(fn ($u) => $u['id'] == $record->id);
                                        session(['demo_users' => $users->values()->toArray()]);

                                        \Accelade\Facades\Notify::danger('User Deleted')->body("Deleted: {$record->name}");

                                        return ['redirect' => $currentPageUrl];
                                    });

                                // ActionGroup - All actions in dropdown
                                $actionGroup = \Accelade\Actions\ActionGroup::make([
                                    $viewAction,
                                    $editAction,
                                    $deleteAction,
                                ])->tooltip('Actions');
                            @endphp
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-900 dark:text-white">{{ $user['id'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">{{ $user['name'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">{{ $user['email'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-400">{{ $user['role'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user['status'] === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : ($user['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                        {{ ucfirst($user['status']) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-end">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Individual actions --}}
                                        <x-accelade::action :action="$viewAction" :record="$userRecord" />
                                        <x-accelade::action :action="$editAction" :record="$userRecord" />
                                        <x-accelade::action :action="$deleteAction" :record="$userRecord" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                    No users found. Click "Add User" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ActionGroup Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">ActionGroup - Dropdown Menu</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Group multiple actions into a dropdown menu.</p>

            <div class="flex flex-wrap items-center gap-6">
                @php
                    // Sample record for ActionGroup demo
                    $sampleUser = (object) ['id' => 99, 'name' => 'Sample User', 'email' => 'sample@test.com', 'role' => 'Tester', 'status' => 'active'];

                    // Basic dropdown group
                    $basicGroup = \Accelade\Actions\ActionGroup::make([
                        \Accelade\Actions\ViewAction::make()->action(function ($r) {
                            \Accelade\Facades\Notify::info('View')->body("Viewing: {$r->name}");
                        }),
                        \Accelade\Actions\EditAction::make()->action(function ($r) {
                            \Accelade\Facades\Notify::info('Edit')->body("Editing: {$r->name}");
                        }),
                        \Accelade\Actions\DeleteAction::make()->action(function ($r) {
                            \Accelade\Facades\Notify::danger('Delete')->body("Would delete: {$r->name}");
                        }),
                    ])->tooltip('More actions');

                    // Labeled dropdown group
                    $labeledGroup = \Accelade\Actions\ActionGroup::make([
                        \Accelade\Actions\Action::make('export-csv')->label('Export CSV')->icon('heroicon-o-arrow-down-tray')->action(function () {
                            \Accelade\Facades\Notify::success('Export')->body('CSV export started');
                        }),
                        \Accelade\Actions\Action::make('export-pdf')->label('Export PDF')->icon('heroicon-o-document-text')->action(function () {
                            \Accelade\Facades\Notify::success('Export')->body('PDF export started');
                        }),
                        \Accelade\Actions\Action::make('print')->label('Print')->icon('heroicon-o-printer')->action(function () {
                            \Accelade\Facades\Notify::info('Print')->body('Preparing print preview');
                        }),
                    ])->label('Export')->icon('heroicon-o-arrow-down-tray');
                @endphp

                <div class="text-center">
                    <x-accelade::action-group :group="$basicGroup" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon only</p>
                </div>

                <div class="text-center">
                    <x-accelade::action-group :group="$labeledGroup" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">With label</p>
                </div>
            </div>
        </div>

        {{-- Preset Actions Overview --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Preset Actions</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Pre-configured action types with default icons, colors, and behaviors.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $presetDemo = (object) ['id' => 1, 'name' => 'Demo Item'];

                    $presetCreate = \Accelade\Actions\CreateAction::make()
                        ->action(fn () => \Accelade\Facades\Notify::success('CreateAction')->body('Primary color, plus icon'));

                    $presetView = \Accelade\Actions\ViewAction::make()
                        ->action(fn () => \Accelade\Facades\Notify::info('ViewAction')->body('Secondary color, eye icon'));

                    $presetEdit = \Accelade\Actions\EditAction::make()
                        ->action(fn () => \Accelade\Facades\Notify::info('EditAction')->body('Primary color, pencil icon'));

                    $presetDelete = \Accelade\Actions\DeleteAction::make()
                        ->action(fn ($r) => \Accelade\Facades\Notify::danger('DeleteAction')->body('Danger color, trash icon, confirmation required'));
                @endphp

                <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-lg text-center">
                    <x-accelade::action :action="$presetCreate" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">CreateAction</p>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-lg text-center">
                    <x-accelade::action :action="$presetView" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">ViewAction</p>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-lg text-center">
                    <x-accelade::action :action="$presetEdit" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">EditAction</p>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-lg text-center">
                    <x-accelade::action :action="$presetDelete" :record="$presetDemo" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">DeleteAction</p>
                </div>
            </div>
        </div>

        {{-- Color Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Color Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Available color options for actions.</p>
            <div class="flex flex-wrap gap-3">
                @php
                    $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];
                @endphp
                @foreach($colors as $color)
                    @php
                        $colorAction = \Accelade\Actions\Action::make("color-{$color}")
                            ->label(ucfirst($color))
                            ->color($color)
                            ->icon('heroicon-s-stop')
                            ->action(function () use ($color) {
                                $method = in_array($color, ['danger', 'warning']) ? $color : (in_array($color, ['success']) ? 'success' : 'info');
                                \Accelade\Facades\Notify::$method(ucfirst($color))->body("{$color} action clicked");
                            });
                    @endphp
                    <x-accelade::action :action="$colorAction" />
                @endforeach
            </div>

            <h4 class="text-md font-medium mt-6 mb-3 text-slate-700 dark:text-slate-300">Outlined Variants</h4>
            <div class="flex flex-wrap gap-3">
                @foreach($colors as $color)
                    @php
                        $outlinedAction = \Accelade\Actions\Action::make("outlined-{$color}")
                            ->label(ucfirst($color))
                            ->color($color)
                            ->outlined()
                            ->icon('heroicon-s-stop')
                            ->action(function () use ($color) {
                                $method = in_array($color, ['danger', 'warning']) ? $color : (in_array($color, ['success']) ? 'success' : 'info');
                                \Accelade\Facades\Notify::$method(ucfirst($color) . ' Outlined')->body("Outlined {$color} action");
                            });
                    @endphp
                    <x-accelade::action :action="$outlinedAction" />
                @endforeach
            </div>
        </div>

        {{-- Size Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Size Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Actions in different sizes.</p>
            <div class="flex flex-wrap items-center gap-4">
                @foreach(['xs', 'sm', 'md', 'lg', 'xl'] as $size)
                    @php
                        $sizeAction = \Accelade\Actions\Action::make("size-{$size}")
                            ->label(strtoupper($size))
                            ->icon('heroicon-o-check')
                            ->size($size)
                            ->action(function () use ($size) {
                                \Accelade\Facades\Notify::success('Size: ' . strtoupper($size))->body("Size {$size} clicked");
                            });
                    @endphp
                    <x-accelade::action :action="$sizeAction" />
                @endforeach
            </div>
        </div>

        {{-- Variants: Button, Link, Icon --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Different display variants for actions.</p>
            <div class="flex flex-wrap items-center gap-6">
                @php
                    $buttonVariant = \Accelade\Actions\Action::make('button-variant')
                        ->label('Button')
                        ->icon('heroicon-o-cursor-arrow-rays')
                        ->variant('button')
                        ->action(fn () => \Accelade\Facades\Notify::info('Button Variant')->body('Default button style'));

                    $linkVariant = \Accelade\Actions\Action::make('link-variant')
                        ->label('Link Style')
                        ->icon('heroicon-o-link')
                        ->variant('link')
                        ->action(fn () => \Accelade\Facades\Notify::info('Link Variant')->body('Link-style button'));

                    $iconVariant = \Accelade\Actions\Action::make('icon-variant')
                        ->icon('heroicon-o-star')
                        ->variant('icon')
                        ->tooltip('Icon only button')
                        ->action(fn () => \Accelade\Facades\Notify::info('Icon Variant')->body('Icon-only button'));
                @endphp

                <div class="text-center">
                    <x-accelade::action :action="$buttonVariant" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Button</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$linkVariant" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Link</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$iconVariant" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon</p>
                </div>
            </div>
        </div>

        {{-- Confirmation Modals --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Confirmation Modals</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Actions that require user confirmation.</p>
            <div class="flex flex-wrap gap-4">
                @php
                    $confirmAction = \Accelade\Actions\Action::make('confirm-action')
                        ->label('Confirm Action')
                        ->icon('heroicon-o-check-circle')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Confirm Action')
                        ->modalDescription('Are you sure you want to perform this action?')
                        ->action(fn () => \Accelade\Facades\Notify::success('Confirmed')->body('Action was confirmed'));

                    $dangerConfirm = \Accelade\Actions\Action::make('danger-confirm')
                        ->label('Danger Confirm')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->confirmDanger()
                        ->modalHeading('Dangerous Action')
                        ->modalDescription('This action cannot be undone. Are you sure?')
                        ->modalSubmitActionLabel('Yes, proceed')
                        ->action(fn () => \Accelade\Facades\Notify::danger('Danger Confirmed')->body('Dangerous action was confirmed'));
                @endphp

                <x-accelade::action :action="$confirmAction" />
                <x-accelade::action :action="$dangerConfirm" />
            </div>
        </div>

        {{-- URL Navigation --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">URL Navigation</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">Actions that navigate to URLs.</p>
            <div class="flex flex-wrap gap-4">
                @php
                    $homeAction = \Accelade\Actions\Action::make('home')
                        ->label('Go Home')
                        ->icon('heroicon-o-home')
                        ->color('primary')
                        ->url('/');

                    $externalAction = \Accelade\Actions\Action::make('external')
                        ->label('GitHub')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('secondary')
                        ->outlined()
                        ->url('https://github.com')
                        ->openUrlInNewTab();
                @endphp

                <x-accelade::action :action="$homeAction" />
                <x-accelade::action :action="$externalAction" />
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
