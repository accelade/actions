<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;

class ReplicateAction extends Action
{
    protected array $excludedAttributes = [];

    protected ?Closure $beforeReplicaSaved = null;

    protected ?Closure $afterReplicaSaved = null;

    protected ?Closure $replicaRedirectUrl = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->name ??= 'replicate';
        $this->label(__('actions::actions.buttons.replicate'));
        $this->icon('heroicon-o-document-duplicate');
        $this->color('gray');
        $this->requiresConfirmation();
        $this->modalHeading(__('actions::actions.modal.replicate_title'));
        $this->modalDescription(__('actions::actions.modal.replicate_description'));
    }

    /**
     * Attributes to exclude when replicating.
     *
     * @param  array<string>  $attributes
     */
    public function excludeAttributes(array $attributes): static
    {
        $this->excludedAttributes = $attributes;

        return $this;
    }

    /**
     * Get excluded attributes.
     */
    public function getExcludedAttributes(): array
    {
        return $this->excludedAttributes;
    }

    /**
     * Callback to run before the replica is saved.
     */
    public function beforeReplicaSaved(?Closure $callback): static
    {
        $this->beforeReplicaSaved = $callback;

        return $this;
    }

    /**
     * Callback to run after the replica is saved.
     */
    public function afterReplicaSaved(?Closure $callback): static
    {
        $this->afterReplicaSaved = $callback;

        return $this;
    }

    /**
     * Set the success redirect URL callback for when the replica is created.
     */
    public function replicaRedirectUrl(?Closure $url): static
    {
        $this->replicaRedirectUrl = $url;

        return $this;
    }

    /**
     * Replicate a record.
     */
    public function replicate(Model $record, array $data = []): ?Model
    {
        // Run before lifecycle hooks
        $data = $this->runBefore($data, $record);

        // Replicate the model
        $replica = $record->replicate($this->excludedAttributes);

        // Run before replica saved callback
        if ($this->beforeReplicaSaved) {
            call_user_func($this->beforeReplicaSaved, $replica, $record, $data);
        }

        // Save the replica
        $replica->save();

        // Run after replica saved callback
        if ($this->afterReplicaSaved) {
            call_user_func($this->afterReplicaSaved, $replica, $record, $data);
        }

        // Run after lifecycle hooks
        $this->runAfter($data, $record, $replica);

        return $replica;
    }

    /**
     * Execute the action.
     */
    public function execute(mixed $record = null, array $data = []): mixed
    {
        if (! $record instanceof Model) {
            return null;
        }

        return $this->replicate($record, $data);
    }

    /**
     * Get the replica redirect URL.
     */
    public function getReplicaRedirectUrl(Model $replica): ?string
    {
        if ($this->replicaRedirectUrl) {
            return call_user_func($this->replicaRedirectUrl, $replica);
        }

        return null;
    }
}
