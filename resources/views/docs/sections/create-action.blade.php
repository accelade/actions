<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    <div class="space-y-8">
        {{-- Basic CreateAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic CreateAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                CreateAction with default settings - plus icon, primary color, "Create" label.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicCreate = \Accelade\Actions\CreateAction::make()
                        ->action(fn () => \Accelade\Facades\Notify::success('Created')->body('New record would be created'));
                @endphp
                <x-accelade::action :action="$basicCreate" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
CreateAction::make()
    ->action(fn () => /* create logic */);
            </x-accelade::code-block>
        </div>

        {{-- Customized CreateAction --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Customized CreateAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Override defaults with custom label, icon, and color.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $customCreate = \Accelade\Actions\CreateAction::make()
                        ->label('Add New User')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->action(fn () => \Accelade\Facades\Notify::success('User Created')->body('New user added successfully'));
                @endphp
                <x-accelade::action :action="$customCreate" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
CreateAction::make()
    ->label('Add New User')
    ->icon('heroicon-o-user-plus')
    ->color('success')
    ->action(fn () => /* create logic */);
            </x-accelade::code-block>
        </div>

        {{-- CreateAction with Form Schema --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">CreateAction with Form Schema</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Add form fields to collect data before creation. Click to see the modal with form inputs.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $schemaCreate = \Accelade\Actions\CreateAction::make()
                        ->label('Create Post')
                        ->modalHeading('Create New Post')
                        ->modalDescription('Fill in the details below to create a new post.')
                        ->schema([
                            \Accelade\Forms\Components\TextInput::make('title')
                                ->label('Title')
                                ->required()
                                ->placeholder('Enter post title'),
                            \Accelade\Forms\Components\Textarea::make('content')
                                ->label('Content')
                                ->placeholder('Write your post content...')
                                ->rows(4),
                            \Accelade\Forms\Components\Select::make('category')
                                ->label('Category')
                                ->options([
                                    'news' => 'News',
                                    'blog' => 'Blog',
                                    'tutorial' => 'Tutorial',
                                ]),
                        ])
                        ->action(function ($record, $data) {
                            \Accelade\Facades\Notify::success('Post Created')
                                ->body("Title: {$data['title']}, Category: " . ($data['category'] ?? 'None'));
                        });
                @endphp
                <x-accelade::action :action="$schemaCreate" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
CreateAction::make()
    ->label('Create Post')
    ->modalHeading('Create New Post')
    ->modalDescription('Fill in the details below.')
    ->schema([
        TextInput::make('title')
            ->label('Title')
            ->required()
            ->placeholder('Enter post title'),
        Textarea::make('content')
            ->label('Content')
            ->rows(4),
        Select::make('category')
            ->label('Category')
            ->options([
                'news' => 'News',
                'blog' => 'Blog',
                'tutorial' => 'Tutorial',
            ]),
    ])
    ->action(function ($record, $data) {
        Post::create($data);
        return ['message' => 'Created!'];
    });
            </x-accelade::code-block>
        </div>

        {{-- CreateAction Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                CreateAction in different button styles.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @php
                    $buttonCreate = \Accelade\Actions\CreateAction::make('create-button')
                        ->button()
                        ->action(fn () => \Accelade\Facades\Notify::info('Button variant'));

                    $linkCreate = \Accelade\Actions\CreateAction::make('create-link')
                        ->link()
                        ->action(fn () => \Accelade\Facades\Notify::info('Link variant'));

                    $iconCreate = \Accelade\Actions\CreateAction::make('create-icon')
                        ->iconButton()
                        ->tooltip('Create new item')
                        ->action(fn () => \Accelade\Facades\Notify::info('Icon variant'));

                    $outlinedCreate = \Accelade\Actions\CreateAction::make('create-outlined')
                        ->outlined()
                        ->action(fn () => \Accelade\Facades\Notify::info('Outlined variant'));
                @endphp

                <div class="text-center">
                    <x-accelade::action :action="$buttonCreate" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Button</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$linkCreate" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Link</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$iconCreate" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$outlinedCreate" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Outlined</p>
                </div>
            </div>
        </div>

        {{-- Size Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Size Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                CreateAction in different sizes.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @foreach(['xs', 'sm', 'md', 'lg', 'xl'] as $size)
                    @php
                        $sizedCreate = \Accelade\Actions\CreateAction::make("create-{$size}")
                            ->size($size)
                            ->action(fn () => \Accelade\Facades\Notify::success("Size: {$size}"));
                    @endphp
                    <x-accelade::action :action="$sizedCreate" />
                @endforeach
            </div>
        </div>

        {{-- Real World Example --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Real World Example: Table Header</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Common pattern: CreateAction in a table header with search.
            </p>

            <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                {{-- Table Header --}}
                <div class="flex items-center justify-between px-4 py-3 bg-slate-50 dark:bg-slate-700 border-b border-slate-200 dark:border-slate-600">
                    <div class="flex items-center gap-4">
                        <h4 class="font-medium text-slate-900 dark:text-white">Users</h4>
                        <span class="text-sm text-slate-500 dark:text-slate-400">3 records</span>
                    </div>
                    @php
                        $headerCreate = \Accelade\Actions\CreateAction::make()
                            ->label('Add User')
                            ->schema([
                                \Accelade\Forms\Components\TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->placeholder('John Doe'),
                                \Accelade\Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->type('email')
                                    ->required()
                                    ->placeholder('john@example.com'),
                            ])
                            ->modalHeading('Add New User')
                            ->action(function ($record, $data) {
                                \Accelade\Facades\Notify::success('User Added')
                                    ->body("Name: {$data['name']}, Email: {$data['email']}");
                            });
                    @endphp
                    <x-accelade::action :action="$headerCreate" />
                </div>

                {{-- Table Body --}}
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead>
                        <tr class="bg-white dark:bg-slate-800">
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Name</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Email</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Role</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                        <tr>
                            <td class="px-4 py-3 text-sm text-slate-900 dark:text-white">John Doe</td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">john@example.com</td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">Admin</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm text-slate-900 dark:text-white">Jane Smith</td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">jane@example.com</td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">Editor</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm text-slate-900 dark:text-white">Bob Wilson</td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">bob@example.com</td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">Viewer</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
