<?php

namespace Accelade\Actions;

class EditAction extends Action
{
    protected function setUp(): void
    {
        $this->name ??= 'edit';

        $this->label(__('actions::actions.edit'));

        $this->icon('pencil');

        $this->color('primary');

        $this->method('GET');
    }
}
