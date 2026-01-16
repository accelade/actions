<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    <div class="space-y-8">
        @php
            // Sample record for demos
            $sampleUser = (object) ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];
        @endphp

        {{-- Basic DeleteAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic DeleteAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                DeleteAction with default settings - trash icon, danger color, built-in confirmation dialog.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicDelete = \Accelade\Actions\DeleteAction::make()
                        ->action(fn ($record) => \Accelade\Facades\Notify::danger('Deleted')->body("Would delete: {$record->name}"));
                @endphp
                <x-accelade::action :action="$basicDelete" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
DeleteAction::make()
    ->action(fn ($record) => $record->delete());
            </x-accelade::code-block>
        </div>

        {{-- Customized DeleteAction --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Customized Confirmation</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Override the default modal text with custom messaging.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $customDelete = \Accelade\Actions\DeleteAction::make()
                        ->label('Remove User')
                        ->modalHeading('Remove this user?')
                        ->modalDescription('The user will be permanently removed from the system. This action cannot be undone.')
                        ->modalSubmitActionLabel('Yes, remove user')
                        ->modalCancelActionLabel('No, keep user')
                        ->action(fn ($record) => \Accelade\Facades\Notify::danger('Removed')->body("User removed: {$record->name}"));
                @endphp
                <x-accelade::action :action="$customDelete" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
DeleteAction::make()
    ->label('Remove User')
    ->modalHeading('Remove this user?')
    ->modalDescription('This action cannot be undone.')
    ->modalSubmitActionLabel('Yes, remove user')
    ->modalCancelActionLabel('No, keep user')
    ->action(fn ($record) => $record->delete());
            </x-accelade::code-block>
        </div>

        {{-- DeleteAction with Form Schema --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">DeleteAction with Deletion Options</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Add form fields for deletion options like reason or notification preferences.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $schemaDelete = \Accelade\Actions\DeleteAction::make()
                        ->modalHeading('Delete User')
                        ->modalDescription('Please provide additional options for this deletion.')
                        ->schema([
                            \Accelade\Forms\Components\Textarea::make('reason')
                                ->label('Reason for deletion')
                                ->placeholder('Why is this user being deleted?')
                                ->rows(3),
                            \Accelade\Forms\Components\Checkbox::make('notify_user')
                                ->label('Send deletion notification to user'),
                            \Accelade\Forms\Components\Checkbox::make('permanent')
                                ->label('Delete permanently (skip trash)'),
                        ])
                        ->action(function ($record, $data) {
                            $msg = "Deleted: {$record->name}";
                            if ($data['reason'] ?? null) {
                                $msg .= " | Reason: {$data['reason']}";
                            }
                            if ($data['permanent'] ?? false) {
                                $msg .= " (Permanent)";
                            }
                            \Accelade\Facades\Notify::danger('Deleted')->body($msg);
                        });
                @endphp
                <x-accelade::action :action="$schemaDelete" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
DeleteAction::make()
    ->modalHeading('Delete User')
    ->schema([
        Textarea::make('reason')
            ->label('Reason for deletion'),
        Checkbox::make('notify_user')
            ->label('Send notification'),
        Checkbox::make('permanent')
            ->label('Delete permanently'),
    ])
    ->action(function ($record, $data) {
        if ($data['permanent']) {
            $record->forceDelete();
        } else {
            $record->delete();
        }
    });
            </x-accelade::code-block>
        </div>

        {{-- Force Delete --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Force Delete (Permanent)</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                More aggressive styling for permanent deletion.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $forceDelete = \Accelade\Actions\DeleteAction::make('force-delete')
                        ->label('Delete Forever')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->modalHeading('Permanently Delete')
                        ->modalDescription('This will permanently delete the record and all associated data. This action CANNOT be undone.')
                        ->modalSubmitActionLabel('Delete Forever')
                        ->action(fn ($record) => \Accelade\Facades\Notify::danger('Permanently Deleted')->body("Force deleted: {$record->name}"));
                @endphp
                <x-accelade::action :action="$forceDelete" :record="$sampleUser" />
            </div>
        </div>

        {{-- Button Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                DeleteAction in different button styles.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @php
                    $buttonDelete = \Accelade\Actions\DeleteAction::make('delete-button')
                        ->button()
                        ->action(fn () => \Accelade\Facades\Notify::danger('Button variant'));

                    $linkDelete = \Accelade\Actions\DeleteAction::make('delete-link')
                        ->link()
                        ->action(fn () => \Accelade\Facades\Notify::danger('Link variant'));

                    $iconDelete = \Accelade\Actions\DeleteAction::make('delete-icon')
                        ->iconButton()
                        ->tooltip('Delete this item')
                        ->action(fn () => \Accelade\Facades\Notify::danger('Icon variant'));

                    $outlinedDelete = \Accelade\Actions\DeleteAction::make('delete-outlined')
                        ->outlined()
                        ->action(fn () => \Accelade\Facades\Notify::danger('Outlined variant'));
                @endphp

                <div class="text-center">
                    <x-accelade::action :action="$buttonDelete" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Button</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$linkDelete" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Link</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$iconDelete" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$outlinedDelete" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Outlined</p>
                </div>
            </div>
        </div>

        {{-- Real World Example: Table Row Actions --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Real World Example: Table with Delete</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Common pattern: DeleteAction in table rows. Try deleting a user (it's just a demo!).
            </p>

            @php
                // Use session-based users for deletable demo
                if (!session()->has('delete_demo_users')) {
                    session(['delete_demo_users' => [
                        ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin'],
                        ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'Editor'],
                        ['id' => 3, 'name' => 'Bob Wilson', 'email' => 'bob@example.com', 'role' => 'Viewer'],
                    ]]);
                }
                $demoUsers = collect(session('delete_demo_users', []));
                $currentUrl = url()->current() . (request()->getQueryString() ? '?' . request()->getQueryString() : '');

                $rowDeleteAction = \Accelade\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete user')
                    ->action(function ($record) use ($currentUrl) {
                        $users = collect(session('delete_demo_users', []));
                        $users = $users->reject(fn ($u) => $u['id'] == $record->id);
                        session(['delete_demo_users' => $users->values()->toArray()]);

                        \Accelade\Facades\Notify::danger('User Deleted')->body("Deleted: {$record->name}");

                        return ['redirect' => $currentUrl];
                    });

                $resetAction = \Accelade\Actions\Action::make('reset-demo')
                    ->label('Reset Demo Data')
                    ->icon('heroicon-o-arrow-path')
                    ->color('secondary')
                    ->size('sm')
                    ->action(function () use ($currentUrl) {
                        session(['delete_demo_users' => [
                            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin'],
                            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'Editor'],
                            ['id' => 3, 'name' => 'Bob Wilson', 'email' => 'bob@example.com', 'role' => 'Viewer'],
                        ]]);
                        \Accelade\Facades\Notify::success('Reset')->body('Demo data has been reset');
                        return ['redirect' => $currentUrl];
                    });
            @endphp

            <div class="flex justify-end mb-4">
                <x-accelade::action :action="$resetAction" />
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Name</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Email</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Role</th>
                            <th class="px-4 py-3 text-end text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse($demoUsers as $user)
                            @php $userRecord = (object) $user; @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white">{{ $user['name'] }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $user['email'] }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $user['role'] }}</td>
                                <td class="px-4 py-3 text-end">
                                    <x-accelade::action :action="$rowDeleteAction" :record="$userRecord" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                    All users deleted! Click "Reset Demo Data" to restore.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Soft Delete vs Force Delete Comparison --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Soft Delete vs Force Delete</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Different actions for different deletion types.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-lg">
                    <h4 class="font-medium text-slate-900 dark:text-white mb-2">Soft Delete (Default)</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Moves to trash, can be restored.</p>
                    @php
                        $softDelete = \Accelade\Actions\DeleteAction::make('soft-delete')
                            ->modalHeading('Move to Trash')
                            ->modalDescription('This record will be moved to trash and can be restored later.')
                            ->modalSubmitActionLabel('Move to Trash')
                            ->action(fn ($record) => \Accelade\Facades\Notify::warning('Trashed')->body("Moved to trash: {$record->name}"));
                    @endphp
                    <x-accelade::action :action="$softDelete" :record="$sampleUser" />
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-700 rounded-lg">
                    <h4 class="font-medium text-slate-900 dark:text-white mb-2">Force Delete</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Permanent deletion, cannot be undone.</p>
                    @php
                        $hardDelete = \Accelade\Actions\DeleteAction::make('hard-delete')
                            ->label('Delete Forever')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->modalHeading('Permanently Delete')
                            ->modalDescription('This will PERMANENTLY delete the record. This cannot be undone!')
                            ->modalSubmitActionLabel('Delete Forever')
                            ->action(fn ($record) => \Accelade\Facades\Notify::danger('Permanently Deleted')->body("Force deleted: {$record->name}"));
                    @endphp
                    <x-accelade::action :action="$hardDelete" :record="$sampleUser" />
                </div>
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
