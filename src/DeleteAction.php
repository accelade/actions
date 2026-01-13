<?php

declare(strict_types=1);

namespace Accelade\Actions;

class DeleteAction extends Action
{
    protected function setUp(): void
    {
        $this->name ??= 'delete';

        $this->label(__('actions::actions.delete'));

        $this->icon('trash-2');

        $this->color('danger');

        $this->requiresConfirmation();

        $this->confirmDanger();

        $this->modalHeading(__('actions::actions.modal.delete_heading'));

        $this->modalDescription(__('actions::actions.modal.delete_description'));

        $this->modalSubmitActionLabel(__('actions::actions.modal.delete_confirm'));

        $this->method('DELETE');
    }
}
