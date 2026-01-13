<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    {{-- Demo Content --}}
    <div class="space-y-8">
        {{-- Basic Actions --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic Actions</h3>
            <div class="flex flex-wrap gap-4">
                @php
                    $basicAction = \Accelade\Actions\Action::make('demo')
                        ->label('Click Me')
                        ->color('primary');

                    $iconAction = \Accelade\Actions\Action::make('save')
                        ->label('Save')
                        ->icon('check')
                        ->color('success');

                    $outlinedAction = \Accelade\Actions\Action::make('cancel')
                        ->label('Cancel')
                        ->icon('x')
                        ->color('secondary')
                        ->outlined();
                @endphp

                <x-accelade::action :action="$basicAction" />
                <x-accelade::action :action="$iconAction" />
                <x-accelade::action :action="$outlinedAction" />
            </div>
        </div>

        {{-- Color Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Color Variants</h3>
            <div class="flex flex-wrap gap-3">
                @foreach(['primary', 'secondary', 'success', 'danger', 'warning', 'info'] as $color)
                    @php
                        $colorAction = \Accelade\Actions\Action::make("color-{$color}")
                            ->label(ucfirst($color))
                            ->color($color);
                    @endphp
                    <x-accelade::action :action="$colorAction" />
                @endforeach
            </div>
        </div>

        {{-- Pre-configured Actions --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Pre-configured Actions</h3>
            <div class="flex flex-wrap gap-4">
                @php
                    $viewAction = \Accelade\Actions\ViewAction::make()->url('#');
                    $editAction = \Accelade\Actions\EditAction::make()->url('#');
                    $createAction = \Accelade\Actions\CreateAction::make()->url('#');
                    $deleteAction = \Accelade\Actions\DeleteAction::make()->url('#');
                @endphp

                <x-accelade::action :action="$viewAction" />
                <x-accelade::action :action="$editAction" />
                <x-accelade::action :action="$createAction" />
                <x-accelade::action :action="$deleteAction" />
            </div>
        </div>

        {{-- Size Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Size Variants</h3>
            <div class="flex flex-wrap items-center gap-4">
                @foreach(['xs', 'sm', 'md', 'lg', 'xl'] as $size)
                    @php
                        $sizeAction = \Accelade\Actions\Action::make("size-{$size}")
                            ->label(strtoupper($size))
                            ->icon('check')
                            ->size($size);
                    @endphp
                    <x-accelade::action :action="$sizeAction" />
                @endforeach
            </div>
        </div>
    </div>
</x-accelade::layouts.docs>
