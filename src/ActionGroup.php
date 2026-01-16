<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Illuminate\Contracts\Support\Arrayable;

class ActionGroup implements Arrayable
{
    protected ?string $label = null;

    protected ?string $icon = null;

    protected ?string $color = null;

    protected ?string $tooltip = null;

    protected ?string $size = null;

    protected bool $dropdown = true;

    /**
     * Layout type: 'dropdown' or 'button'.
     */
    protected string $layout = 'dropdown';

    protected bool $divided = false;

    /**
     * @var array<Action>
     */
    protected array $actions = [];

    /**
     * @param  array<Action>  $actions
     */
    final public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * @param  array<Action>  $actions
     */
    public static function make(array $actions = []): static
    {
        return new static($actions);
    }

    public function label(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function color(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function tooltip(?string $tooltip): static
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function size(?string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function dropdown(bool $condition = true): static
    {
        $this->dropdown = $condition;

        return $this;
    }

    public function iconButton(): static
    {
        $this->dropdown = true;
        $this->layout = 'dropdown';

        return $this;
    }

    /**
     * Render actions as a button group (horizontal row of buttons).
     */
    public function button(): static
    {
        $this->dropdown = false;
        $this->layout = 'button';

        return $this;
    }

    /**
     * Add dividers between actions (for button layout).
     */
    public function divided(bool $condition = true): static
    {
        $this->divided = $condition;

        return $this;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function isDivided(): bool
    {
        return $this->divided;
    }

    public function isButtonLayout(): bool
    {
        return $this->layout === 'button';
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getIcon(): ?string
    {
        return $this->icon ?? 'more-vertical';
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getTooltip(): ?string
    {
        return $this->tooltip;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function isDropdown(): bool
    {
        return $this->dropdown;
    }

    /**
     * @return array<Action>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Get visible actions for a record.
     *
     * @return array<Action>
     */
    public function getVisibleActions(mixed $record = null): array
    {
        return array_filter(
            $this->actions,
            fn (Action $action) => $action->isVisible($record) && $action->canAuthorize($record)
        );
    }

    public function toArray(): array
    {
        return $this->toArrayWithRecord(null);
    }

    public function toArrayWithRecord(mixed $record = null): array
    {
        return [
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(),
            'color' => $this->getColor(),
            'tooltip' => $this->getTooltip(),
            'size' => $this->getSize(),
            'dropdown' => $this->isDropdown(),
            'layout' => $this->getLayout(),
            'divided' => $this->isDivided(),
            'actions' => array_map(
                fn (Action $action) => $action->toArrayWithRecord($record),
                $this->getVisibleActions($record)
            ),
        ];
    }
}
