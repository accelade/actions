<?php

use Accelade\Actions\Action;
use Accelade\Actions\ActionGroup;

it('can create an action group', function () {
    $group = ActionGroup::make([
        Action::make('view'),
        Action::make('edit'),
    ]);

    expect($group)->toBeInstanceOf(ActionGroup::class);
    expect($group->getActions())->toHaveCount(2);
});

it('can set label', function () {
    $group = ActionGroup::make([])->label('Actions');

    expect($group->getLabel())->toBe('Actions');
});

it('can set icon', function () {
    $group = ActionGroup::make([])->icon('settings');

    expect($group->getIcon())->toBe('settings');
});

it('has default icon', function () {
    $group = ActionGroup::make([]);

    expect($group->getIcon())->toBe('more-vertical');
});

it('can set color', function () {
    $group = ActionGroup::make([])->color('primary');

    expect($group->getColor())->toBe('primary');
});

it('can set tooltip', function () {
    $group = ActionGroup::make([])->tooltip('More options');

    expect($group->getTooltip())->toBe('More options');
});

it('can set size', function () {
    $group = ActionGroup::make([])->size('lg');

    expect($group->getSize())->toBe('lg');
});

it('is dropdown by default', function () {
    $group = ActionGroup::make([]);

    expect($group->isDropdown())->toBeTrue();
});

it('can disable dropdown mode', function () {
    $group = ActionGroup::make([])->dropdown(false);

    expect($group->isDropdown())->toBeFalse();
});

it('filters hidden actions', function () {
    $group = ActionGroup::make([
        Action::make('visible'),
        Action::make('hidden')->hidden(),
    ]);

    expect($group->getVisibleActions())->toHaveCount(1);
});

it('converts to array', function () {
    $group = ActionGroup::make([
        Action::make('view')->label('View'),
        Action::make('edit')->label('Edit'),
    ])
        ->label('Actions')
        ->icon('settings')
        ->tooltip('More');

    $array = $group->toArray();

    expect($array)->toBeArray();
    expect($array['label'])->toBe('Actions');
    expect($array['icon'])->toBe('settings');
    expect($array['tooltip'])->toBe('More');
    expect($array['actions'])->toHaveCount(2);
});
