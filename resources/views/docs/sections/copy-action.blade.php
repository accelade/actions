<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    <div class="space-y-8">
        {{-- Basic CopyAction --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic CopyAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Click to copy text to clipboard.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicCopy = \Accelade\Actions\CopyAction::make()
                        ->copyValue('Hello, World!');
                @endphp
                <x-accelade::action :action="$basicCopy" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
CopyAction::make()
    ->copyValue('Hello, World!');
            </x-accelade::code-block>
        </div>

        {{-- Copy with Custom Label --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Copy with Custom Label</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Customize the button label and icon.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $copyUrl = \Accelade\Actions\CopyAction::make('copy-url')
                        ->label('Copy URL')
                        ->icon('heroicon-o-link')
                        ->copyValue(url('/'));

                    $copyCode = \Accelade\Actions\CopyAction::make('copy-code')
                        ->label('Copy Discount Code')
                        ->icon('heroicon-o-ticket')
                        ->copyValue('SAVE20');
                @endphp
                <x-accelade::action :action="$copyUrl" />
                <x-accelade::action :action="$copyCode" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
CopyAction::make()
    ->label('Copy URL')
    ->icon('heroicon-o-link')
    ->copyValue(url('/'));
            </x-accelade::code-block>
        </div>

        {{-- Copy Record Attribute --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Copy Record Attribute</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Copy a specific attribute from a record.
            </p>

            @php
                $user = (object) [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'api_token' => 'sk_live_abc123xyz',
                ];
            @endphp

            <div class="flex flex-wrap gap-4">
                @php
                    $copyEmail = \Accelade\Actions\CopyAction::make('copy-email')
                        ->label('Copy Email')
                        ->icon('heroicon-o-envelope')
                        ->copyAttribute('email');

                    $copyToken = \Accelade\Actions\CopyAction::make('copy-token')
                        ->label('Copy Token')
                        ->icon('heroicon-o-key')
                        ->copyAttribute('api_token');
                @endphp
                <x-accelade::action :action="$copyEmail" :record="$user" />
                <x-accelade::action :action="$copyToken" :record="$user" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
CopyAction::make()
    ->label('Copy Email')
    ->copyAttribute('email');
            </x-accelade::code-block>
        </div>

        {{-- Icon Button Style --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Icon Button Style</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Compact icon-only button with tooltip.
            </p>

            @php
                $apiKey = 'sk_live_' . substr(md5(time()), 0, 16);
            @endphp

            <div class="flex items-center gap-3 p-3 bg-slate-100 dark:bg-slate-700 rounded-lg">
                <code class="flex-1 font-mono text-sm">{{ $apiKey }}</code>
                @php
                    $copyIcon = \Accelade\Actions\CopyAction::make('copy-key')
                        ->iconButton()
                        ->tooltip('Copy API Key')
                        ->copyValue($apiKey);
                @endphp
                <x-accelade::action :action="$copyIcon" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
CopyAction::make()
    ->iconButton()
    ->tooltip('Copy API Key')
    ->copyValue($apiKey);
            </x-accelade::code-block>
        </div>

        {{-- Button Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Different button styles for CopyAction.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @php
                    $btnCopy = \Accelade\Actions\CopyAction::make('btn')
                        ->button()
                        ->copyValue('Button');

                    $linkCopy = \Accelade\Actions\CopyAction::make('link')
                        ->link()
                        ->copyValue('Link');

                    $outlineCopy = \Accelade\Actions\CopyAction::make('outline')
                        ->outlined()
                        ->copyValue('Outlined');

                    $iconCopy = \Accelade\Actions\CopyAction::make('icon')
                        ->iconButton()
                        ->copyValue('Icon');
                @endphp

                <x-accelade::action :action="$btnCopy" />
                <x-accelade::action :action="$linkCopy" />
                <x-accelade::action :action="$outlineCopy" />
                <x-accelade::action :action="$iconCopy" />
            </div>
        </div>

        {{-- Table Example --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Table with Copy Actions</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Copy emails directly from a data table.
            </p>

            @php
                $users = [
                    (object) ['name' => 'Alice', 'email' => 'alice@example.com'],
                    (object) ['name' => 'Bob', 'email' => 'bob@example.com'],
                    (object) ['name' => 'Carol', 'email' => 'carol@example.com'],
                ];

                $tableCopy = \Accelade\Actions\CopyAction::make()
                    ->iconButton()
                    ->tooltip('Copy email')
                    ->copyAttribute('email');
            @endphp

            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-start text-xs font-medium uppercase">Name</th>
                        <th class="px-4 py-3 text-start text-xs font-medium uppercase">Email</th>
                        <th class="px-4 py-3 text-end text-xs font-medium uppercase">Copy</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @foreach($users as $u)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-400">{{ $u->email }}</td>
                            <td class="px-4 py-3 text-end">
                                <x-accelade::action :action="$tableCopy" :record="$u" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-accelade::layouts.docs>
