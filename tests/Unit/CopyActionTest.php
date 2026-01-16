<?php

use Accelade\Actions\CopyAction;

it('has copy defaults', function () {
    $action = CopyAction::make();

    expect($action->getName())->toBe('copy');
    expect($action->getIcon())->toBe('heroicon-o-clipboard-document');
    expect($action->getColor())->toBe('gray');
    expect($action->getRequiresConfirmation())->toBeFalse();
    expect($action->shouldShowNotification())->toBeTrue();
    expect($action->getCopyMode())->toBe('value');
    expect($action->getCopyAs())->toBe('text');
    expect($action->getNotificationDuration())->toBe(2000);
});

it('can set copy value', function () {
    $action = CopyAction::make()
        ->copyValue('test value');

    expect($action->getCopyMode())->toBe('value');
    expect($action->getCopyValue())->toBe('test value');
});

it('can set copy value using alias', function () {
    $action = CopyAction::make()
        ->value('another value');

    expect($action->getCopyValue())->toBe('another value');
});

it('can set copy value with closure', function () {
    $action = CopyAction::make()
        ->copyValue(fn ($record) => "Hello {$record->name}");

    $record = (object) ['name' => 'World'];
    expect($action->getCopyValue($record))->toBe('Hello World');
});

it('can copy record attribute', function () {
    $action = CopyAction::make()
        ->copyAttribute('email');

    expect($action->getCopyMode())->toBe('attribute');

    $record = (object) ['email' => 'test@example.com'];
    expect($action->getCopyValue($record))->toBe('test@example.com');
});

it('can copy multiple record attributes', function () {
    $action = CopyAction::make()
        ->copyAttributes(['name', 'email']);

    $record = (object) ['name' => 'John', 'email' => 'john@example.com'];
    expect($action->getCopyValue($record))->toBe("John\njohn@example.com");
});

it('can set attribute separator', function () {
    $action = CopyAction::make()
        ->copyAttributes(['first', 'second'])
        ->attributeSeparator(' - ');

    $record = (object) ['first' => 'A', 'second' => 'B'];
    expect($action->getCopyValue($record))->toBe('A - B');
});

it('can copy element content', function () {
    $action = CopyAction::make()
        ->copyElement('#my-element');

    expect($action->getCopyMode())->toBe('element');
    expect($action->getElementSelector())->toBe('#my-element');
});

it('can copy current selection', function () {
    $action = CopyAction::make()
        ->copySelection();

    expect($action->getCopyMode())->toBe('selection');
});

it('can format copy value', function () {
    $action = CopyAction::make()
        ->copyValue('hello')
        ->formatValue(fn ($value) => strtoupper($value));

    expect($action->getCopyValue())->toBe('HELLO');
});

it('can set copy as text', function () {
    $action = CopyAction::make()->asText();

    expect($action->getCopyAs())->toBe('text');
});

it('can set copy as html', function () {
    $action = CopyAction::make()->asHtml();

    expect($action->getCopyAs())->toBe('html');
});

it('can set copy as json', function () {
    $action = CopyAction::make()->asJson();

    expect($action->getCopyAs())->toBe('json');
});

it('can show and hide notification', function () {
    $action = CopyAction::make();
    expect($action->shouldShowNotification())->toBeTrue();

    $action->hideNotification();
    expect($action->shouldShowNotification())->toBeFalse();

    $action->showNotification(true);
    expect($action->shouldShowNotification())->toBeTrue();
});

it('can set success message', function () {
    $action = CopyAction::make()
        ->successMessage('Custom success');

    expect($action->getSuccessMessage())->toBe('Custom success');
});

it('can set failure message', function () {
    $action = CopyAction::make()
        ->failureMessage('Custom failure');

    expect($action->getFailureMessage())->toBe('Custom failure');
});

it('can set notification duration', function () {
    $action = CopyAction::make()
        ->notificationDuration(5000);

    expect($action->getNotificationDuration())->toBe(5000);
});

it('can set before copy callback', function () {
    $called = false;
    $action = CopyAction::make()
        ->beforeCopy(function () use (&$called) {
            $called = true;
        });

    expect($action->getBeforeCopyCallback())->not->toBeNull();
});

it('can set after copy callback', function () {
    $called = false;
    $action = CopyAction::make()
        ->afterCopy(function () use (&$called) {
            $called = true;
        });

    expect($action->getAfterCopyCallback())->not->toBeNull();
});

it('executes with correct configuration', function () {
    $action = CopyAction::make()
        ->copyValue('test')
        ->successMessage('Copied!')
        ->notificationDuration(3000);

    $result = $action->execute();

    expect($result)->toBeArray();
    expect($result['mode'])->toBe('value');
    expect($result['value'])->toBe('test');
    expect($result['showNotification'])->toBeTrue();
    expect($result['successMessage'])->toBe('Copied!');
    expect($result['notificationDuration'])->toBe(3000);
});

it('converts to array with record', function () {
    $action = CopyAction::make()
        ->copyAttribute('email');

    $record = (object) ['email' => 'user@test.com'];
    $data = $action->toArrayWithRecord($record);

    expect($data['copyMode'])->toBe('attribute');
    expect($data['copyValue'])->toBe('user@test.com');
    expect($data['showNotification'])->toBeTrue();
});

it('can override defaults', function () {
    $action = CopyAction::make('copy-email')
        ->label('Copy Email')
        ->icon('heroicon-o-envelope')
        ->color('primary');

    expect($action->getName())->toBe('copy-email');
    expect($action->getLabel())->toBe('Copy Email');
    expect($action->getIcon())->toBe('heroicon-o-envelope');
    expect($action->getColor())->toBe('primary');
});
