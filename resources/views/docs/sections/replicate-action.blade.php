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
            $samplePost = (object) [
                'id' => 1,
                'title' => 'My Amazing Blog Post',
                'slug' => 'my-amazing-blog-post',
                'content' => 'Lorem ipsum dolor sit amet...',
                'status' => 'published',
                'author_id' => 1,
                'created_at' => now()->subDays(30),
            ];
        @endphp

        {{-- Basic ReplicateAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic ReplicateAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ReplicateAction creates a copy of an existing record using Laravel's <code>replicate()</code> method.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicReplicate = \Accelade\Actions\ReplicateAction::make()
                        ->action(fn ($record) => \Accelade\Facades\Notify::success('Replicated')->body("Would replicate: {$record->title}"));
                @endphp
                <x-accelade::action :action="$basicReplicate" :record="$samplePost" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\ReplicateAction;

ReplicateAction::make();
            </x-accelade::code-block>
        </div>

        {{-- Exclude Attributes --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Exclude Attributes</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Specify which attributes should NOT be copied to the replica.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $excludeReplicate = \Accelade\Actions\ReplicateAction::make('replicate-exclude')
                        ->label('Duplicate Post')
                        ->excludeAttributes(['slug', 'published_at', 'views_count'])
                        ->action(fn ($record) => \Accelade\Facades\Notify::success('Duplicated')->body("Duplicated without slug, published_at, views_count"));
                @endphp
                <x-accelade::action :action="$excludeReplicate" :record="$samplePost" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ReplicateAction::make()
    ->excludeAttributes([
        'slug',           // Will generate new slug
        'published_at',   // Won't copy publish date
        'views_count',    // Reset view counter
    ]);
            </x-accelade::code-block>
        </div>

        {{-- Before Replica Saved --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Before Replica Saved</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Modify the replica before it's saved to the database.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $beforeReplicate = \Accelade\Actions\ReplicateAction::make('replicate-before')
                        ->label('Clone as Draft')
                        ->beforeReplicaSaved(function ($replica, $original, $data) {
                            // These changes would be applied to $replica
                        })
                        ->action(fn ($record) => \Accelade\Facades\Notify::success('Cloned')->body("Created draft copy of: {$record->title}"));
                @endphp
                <x-accelade::action :action="$beforeReplicate" :record="$samplePost" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ReplicateAction::make()
    ->beforeReplicaSaved(function ($replica, $original, $data) {
        // Modify the replica before saving
        $replica->title = 'Copy of ' . $replica->title;
        $replica->slug = Str::slug($replica->title);
        $replica->status = 'draft';
        $replica->published_at = null;
    });
            </x-accelade::code-block>
        </div>

        {{-- After Replica Saved --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">After Replica Saved</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Execute logic after the replica has been saved (e.g., copy relationships).
            </p>

            <x-accelade::code-block language="php" class="mt-4">
ReplicateAction::make()
    ->afterReplicaSaved(function ($replica, $original, $data) {
        // Copy tags relationship
        $replica->tags()->attach($original->tags->pluck('id'));

        // Copy media/attachments
        foreach ($original->media as $media) {
            $replica->addMedia($media->getPath())
                ->preservingOriginal()
                ->toMediaCollection($media->collection_name);
        }

        // Log the replication
        activity()
            ->performedOn($replica)
            ->causedBy(auth()->user())
            ->log("Replicated from #{$original->id}");
    });
            </x-accelade::code-block>
        </div>

        {{-- With Form Schema --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">With Form Schema</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Collect additional data when replicating (e.g., new title or settings).
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $schemaReplicate = \Accelade\Actions\ReplicateAction::make('replicate-schema')
                        ->label('Duplicate with Options')
                        ->modalHeading('Duplicate Post')
                        ->modalDescription('Choose options for the duplicate.')
                        ->schema([
                            \Accelade\Forms\Components\TextInput::make('new_title')
                                ->label('New Title')
                                ->placeholder('Leave blank to auto-generate')
                                ->helperText('Will prepend "Copy of" if left blank'),
                            \Accelade\Forms\Components\Checkbox::make('copy_tags')
                                ->label('Copy tags')
                                ->default(true),
                            \Accelade\Forms\Components\Checkbox::make('copy_media')
                                ->label('Copy media attachments')
                                ->default(true),
                        ])
                        ->action(fn ($record, $data) => \Accelade\Facades\Notify::success('Duplicated')->body("Options: " . json_encode($data)));
                @endphp
                <x-accelade::action :action="$schemaReplicate" :record="$samplePost" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ReplicateAction::make()
    ->modalHeading('Duplicate Post')
    ->schema([
        TextInput::make('new_title')
            ->label('New Title')
            ->placeholder('Leave blank to auto-generate'),
        Checkbox::make('copy_tags')
            ->label('Copy tags')
            ->default(true),
        Checkbox::make('copy_media')
            ->label('Copy media attachments')
            ->default(true),
    ])
    ->beforeReplicaSaved(function ($replica, $original, $data) {
        if ($data['new_title']) {
            $replica->title = $data['new_title'];
            $replica->slug = Str::slug($data['new_title']);
        }
    })
    ->afterReplicaSaved(function ($replica, $original, $data) {
        if ($data['copy_tags']) {
            $replica->tags()->attach($original->tags->pluck('id'));
        }
        if ($data['copy_media']) {
            // Copy media logic
        }
    });
            </x-accelade::code-block>
        </div>

        {{-- Redirect After Replication --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Redirect After Replication</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Automatically redirect to the new record's edit page after replication.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
ReplicateAction::make()
    ->replicaRedirectUrl(fn ($replica) => route('posts.edit', $replica))
    ->successNotificationTitle('Post Duplicated')
    ->successNotificationBody(fn ($replica) => "Redirecting to edit '{$replica->title}'...");
            </x-accelade::code-block>
        </div>

        {{-- Button Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ReplicateAction in different button styles.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @php
                    $buttonReplicate = \Accelade\Actions\ReplicateAction::make('replicate-button')
                        ->button()
                        ->action(fn () => \Accelade\Facades\Notify::success('Button variant'));

                    $linkReplicate = \Accelade\Actions\ReplicateAction::make('replicate-link')
                        ->link()
                        ->action(fn () => \Accelade\Facades\Notify::success('Link variant'));

                    $iconReplicate = \Accelade\Actions\ReplicateAction::make('replicate-icon')
                        ->iconButton()
                        ->tooltip('Duplicate')
                        ->action(fn () => \Accelade\Facades\Notify::success('Icon variant'));

                    $outlinedReplicate = \Accelade\Actions\ReplicateAction::make('replicate-outlined')
                        ->outlined()
                        ->action(fn () => \Accelade\Facades\Notify::success('Outlined variant'));
                @endphp

                <div class="text-center">
                    <x-accelade::action :action="$buttonReplicate" :record="$samplePost" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Button</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$linkReplicate" :record="$samplePost" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Link</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$iconReplicate" :record="$samplePost" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$outlinedReplicate" :record="$samplePost" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Outlined</p>
                </div>
            </div>
        </div>

        {{-- Real World Example --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Real World Example: Content Management</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Common pattern: ReplicateAction in a CMS for duplicating posts, pages, or templates.
            </p>

            @php
                $posts = [
                    (object) ['id' => 1, 'title' => 'Getting Started Guide', 'status' => 'published', 'author' => 'John'],
                    (object) ['id' => 2, 'title' => 'Advanced Techniques', 'status' => 'draft', 'author' => 'Jane'],
                    (object) ['id' => 3, 'title' => 'Best Practices', 'status' => 'published', 'author' => 'Bob'],
                ];

                $rowReplicate = \Accelade\Actions\ReplicateAction::make()
                    ->iconButton()
                    ->tooltip('Duplicate')
                    ->action(fn ($record) => \Accelade\Facades\Notify::success('Duplicated')->body("Would duplicate: {$record->title}"));
            @endphp

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Title</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Status</th>
                            <th class="px-4 py-3 text-start text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Author</th>
                            <th class="px-4 py-3 text-end text-xs font-medium text-slate-500 dark:text-slate-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                        @foreach($posts as $post)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white">{{ $post->title }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $post->status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $post->author }}</td>
                                <td class="px-4 py-3 text-end">
                                    <x-accelade::action :action="$rowReplicate" :record="$post" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
