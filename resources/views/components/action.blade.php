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

    // Check if hidden
    $isActionHidden = $actionData['isHidden'] ?? false;

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
    $hasSchema = $actionData['hasSchema'] ?? false;
    $schema = $actionData['schema'] ?? [];
    $schemaDefaults = $actionData['schemaDefaults'] ?? [];
    $modalHeading = $actionData['modalHeading'] ?? null;
    $modalDescription = $actionData['modalDescription'] ?? null;
    $modalSubmitLabel = $actionData['modalSubmitActionLabel'] ?? __('actions::actions.modal.confirm');
    $modalCancelLabel = $actionData['modalCancelActionLabel'] ?? __('actions::actions.modal.cancel');
    $modalIcon = $actionData['modalIcon'] ?? null;
    $modalIconColor = $actionData['modalIconColor'] ?? 'primary';
    $modalWidth = $actionData['modalWidth'] ?? 'md';
    $slideOver = $actionData['slideOver'] ?? false;
    $confirmDanger = $actionData['confirmDanger'] ?? false;
    $isDisabled = $actionData['isDisabled'] ?? false;
    $isOutlined = $actionData['isOutlined'] ?? false;
    $size = $actionData['size'] ?? 'md';
    $variant = $actionData['variant'] ?? 'button';
    $tooltip = $actionData['tooltip'] ?? null;
    $tooltipConfig = $actionData['tooltipConfig'] ?? $tooltip;
    $hasAction = $actionData['hasAction'] ?? false;
    $actionUrl = $actionData['actionUrl'] ?? null;
    $actionToken = $actionData['actionToken'] ?? null;
    $method = $actionData['method'] ?? 'POST';
    $spa = $actionData['spa'] ?? true;
    $preserveScroll = $actionData['preserveScroll'] ?? false;
    $preserveState = $actionData['preserveState'] ?? true;
    $extraAttributes = $actionData['extraAttributes'] ?? [];

    // Check if this is a client-side action (copy or print)
    $isClientSideAction = $action instanceof \Accelade\Actions\CopyAction || $action instanceof \Accelade\Actions\PrintAction;

    // Generate unique modal ID if action has schema
    $modalId = $hasSchema ? 'action-modal-' . \Illuminate\Support\Str::random(8) : null;

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

    // Only add server-side action attributes for non-client-side actions
    // CopyAction and PrintAction are handled entirely by JavaScript
    if ($hasAction && $actionToken && ! $isClientSideAction) {
        $dataAttributes['data-action-token'] = $actionToken;
        $dataAttributes['data-action-url'] = $actionUrl;
        $dataAttributes['data-action-method'] = $method;
        if ($record !== null) {
            $dataAttributes['data-action-record'] = json_encode($record, JSON_THROW_ON_ERROR);
        }
    }

    // CopyAction specific data attributes
    if ($action instanceof \Accelade\Actions\CopyAction) {
        $dataAttributes['data-action-type'] = 'copy';
        $dataAttributes['data-copy-mode'] = $actionData['copyMode'] ?? 'value';
        $dataAttributes['data-copy-as'] = $actionData['copyAs'] ?? 'text';
        if (isset($actionData['copyValue'])) {
            $dataAttributes['data-copy-value'] = $actionData['copyValue'];
        }
        if (isset($actionData['elementSelector'])) {
            $dataAttributes['data-copy-element'] = $actionData['elementSelector'];
        }
        $dataAttributes['data-show-notification'] = ($actionData['showNotification'] ?? true) ? 'true' : 'false';
        $dataAttributes['data-success-message'] = $actionData['successMessage'] ?? __('actions::actions.notifications.copied');
        $dataAttributes['data-failure-message'] = $actionData['failureMessage'] ?? __('actions::actions.notifications.copy_failed');
        $dataAttributes['data-notification-duration'] = $actionData['notificationDuration'] ?? 2000;
    }

    // PrintAction specific data attributes
    if ($action instanceof \Accelade\Actions\PrintAction) {
        $dataAttributes['data-action-type'] = 'print';
        $dataAttributes['data-print-mode'] = $actionData['printMode'] ?? 'page';
        if (isset($actionData['selector'])) {
            $dataAttributes['data-print-element'] = $actionData['selector'];
        }
        if (isset($actionData['htmlContent'])) {
            $dataAttributes['data-print-html'] = base64_encode($actionData['htmlContent']);
        }
        if (isset($actionData['printUrl'])) {
            $dataAttributes['data-print-url'] = $actionData['printUrl'];
        }
        if (isset($actionData['documentTitle'])) {
            $dataAttributes['data-document-title'] = $actionData['documentTitle'];
        }
        $dataAttributes['data-auto-print'] = ($actionData['autoPrint'] ?? true) ? 'true' : 'false';
        $dataAttributes['data-print-delay'] = $actionData['printDelay'] ?? 0;
        if (isset($actionData['printCss'])) {
            $dataAttributes['data-print-css'] = base64_encode($actionData['printCss']);
        }
        $dataAttributes['data-page-size'] = $actionData['pageSize'] ?? 'A4';
        $dataAttributes['data-orientation'] = $actionData['orientation'] ?? 'portrait';
    }

    // Schema/modal data attributes
    if ($hasSchema) {
        $dataAttributes['data-has-schema'] = 'true';
        $dataAttributes['data-modal-id'] = $modalId;
        // Render schema HTML server-side using Forms package components
        if ($action instanceof \Accelade\Actions\Action) {
            $renderedSchemaHtml = $action->renderSchema($record);
            $dataAttributes['data-schema-html'] = base64_encode($renderedSchemaHtml);
        }
        // Also pass schema array for field names (needed for form submission handling)
        $dataAttributes['data-schema'] = json_encode($schema, JSON_THROW_ON_ERROR);
        if (!empty($schemaDefaults)) {
            $dataAttributes['data-schema-defaults'] = json_encode($schemaDefaults, JSON_THROW_ON_ERROR);
        }
        $dataAttributes['data-modal-heading'] = $modalHeading ?? $label;
        if ($modalDescription) {
            $dataAttributes['data-modal-description'] = $modalDescription;
        }
        $dataAttributes['data-modal-submit-label'] = $modalSubmitLabel;
        $dataAttributes['data-modal-cancel-label'] = $modalCancelLabel;
        if ($modalIcon) {
            $dataAttributes['data-modal-icon'] = $modalIcon;
        }
        $dataAttributes['data-modal-icon-color'] = $modalIconColor;
        $dataAttributes['data-modal-width'] = $modalWidth;
        if ($slideOver) {
            $dataAttributes['data-slide-over'] = 'true';
        }
        $dataAttributes['data-action-color'] = $color;
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

    // Helper function to render icon inline (avoiding Blade component compilation issues)
    $renderIcon = function($iconName, $iconClass) {
        if (!$iconName) {
            return '';
        }
        try {
            if (app()->bound(\BladeUI\Icons\Factory::class)) {
                return app(\BladeUI\Icons\Factory::class)->svg($iconName, $iconClass)->toHtml();
            }
        } catch (\Exception $e) {
            // Fallback to empty on error
        }
        return '';
    };
@endphp

@php
    // Build tooltip attribute if configured
    $tooltipAttr = $tooltipConfig ? ['a-tooltip' => $tooltipConfig] : [];

    // Build link-specific attributes for URL actions
    $framework = config('accelade.framework', 'vanilla');
    $linkDataAttrs = [];
    if ($url) {
        if ($spa && !$openInNewTab) {
            $linkDataAttrs[$framework === 'vanilla' ? 'a-link' : ($framework === 'vue' ? 'data-accelade-link' : 'data-spa-link')] = true;
        }
        if ($openInNewTab) {
            $linkDataAttrs['target'] = '_blank';
            $linkDataAttrs['rel'] = 'noopener noreferrer';
            $linkDataAttrs['data-away'] = true;
        }
        if ($method !== 'GET') {
            $linkDataAttrs['data-method'] = strtoupper($method);
        }
        if ($preserveScroll) {
            $linkDataAttrs['data-preserve-scroll'] = true;
        }
        if ($preserveState) {
            $linkDataAttrs['data-preserve-state'] = true;
        }
        if ($requiresConfirmation) {
            $linkDataAttrs['data-confirm'] = $modalDescription ?: 'Are you sure you want to continue?';
            if ($modalHeading) {
                $linkDataAttrs['data-confirm-title'] = $modalHeading;
            }
            if ($modalSubmitLabel) {
                $linkDataAttrs['data-confirm-button'] = $modalSubmitLabel;
            }
            if ($modalCancelLabel) {
                $linkDataAttrs['data-cancel-button'] = $modalCancelLabel;
            }
            if ($confirmDanger) {
                $linkDataAttrs['data-confirm-danger'] = true;
            }
        }
        if ($isDisabled) {
            $linkDataAttrs['aria-disabled'] = 'true';
            $linkDataAttrs['tabindex'] = '-1';
        }
    }
@endphp

@if(! $isActionHidden)
    {{-- URL-based action - rendered as native <a> tag with SPA attributes --}}
    @if($url)
        <a href="{{ $url }}" {{ $attributes->merge(['class' => $classes])->merge($linkDataAttrs)->merge($tooltipAttr)->merge($extraAttributes) }}>
            @if($variant === 'icon' && $icon)
                <span class="action-icon">{!! $renderIcon($icon, $iconSize) !!}</span>
            @else
                @if($icon && $iconPosition === 'before')
                    <span class="action-icon {{ $iconBeforeClass }}">{!! $renderIcon($icon, $iconSize) !!}</span>
                @endif
                <span>{{ $label }}</span>
                @if($icon && $iconPosition === 'after')
                    <span class="action-icon {{ $iconAfterClass }}">{!! $renderIcon($icon, $iconSize) !!}</span>
                @endif
            @endif
        </a>
    @endif

    {{-- Client-side actions (CopyAction, PrintAction) - handled entirely by JavaScript --}}
    @if(! $url && $isClientSideAction)
        <button type="button" data-action-button {{ $attributes->merge(['class' => $classes])->merge($dataAttributes)->merge($tooltipAttr)->merge($extraAttributes) }} @disabled($isDisabled)>
            @if($variant === 'icon' && $icon)
                <span class="action-icon">{!! $renderIcon($icon, $iconSize) !!}</span>
            @else
                @if($icon && $iconPosition === 'before')
                    <span class="action-icon {{ $iconBeforeClass }}">{!! $renderIcon($icon, $iconSize) !!}</span>
                @endif
                <span>{{ $label }}</span>
                @if($icon && $iconPosition === 'after')
                    <span class="action-icon {{ $iconAfterClass }}">{!! $renderIcon($icon, $iconSize) !!}</span>
                @endif
            @endif
        </button>
    @endif

    {{-- Server-side actions - sends request to backend --}}
    @if(! $url && ! $isClientSideAction && $hasAction)
        <button type="button" data-action-button {{ $attributes->merge(['class' => $classes])->merge($dataAttributes)->merge($tooltipAttr)->merge($extraAttributes) }} @disabled($isDisabled)>
            {{-- Loading spinner (hidden by default via inline style, shown via JS when data-loading is set) --}}
            <span class="action-spinner {{ $iconBeforeClass }}" style="display:none">
                <svg class="{{ $iconSize }}" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            @if($variant === 'icon' && $icon)
                <span class="action-icon">{!! $renderIcon($icon, $iconSize) !!}</span>
            @else
                @if($icon && $iconPosition === 'before')
                    <span class="action-icon {{ $iconBeforeClass }}">{!! $renderIcon($icon, $iconSize) !!}</span>
                @endif
                <span>{{ $label }}</span>
                @if($icon && $iconPosition === 'after')
                    <span class="action-icon {{ $iconAfterClass }}">{!! $renderIcon($icon, $iconSize) !!}</span>
                @endif
            @endif
        </button>
    @endif

    {{-- Generic button (no URL, not client-side, no server action) --}}
    @if(! $url && ! $isClientSideAction && ! $hasAction)
        <button type="{{ $as === 'submit' ? 'submit' : 'button' }}" {{ $attributes->merge(['class' => $classes])->merge($dataAttributes)->merge($tooltipAttr)->merge($extraAttributes) }} @disabled($isDisabled)>
            @if($variant === 'icon' && $icon)
                <span class="action-icon">{!! $renderIcon($icon, $iconSize) !!}</span>
            @else
                @if($icon && $iconPosition === 'before')
                    <span class="action-icon {{ $iconBeforeClass }}">{!! $renderIcon($icon, $iconSize) !!}</span>
                @endif
                <span>{{ (isset($slot) && !$slot->isEmpty()) ? $slot : $label }}</span>
                @if($icon && $iconPosition === 'after')
                    <span class="action-icon {{ $iconAfterClass }}">{!! $renderIcon($icon, $iconSize) !!}</span>
                @endif
            @endif
        </button>
    @endif
@endif
