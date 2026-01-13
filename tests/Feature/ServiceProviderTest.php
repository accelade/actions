<?php

it('registers the service provider', function () {
    expect(app()->providerIsLoaded(\Accelade\Actions\ActionsServiceProvider::class))->toBeTrue();
});

it('registers routes', function () {
    $routes = app('router')->getRoutes();

    $executeRoute = $routes->getByName('actions.execute');
    expect($executeRoute)->not->toBeNull();
});

it('registers blade directives', function () {
    $directives = app('blade.compiler')->getCustomDirectives();

    expect($directives)->toHaveKey('actionsScripts');
    expect($directives)->toHaveKey('actionsStyles');
});

it('merges config', function () {
    expect(config('actions'))->toBeArray();
    expect(config('actions.prefix'))->toBe('actions');
});

it('loads views under accelade namespace', function () {
    $hints = app('view')->getFinder()->getHints();

    expect($hints)->toHaveKey('accelade');
});
