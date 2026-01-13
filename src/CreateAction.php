<?php

declare(strict_types=1);

namespace Accelade\Actions;

class CreateAction extends Action
{
    protected function setUp(): void
    {
        $this->name ??= 'create';

        $this->label(__('actions::actions.create'));

        $this->icon('plus');

        $this->color('primary');

        $this->method('POST');
    }
}
