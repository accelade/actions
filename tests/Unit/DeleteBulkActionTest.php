<?php

use Accelade\Actions\DeleteBulkAction;

it('has delete defaults', function () {
    $action = DeleteBulkAction::make();

    expect($action->getName())->toBe('delete');
    expect($action->getIcon())->toBe('heroicon-o-trash');
    expect($action->getColor())->toBe('danger');
    expect($action->getRequiresConfirmation())->toBeTrue();
    expect($action->shouldDeselectRecordsAfterCompletion())->toBeTrue();
});

it('is a bulk action', function () {
    $action = DeleteBulkAction::make();

    expect($action->isBulkAction())->toBeTrue();
});

it('can set model', function () {
    $action = DeleteBulkAction::make()
        ->model('App\\Models\\Post');

    expect($action->getModel())->toBe('App\\Models\\Post');
});

it('can override defaults', function () {
    $action = DeleteBulkAction::make('remove')
        ->label('Remove Selected')
        ->icon('heroicon-o-x-mark')
        ->color('warning');

    expect($action->getName())->toBe('remove');
    expect($action->getLabel())->toBe('Remove Selected');
    expect($action->getIcon())->toBe('heroicon-o-x-mark');
    expect($action->getColor())->toBe('warning');
});
