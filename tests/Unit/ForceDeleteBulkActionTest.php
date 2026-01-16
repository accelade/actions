<?php

use Accelade\Actions\ForceDeleteBulkAction;

it('has force delete bulk defaults', function () {
    $action = ForceDeleteBulkAction::make();

    expect($action->getName())->toBe('force-delete');
    expect($action->getIcon())->toBe('heroicon-o-trash');
    expect($action->getColor())->toBe('danger');
    expect($action->getRequiresConfirmation())->toBeTrue();
    expect($action->shouldDeselectRecordsAfterCompletion())->toBeTrue();
});

it('is a bulk action', function () {
    $action = ForceDeleteBulkAction::make();

    expect($action->isBulkAction())->toBeTrue();
});

it('can set model', function () {
    $action = ForceDeleteBulkAction::make()
        ->model('App\\Models\\Post');

    expect($action->getModel())->toBe('App\\Models\\Post');
});

it('includes soft delete visibility flags in array', function () {
    $action = ForceDeleteBulkAction::make();

    $array = $action->toArray();

    expect($array['visibleWhenTrashed'])->toBeTrue();
    expect($array['hiddenWhenNotTrashed'])->toBeTrue();
    expect($array['isBulkAction'])->toBeTrue();
});
