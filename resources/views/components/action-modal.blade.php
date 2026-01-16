@props([
    'action' => null,
    'record' => null,
    'modalId' => null,
])

@php
    // Get action data
    $actionData = $action instanceof \Accelade\Actions\Action
        ? $action->toArrayWithRecord($record)
        : (is_array($action) ? $action : []);

    // Modal properties
    $hasModal = $actionData['hasModal'] ?? false;
    $hasSchema = $actionData['hasSchema'] ?? false;
    $requiresConfirmation = $actionData['requiresConfirmation'] ?? false;

    if (!$hasModal && !$hasSchema && !$requiresConfirmation) {
        return;
    }

    $modalHeading = $actionData['modalHeading'] ?? __('actions::actions.modal.confirm_title');
    $modalDescription = $actionData['modalDescription'] ?? null;
    $modalSubmitLabel = $actionData['modalSubmitActionLabel'] ?? __('actions::actions.modal.confirm');
    $modalCancelLabel = $actionData['modalCancelActionLabel'] ?? __('actions::actions.modal.cancel');
    $modalIcon = $actionData['modalIcon'] ?? null;
    $modalIconColor = $actionData['modalIconColor'] ?? 'primary';
    $confirmDanger = $actionData['confirmDanger'] ?? false;
    $slideOver = $actionData['slideOver'] ?? false;
    $modalWidth = $actionData['modalWidth'] ?? 'md';
    $color = $actionData['color'] ?? 'primary';
    $actionName = $actionData['name'] ?? 'action';
    $actionToken = $actionData['actionToken'] ?? null;
    $actionUrl = $actionData['actionUrl'] ?? null;

    // Generate unique modal ID
    $modalId = $modalId ?? 'action-modal-' . \Illuminate\Support\Str::random(8);

    // Width classes
    $widthClasses = match($modalWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        'full' => 'max-w-full',
        default => 'max-w-md',
    };

    // Button color classes
    $colors = config('actions.colors', []);
    $colorConfig = $colors[$confirmDanger ? 'danger' : $color] ?? $colors['primary'] ?? [];
    $submitBgClass = $colorConfig['bg'] ?? 'bg-indigo-600 hover:bg-indigo-700';
    $submitTextClass = $colorConfig['text'] ?? 'text-white';

    // Icon color classes
    $iconColorClass = match($modalIconColor) {
        'danger', 'red' => 'text-red-600 dark:text-red-400',
        'warning', 'yellow' => 'text-yellow-600 dark:text-yellow-400',
        'success', 'green' => 'text-green-600 dark:text-green-400',
        'info', 'blue' => 'text-blue-600 dark:text-blue-400',
        default => 'text-indigo-600 dark:text-indigo-400',
    };
@endphp

<template id="{{ $modalId }}-template">
    <div
        class="action-modal fixed inset-0 z-50 flex items-center justify-center p-4 {{ $slideOver ? 'justify-end' : '' }}"
        data-action-modal="{{ $modalId }}"
        data-action-token="{{ $actionToken }}"
        data-action-url="{{ $actionUrl }}"
        @if($record) data-action-record="{{ json_encode($record) }}" @endif
    >
        {{-- Backdrop --}}
        <div
            class="action-modal-backdrop absolute inset-0 bg-black/50 dark:bg-black/70 transition-opacity"
            data-modal-backdrop
        ></div>

        {{-- Modal Panel --}}
        <div
            class="action-modal-panel relative bg-white dark:bg-slate-800 rounded-xl shadow-xl {{ $widthClasses }} w-full {{ $slideOver ? 'h-full rounded-none' : '' }} transform transition-all"
            data-modal-panel
        >
            {{-- Header --}}
            <div class="flex items-start gap-4 p-6 border-b border-gray-200 dark:border-slate-700">
                @if($modalIcon)
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full {{ $confirmDanger ? 'bg-red-100 dark:bg-red-900/30' : 'bg-indigo-100 dark:bg-indigo-900/30' }}">
                        <x-accelade::icon :name="$modalIcon" class="w-5 h-5 {{ $iconColorClass }}" />
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $modalHeading }}
                    </h3>
                    @if($modalDescription)
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ $modalDescription }}
                        </p>
                    @endif
                </div>
                <button
                    type="button"
                    class="flex-shrink-0 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors"
                    data-modal-close
                >
                    <x-accelade::icon name="x" class="w-5 h-5" />
                </button>
            </div>

            {{-- Form Content --}}
            <form data-action-form class="action-modal-form">
                @if($hasSchema && $action instanceof \Accelade\Actions\Action)
                    <div class="p-6 space-y-4">
                        {!! $action->renderSchema($record) !!}
                    </div>
                @elseif(isset($slot) && !$slot->isEmpty())
                    <div class="p-6">
                        {{ $slot }}
                    </div>
                @endif

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50 rounded-b-xl">
                    <button
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800 transition-colors"
                        data-modal-close
                    >
                        {{ $modalCancelLabel }}
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium {{ $submitTextClass }} {{ $submitBgClass }} rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $confirmDanger ? 'focus:ring-red-500' : 'focus:ring-indigo-500' }} dark:focus:ring-offset-slate-800 transition-colors disabled:opacity-50"
                        data-modal-submit
                    >
                        <span class="action-submit-text">{{ $modalSubmitLabel }}</span>
                        <span class="action-submit-loading hidden">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
