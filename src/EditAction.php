<?php

declare(strict_types=1);

namespace Accelade\Actions;

class EditAction extends Action
{
    protected function setUp(): void
    {
        $this->name ??= 'edit';

        $this->label(__('actions::actions.edit'));

        $this->icon('pencil');

        $this->color('primary');

        $this->method('POST');
    }
}
