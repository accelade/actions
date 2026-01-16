<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForceDeleteBulkAction extends BulkAction
{
    protected ?string $model = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->name ??= 'force-delete';
        $this->label(__('actions::actions.buttons.force_delete'));
        $this->icon('heroicon-o-trash');
        $this->color('danger');
        $this->requiresConfirmation();
        $this->modalHeading(__('actions::actions.modal.bulk_force_delete_title'));
        $this->modalDescription(__('actions::actions.modal.bulk_force_delete_description'));
        $this->deselectRecordsAfterCompletion();
    }

    /**
     * Set the model class for this bulk action.
     *
     * @param  class-string<Model>  $model
     */
    public function model(string $model): static
    {
        $this->model = $model;

        // Capture the model class in a local variable
        $modelClass = $model;

        // Set up the default force delete action
        $this->action(function (array $ids) use ($modelClass) {
            if (empty($ids)) {
                return 0;
            }

            // Check if model uses SoftDeletes
            $uses = class_uses_recursive($modelClass);
            if (in_array(SoftDeletes::class, $uses, true)) {
                return $modelClass::withTrashed()->whereIn('id', $ids)->forceDelete();
            }

            return $modelClass::whereIn('id', $ids)->delete();
        });

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
