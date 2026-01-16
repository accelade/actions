<?php

use Accelade\Actions\BulkAction;
use Accelade\Actions\BulkActionGroup;

it('can be created with actions', function () {
    $group = BulkActionGroup::make([
        BulkAction::make('delete'),
        BulkAction::make('archive'),
    ]);

    expect($group->getActions())->toHaveCount(2);
});

it('has default label', function () {
    $group = BulkActionGroup::make([]);

    $array = $group->toArray();

    expect($array['label'])->toBe('Bulk Actions');
});

it('can set custom label', function () {
    $group = BulkActionGroup::make([])
        ->label('Actions');

    expect($group->getLabel())->toBe('Actions');
});

it('can set icon', function () {
    $group = BulkActionGroup::make([])
        ->icon('heroicon-o-cog');

    expect($group->getIcon())->toBe('heroicon-o-cog');
});

it('can set color', function () {
    $group = BulkActionGroup::make([])
        ->color('primary');

    expect($group->getColor())->toBe('primary');
});

it('is dropdown by default', function () {
    $group = BulkActionGroup::make([]);

    expect($group->isDropdown())->toBeTrue();
});

it('can be button group', function () {
    $group = BulkActionGroup::make([])
        ->button();

    expect($group->isDropdown())->toBeFalse();
});

it('converts to array with actions', function () {
    $group = BulkActionGroup::make([
        BulkAction::make('delete')->label('Delete'),
        BulkAction::make('archive')->label('Archive'),
    ])
        ->label('Actions')
        ->icon('heroicon-o-cog')
        ->color('primary');

    $array = $group->toArray();

    expect($array['type'])->toBe('bulk-action-group');
    expect($array['label'])->toBe('Actions');
    expect($array['icon'])->toBe('heroicon-o-cog');
    expect($array['color'])->toBe('primary');
    expect($array['actions'])->toHaveCount(2);
});
