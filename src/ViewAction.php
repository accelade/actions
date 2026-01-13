<?php

namespace Accelade\Actions;

class ViewAction extends Action
{
    protected function setUp(): void
    {
        $this->name ??= 'view';

        $this->label(__('actions::actions.view'));

        $this->icon('eye');

        $this->color('secondary');

        $this->method('GET');
    }
}
