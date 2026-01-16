<?php

use Accelade\Actions\RestoreBulkAction;

it('has restore bulk defaults', function () {
    $action = RestoreBulkAction::make();

    expect($action->getName())->toBe('restore');
    expect($action->getIcon())->toBe('heroicon-o-arrow-uturn-left');
    expect($action->getColor())->toBe('success');
    expect($action->getRequiresConfirmation())->toBeTrue();
    expect($action->shouldDeselectRecordsAfterCompletion())->toBeTrue();
});

it('is a bulk action', function () {
    $action = RestoreBulkAction::make();

    expect($action->isBulkAction())->toBeTrue();
});

it('can set model', function () {
    $action = RestoreBulkAction::make()
        ->model('App\\Models\\Post');

    expect($action->getModel())->toBe('App\\Models\\Post');
});

it('includes soft delete visibility flags in array', function () {
    $action = RestoreBulkAction::make();

    $array = $action->toArray();

    expect($array['visibleWhenTrashed'])->toBeTrue();
    expect($array['hiddenWhenNotTrashed'])->toBeTrue();
    expect($array['isBulkAction'])->toBeTrue();
});
