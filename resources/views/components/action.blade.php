@props([
    'action' => null,
    'record' => null,
    'as' => 'button',
])

@php
    // Get action data
    $actionData = $action instanceof \Accelade\Actions\Action
        ? $action->toArrayWithRecord($record)
        : (is_array($action) ? $action : []);

    // Skip if hidden
    if ($actionData['isHidden'] ?? false) {
        return;
    }

    // Extract properties
    $name = $actionData['name'] ?? 'action';
    $label = $actionData['label'] ?? 'Action';
    $color = $actionData['color'] ?? 'primary';
    $icon = $actionData['icon'] ?? null;
    $iconPosition = $actionData['iconPosition'] ?? 'before';
    $url = $actionData['url'] ?? null;
    $openInNewTab = $actionData['openUrlInNewTab'] ?? false;
    $requiresConfirmation = $actionData['requiresConfirmation'] ?? false;
    $hasModal = $actionData['hasModal'] ?? false;
    $modalHeading = $actionData['modalHeading'] ?? null;
    $modalDescription = $actionData['modalDescription'] ?? null;
    $modalSubmitLabel = $actionData['modalSubmitActionLabel'] ?? __('actions::actions.modal.confirm');
    $modalCancelLabel = $actionData['modalCancelActionLabel'] ?? __('actions::actions.modal.cancel');
    $confirmDanger = $actionData['confirmDanger'] ?? false;
    $isDisabled = $actionData['isDisabled'] ?? false;
    $isOutlined = $actionData['isOutlined'] ?? false;
    $size = $actionData['size'] ?? 'md';
    $variant = $actionData['variant'] ?? 'button';
    $tooltip = $actionData['tooltip'] ?? null;
    $hasAction = $actionData['hasAction'] ?? false;
    $actionUrl = $actionData['actionUrl'] ?? null;
    $actionToken = $actionData['actionToken'] ?? null;
    $method = $actionData['method'] ?? 'POST';
    $spa = $actionData['spa'] ?? true;
    $preserveScroll = $actionData['preserveScroll'] ?? false;
    $preserveState = $actionData['preserveState'] ?? true;
    $extraAttributes = $actionData['extraAttributes'] ?? [];

    // Get color classes from config
    $colors = config('actions.colors', []);
    $colorConfig = $colors[$color] ?? $colors['primary'] ?? [];

    // Build button classes with RTL support (using logical properties)
    $baseClasses = 'action-button inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-900';

    // Size classes with RTL-friendly padding (using logical properties)
    $sizeClasses = match($size) {
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
        'xl' => 'px-6 py-3.5 text-lg',
        default => 'px-4 py-2 text-sm',
    };

    // Color classes (now include dark mode from config)
    if ($isOutlined) {
        $colorClasses = ($colorConfig['outline'] ?? 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800') . ' bg-transparent border';
    } else {
        $colorClasses = ($colorConfig['bg'] ?? 'bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600') . ' ' . ($colorConfig['text'] ?? 'text-white');
    }

    // Variant-specific classes with dark mode support (using config colors)
    if ($variant === 'link') {
        $baseClasses = 'action-button inline-flex items-center gap-1 font-medium transition-colors duration-200 underline-offset-4 hover:underline';
        $sizeClasses = match($size) {
            'xs' => 'text-xs',
            'sm' => 'text-sm',
            'lg' => 'text-base',
            'xl' => 'text-lg',
            default => 'text-sm',
        };
        // Use config colors for link variant
        $colorClasses = $colorConfig['link'] ?? 'text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300';
    } elseif ($variant === 'icon') {
        $baseClasses = 'action-button inline-flex items-center justify-center rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-900';
        $sizeClasses = match($size) {
            'xs' => 'p-1',
            'sm' => 'p-1.5',
            'lg' => 'p-3',
            'xl' => 'p-4',
            default => 'p-2',
        };
    }

    // Disabled classes
    $disabledClasses = $isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';

    // Focus ring color from config with dark mode support
    $focusRingClass = $colorConfig['focus'] ?? 'focus:ring-indigo-500 dark:focus:ring-indigo-400';

    // Combine all classes
    $classes = implode(' ', [$baseClasses, $sizeClasses, $colorClasses, $disabledClasses, $focusRingClass]);

    // Determine the element type and href
    $element = $url ? 'a' : ($as === 'button' ? 'button' : $as);

    // Build data attributes for Accelade integration
    $dataAttributes = [];
    if ($requiresConfirmation) {
        $dataAttributes['data-confirm'] = $modalDescription ?: __('actions::actions.modal.confirm_description');
        if ($modalHeading) {
            $dataAttributes['data-confirm-title'] = $modalHeading;
        }
        $dataAttributes['data-confirm-button'] = $modalSubmitLabel;
        $dataAttributes['data-cancel-button'] = $modalCancelLabel;
        if ($confirmDanger) {
            $dataAttributes['data-confirm-danger'] = 'true';
        }
    }

    if ($hasAction && $actionToken) {
        $dataAttributes['data-action-token'] = $actionToken;
        $dataAttributes['data-action-url'] = $actionUrl;
        $dataAttributes['data-action-method'] = $method;
        if ($record !== null) {
            $dataAttributes['data-action-record'] = json_encode($record, JSON_THROW_ON_ERROR);
        }
    }

    if ($preserveScroll) {
        $dataAttributes['data-preserve-scroll'] = 'true';
    }

    if ($preserveState) {
        $dataAttributes['data-preserve-state'] = 'true';
    }

    // Icon size
    $iconSize = match($size) {
        'xs' => 'w-3 h-3',
        'sm' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
        'xl' => 'w-6 h-6',
        default => 'w-4 h-4',
    };

    // RTL-aware icon position classes
    $iconBeforeClass = 'rtl:order-last';
    $iconAfterClass = 'rtl:order-first';
@endphp

@if($url)
<x-accelade::link
    :href="$url"
    :method="$method"
    :spa="$spa"
    :away="$openInNewTab"
    :preserveScroll="$preserveScroll"
    :preserveState="$preserveState"
    :confirm="$requiresConfirmation ? ($modalDescription ?: true) : null"
    :confirmTitle="$modalHeading"
    :confirmButton="$modalSubmitLabel"
    :cancelButton="$modalCancelLabel"
    :confirmDanger="$confirmDanger"
    :class="$classes"
    :title="$tooltip"
    :disabled="$isDisabled"
    {{ $attributes->except(['class', 'title', 'disabled'])->merge($extraAttributes) }}
>@if($icon && $iconPosition === 'before')<span class="action-icon {{ $iconBeforeClass }}"><x-accelade::icon :name="$icon" :class="$iconSize" /></span>@endif @if($variant !== 'icon')<span>{{ $label }}</span>@endif @if($icon && $iconPosition === 'after')<span class="action-icon {{ $iconAfterClass }}"><x-accelade::icon :name="$icon" :class="$iconSize" /></span>@endif @if($variant === 'icon' && $icon && $iconPosition !== 'after')<span class="action-icon"><x-accelade::icon :name="$icon" :class="$iconSize" /></span>@endif</x-accelade::link>
@endif

@if(! $url && $hasAction)
<button
    type="button"
    data-action-button
    {{ $attributes->merge(['class' => $classes, 'title' => $tooltip])->merge($dataAttributes)->merge($extraAttributes) }}
    @if($isDisabled) disabled @endif
>
    {{-- Loading spinner (hidden by default via inline style, shown via JS when data-loading is set) --}}
    <span class="action-spinner {{ $iconBeforeClass }}" style="display:none">
        <svg class="{{ $iconSize }}" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </span>

    @if($icon && $iconPosition === 'before')
        <span class="action-icon {{ $iconBeforeClass }}">
            <x-accelade::icon :name="$icon" :class="$iconSize" />
        </span>
    @endif

    @if($variant !== 'icon')
        <span>{{ $label }}</span>
    @endif

    @if($icon && $iconPosition === 'after')
        <span class="action-icon {{ $iconAfterClass }}">
            <x-accelade::icon :name="$icon" :class="$iconSize" />
        </span>
    @endif

    @if($variant === 'icon' && $icon && $iconPosition !== 'after')
        <span class="action-icon">
            <x-accelade::icon :name="$icon" :class="$iconSize" />
        </span>
        <span class="action-spinner" style="display:none">
            <svg class="{{ $iconSize }}" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
    @endif
</button>
@endif

@if(! $url && ! $hasAction)
<button
    type="{{ $as === 'submit' ? 'submit' : 'button' }}"
    {{ $attributes->merge(['class' => $classes, 'title' => $tooltip])->merge($dataAttributes)->merge($extraAttributes) }}
    @if($isDisabled) disabled @endif
>
    @if($icon && $iconPosition === 'before')
        <span class="action-icon {{ $iconBeforeClass }}">
            <x-accelade::icon :name="$icon" :class="$iconSize" />
        </span>
    @endif

    @if($variant !== 'icon')
        <span>{{ $slot->isEmpty() ? $label : $slot }}</span>
    @endif

    @if($icon && $iconPosition === 'after')
        <span class="action-icon {{ $iconAfterClass }}">
            <x-accelade::icon :name="$icon" :class="$iconSize" />
        </span>
    @endif

    @if($variant === 'icon' && $icon && $iconPosition !== 'after')
        <span class="action-icon">
            <x-accelade::icon :name="$icon" :class="$iconSize" />
        </span>
    @endif
</button>
@endif
