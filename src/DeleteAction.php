<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DeleteAction extends Action
{
    protected ?Closure $deleteUsing = null;

    protected bool $softDeletes = false;

    protected bool $forceDelete = false;

    protected function setUp(): void
    {
        $this->name ??= 'delete';

        $this->label(__('actions::actions.delete'));

        $this->icon('heroicon-o-trash');

        $this->color('danger');

        $this->requiresConfirmation();

        $this->confirmDanger();

        $this->modalHeading(__('actions::actions.modal.delete_heading'));

        $this->modalDescription(__('actions::actions.modal.delete_description'));

        $this->modalSubmitActionLabel(__('actions::actions.modal.delete_confirm'));

        $this->method('DELETE');

        $this->successNotificationTitle(__('actions::actions.notifications.deleted'));
    }

    /**
     * Define a custom callback for deleting the record.
     * The callback receives: function(Model $record): void
     */
    public function deleteUsing(?Closure $callback): static
    {
        $this->deleteUsing = $callback;

        return $this;
    }

    /**
     * Alias for deleteUsing() - Filament compatibility.
     */
    public function using(?Closure $callback): static
    {
        return $this->deleteUsing($callback);
    }

    /**
     * Enable soft deletes support.
     */
    public function softDeletes(bool $condition = true): static
    {
        $this->softDeletes = $condition;

        return $this;
    }

    /**
     * Force delete even if model uses soft deletes.
     */
    public function forceDelete(bool $condition = true): static
    {
        $this->forceDelete = $condition;

        return $this;
    }

    /**
     * Delete a single record.
     */
    public function delete(Model $record): bool
    {
        // Run before hook
        $this->runBefore([], $record);

        // Delete using custom callback or default
        if ($this->deleteUsing !== null) {
            ($this->deleteUsing)($record);
        } elseif ($this->forceDelete && method_exists($record, 'forceDelete')) {
            $record->forceDelete();
        } else {
            $record->delete();
        }

        // Run after hook
        $this->runAfter([], $record);

        return true;
    }

    /**
     * Delete multiple records (bulk action).
     *
     * @param  Collection<int, Model>|array<Model>  $records
     */
    public function deleteMany(Collection|array $records): int
    {
        $count = 0;

        foreach ($records as $record) {
            if ($this->delete($record)) {
                $count++;
            }
        }

        return $count;
    }

    public function hasDeleteUsing(): bool
    {
        return $this->deleteUsing !== null;
    }

    public function usesSoftDeletes(): bool
    {
        return $this->softDeletes;
    }

    public function shouldForceDelete(): bool
    {
        return $this->forceDelete;
    }
}
