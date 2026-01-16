<?php

use Accelade\Actions\BulkAction;

it('is marked as bulk action', function () {
    $action = BulkAction::make('test');

    expect($action->isBulkAction())->toBeTrue();
});

it('has default confirmation', function () {
    $action = BulkAction::make('test');

    expect($action->getRequiresConfirmation())->toBeTrue();
});

it('can deselect records after completion', function () {
    $action = BulkAction::make('test')
        ->deselectRecordsAfterCompletion();

    expect($action->shouldDeselectRecordsAfterCompletion())->toBeTrue();
});

it('does not deselect by default', function () {
    $action = BulkAction::make('test');

    expect($action->shouldDeselectRecordsAfterCompletion())->toBeFalse();
});

it('can execute for records', function () {
    $executed = false;
    $passedIds = [];

    $action = BulkAction::make('test')
        ->action(function (array $ids) use (&$executed, &$passedIds) {
            $executed = true;
            $passedIds = $ids;

            return count($ids);
        });

    $result = $action->executeForRecords([1, 2, 3]);

    expect($executed)->toBeTrue();
    expect($passedIds)->toBe([1, 2, 3]);
    expect($result)->toBe(3);
});

it('includes bulk action flag in array output', function () {
    $action = BulkAction::make('test')
        ->deselectRecordsAfterCompletion();

    $array = $action->toArray();

    expect($array['isBulkAction'])->toBeTrue();
    expect($array['deselectRecordsAfterCompletion'])->toBeTrue();
});
