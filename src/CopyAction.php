<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;

/**
 * CopyAction - Action for copying values to the clipboard.
 *
 * Copies any injected value, computed value, or record attribute to the clipboard.
 * Shows a notification on success/failure.
 */
class CopyAction extends Action
{
    // ============================================
    // COPY CONFIGURATION
    // ============================================

    /**
     * The value to copy (static string or closure)
     */
    protected string|Closure|null $copyValue = null;

    /**
     * The record attribute to copy
     */
    protected ?string $copyAttribute = null;

    /**
     * Multiple attributes to copy (joined)
     *
     * @var array<string>|null
     */
    protected ?array $copyAttributes = null;

    /**
     * Separator when copying multiple attributes
     */
    protected string $attributeSeparator = "\n";

    /**
     * Format the copied value
     */
    protected ?Closure $formatValue = null;

    // ============================================
    // COPY MODE
    // ============================================

    /**
     * Copy mode: 'value', 'attribute', 'element', 'selection'
     */
    protected string $copyMode = 'value';

    /**
     * CSS selector for element to copy (when mode is 'element')
     */
    protected ?string $elementSelector = null;

    /**
     * Copy as: 'text', 'html', 'json'
     */
    protected string $copyAs = 'text';

    // ============================================
    // NOTIFICATION CONFIGURATION
    // ============================================

    /**
     * Show notification after copy
     */
    protected bool $showNotification = true;

    /**
     * Success notification message
     */
    protected string|Closure|null $successMessage = null;

    /**
     * Failure notification message
     */
    protected string|Closure|null $failureMessage = null;

    /**
     * Notification duration in milliseconds
     */
    protected int $notificationDuration = 2000;

    // ============================================
    // CALLBACKS
    // ============================================

    /**
     * Callback before copying
     */
    protected ?Closure $beforeCopy = null;

    /**
     * Callback after copying
     */
    protected ?Closure $afterCopy = null;

    // ============================================
    // SETUP
    // ============================================

    protected function setUp(): void
    {
        parent::setUp();

        $this->name ??= 'copy';
        $this->label(__('actions::actions.buttons.copy'));
        $this->icon('heroicon-o-clipboard-document');
        $this->color('gray');

        // CopyAction should NOT require confirmation
        $this->requiresConfirmation(false);

        // Default success message
        $this->successMessage = __('actions::actions.notifications.copied');
        $this->failureMessage = __('actions::actions.notifications.copy_failed');
    }

    // ============================================
    // COPY VALUE METHODS
    // ============================================

    /**
     * Set the static value to copy.
     */
    public function copyValue(string|Closure $value): static
    {
        $this->copyMode = 'value';
        $this->copyValue = $value;

        return $this;
    }

    /**
     * Alias for copyValue.
     */
    public function value(string|Closure $value): static
    {
        return $this->copyValue($value);
    }

    /**
     * Set the record attribute to copy.
     */
    public function copyAttribute(string $attribute): static
    {
        $this->copyMode = 'attribute';
        $this->copyAttribute = $attribute;

        return $this;
    }

    /**
     * Alias for copyAttribute.
     */
    public function attribute(string $attribute): static
    {
        return $this->copyAttribute($attribute);
    }

    /**
     * Set multiple record attributes to copy.
     *
     * @param  array<string>  $attributes
     */
    public function copyAttributes(array $attributes): static
    {
        $this->copyMode = 'attribute';
        $this->copyAttributes = $attributes;

        return $this;
    }

    /**
     * Alias for copyAttributes.
     *
     * @param  array<string>  $attributes
     */
    public function attributes(array $attributes): static
    {
        return $this->copyAttributes($attributes);
    }

    /**
     * Set the separator for multiple attributes.
     */
    public function attributeSeparator(string $separator): static
    {
        $this->attributeSeparator = $separator;

        return $this;
    }

    /**
     * Set a callback to format the value before copying.
     */
    public function formatValue(?Closure $callback): static
    {
        $this->formatValue = $callback;

        return $this;
    }

    // ============================================
    // COPY MODE METHODS
    // ============================================

    /**
     * Copy the text content of an element.
     */
    public function copyElement(string $selector): static
    {
        $this->copyMode = 'element';
        $this->elementSelector = $selector;

        return $this;
    }

    /**
     * Alias for copyElement.
     */
    public function element(string $selector): static
    {
        return $this->copyElement($selector);
    }

    /**
     * Copy the current text selection.
     */
    public function copySelection(): static
    {
        $this->copyMode = 'selection';

        return $this;
    }

    /**
     * Alias for copySelection.
     */
    public function selection(): static
    {
        return $this->copySelection();
    }

    // ============================================
    // COPY FORMAT METHODS
    // ============================================

    /**
     * Copy as plain text.
     */
    public function asText(): static
    {
        $this->copyAs = 'text';

        return $this;
    }

    /**
     * Copy as HTML.
     */
    public function asHtml(): static
    {
        $this->copyAs = 'html';

        return $this;
    }

    /**
     * Copy as JSON.
     */
    public function asJson(): static
    {
        $this->copyAs = 'json';

        return $this;
    }

    /**
     * Set the copy format.
     */
    public function copyAs(string $format): static
    {
        $this->copyAs = $format;

        return $this;
    }

    // ============================================
    // NOTIFICATION METHODS
    // ============================================

    /**
     * Show/hide notification after copy.
     */
    public function showNotification(bool $show = true): static
    {
        $this->showNotification = $show;

        return $this;
    }

    /**
     * Hide notification after copy.
     */
    public function hideNotification(): static
    {
        $this->showNotification = false;

        return $this;
    }

