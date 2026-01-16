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

        {{-- Basic ForceDeleteAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic ForceDeleteAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ForceDeleteAction permanently deletes soft-deleted records. It's automatically hidden for non-trashed records.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicForceDelete = \Accelade\Actions\ForceDeleteAction::make()
                        ->action(fn ($record) => \Accelade\Facades\Notify::danger('Permanently Deleted')->body("Would permanently delete: {$record->name}"));
                @endphp
                <x-accelade::action :action="$basicForceDelete" :record="$trashedUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\ForceDeleteAction;

ForceDeleteAction::make();

// With custom delete logic
ForceDeleteAction::make()
    ->forceDeleteUsing(function ($record) {
        // Clean up related data first
        $record->attachments()->delete();
        $record->forceDelete();
        return true;
    });
            </x-accelade::code-block>
        </div>

        {{-- Customized ForceDeleteAction --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Customized ForceDeleteAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Override default labels, icons, and confirmation messages.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $customForceDelete = \Accelade\Actions\ForceDeleteAction::make()
                        ->label('Delete Forever')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->modalHeading('Permanently Delete Record?')
                        ->modalDescription('This action is irreversible. All data associated with this record will be permanently lost.')
                        ->modalSubmitActionLabel('Yes, Delete Forever')
                        ->action(fn ($record) => \Accelade\Facades\Notify::danger('Destroyed')->body("Permanently deleted: {$record->name}"));
                @endphp
                <x-accelade::action :action="$customForceDelete" :record="$trashedUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ForceDeleteAction::make()
    ->label('Delete Forever')
    ->icon('heroicon-o-exclamation-triangle')
    ->modalHeading('Permanently Delete Record?')
    ->modalDescription('This action is irreversible.')
    ->modalSubmitActionLabel('Yes, Delete Forever');
            </x-accelade::code-block>
        </div>

        {{-- Before/After Hooks --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Before & After Hooks</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Execute custom logic before or after the force delete operation.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
ForceDeleteAction::make()
    ->before(function ($record) {
        // Log the permanent deletion
        activity()
            ->performedOn($record)
            ->log('Record permanently deleted');
    })
    ->after(function ($record, $result) {
        // Clear any cached data
        Cache::forget("user.{$record->id}");

        // Notify admins
        AdminNotification::send('Record permanently deleted');
    });
            </x-accelade::code-block>
        </div>

        {{-- Trash View Integration --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Trash View Integration</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ForceDeleteAction is typically used in a "Trash" or "Deleted Items" view alongside RestoreAction.
            </p>

            {{-- Visual demo table --}}
            @php
                $trashedUsers = [
                    (object) ['id' => 1, 'name' => 'Deleted User 1', 'email' => 'user1@example.com', 'deleted_at' => now()->subDays(7)],
                    (object) ['id' => 2, 'name' => 'Deleted User 2', 'email' => 'user2@example.com', 'deleted_at' => now()->subDays(3)],
                    (object) ['id' => 3, 'name' => 'Deleted User 3', 'email' => 'user3@example.com', 'deleted_at' => now()->subDay()],
                ];

                $restoreAction = \Accelade\Actions\RestoreAction::make()
                    ->iconButton()
                    ->tooltip('Restore')
                    ->action(fn ($record) => \Accelade\Facades\Notify::success('Restored')->body("Would restore: {$record->name}"));

                $forceDeleteAction = \Accelade\Actions\ForceDeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete Forever')
                    ->action(fn ($record) => \Accelade\Facades\Notify::danger('Deleted Forever')->body("Would permanently delete: {$record->name}"));
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

            <x-accelade::code-block language="php" class="mt-4">
// Typical trash view action setup
$actions = [
    RestoreAction::make()
        ->iconButton()
        ->tooltip('Restore'),

    ForceDeleteAction::make()
        ->iconButton()
        ->tooltip('Delete Forever'),
];
            </x-accelade::code-block>
        </div>

        {{-- Visibility Behavior --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Automatic Visibility</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ForceDeleteAction automatically handles visibility based on the record's trashed state.
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

            <x-accelade::code-block language="php" class="mt-4">
// ForceDeleteAction includes these by default:
ForceDeleteAction::make()
    ->hidden(fn ($record) => ! $record?->trashed());

// toArrayWithRecord() includes:
// 'visibleWhenTrashed' => true
// 'hiddenWhenNotTrashed' => true
            </x-accelade::code-block>
        </div>
    </div>
</x-accelade::layouts.docs>
