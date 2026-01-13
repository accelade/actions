<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Accelade\Actions\Concerns\CanBeHidden;
use Accelade\Actions\Concerns\HasColor;
use Accelade\Actions\Concerns\HasIcon;
use Accelade\Actions\Concerns\HasLabel;
use Accelade\Actions\Concerns\HasModal;
use Accelade\Actions\Concerns\HasUrl;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Action implements Arrayable
{
    use CanBeHidden;
    use HasColor;
    use HasIcon;
    use HasLabel;
    use HasModal;
    use HasUrl;

    protected ?string $name = null;

    protected ?Closure $action = null;

    protected ?Closure $authorize = null;

    protected array $extraAttributes = [];

    protected ?string $size = null;

    protected bool $isDisabled = false;

    protected bool $isOutlined = false;

    protected ?string $variant = null;

    protected ?string $tooltip = null;

    protected ?string $cachedActionToken = null;

    protected bool $preserveState = true;

    protected bool $preserveScroll = true;

    protected string $method = 'POST';

    protected bool $spa = true;

    final public function __construct(?string $name = null)
    {
        $this->name = $name;
        $this->setUp();
    }

    /**
     * Configure the action.
     * Override this method in subclasses to set up defaults.
     */
    protected function setUp(): void {}

    public static function make(?string $name = null): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function name(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function action(?Closure $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function authorize(?Closure $authorize): static
    {
        $this->authorize = $authorize;

        return $this;
    }

    public function disabled(bool $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function outlined(bool $condition = true): static
    {
        $this->isOutlined = $condition;

        return $this;
    }

    public function size(?string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function tooltip(?string $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function preserveState(bool $preserve = true): static
    {
        $this->preserveState = $preserve;

        return $this;
    }

    public function preserveScroll(bool $preserve = true): static
    {
        $this->preserveScroll = $preserve;

        return $this;
    }

    public function method(string $method): static
    {
        $this->method = strtoupper($method);

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function spa(bool $condition = true): static
    {
        $this->spa = $condition;

        return $this;
    }

    public function isSpa(): bool
    {
        return $this->spa;
    }

    public function extraAttributes(array $attributes): static
    {
        $this->extraAttributes = array_merge($this->extraAttributes, $attributes);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getAction(): ?Closure
    {
        return $this->action;
    }

    public function canAuthorize(mixed $record = null): bool
    {
        if ($this->authorize !== null) {
            return (bool) call_user_func($this->authorize, $record);
        }

        return true;
    }

    public function isDisabled(): bool
    {
        return $this->isDisabled;
    }

    public function isOutlined(): bool
    {
        return $this->isOutlined;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function getTooltip(): ?string
    {
        return $this->tooltip;
    }

    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }

    /**
     * Get default label from name if label is not set.
     */
    protected function getDefaultLabel(): ?string
    {
        if (! $this->name) {
            return null;
        }

        return Str::of($this->name)
            ->replace(['-', '_'], ' ')
            ->title()
            ->toString();
    }

    /**
     * Set the action variant to 'link'.
     */
    public function link(): static
    {
        $this->variant = 'link';

        return $this;
    }

    /**
     * Set the action variant to 'button'.
     */
    public function button(): static
    {
        $this->variant = 'button';

        return $this;
    }

    /**
     * Set the action variant to 'icon'.
     */
    public function iconButton(): static
    {
        $this->variant = 'icon';

        return $this;
    }

    /**
     * Set the action variant directly.
     */
    public function variant(?string $variant): static
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Get the action variant.
     */
    public function getVariant(): ?string
    {
        return $this->variant;
    }

    /**
     * Generate an encrypted action token for server-side execution.
     */
    public function getActionToken(): ?string
    {
        if ($this->action === null) {
            return null;
        }

        if ($this->cachedActionToken !== null) {
            return $this->cachedActionToken;
        }

        // Generate unique action ID
        $actionId = 'action_'.$this->getName().'_'.uniqid();

        // Store the closure in session
        $serializableClosure = new \Laravel\SerializableClosure\SerializableClosure($this->action);
        session()->put("actions.{$actionId}", $serializableClosure);

        // Return encrypted token
        $this->cachedActionToken = Crypt::encrypt([
            'action_id' => $actionId,
            'name' => $this->getName(),
        ]);

        return $this->cachedActionToken;
    }

    /**
     * Get the action execution URL.
     */
    public function getActionUrl(): ?string
    {
        if ($this->action === null) {
            return null;
        }

        return route('actions.execute');
    }

    public function execute(mixed $record = null, array $data = []): mixed
    {
        if ($this->action === null) {
            return null;
        }

        return call_user_func($this->action, $record, $data);
    }

    /**
     * Resolve the action's configuration based on record context.
     */
    public function resolveRecordContext(mixed $record): static
    {
        return $this;
    }

    public function toArray(): array
    {
        return $this->toArrayWithRecord(null);
    }

    /**
     * Convert action to array with record context.
     */
    public function toArrayWithRecord(mixed $record = null): array
    {
        $data = [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'color' => $this->getColor(),
            'icon' => $this->getIcon(),
            'iconPosition' => $this->getIconPosition(),
            'url' => $this->getUrl($record),
            'openUrlInNewTab' => $this->shouldOpenUrlInNewTab(),
            'requiresConfirmation' => $this->getRequiresConfirmation(),
            'hasModal' => $this->hasModal(),
            'modalHeading' => $this->getModalHeading(),
            'modalDescription' => $this->getModalDescription(),
            'modalSubmitActionLabel' => $this->getModalSubmitActionLabel(),
            'modalCancelActionLabel' => $this->getModalCancelActionLabel(),
            'modalIcon' => $this->getModalIcon(),
            'modalIconColor' => $this->getModalIconColor(),
            'modalWidth' => $this->getModalWidth(),
            'confirmDanger' => $this->isConfirmDanger(),
            'slideOver' => $this->isSlideOver(),
            'isHidden' => $this->isHidden($record),
            'isDisabled' => $this->isDisabled(),
            'isOutlined' => $this->isOutlined(),
            'size' => $this->getSize(),
            'variant' => $this->getVariant(),
            'tooltip' => $this->getTooltip(),
            'extraAttributes' => $this->getExtraAttributes(),
            'hasAction' => $this->action !== null,
            'actionUrl' => $this->getActionUrl(),
            'actionToken' => $this->getActionToken(),
            'preserveState' => $this->preserveState,
            'preserveScroll' => $this->preserveScroll,
            'method' => $this->getMethod(),
            'spa' => $this->isSpa(),
        ];

        return array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