    /**
     * Set the success notification message.
     */
    public function successMessage(string|Closure $message): static
    {
        $this->successMessage = $message;

        return $this;
    }

    /**
     * Set the failure notification message.
     */
    public function failureMessage(string|Closure $message): static
    {
        $this->failureMessage = $message;

        return $this;
    }

    /**
     * Set the notification duration.
     */
    public function notificationDuration(int $ms): static
    {
        $this->notificationDuration = $ms;

        return $this;
    }

    // ============================================
    // CALLBACK METHODS
    // ============================================

    /**
     * Callback to run before copying.
     */
    public function beforeCopy(?Closure $callback): static
    {
        $this->beforeCopy = $callback;

        return $this;
    }

    /**
     * Callback to run after copying.
     */
    public function afterCopy(?Closure $callback): static
    {
        $this->afterCopy = $callback;

        return $this;
    }

    // ============================================
    // GETTER METHODS
    // ============================================

    /**
     * Get the value to copy.
     */
    public function getCopyValue(mixed $record = null): ?string
    {
        $value = $this->resolveRawValue($record);
        $value = $this->applyFormatting($value, $record);

        return is_string($value) ? $value : (string) $value;
    }

    /**
     * Resolve the raw value based on copy mode.
     */
    protected function resolveRawValue(mixed $record): mixed
    {
        return match ($this->copyMode) {
            'value' => $this->resolveValueMode($record),
            'attribute' => $this->resolveAttributeMode($record),
            default => null, // 'element' and 'selection' are handled client-side
        };
    }

    /**
     * Resolve value for 'value' copy mode.
     */
    protected function resolveValueMode(mixed $record): mixed
    {
        if ($this->copyValue instanceof Closure) {
            return call_user_func($this->copyValue, $record);
        }

        return $this->copyValue;
    }

    /**
     * Resolve value for 'attribute' copy mode.
     */
    protected function resolveAttributeMode(mixed $record): mixed
    {
        if ($this->copyAttributes) {
            $values = array_map(
                fn (string $attr) => data_get($record, $attr),
                $this->copyAttributes
            );

            return implode($this->attributeSeparator, array_filter($values));
        }

        if ($this->copyAttribute) {
            return data_get($record, $this->copyAttribute);
        }

        return null;
    }

    /**
     * Apply formatting and JSON conversion to the value.
     */
    protected function applyFormatting(mixed $value, mixed $record): mixed
    {
        if ($value !== null && $this->formatValue) {
            $value = call_user_func($this->formatValue, $value, $record);
        }

        if ($value !== null && $this->copyAs === 'json' && ! is_string($value)) {
            $value = json_encode($value, JSON_PRETTY_PRINT);
        }

        return $value;
    }

    public function getCopyMode(): string
    {
        return $this->copyMode;
    }

    public function getElementSelector(): ?string
    {
        return $this->elementSelector;
    }

    public function getCopyAs(): string
    {
        return $this->copyAs;
    }

    public function shouldShowNotification(): bool
    {
        return $this->showNotification;
    }

    public function getSuccessMessage(mixed $record = null): ?string
    {
        if ($this->successMessage instanceof Closure) {
            return call_user_func($this->successMessage, $record);
        }

        return $this->successMessage;
    }

    public function getFailureMessage(mixed $record = null): ?string
    {
        if ($this->failureMessage instanceof Closure) {
            return call_user_func($this->failureMessage, $record);
        }

        return $this->failureMessage;
    }

    public function getNotificationDuration(): int
    {
        return $this->notificationDuration;
    }

    public function getBeforeCopyCallback(): ?Closure
    {
        return $this->beforeCopy;
    }

    public function getAfterCopyCallback(): ?Closure
    {
        return $this->afterCopy;
    }

    // ============================================
    // EXECUTION
    // ============================================

    /**
     * Execute the copy action.
     * Returns configuration for the JavaScript clipboard handler.
     */
    public function execute(mixed $record = null, array $data = []): mixed
    {
        // Run before callback
        if ($this->beforeCopy) {
            call_user_func($this->beforeCopy, $record, $data);
        }

        $result = [
            'mode' => $this->copyMode,
            'copyAs' => $this->copyAs,
            'showNotification' => $this->showNotification,
            'successMessage' => $this->getSuccessMessage($record),
            'failureMessage' => $this->getFailureMessage($record),
            'notificationDuration' => $this->notificationDuration,
        ];

        // Add value for value/attribute modes
        if (in_array($this->copyMode, ['value', 'attribute'], true)) {
            $result['value'] = $this->getCopyValue($record);
        }

        // Add selector for element mode
        if ($this->copyMode === 'element') {
            $result['selector'] = $this->elementSelector;
        }

        // Run after callback
        if ($this->afterCopy) {
            call_user_func($this->afterCopy, $record, $data);
        }

        return $result;
    }

    /**
     * Override toArrayWithRecord to include copy-specific data.
     */
    public function toArrayWithRecord(mixed $record = null): array
    {
        $data = parent::toArrayWithRecord($record);

        $data['copyMode'] = $this->copyMode;
        $data['copyAs'] = $this->copyAs;
        $data['showNotification'] = $this->showNotification;
        $data['successMessage'] = $this->getSuccessMessage($record);
        $data['failureMessage'] = $this->getFailureMessage($record);
        $data['notificationDuration'] = $this->notificationDuration;

        // Add mode-specific data
        if (in_array($this->copyMode, ['value', 'attribute'], true)) {
            $data['copyValue'] = $this->getCopyValue($record);
        }

        if ($this->copyMode === 'element') {
            $data['elementSelector'] = $this->elementSelector;
        }

        return $data;
    }
}
