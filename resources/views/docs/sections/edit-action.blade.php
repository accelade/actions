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
            $sampleUser = (object) ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin', 'status' => 'active'];
        @endphp

        {{-- Basic EditAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic EditAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                EditAction with default settings - pencil icon, primary color, "Edit" label.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicEdit = \Accelade\Actions\EditAction::make()
                        ->action(fn ($record) => \Accelade\Facades\Notify::info('Edit')->body("Editing: {$record->name}"));
                @endphp
                <x-accelade::action :action="$basicEdit" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
EditAction::make()
    ->action(fn ($record) => /* edit logic */);
            </x-accelade::code-block>
        </div>

        {{-- Customized EditAction --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Customized EditAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Override defaults with custom label, icon, and color.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $customEdit = \Accelade\Actions\EditAction::make()
                        ->label('Modify User')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->action(fn ($record) => \Accelade\Facades\Notify::warning('Modified')->body("Modified: {$record->name}"));
                @endphp
                <x-accelade::action :action="$customEdit" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
EditAction::make()
    ->label('Modify User')
    ->icon('heroicon-o-pencil-square')
    ->color('warning')
    ->action(fn ($record) => /* modify logic */);
            </x-accelade::code-block>
        </div>

        {{-- EditAction with Form Schema --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">EditAction with Form Schema</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Add form fields for inline editing. Fields can be pre-filled with current values.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $schemaEdit = \Accelade\Actions\EditAction::make()
                        ->label('Quick Edit')
                        ->modalHeading(fn ($record) => "Edit: {$record->name}")
                        ->modalDescription('Update the user details below.')
                        ->schema([
                            \Accelade\Forms\Components\TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->default(fn ($record) => $record->name ?? ''),
                            \Accelade\Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->type('email')
                                ->default(fn ($record) => $record->email ?? ''),
                            \Accelade\Forms\Components\Select::make('role')
                                ->label('Role')
                                ->options([
                                    'Admin' => 'Admin',
                                    'Editor' => 'Editor',
                                    'Viewer' => 'Viewer',
                                ])
                                ->default(fn ($record) => $record->role ?? ''),
                        ])
                        ->action(function ($record, $data) {
                            \Accelade\Facades\Notify::success('Updated')
                                ->body("Name: {$data['name']}, Role: {$data['role']}");
                        });
                @endphp
                <x-accelade::action :action="$schemaEdit" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
EditAction::make()
    ->label('Quick Edit')
    ->modalHeading(fn ($record) => "Edit: {$record->name}")
    ->schema([
        TextInput::make('name')
            ->label('Name')
            ->required()
            ->default(fn ($record) => $record->name),
        TextInput::make('email')
            ->label('Email')
            ->type('email')
            ->default(fn ($record) => $record->email),
        Select::make('role')
            ->label('Role')
            ->options(['Admin', 'Editor', 'Viewer'])
            ->default(fn ($record) => $record->role),
    ])
    ->action(function ($record, $data) {
        $record->update($data);
        return ['message' => 'Updated!'];
    });
            </x-accelade::code-block>
        </div>

        {{-- EditAction with Confirmation --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">EditAction with Confirmation</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Require user confirmation before editing.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $confirmEdit = \Accelade\Actions\EditAction::make()
                        ->label('Edit with Confirm')
                        ->requiresConfirmation()
                        ->modalHeading('Confirm Changes')
                        ->modalDescription('Are you sure you want to modify this record?')
                        ->action(fn ($record) => \Accelade\Facades\Notify::success('Confirmed Edit')->body("Edited: {$record->name}"));
                @endphp
                <x-accelade::action :action="$confirmEdit" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
EditAction::make()
    ->requiresConfirmation()
    ->modalHeading('Confirm Changes')
    ->modalDescription('Are you sure?')
    ->action(fn ($record) => $record->update([...]));
            </x-accelade::code-block>
        </div>

        {{-- Button Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                EditAction in different button styles.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @php
                    $buttonEdit = \Accelade\Actions\EditAction::make('edit-button')
                        ->button()
                        ->action(fn () => \Accelade\Facades\Notify::info('Button variant'));

                    $linkEdit = \Accelade\Actions\EditAction::make('edit-link')
                        ->link()
                        ->action(fn () => \Accelade\Facades\Notify::info('Link variant'));

                    $iconEdit = \Accelade\Actions\EditAction::make('edit-icon')
                        ->iconButton()
                        ->tooltip('Edit this item')
                        ->action(fn () => \Accelade\Facades\Notify::info('Icon variant'));

                    $outlinedEdit = \Accelade\Actions\EditAction::make('edit-outlined')
                        ->outlined()
                        ->action(fn () => \Accelade\Facades\Notify::info('Outlined variant'));
                @endphp

                <div class="text-center">
                    <x-accelade::action :action="$buttonEdit" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Button</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$linkEdit" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Link</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$iconEdit" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$outlinedEdit" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Outlined</p>
                </div>
            </div>
        </div>

        {{-- Real World Example: Table Row Actions --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Real World Example: Table Row Actions</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Common pattern: EditAction in table rows with record context.
            </p>

            @php
                $users = [
                    (object) ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin'],
                    (object) ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'Editor'],
                    (object) ['id' => 3, 'name' => 'Bob Wilson', 'email' => 'bob@example.com', 'role' => 'Viewer'],
                ];

                $rowEditAction = \Accelade\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit user')
                    ->modalHeading(fn ($record) => "Edit: {$record->name}")
                    ->schema([
                        \Accelade\Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->default(fn ($record) => $record->name ?? ''),
                        \Accelade\Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                'Admin' => 'Admin',
                                'Editor' => 'Editor',
                                'Viewer' => 'Viewer',
                            ])
                            ->default(fn ($record) => $record->role ?? ''),
                    ])
                    ->action(function ($record, $data) {
                        \Accelade\Facades\Notify::success('User Updated')
                            ->body("{$record->name} → {$data['name']} ({$data['role']})");
                    });
            @endphp

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
                        @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $user->role }}</td>
                                <td class="px-4 py-3 text-end">
                                    <x-accelade::action :action="$rowEditAction" :record="$user" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
