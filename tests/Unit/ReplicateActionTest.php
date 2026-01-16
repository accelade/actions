<?php

use Accelade\Actions\ReplicateAction;

it('has replicate defaults', function () {
    $action = ReplicateAction::make();

    expect($action->getName())->toBe('replicate');
    expect($action->getIcon())->toBe('heroicon-o-document-duplicate');
    expect($action->getColor())->toBe('gray');
    expect($action->getRequiresConfirmation())->toBeTrue();
});

it('can exclude attributes', function () {
    $action = ReplicateAction::make()
        ->excludeAttributes(['slug', 'published_at']);

    expect($action->getExcludedAttributes())->toBe(['slug', 'published_at']);
});

it('has empty excluded attributes by default', function () {
    $action = ReplicateAction::make();

    expect($action->getExcludedAttributes())->toBe([]);
});

it('can set before replica saved callback', function () {
    $action = ReplicateAction::make()
        ->beforeReplicaSaved(function ($replica, $original) {
            $replica->title = $original->title.' (Copy)';
        });

    expect($action)->toBeInstanceOf(ReplicateAction::class);
});

it('can set after replica saved callback', function () {
    $action = ReplicateAction::make()
        ->afterReplicaSaved(function ($replica, $original) {
            // Copy relationships
        });

    expect($action)->toBeInstanceOf(ReplicateAction::class);
});

it('can set success redirect url', function () {
    $action = ReplicateAction::make()
        ->successRedirectUrl(function ($replica) {
            return '/posts/'.$replica->id.'/edit';
        });

    expect($action)->toBeInstanceOf(ReplicateAction::class);
});

it('can override defaults', function () {
    $action = ReplicateAction::make('duplicate')
        ->label('Duplicate')
        ->icon('heroicon-o-square-2-stack')
        ->color('primary');

    expect($action->getName())->toBe('duplicate');
    expect($action->getLabel())->toBe('Duplicate');
    expect($action->getIcon())->toBe('heroicon-o-square-2-stack');
    expect($action->getColor())->toBe('primary');
});
