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

    // Build button classes
    $baseClasses = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

    // Size classes
    $sizeClasses = match($size) {
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
        'xl' => 'px-6 py-3.5 text-lg',
        default => 'px-4 py-2 text-sm',
    };

    // Color classes
    if ($isOutlined) {
        $colorClasses = ($colorConfig['outline'] ?? 'border border-gray-300 text-gray-700 hover:bg-gray-50') . ' bg-transparent border';
    } else {
        $colorClasses = ($colorConfig['bg'] ?? 'bg-indigo-600 hover:bg-indigo-700') . ' ' . ($colorConfig['text'] ?? 'text-white');
    }

    // Variant-specific classes
    if ($variant === 'link') {
        $baseClasses = 'inline-flex items-center gap-1 font-medium transition-colors duration-200 underline-offset-4 hover:underline';
        $sizeClasses = match($size) {
            'xs' => 'text-xs',
            'sm' => 'text-sm',
            'lg' => 'text-base',
            'xl' => 'text-lg',
            default => 'text-sm',
        };
        $colorClasses = match($color) {
            'primary' => 'text-indigo-600 hover:text-indigo-800',
            'secondary' => 'text-gray-600 hover:text-gray-800',
            'success' => 'text-green-600 hover:text-green-800',
            'danger' => 'text-red-600 hover:text-red-800',
            'warning' => 'text-yellow-600 hover:text-yellow-800',
            'info' => 'text-cyan-600 hover:text-cyan-800',
            default => 'text-indigo-600 hover:text-indigo-800',
        };
    } elseif ($variant === 'icon') {
        $baseClasses = 'inline-flex items-center justify-center rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';
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

    // Focus ring color
    $focusRingClass = match($color) {
        'primary' => 'focus:ring-indigo-500',
        'secondary' => 'focus:ring-gray-500',
        'success' => 'focus:ring-green-500',
        'danger' => 'focus:ring-red-500',
        'warning' => 'focus:ring-yellow-500',
        'info' => 'focus:ring-cyan-500',
        default => 'focus:ring-indigo-500',
    };

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
>@if($icon && $iconPosition === 'before')<x-accelade::icon :name="$icon" :class="$iconSize" />@endif @if($variant !== 'icon')<span>{{ $label }}</span>@endif @if($icon && $iconPosition === 'after')<x-accelade::icon :name="$icon" :class="$iconSize" />@endif @if($variant === 'icon' && $icon && $iconPosition !== 'after')<x-accelade::icon :name="$icon" :class="$iconSize" />@endif</x-accelade::link>
@endif

@if(! $url && $hasAction)
<button
    type="button"
    data-action-button
    {{ $attributes->merge(['class' => $classes, 'title' => $tooltip])->merge($dataAttributes)->merge($extraAttributes) }}
    @if($isDisabled) disabled @endif
>
    @if($icon && $iconPosition === 'before')
        <x-accelade::icon :name="$icon" :class="$iconSize" />
    @endif

    @if($variant !== 'icon')
        <span>{{ $label }}</span>
    @endif

    @if($icon && $iconPosition === 'after')
        <x-accelade::icon :name="$icon" :class="$iconSize" />
    @endif

    @if($variant === 'icon' && $icon && $iconPosition !== 'after')
        <x-accelade::icon :name="$icon" :class="$iconSize" />
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
        <x-accelade::icon :name="$icon" :class="$iconSize" />
    @endif

    @if($variant !== 'icon')
        <span>{{ $slot->isEmpty() ? $label : $slot }}</span>
    @endif

    @if($icon && $iconPosition === 'after')
        <x-accelade::icon :name="$icon" :class="$iconSize" />
    @endif

    @if($variant === 'icon' && $icon && $iconPosition !== 'after')
        <x-accelade::icon :name="$icon" :class="$iconSize" />
    @endif
</button>
@endif
