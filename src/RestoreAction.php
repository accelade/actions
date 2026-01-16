<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestoreAction extends Action
{
    protected ?string $model = null;

    protected ?Closure $restoreUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->name ??= 'restore';
        $this->label(__('actions::actions.buttons.restore'));
        $this->icon('heroicon-o-arrow-uturn-left');
        $this->color('success');
        $this->requiresConfirmation();
        $this->modalHeading(__('actions::actions.modal.restore_title'));
        $this->modalDescription(__('actions::actions.modal.restore_description'));

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
     * Custom restore logic.
     */
    public function restoreUsing(?Closure $callback): static
    {
        $this->restoreUsing = $callback;

        return $this;
    }

    /**
     * Alias for restoreUsing.
     */
    public function using(?Closure $callback): static
    {
        return $this->restoreUsing($callback);
    }

    /**
     * Get the model class.
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * Restore a soft-deleted record.
     */
    public function restore(Model $record): bool
    {
        // Run before hooks
        $data = $this->runBefore([], $record);

        if ($this->restoreUsing) {
            $result = call_user_func($this->restoreUsing, $record, $data);
        } else {
            // Check if model uses SoftDeletes
            if (in_array(SoftDeletes::class, class_uses_recursive($record), true)) {
                $result = $record->restore();
            } else {
                $result = false;
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
