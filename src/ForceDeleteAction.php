<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForceDeleteAction extends Action
{
    protected ?string $model = null;

    protected ?Closure $forceDeleteUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->name ??= 'force-delete';
        $this->label(__('actions::actions.buttons.force_delete'));
        $this->icon('heroicon-o-trash');
        $this->color('danger');
        $this->requiresConfirmation();
        $this->modalHeading(__('actions::actions.modal.force_delete_title'));
        $this->modalDescription(__('actions::actions.modal.force_delete_description'));

        // Only show for soft-deleted records
        $this->hidden(function ($record) {
            if ($record === null) {
                return true;
            }

            // Check if record is trashed
            if (is_object($record) && method_exists($record, 'trashed')) {
                return ! $record->trashed();
            }

            if (is_array($record)) {
                return empty($record['deleted_at']);
            }

            return true;
        });
    }

    /**
     * Set the model class.
     *
     * @param  class-string<Model>  $model
     */
    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Custom force delete logic.
     */
    public function forceDeleteUsing(?Closure $callback): static
    {
        $this->forceDeleteUsing = $callback;

        return $this;
    }

    /**
     * Alias for forceDeleteUsing.
     */
    public function using(?Closure $callback): static
    {
        return $this->forceDeleteUsing($callback);
    }

    /**
     * Get the model class.
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * Force delete a record.
     */
    public function forceDelete(Model $record): bool
    {
        // Run before hooks
        $data = $this->runBefore([], $record);

        if ($this->forceDeleteUsing) {
            $result = call_user_func($this->forceDeleteUsing, $record, $data);
        } else {
            // Check if model uses SoftDeletes
            if (in_array(SoftDeletes::class, class_uses_recursive($record), true)) {
                $result = $record->forceDelete();
            } else {
                $result = $record->delete();
            }
        }

        // Run after hooks
        $this->runAfter($data, $record, $result);

        return (bool) $result;
    }

    /**
     * Override toArrayWithRecord to include soft delete visibility.
     */
    public function toArrayWithRecord(mixed $record = null): array
    {
        $data = parent::toArrayWithRecord($record);

        $data['visibleWhenTrashed'] = true;
        $data['hiddenWhenNotTrashed'] = true;

        return $data;
    }
}
