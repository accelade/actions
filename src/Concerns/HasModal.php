<?php

declare(strict_types=1);

namespace Accelade\Actions\Concerns;

trait HasModal
{
    protected bool $requiresConfirmation = false;

    protected bool $hasModal = false;

    protected ?string $modalHeading = null;

    protected ?string $modalDescription = null;

    protected ?string $modalSubmitActionLabel = null;

    protected ?string $modalCancelActionLabel = null;

    protected ?string $modalIcon = null;

    protected ?string $modalIconColor = null;

    protected bool $requiresPassword = false;

    protected ?string $modalContent = null;

    protected bool $slideOver = false;

    protected ?string $modalWidth = null;

    protected bool $confirmDanger = false;

    public function requiresConfirmation(bool $condition = true): static
    {
        $this->requiresConfirmation = $condition;

        return $this;
    }

    /**
     * Enable modal display without confirmation UI.
     */
    public function modal(bool $condition = true): static
    {
        $this->hasModal = $condition;

        return $this;
    }

    public function hasModal(): bool
    {
        return $this->hasModal || $this->requiresConfirmation;
    }

    public function modalHeading(?string $heading): static
    {
        $this->modalHeading = $heading;

        return $this;
    }

    public function modalDescription(?string $description): static
    {
        $this->modalDescription = $description;

        return $this;
    }

    public function modalSubmitActionLabel(?string $label): static
    {
        $this->modalSubmitActionLabel = $label;

        return $this;
    }

    public function modalCancelActionLabel(?string $label): static
    {
        $this->modalCancelActionLabel = $label;

        return $this;
    }

    public function modalIcon(?string $icon): static
    {
        $this->modalIcon = $icon;

        return $this;
    }

    public function modalIconColor(?string $color): static
    {
        $this->modalIconColor = $color;

        return $this;
    }

    public function getRequiresConfirmation(): bool
    {
        return $this->requiresConfirmation;
    }

    public function getModalHeading(): ?string
    {
        if ($this->modalHeading !== null) {
            return $this->modalHeading;
        }

        if ($this->requiresConfirmation) {
            $label = method_exists($this, 'getLabel') ? $this->getLabel() : null;

            return $label
                ? __('actions::actions.modal.confirm_action', ['action' => $label])
                : __('actions::actions.modal.confirm_title');
        }

        return null;
    }

    public function getModalDescription(): ?string
    {
        if ($this->modalDescription !== null) {
            return $this->modalDescription;
        }

        if ($this->requiresConfirmation) {
            return __('actions::actions.modal.confirm_description');
        }

        return null;
    }

    public function getModalSubmitActionLabel(): ?string
    {
        return $this->modalSubmitActionLabel ?? ($this->requiresConfirmation ? __('actions::actions.modal.confirm') : null);
    }

    public function getModalCancelActionLabel(): ?string
    {
        return $this->modalCancelActionLabel ?? ($this->requiresConfirmation ? __('actions::actions.modal.cancel') : null);
    }

    public function getModalIcon(): ?string
    {
        if ($this->modalIcon !== null) {
            return $this->modalIcon;
        }

        if ($this->requiresConfirmation) {
            $actionIcon = method_exists($this, 'getIcon') ? $this->getIcon() : null;

            return $actionIcon ?? 'alert-circle';
        }

        return null;
    }

    public function getModalIconColor(): ?string
    {
        if ($this->modalIconColor !== null) {
            return $this->modalIconColor;
        }

        if ($this->requiresConfirmation) {
            $actionColor = method_exists($this, 'getColor') ? $this->getColor() : null;

            return $actionColor ?? 'primary';
        }

        return null;
    }

    public function slideOver(bool $condition = true): static
    {
        $this->slideOver = $condition;
        $this->requiresConfirmation($condition);

        return $this;
    }

    public function isSlideOver(): bool
    {
        return $this->slideOver;
    }

    public function requiresPassword(bool $condition = true): static
    {
        $this->requiresPassword = $condition;
        $this->requiresConfirmation($condition);

        return $this;
    }

    public function content(string $content): static
    {
        $this->modalContent = $content;

        return $this;
    }

    public function getRequiresPassword(): bool
    {
        return $this->requiresPassword;
    }

    public function getModalContent(): ?string
    {
        return $this->modalContent;
    }

    public function modalWidth(?string $width): static
    {
        $this->modalWidth = $width;

        return $this;
    }

    public function getModalWidth(): ?string
    {
        return $this->modalWidth;
    }

    public function confirmDanger(bool $condition = true): static
    {
        $this->confirmDanger = $condition;

        return $this;
    }

    public function isConfirmDanger(): bool
    {
        return $this->confirmDanger;
    }
}
