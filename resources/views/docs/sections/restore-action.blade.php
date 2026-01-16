<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    <div class="space-y-8">
        @php
            // Sample trashed record for demos
            $trashedUser = (object) [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'deleted_at' => now()->subDays(7),
            ];
        @endphp

        {{-- Basic RestoreAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic RestoreAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                RestoreAction restores soft-deleted records. It's automatically hidden for non-trashed records.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicRestore = \Accelade\Actions\RestoreAction::make()
                        ->action(fn ($record) => \Accelade\Facades\Notify::success('Restored')->body("Would restore: {$record->name}"));
                @endphp
                <x-accelade::action :action="$basicRestore" :record="$trashedUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\RestoreAction;

RestoreAction::make();

// With custom restore logic
RestoreAction::make()
    ->restoreUsing(function ($record) {
        $record->restore();
        $record->update(['restored_at' => now()]);
        return true;
    });
            </x-accelade::code-block>
        </div>

        {{-- Customized RestoreAction --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Customized RestoreAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Override default labels, icons, and confirmation messages.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $customRestore = \Accelade\Actions\RestoreAction::make()
                        ->label('Recover')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->modalHeading('Recover Deleted Record?')
                        ->modalDescription('This will restore the record and make it active again.')
                        ->modalSubmitActionLabel('Yes, Recover')
                        ->action(fn ($record) => \Accelade\Facades\Notify::success('Recovered')->body("Recovered: {$record->name}"));
                @endphp
                <x-accelade::action :action="$customRestore" :record="$trashedUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
RestoreAction::make()
    ->label('Recover')
    ->icon('heroicon-o-arrow-path')
    ->color('info')
    ->modalHeading('Recover Deleted Record?')
    ->modalDescription('This will restore the record.')
    ->modalSubmitActionLabel('Yes, Recover');
            </x-accelade::code-block>
        </div>

        {{-- Without Confirmation --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Without Confirmation</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Restore immediately without a confirmation dialog.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $quickRestore = \Accelade\Actions\RestoreAction::make('quick-restore')
                        ->label('Quick Restore')
                        ->requiresConfirmation(false)
                        ->action(fn ($record) => \Accelade\Facades\Notify::success('Restored')->body("Instantly restored: {$record->name}"));
                @endphp
                <x-accelade::action :action="$quickRestore" :record="$trashedUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
RestoreAction::make()
    ->requiresConfirmation(false);
            </x-accelade::code-block>
        </div>

        {{-- Before/After Hooks --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Before & After Hooks</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Execute custom logic before or after the restore operation.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
RestoreAction::make()
    ->before(function ($record) {
        // Log the restoration
        activity()
            ->performedOn($record)
            ->log('Record restoration started');
    })
    ->after(function ($record, $result) {
        // Send notification
        $record->notify(new AccountRestoredNotification());

        // Clear cache
        Cache::forget("user.{$record->id}.profile");
    });
            </x-accelade::code-block>
        </div>

        {{-- Button Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                RestoreAction in different button styles.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @php
                    $buttonRestore = \Accelade\Actions\RestoreAction::make('restore-button')
                        ->button()
                        ->action(fn () => \Accelade\Facades\Notify::success('Button variant'));

                    $linkRestore = \Accelade\Actions\RestoreAction::make('restore-link')
                        ->link()
                        ->action(fn () => \Accelade\Facades\Notify::success('Link variant'));

                    $iconRestore = \Accelade\Actions\RestoreAction::make('restore-icon')
                        ->iconButton()
                        ->tooltip('Restore this record')
                        ->action(fn () => \Accelade\Facades\Notify::success('Icon variant'));

                    $outlinedRestore = \Accelade\Actions\RestoreAction::make('restore-outlined')
                        ->outlined()
                        ->action(fn () => \Accelade\Facades\Notify::success('Outlined variant'));
                @endphp

                <div class="text-center">
                    <x-accelade::action :action="$buttonRestore" :record="$trashedUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Button</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$linkRestore" :record="$trashedUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Link</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$iconRestore" :record="$trashedUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$outlinedRestore" :record="$trashedUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Outlined</p>
                </div>
            </div>
        </div>

        {{-- Trash View Example --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Trash View Example</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                RestoreAction is typically paired with ForceDeleteAction in trash views.
            </p>

            @php
                $trashedUsers = [
                    (object) ['id' => 1, 'name' => 'Archived User 1', 'email' => 'user1@example.com', 'deleted_at' => now()->subDays(14)],
                    (object) ['id' => 2, 'name' => 'Archived User 2', 'email' => 'user2@example.com', 'deleted_at' => now()->subDays(5)],
                    (object) ['id' => 3, 'name' => 'Archived User 3', 'email' => 'user3@example.com', 'deleted_at' => now()->subHours(12)],
                ];

                $restoreAction = \Accelade\Actions\RestoreAction::make()
                    ->iconButton()
                    ->tooltip('Restore')
                    ->action(fn ($record) => \Accelade\Facades\Notify::success('Restored')->body("Would restore: {$record->name}"));

                $forceDeleteAction = \Accelade\Actions\ForceDeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete Forever')
                    ->action(fn ($record) => \Accelade\Facades\Notify::danger('Deleted')->body("Would delete: {$record->name}"));
            @endphp

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Name</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Email</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Deleted</th>
                            <th class="px-4 py-3 text-end text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($trashedUsers as $user)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $user->deleted_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-end">
                                    <div class="flex items-center justify-end gap-2">
                                        <x-accelade::action :action="$restoreAction" :record="$user" />
                                        <x-accelade::action :action="$forceDeleteAction" :record="$user" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Visibility Info --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Automatic Visibility</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                RestoreAction automatically handles visibility based on the record's trashed state.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <h4 class="font-medium text-green-800 dark:text-green-200 mb-2">✓ Visible When</h4>
                    <ul class="text-sm text-green-700 dark:text-green-300 space-y-1">
                        <li>• Record has <code>deleted_at</code> set</li>
                        <li>• Record's <code>trashed()</code> returns true</li>
                        <li>• In trash/archive views</li>
                    </ul>
                </div>
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <h4 class="font-medium text-red-800 dark:text-red-200 mb-2">✗ Hidden When</h4>
                    <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                        <li>• Record is not soft-deleted</li>
                        <li>• Record's <code>trashed()</code> returns false</li>
                        <li>• In normal list views</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
