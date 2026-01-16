<?php

use Accelade\Actions\ForceDeleteAction;

it('has force delete defaults', function () {
    $action = ForceDeleteAction::make();

    expect($action->getName())->toBe('force-delete');
    expect($action->getIcon())->toBe('heroicon-o-trash');
    expect($action->getColor())->toBe('danger');
    expect($action->getRequiresConfirmation())->toBeTrue();
});

it('can set model', function () {
    $action = ForceDeleteAction::make()
        ->model('App\\Models\\Post');

    expect($action->getModel())->toBe('App\\Models\\Post');
});

it('can set custom force delete logic', function () {
    $executed = false;

    $action = ForceDeleteAction::make()
        ->forceDeleteUsing(function ($record) use (&$executed) {
            $executed = true;

            return true;
        });

    // Note: We can't fully test execute without a real model
    expect($action)->toBeInstanceOf(ForceDeleteAction::class);
});

it('can use using alias', function () {
    $action = ForceDeleteAction::make()
        ->using(function ($record) {
            return true;
        });

    expect($action)->toBeInstanceOf(ForceDeleteAction::class);
});

it('includes soft delete visibility flags in array', function () {
    $action = ForceDeleteAction::make();

    $array = $action->toArray();

    expect($array['visibleWhenTrashed'])->toBeTrue();
    expect($array['hiddenWhenNotTrashed'])->toBeTrue();
});

it('can override defaults', function () {
    $action = ForceDeleteAction::make('permanent-delete')
        ->label('Delete Forever')
        ->icon('heroicon-o-x-circle');

    expect($action->getName())->toBe('permanent-delete');
    expect($action->getLabel())->toBe('Delete Forever');
    expect($action->getIcon())->toBe('heroicon-o-x-circle');
});
