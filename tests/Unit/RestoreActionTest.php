<?php

use Accelade\Actions\RestoreAction;

it('has restore defaults', function () {
    $action = RestoreAction::make();

    expect($action->getName())->toBe('restore');
    expect($action->getIcon())->toBe('heroicon-o-arrow-uturn-left');
    expect($action->getColor())->toBe('success');
    expect($action->getRequiresConfirmation())->toBeTrue();
});

it('can set model', function () {
    $action = RestoreAction::make()
        ->model('App\\Models\\Post');

    expect($action->getModel())->toBe('App\\Models\\Post');
});

it('can set custom restore logic', function () {
    $action = RestoreAction::make()
        ->restoreUsing(function ($record) {
            return true;
        });

    expect($action)->toBeInstanceOf(RestoreAction::class);
});

it('can use using alias', function () {
    $action = RestoreAction::make()
        ->using(function ($record) {
            return true;
        });

    expect($action)->toBeInstanceOf(RestoreAction::class);
});

it('includes soft delete visibility flags in array', function () {
    $action = RestoreAction::make();

    $array = $action->toArray();

    expect($array['visibleWhenTrashed'])->toBeTrue();
    expect($array['hiddenWhenNotTrashed'])->toBeTrue();
});

it('can override defaults', function () {
    $action = RestoreAction::make('recover')
        ->label('Recover')
        ->icon('heroicon-o-arrow-path')
        ->color('primary');

    expect($action->getName())->toBe('recover');
    expect($action->getLabel())->toBe('Recover');
    expect($action->getIcon())->toBe('heroicon-o-arrow-path');
    expect($action->getColor())->toBe('primary');
});
