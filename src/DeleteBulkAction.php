<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Illuminate\Database\Eloquent\Model;

class DeleteBulkAction extends BulkAction
{
    protected ?string $model = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->name ??= 'delete';
        $this->label(__('actions::actions.buttons.delete'));
        $this->icon('heroicon-o-trash');
        $this->color('danger');
        $this->requiresConfirmation();
        $this->modalHeading(__('actions::actions.modal.bulk_delete_title'));
        $this->modalDescription(__('actions::actions.modal.bulk_delete_description'));
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

        // Capture the model class in a local variable to avoid capturing $this
        $modelClass = $model;

        // Set up the default delete action
        $this->action(function (array $ids) use ($modelClass) {
            if (empty($ids)) {
                return 0;
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
}
