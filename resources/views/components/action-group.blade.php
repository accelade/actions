@props([
    'group' => null,
    'record' => null,
    'align' => 'end',
])

@php
    // Get group data
    $groupData = $group instanceof \Accelade\Actions\ActionGroup
        ? $group->toArrayWithRecord($record)
        : (is_array($group) ? $group : []);

    $label = $groupData['label'] ?? null;
    $icon = $groupData['icon'] ?? 'more-vertical';
    $color = $groupData['color'] ?? 'secondary';
    $tooltip = $groupData['tooltip'] ?? null;
    $size = $groupData['size'] ?? 'md';
    $isDropdown = $groupData['dropdown'] ?? true;
    $actions = $groupData['actions'] ?? [];

    // Filter out empty/hidden actions
    $actions = array_filter($actions, fn($a) => !($a['isHidden'] ?? false));

    if (empty($actions)) {
        return;
    }

    // Icon size
    $iconSize = match($size) {
        'xs' => 'w-3 h-3',
        'sm' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
        'xl' => 'w-6 h-6',
        default => 'w-4 h-4',
    };

    // Button size
    $buttonSize = match($size) {
        'xs' => 'p-1',
        'sm' => 'p-1.5',
        'lg' => 'p-3',
        'xl' => 'p-4',
        default => 'p-2',
    };

    // Alignment with RTL support (using logical properties)
    $alignClass = match($align) {
        'start', 'left' => 'start-0 rtl:start-auto rtl:end-0',
        'center' => 'start-1/2 -translate-x-1/2 rtl:translate-x-1/2',
        default => 'end-0 rtl:end-auto rtl:start-0',
    };
@endphp

@if($isDropdown)
    <x-accelade::toggle class="relative inline-block">
        {{-- Dropdown trigger --}}
        <button
            type="button"
            @click.prevent="toggle()"
            class="inline-flex items-center justify-center gap-1 rounded-lg {{ $buttonSize }} text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-900 dark:focus:ring-indigo-400"
            @if($tooltip) title="{{ $tooltip }}" @endif
            aria-haspopup="true"
            :aria-expanded="toggled"
        >
            @if($label)
                <span>{{ $label }}</span>
            @endif
            <x-accelade::icon :name="$icon" :class="$iconSize" />
        </button>

        {{-- Dropdown menu --}}
        <x-accelade::transition show="toggled" animation="default">
            <div
                @click.outside="close()"
                class="absolute {{ $alignClass }} mt-2 w-48 rounded-lg bg-white dark:bg-slate-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10 z-50"
                role="menu"
                aria-orientation="vertical"
            >
                <div class="py-1">
                @foreach($actions as $actionData)
                    @php
                        $actionIcon = $actionData['icon'] ?? null;
                        $actionLabel = $actionData['label'] ?? 'Action';
                        $actionColor = $actionData['color'] ?? 'secondary';
                        $actionUrl = $actionData['url'] ?? null;
                        $actionDisabled = $actionData['isDisabled'] ?? false;
                        $actionMethod = $actionData['method'] ?? 'GET';
                        $requiresConfirmation = $actionData['requiresConfirmation'] ?? false;
                        $confirmDanger = $actionData['confirmDanger'] ?? false;
                        $modalHeading = $actionData['modalHeading'] ?? null;
                        $modalDescription = $actionData['modalDescription'] ?? null;
                        $modalSubmitLabel = $actionData['modalSubmitActionLabel'] ?? __('actions::actions.modal.confirm');
                        $modalCancelLabel = $actionData['modalCancelActionLabel'] ?? __('actions::actions.modal.cancel');
                        $hasAction = $actionData['hasAction'] ?? false;
                        $actionToken = $actionData['actionToken'] ?? null;
                        $actionUrlEndpoint = $actionData['actionUrl'] ?? null;

                        $textColor = match($actionColor) {
                            'danger' => 'text-red-600 dark:text-red-400',
                            'warning' => 'text-yellow-600 dark:text-yellow-400',
                            'success' => 'text-green-600 dark:text-green-400',
                            default => 'text-gray-700 dark:text-gray-200',
                        };
                    @endphp

                    @if($actionUrl)
                        <x-accelade::link
                            :href="$actionUrl"
                            :method="$actionMethod"
                            :spa="true"
                            :confirm="$requiresConfirmation ? ($modalDescription ?: true) : null"
                            :confirmTitle="$modalHeading"
                            :confirmButton="$modalSubmitLabel"
                            :cancelButton="$modalCancelLabel"
                            :confirmDanger="$confirmDanger"
                            @click="close()"
                            class="flex items-center gap-2 w-full px-4 py-2 text-sm {{ $textColor }} hover:bg-gray-100 dark:hover:bg-slate-700 {{ $actionDisabled ? 'opacity-50 cursor-not-allowed' : '' }}"
                            role="menuitem"
                        >
                            @if($actionIcon)
                                <x-accelade::icon :name="$actionIcon" class="w-4 h-4 rtl:order-last" />
                            @endif
                            <span>{{ $actionLabel }}</span>
                        </x-accelade::link>
                    @elseif($hasAction)
                        <button
                            type="button"
                            data-action-button
                            data-action-token="{{ $actionToken }}"
                            data-action-url="{{ $actionUrlEndpoint }}"
                            data-action-method="{{ $actionMethod }}"
                            @if($record !== null) data-action-record="{{ json_encode($record, JSON_THROW_ON_ERROR) }}" @endif
                            @if($requiresConfirmation)
                                data-confirm="{{ $modalDescription ?: __('actions::actions.modal.confirm_description') }}"
                                @if($modalHeading) data-confirm-title="{{ $modalHeading }}" @endif
                                data-confirm-button="{{ $modalSubmitLabel }}"
                                data-cancel-button="{{ $modalCancelLabel }}"
                                @if($confirmDanger) data-confirm-danger="true" @endif
                            @endif
                            @click="close()"
                            class="flex items-center gap-2 w-full px-4 py-2 text-sm {{ $textColor }} hover:bg-gray-100 dark:hover:bg-slate-700 {{ $actionDisabled ? 'opacity-50 cursor-not-allowed' : '' }}"
                            role="menuitem"
                            @if($actionDisabled) disabled @endif
                        >
                            @if($actionIcon)
                                <x-accelade::icon :name="$actionIcon" class="w-4 h-4 rtl:order-last" />
                            @endif
                            <span>{{ $actionLabel }}</span>
                        </button>
                    @endif
                @endforeach
                </div>
            </div>
        </x-accelade::transition>
    </x-accelade::toggle>
@else
    {{-- Inline actions (not dropdown) --}}
    <div class="flex items-center gap-2">
        @foreach($actions as $actionData)
            <x-accelade::action :action="$actionData" :record="$record" />
        @endforeach
    </div>
@endif
