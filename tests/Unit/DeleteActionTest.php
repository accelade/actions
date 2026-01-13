<?php

use Accelade\Actions\DeleteAction;

it('has delete defaults', function () {
    $action = DeleteAction::make();

    expect($action->getName())->toBe('delete');
    expect($action->getIcon())->toBe('trash-2');
    expect($action->getColor())->toBe('danger');
    expect($action->getRequiresConfirmation())->toBeTrue();
    expect($action->isConfirmDanger())->toBeTrue();
    expect($action->getMethod())->toBe('DELETE');
});

it('can override defaults', function () {
    $action = DeleteAction::make('remove')
        ->label('Remove')
        ->icon('x')
        ->color('warning');

    expect($action->getName())->toBe('remove');
    expect($action->getLabel())->toBe('Remove');
    expect($action->getIcon())->toBe('x');
    expect($action->getColor())->toBe('warning');
});
