<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Illuminate\Contracts\Support\Arrayable;

class BulkActionGroup implements Arrayable
{
    protected array $actions = [];

    protected ?string $label = null;

    protected ?string $icon = null;

    protected ?string $color = null;

    protected bool $dropdown = true;

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

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

    /**
     * Display as dropdown menu (default).
     */
    public function dropdown(): static
    {
        $this->dropdown = true;

        return $this;
    }

    /**
     * Display as button group instead of dropdown.
     */
    public function button(): static
    {
        $this->dropdown = false;

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function setActions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function isDropdown(): bool
    {
        return $this->dropdown;
    }

    public function toArray(): array
    {
        return [
            'type' => 'bulk-action-group',
            'label' => $this->label ?? __('actions::actions.bulk_actions'),
            'icon' => $this->icon,
            'color' => $this->color,
            'dropdown' => $this->dropdown,
            'actions' => collect($this->actions)
                ->map(fn ($action) => $action->toArray())
                ->all(),
        ];
    }
}
