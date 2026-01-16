<?php

declare(strict_types=1);

namespace Accelade\Actions\Concerns;

use Closure;

trait HasModal
{
    protected bool $requiresConfirmation = false;

    protected bool $hasModal = false;

    protected string|Closure|null $modalHeading = null;

    protected string|Closure|null $modalDescription = null;

    protected ?string $modalSubmitActionLabel = null;

    protected ?string $modalCancelActionLabel = null;

    protected ?string $modalIcon = null;

    protected ?string $modalIconColor = null;

    protected bool $requiresPassword = false;

    protected ?string $modalContent = null;

    protected bool $slideOver = false;

    protected ?string $modalWidth = null;

    protected bool $confirmDanger = false;

    protected bool $stickyModalHeader = false;

    protected bool $stickyModalFooter = false;

    protected bool $closeModalByClickingAway = true;

    protected bool $closeModalByEscaping = true;

    protected ?string $modalAlignment = null;

    /**
     * @var array<mixed>|null
     */
    protected ?array $modalFooterActions = null;

    /**
     * @var array<mixed>|null
     */
    protected ?array $extraModalFooterActions = null;

    protected string $modalFooterActionsAlignment = 'end';

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

    public function modalHeading(string|Closure|null $heading): static
    {
        $this->modalHeading = $heading;

        return $this;
    }

    public function modalDescription(string|Closure|null $description): static
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

    public function getModalHeading(mixed $record = null): ?string
    {
        if ($this->modalHeading !== null) {
            if ($this->modalHeading instanceof Closure) {
                return ($this->modalHeading)($record);
            }

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

    public function getModalDescription(mixed $record = null): ?string
    {
        if ($this->modalDescription !== null) {
            if ($this->modalDescription instanceof Closure) {
                return ($this->modalDescription)($record);
            }

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

    /**
     * Make the modal header sticky (fixed at top when scrolling).
     */
    public function stickyModalHeader(bool $condition = true): static
    {
        $this->stickyModalHeader = $condition;

        return $this;
    }

    public function hasStickyModalHeader(): bool
    {
        return $this->stickyModalHeader;
    }

    /**
     * Make the modal footer sticky (fixed at bottom when scrolling).
     */
    public function stickyModalFooter(bool $condition = true): static
    {
        $this->stickyModalFooter = $condition;

        return $this;
    }

    public function hasStickyModalFooter(): bool
    {
        return $this->stickyModalFooter;
    }

    /**
     * Control if modal can be closed by clicking outside.
     */
    public function closeModalByClickingAway(bool $condition = true): static
    {
        $this->closeModalByClickingAway = $condition;

        return $this;
    }

    public function canCloseModalByClickingAway(): bool
    {
        return $this->closeModalByClickingAway;
    }

    /**
     * Control if modal can be closed by pressing Escape.
     */
    public function closeModalByEscaping(bool $condition = true): static
    {
        $this->closeModalByEscaping = $condition;

        return $this;
    }

    public function canCloseModalByEscaping(): bool
    {
        return $this->closeModalByEscaping;
    }

    /**
     * Set the modal content alignment.
     *
     * @param  string  $alignment  'start', 'center', 'end'
     */
    public function modalAlignment(?string $alignment): static
    {
        $this->modalAlignment = $alignment;

        return $this;
    }

    public function getModalAlignment(): ?string
    {
        return $this->modalAlignment;
    }

    /**
     * Replace the default modal footer actions.
     *
     * @param  array<mixed>  $actions
     */
    public function modalFooterActions(array $actions): static
    {
        $this->modalFooterActions = $actions;

        return $this;
    }

    /**
     * Add extra actions to the modal footer.
     *
     * @param  array<mixed>  $actions
     */
    public function extraModalFooterActions(array $actions): static
    {
        $this->extraModalFooterActions = $actions;

        return $this;
    }

    /**
     * @return array<mixed>|null
     */
    public function getModalFooterActions(): ?array
    {
        return $this->modalFooterActions;
    }

    /**
     * @return array<mixed>|null
     */
    public function getExtraModalFooterActions(): ?array
    {
        return $this->extraModalFooterActions;
    }

    /**
     * Set the modal footer actions alignment.
     *
     * @param  string  $alignment  'start', 'center', 'end', 'between'
     */
    public function modalFooterActionsAlignment(string $alignment): static
    {
        $this->modalFooterActionsAlignment = $alignment;

        return $this;
    }

    public function getModalFooterActionsAlignment(): string
    {
        return $this->modalFooterActionsAlignment;
    }
}
