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
            $sampleUser = (object) [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'role' => 'Admin',
                'status' => 'active',
                'created_at' => now()->subDays(30),
            ];
        @endphp

        {{-- Basic ViewAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic ViewAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ViewAction with default settings - eye icon, secondary color, "View" label.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicView = \Accelade\Actions\ViewAction::make()
                        ->action(fn ($record) => \Accelade\Facades\Notify::info('Viewing')->body("Record: {$record->name}"));
                @endphp
                <x-accelade::action :action="$basicView" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ViewAction::make()
    ->url(fn ($record) => route('users.show', $record));
            </x-accelade::code-block>
        </div>

        {{-- Customized ViewAction --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Customized ViewAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Override defaults with custom label, icon, and color.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $customView = \Accelade\Actions\ViewAction::make()
                        ->label('Details')
                        ->icon('heroicon-o-information-circle')
                        ->color('info')
                        ->action(fn ($record) => \Accelade\Facades\Notify::info('Details')->body("Viewing details for: {$record->name}"));
                @endphp
                <x-accelade::action :action="$customView" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ViewAction::make()
    ->label('Details')
    ->icon('heroicon-o-information-circle')
    ->color('info')
    ->url(fn ($record) => route('users.show', $record));
            </x-accelade::code-block>
        </div>

        {{-- ViewAction with Modal Display --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">ViewAction with Modal Preview</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Show record details in a modal without navigating away.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $modalView = \Accelade\Actions\ViewAction::make()
                        ->label('Quick Preview')
                        ->modalHeading(fn ($record) => "User: {$record->name}")
                        ->modalDescription('User details')
                        ->modalWidth('lg')
                        ->schema([
                            \Accelade\Forms\Components\TextInput::make('id')
                                ->label('ID')
                                ->disabled()
                                ->default(fn ($record) => (string) $record->id),
                            \Accelade\Forms\Components\TextInput::make('name')
                                ->label('Name')
                                ->disabled()
                                ->default(fn ($record) => $record->name),
                            \Accelade\Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->disabled()
                                ->default(fn ($record) => $record->email),
                            \Accelade\Forms\Components\TextInput::make('role')
                                ->label('Role')
                                ->disabled()
                                ->default(fn ($record) => $record->role),
                            \Accelade\Forms\Components\TextInput::make('status')
                                ->label('Status')
                                ->disabled()
                                ->default(fn ($record) => ucfirst($record->status)),
                        ])
                        ->modalSubmitActionLabel('Close')
                        ->action(fn () => null);
                @endphp
                <x-accelade::action :action="$modalView" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ViewAction::make()
    ->label('Quick Preview')
    ->modalHeading(fn ($record) => "User: {$record->name}")
    ->modalWidth('lg')
    ->schema([
        TextInput::make('name')
            ->label('Name')
            ->disabled()
            ->default(fn ($record) => $record->name),
        TextInput::make('email')
            ->label('Email')
            ->disabled()
            ->default(fn ($record) => $record->email),
        // ... more fields
    ])
    ->modalSubmitActionLabel('Close');
            </x-accelade::code-block>
        </div>

        {{-- Button Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ViewAction in different button styles.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @php
                    $buttonView = \Accelade\Actions\ViewAction::make('view-button')
                        ->button()
                        ->action(fn () => \Accelade\Facades\Notify::info('Button variant'));

                    $linkView = \Accelade\Actions\ViewAction::make('view-link')
                        ->link()
                        ->action(fn () => \Accelade\Facades\Notify::info('Link variant'));

                    $iconView = \Accelade\Actions\ViewAction::make('view-icon')
                        ->iconButton()
                        ->tooltip('View details')
                        ->action(fn () => \Accelade\Facades\Notify::info('Icon variant'));

                    $outlinedView = \Accelade\Actions\ViewAction::make('view-outlined')
                        ->outlined()
                        ->action(fn () => \Accelade\Facades\Notify::info('Outlined variant'));
                @endphp

                <div class="text-center">
                    <x-accelade::action :action="$buttonView" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Button</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$linkView" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Link</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$iconView" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$outlinedView" :record="$sampleUser" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Outlined</p>
                </div>
            </div>
        </div>

        {{-- Open in New Tab --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Open in New Tab</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ViewAction that opens the target URL in a new browser tab.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $newTabView = \Accelade\Actions\ViewAction::make()
                        ->label('View (New Tab)')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url('/')
                        ->openUrlInNewTab();
                @endphp
                <x-accelade::action :action="$newTabView" :record="$sampleUser" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ViewAction::make()
    ->label('View (New Tab)')
    ->icon('heroicon-o-arrow-top-right-on-square')
    ->url(fn ($record) => route('users.show', $record))
    ->openUrlInNewTab();
            </x-accelade::code-block>
        </div>

        {{-- Real World Example: Table Row Actions --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Real World Example: Table Row Actions</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Common pattern: ViewAction in table rows with record context.
            </p>

            @php
                $users = [
                    (object) ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'role' => 'Admin', 'status' => 'active', 'created_at' => now()->subDays(30)],
                    (object) ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'role' => 'Editor', 'status' => 'active', 'created_at' => now()->subDays(15)],
                    (object) ['id' => 3, 'name' => 'Bob Wilson', 'email' => 'bob@example.com', 'role' => 'Viewer', 'status' => 'inactive', 'created_at' => now()->subDays(7)],
                ];

                $rowViewAction = \Accelade\Actions\ViewAction::make()
                    ->iconButton()
                    ->tooltip('View user details')
                    ->modalHeading(fn ($record) => "User Details: {$record->name}")
                    ->modalWidth('lg')
                    ->schema([
                        \Accelade\Forms\Components\TextInput::make('id')
                            ->label('User ID')
                            ->disabled()
                            ->default(fn ($record) => "#{$record->id}"),
                        \Accelade\Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->disabled()
                            ->default(fn ($record) => $record->name),
                        \Accelade\Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->disabled()
                            ->default(fn ($record) => $record->email),
                        \Accelade\Forms\Components\TextInput::make('role')
                            ->label('Role')
                            ->disabled()
                            ->default(fn ($record) => $record->role),
                        \Accelade\Forms\Components\TextInput::make('status')
                            ->label('Account Status')
                            ->disabled()
                            ->default(fn ($record) => ucfirst($record->status)),
                        \Accelade\Forms\Components\TextInput::make('created_at')
                            ->label('Member Since')
                            ->disabled()
                            ->default(fn ($record) => $record->created_at->format('F j, Y')),
                    ])
                    ->modalSubmitActionLabel('Close')
                    ->action(fn () => null);
            @endphp

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Name</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Email</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Role</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Status</th>
                            <th class="px-4 py-3 text-end text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $user->role }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <x-accelade::action :action="$rowViewAction" :record="$user" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Link Style for Content --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Link Style for Content</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ViewAction styled as a link, perfect for "Read more" patterns.
            </p>

            <div class="max-w-md">
                <div class="p-4 border border-slate-200 dark:border-slate-700 rounded-lg">
                    <h4 class="font-medium text-slate-900 dark:text-white">Blog Post Title</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore...
                    </p>
                    <div class="mt-3">
                        @php
                            $readMoreView = \Accelade\Actions\ViewAction::make()
                                ->label('Read more')
                                ->link()
                                ->icon('heroicon-o-arrow-right')
                                ->iconPosition('after')
                                ->color('primary')
                                ->action(fn () => \Accelade\Facades\Notify::info('View Post')->body('Would navigate to full post'));
                        @endphp
                        <x-accelade::action :action="$readMoreView" :record="$sampleUser" />
                    </div>
                </div>
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ViewAction::make()
    ->label('Read more')
    ->link()
    ->icon('heroicon-o-arrow-right')
    ->iconPosition('after')
    ->url(fn ($record) => route('posts.show', $record));
            </x-accelade::code-block>
        </div>
    </div>
</x-accelade::layouts.docs>
