<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    <div class="space-y-8">
        @php
            // Sample records for demos
            $sampleUsers = collect([
                (object) ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin'],
                (object) ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'Editor'],
                (object) ['id' => 3, 'name' => 'Bob Wilson', 'email' => 'bob@example.com', 'role' => 'Viewer'],
            ]);
        @endphp

        {{-- Basic BulkAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic BulkAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                BulkAction operates on multiple selected records. Configure with <code>->action()</code> callback that receives an array of IDs.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\BulkAction;

BulkAction::make('archive')
    ->label('Archive Selected')
    ->icon('heroicon-o-archive-box')
    ->color('warning')
    ->requiresConfirmation()
    ->action(function (array $ids) {
        User::whereIn('id', $ids)->update(['archived' => true]);
        return count($ids);
    })
    ->successNotificationTitle(fn ($count) => "{$count} users archived");
            </x-accelade::code-block>
        </div>

        {{-- DeleteBulkAction --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">DeleteBulkAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Pre-configured bulk action for deleting multiple records with confirmation.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\DeleteBulkAction;

DeleteBulkAction::make()
    ->model(User::class);

// Custom delete logic
DeleteBulkAction::make()
    ->action(function (array $ids) {
        // Custom delete logic
        User::whereIn('id', $ids)->delete();
        return count($ids);
    });
            </x-accelade::code-block>
        </div>

        {{-- ForceDeleteBulkAction --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">ForceDeleteBulkAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Permanently delete multiple soft-deleted records. Only visible in trash/archived views.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\ForceDeleteBulkAction;

ForceDeleteBulkAction::make()
    ->model(User::class);

// Automatically handles:
// - Shows only for trashed records (visibleWhenTrashed)
// - Uses withTrashed() query scope
// - Calls forceDelete() on matching records
            </x-accelade::code-block>
        </div>

        {{-- RestoreBulkAction --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">RestoreBulkAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Restore multiple soft-deleted records at once.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\RestoreBulkAction;

RestoreBulkAction::make()
    ->model(User::class);

// Automatically handles:
// - Shows only for trashed records
// - Uses withTrashed() query scope
// - Calls restore() on matching records
// - Deselects records after completion
            </x-accelade::code-block>
        </div>

        {{-- BulkActionGroup --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">BulkActionGroup</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Group multiple bulk actions into a dropdown menu.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\BulkActionGroup;
use Accelade\Actions\BulkAction;
use Accelade\Actions\DeleteBulkAction;

BulkActionGroup::make([
    BulkAction::make('archive')
        ->label('Archive')
        ->icon('heroicon-o-archive-box')
        ->action(fn (array $ids) => User::whereIn('id', $ids)->update(['archived' => true])),

    BulkAction::make('export')
        ->label('Export')
        ->icon('heroicon-o-arrow-down-tray')
        ->action(fn (array $ids) => /* export logic */),

    DeleteBulkAction::make()
        ->model(User::class),
])
->label('Bulk Actions')
->icon('heroicon-o-chevron-down');
            </x-accelade::code-block>
        </div>

        {{-- BulkAction with Form Schema --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">BulkAction with Form Schema</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Collect additional data before executing the bulk action.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\BulkAction;
use Accelade\Forms\Components\Select;
use Accelade\Forms\Components\Textarea;

BulkAction::make('assign-role')
    ->label('Assign Role')
    ->icon('heroicon-o-user-group')
    ->requiresConfirmation()
    ->modalHeading('Assign Role to Selected Users')
    ->schema([
        Select::make('role')
            ->label('Select Role')
            ->options([
                'admin' => 'Administrator',
                'editor' => 'Editor',
                'viewer' => 'Viewer',
            ])
            ->required(),
        Textarea::make('reason')
            ->label('Reason for Change')
            ->placeholder('Optional note for audit log'),
    ])
    ->action(function (array $ids, array $data) {
        User::whereIn('id', $ids)->update(['role' => $data['role']]);

        if ($data['reason']) {
            AuditLog::create([
                'action' => 'bulk_role_assign',
                'data' => $data,
                'affected_ids' => $ids,
            ]);
        }

        return count($ids);
    });
            </x-accelade::code-block>
        </div>

        {{-- Deselect After Completion --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Deselect Records After Completion</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Automatically clear the selection after a successful bulk action.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
BulkAction::make('send-notification')
    ->label('Send Notification')
    ->icon('heroicon-o-bell')
    ->action(function (array $ids) {
        foreach ($ids as $id) {
            User::find($id)?->notify(new BulkNotification());
        }
        return count($ids);
    })
    ->deselectRecordsAfterCompletion(); // Clears checkbox selection
            </x-accelade::code-block>
        </div>

        {{-- Table Integration Example --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Table Integration</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Example of how bulk actions integrate with a table component.
            </p>

            {{-- Visual demo table --}}
            <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden mb-4">
                <div class="bg-slate-50 dark:bg-slate-700 px-4 py-2 flex items-center justify-between border-b border-slate-200 dark:border-slate-600">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" class="rounded border-slate-300 dark:border-slate-600" checked>
                        <span class="text-sm text-slate-600 dark:text-slate-400">3 selected</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Selected
                        </button>
                        <button class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-600 border border-slate-300 dark:border-slate-500 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-500">
                            More Actions
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($sampleUsers as $user)
                            <tr class="bg-indigo-50 dark:bg-indigo-900/20">
                                <td class="px-4 py-3 w-10">
                                    <input type="checkbox" class="rounded border-slate-300 dark:border-slate-600" checked>
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $user->role }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-accelade::code-block language="php" class="mt-4">
// In your table component or controller
$bulkActions = [
    DeleteBulkAction::make()->model(User::class),

    BulkActionGroup::make([
        BulkAction::make('archive')->label('Archive'),
        BulkAction::make('export')->label('Export'),
        RestoreBulkAction::make()->model(User::class),
    ])->label('More Actions'),
];
            </x-accelade::code-block>
        </div>
    </div>
</x-accelade::layouts.docs>
