<?php

use Accelade\Actions\Action;

it('can create an action with make()', function () {
    $action = Action::make('test');

    expect($action)->toBeInstanceOf(Action::class);
    expect($action->getName())->toBe('test');
});

it('can set a label', function () {
    $action = Action::make('test')->label('Test Label');

    expect($action->getLabel())->toBe('Test Label');
});

it('generates default label from name', function () {
    $action = Action::make('create-user');

    expect($action->getLabel())->toBe('Create User');
});

it('can set color', function () {
    $action = Action::make('test')->color('danger');

    expect($action->getColor())->toBe('danger');
});

it('has color shortcuts', function () {
    expect(Action::make('test')->primary()->getColor())->toBe('primary');
    expect(Action::make('test')->danger()->getColor())->toBe('danger');
    expect(Action::make('test')->success()->getColor())->toBe('success');
    expect(Action::make('test')->warning()->getColor())->toBe('warning');
    expect(Action::make('test')->info()->getColor())->toBe('info');
    expect(Action::make('test')->secondary()->getColor())->toBe('secondary');
});

it('can set icon', function () {
    $action = Action::make('test')->icon('check');

    expect($action->getIcon())->toBe('check');
    expect($action->getIconPosition())->toBe('before');
});

it('can set icon position', function () {
    $action = Action::make('test')->icon('check', 'after');

    expect($action->getIconPosition())->toBe('after');
});

it('can set size', function () {
    $action = Action::make('test')->size('lg');

    expect($action->getSize())->toBe('lg');
});

it('can set url', function () {
    $action = Action::make('test')->url('/dashboard');

    expect($action->getUrl())->toBe('/dashboard');
});

it('can set dynamic url with closure', function () {
    $action = Action::make('test')
        ->url(fn ($record) => "/posts/{$record['id']}/edit");

    expect($action->getUrl(['id' => 123]))->toBe('/posts/123/edit');
});

it('can open url in new tab', function () {
    $action = Action::make('test')
        ->url('https://example.com')
        ->openUrlInNewTab();

    expect($action->shouldOpenUrlInNewTab())->toBeTrue();
});

it('can set http method', function () {
    $action = Action::make('test')->method('DELETE');

    expect($action->getMethod())->toBe('DELETE');
});

it('can require confirmation', function () {
    $action = Action::make('test')
        ->requiresConfirmation()
        ->modalHeading('Confirm?')
        ->modalDescription('Are you sure?');

    expect($action->getRequiresConfirmation())->toBeTrue();
    expect($action->hasModal())->toBeTrue();
    expect($action->getModalHeading())->toBe('Confirm?');
    expect($action->getModalDescription())->toBe('Are you sure?');
});

it('can set confirm danger', function () {
    $action = Action::make('test')
        ->requiresConfirmation()
        ->confirmDanger();

    expect($action->isConfirmDanger())->toBeTrue();
});

it('can set variants', function () {
    expect(Action::make('test')->button()->getVariant())->toBe('button');
    expect(Action::make('test')->link()->getVariant())->toBe('link');
    expect(Action::make('test')->iconButton()->getVariant())->toBe('icon');
});

it('can be outlined', function () {
    $action = Action::make('test')->outlined();

    expect($action->isOutlined())->toBeTrue();
});

it('can be disabled', function () {
    $action = Action::make('test')->disabled();

    expect($action->isDisabled())->toBeTrue();
});

it('can be hidden', function () {
    $action = Action::make('test')->hidden();

    expect($action->isHidden())->toBeTrue();
    expect($action->isVisible())->toBeFalse();
});

it('can be visible based on condition', function () {
    $action = Action::make('test')->visible(fn () => false);

    expect($action->isHidden())->toBeTrue();
});

it('can have tooltip', function () {
    $action = Action::make('test')->tooltip('Click me');

    expect($action->getTooltip())->toBe('Click me');
});

it('can have extra attributes', function () {
    $action = Action::make('test')
        ->extraAttributes(['data-custom' => 'value']);

    expect($action->getExtraAttributes())->toBe(['data-custom' => 'value']);
});

it('can set action closure', function () {
    $action = Action::make('test')
        ->action(fn ($record, $data) => 'executed');

    expect($action->getAction())->toBeInstanceOf(Closure::class);
    expect($action->execute())->toBe('executed');
});

it('can convert to array', function () {
    $action = Action::make('test')
        ->label('Test')
        ->icon('check')
        ->color('primary')
        ->url('/test');

    $array = $action->toArray();

    expect($array)->toBeArray();
    expect($array['name'])->toBe('test');
    expect($array['label'])->toBe('Test');
    expect($array['icon'])->toBe('check');
    expect($array['color'])->toBe('primary');
    expect($array['url'])->toBe('/test');
});

it('can authorize actions', function () {
    $authorizedAction = Action::make('test')
        ->authorize(fn () => true);

    $unauthorizedAction = Action::make('test')
        ->authorize(fn () => false);

    expect($authorizedAction->canAuthorize())->toBeTrue();
    expect($unauthorizedAction->canAuthorize())->toBeFalse();
});
