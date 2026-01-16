<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;

class CreateAction extends Action
{
    protected ?string $model = null;

    protected ?Closure $createUsing = null;

    protected function setUp(): void
    {
        $this->name ??= 'create';

        $this->label(__('actions::actions.create'));

        $this->icon('heroicon-o-plus');

        $this->color('primary');

        $this->method('POST');

        $this->modalSubmitActionLabel(__('actions::actions.modal.create'));

        $this->successNotificationTitle(__('actions::actions.notifications.created'));
    }

    /**
     * Set the model class for creation.
     */
    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the model class.
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * Define a custom callback for creating the record.
     * The callback receives: function(array $data): Model
     */
    public function createUsing(?Closure $callback): static
    {
        $this->createUsing = $callback;

        return $this;
    }

    /**
     * Alias for createUsing() - Filament compatibility.
     */
    public function using(?Closure $callback): static
    {
        return $this->createUsing($callback);
    }

    /**
     * Create a record using the configured model or custom callback.
     */
    public function create(array $data): ?Model
    {
        $data = $this->mutateFormData($data);
        $data = $this->runBefore($data);

        $record = $this->performCreate($data);

        if ($record !== null) {
            $this->runAfter($data, $record, $record);
        }

        return $record;
    }

    /**
     * Perform the actual record creation.
     */
    protected function performCreate(array $data): ?Model
    {
        if ($this->createUsing !== null) {
            return ($this->createUsing)($data);
        }

        if ($this->model !== null) {
            return $this->model::create($data);
        }

        return null;
    }

    public function hasCreateUsing(): bool
    {
        return $this->createUsing !== null;
    }
}
