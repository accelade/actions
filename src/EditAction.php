<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;

class EditAction extends Action
{
    protected ?Closure $updateUsing = null;

    protected ?Closure $fillFormUsing = null;

    protected function setUp(): void
    {
        $this->name ??= 'edit';

        $this->label(__('actions::actions.edit'));

        $this->icon('heroicon-o-pencil');

        $this->color('primary');

        $this->method('POST');

        $this->modalSubmitActionLabel(__('actions::actions.modal.save'));

        $this->successNotificationTitle(__('actions::actions.notifications.updated'));
    }

    /**
     * Define a custom callback for updating the record.
     * The callback receives: function(array $data, Model $record): Model
     */
    public function updateUsing(?Closure $callback): static
    {
        $this->updateUsing = $callback;

        return $this;
    }

    /**
     * Alias for updateUsing() - Filament compatibility.
     */
    public function using(?Closure $callback): static
    {
        return $this->updateUsing($callback);
    }

    /**
     * Define a custom callback for filling the form with record data.
     * The callback receives: function(Model $record): array
     */
    public function fillFormUsing(?Closure $callback): static
    {
        $this->fillFormUsing = $callback;

        return $this;
    }

    /**
     * Get the data to fill the form with.
     */
    public function getFormData(Model $record): array
    {
        if ($this->fillFormUsing !== null) {
            $data = ($this->fillFormUsing)($record);
        } else {
            $data = $record->toArray();
        }

        // Apply record data mutation
        $data = $this->mutateRecordData($data, $record);

        // Run form fill hooks
        $data = $this->runBeforeFormFilled($data);
        $data = $this->runAfterFormFilled($data);

        return $data;
    }

    /**
     * Update a record using the configured callback or default behavior.
     */
    public function update(Model $record, array $data): Model
    {
        // Apply data mutation
        $data = $this->mutateFormData($data);

        // Run before hook
        $data = $this->runBefore($data, $record);

        // Update using custom callback or default
        if ($this->updateUsing !== null) {
            $result = ($this->updateUsing)($data, $record);
            if ($result instanceof Model) {
                $record = $result;
            }
        } else {
            $record->update($data);
        }

        // Run after hook
        $this->runAfter($data, $record, $record);

        return $record;
    }

    public function hasUpdateUsing(): bool
    {
        return $this->updateUsing !== null;
    }

    public function hasFillFormUsing(): bool
    {
        return $this->fillFormUsing !== null;
    }

    /**
     * Override getSchemaDefaults to include record data for editing.
     *
     * @return array<string, mixed>
     */
    public function getSchemaDefaults(mixed $record = null): array
    {
        // Start with parent defaults (from field defaults and explicit schemaDefaults)
        $defaults = parent::getSchemaDefaults($record);

        // If we have a record, merge in the form data (record data takes precedence)
        if ($record instanceof Model) {
            $formData = $this->getFormData($record);
            $defaults = array_merge($defaults, $formData);
        }

        return $defaults;
    }
}
