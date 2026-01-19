<?php

use Accelade\Actions\Action;

it('renders action button', function () {
    $action = Action::make('test')
        ->label('Test Button')
        ->color('primary');

    $view = $this->blade(
        '<x-accelade::action :action="$action" />',
        ['action' => $action]
    );

    $view->assertSee('Test Button');
});

it('renders action with icon', function () {
    $action = Action::make('test')
        ->label('Save')
        ->icon('heroicon-o-check');

    $view = $this->blade(
        '<x-accelade::action :action="$action" />',
        ['action' => $action]
    );

    $view->assertSee('Save');
    $view->assertSee('svg');
});

it('renders hidden action as empty', function () {
    $action = Action::make('test')
        ->label('Hidden')
        ->hidden();

    $view = $this->blade(
        '<x-accelade::action :action="$action" />',
        ['action' => $action]
    );

    $view->assertDontSee('Hidden');
});

it('renders disabled action', function () {
    $action = Action::make('test')
        ->label('Disabled')
        ->disabled();

    $view = $this->blade(
        '<x-accelade::action :action="$action" />',
        ['action' => $action]
    );

    $view->assertSee('disabled');
    $view->assertSee('opacity-50');
});
