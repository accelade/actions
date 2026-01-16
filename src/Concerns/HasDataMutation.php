<?php

declare(strict_types=1);

namespace Accelade\Actions\Concerns;

use Closure;

trait HasDataMutation
{
    protected ?Closure $mutateFormDataUsing = null;

    protected ?Closure $mutateRecordDataUsing = null;

    /**
     * Mutate the form data before it's used for creating/updating.
     * Useful for transforming data, adding computed fields, etc.
     *
     * The callback receives: function(array $data): array
     */
    public function mutateFormDataUsing(?Closure $callback): static
    {
        $this->mutateFormDataUsing = $callback;

        return $this;
    }

    /**
     * Alias for mutateFormDataUsing() - Filament compatibility.
     */
    public function mutateDataUsing(?Closure $callback): static
    {
        return $this->mutateFormDataUsing($callback);
    }

    /**
     * Mutate the record data before filling the form (for edit actions).
     * Useful for transforming model data before display.
     *
     * The callback receives: function(array $data, $record): array
     */
    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }

    /**
     * Apply form data mutation.
     */
    public function mutateFormData(array $data): array
    {
        if ($this->mutateFormDataUsing !== null) {
            return call_user_func($this->mutateFormDataUsing, $data);
        }

        return $data;
    }

    /**
     * Apply record data mutation.
     */
    public function mutateRecordData(array $data, mixed $record): array
    {
        if ($this->mutateRecordDataUsing !== null) {
            return call_user_func($this->mutateRecordDataUsing, $data, $record);
        }

        return $data;
    }

    public function hasMutateFormDataUsing(): bool
    {
        return $this->mutateFormDataUsing !== null;
    }

    public function hasMutateRecordDataUsing(): bool
    {
        return $this->mutateRecordDataUsing !== null;
    }
}
