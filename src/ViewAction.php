<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;

class ViewAction extends Action
{
    protected ?Closure $fillFormUsing = null;

    protected function setUp(): void
    {
        $this->name ??= 'view';

        $this->label(__('actions::actions.view'));

        $this->icon('heroicon-o-eye');

        $this->color('secondary');

        $this->method('POST');

        // View actions don't need a submit button by default
        $this->modalSubmitActionLabel(null);

        // Just a close button
        $this->modalCancelActionLabel(__('actions::actions.modal.close'));
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
     * Get the data to display in the modal.
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

        return $data;
    }

    public function hasFillFormUsing(): bool
    {
        return $this->fillFormUsing !== null;
    }

    /**
     * Override getSchemaDefaults to include record data for viewing.
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
