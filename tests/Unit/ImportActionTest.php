<?php

use Accelade\Actions\ImportAction;

it('has import defaults', function () {
    $action = ImportAction::make();

    expect($action->getName())->toBe('import');
    expect($action->getIcon())->toBe('heroicon-o-arrow-up-tray');
    expect($action->getColor())->toBe('gray');
    expect($action->getDefaultFormat())->toBe('xlsx');
    expect($action->getRequiresConfirmation())->toBeTrue();
});

it('can set importer class', function () {
    $action = ImportAction::make()
        ->importer('App\\Imports\\PostsImport');

    expect($action->getImporterClass())->toBe('App\\Imports\\PostsImport');
});

it('can set xlsx as default format', function () {
    $action = ImportAction::make()
        ->xlsx();

    expect($action->getDefaultFormat())->toBe('xlsx');
});

it('can set csv as default format', function () {
    $action = ImportAction::make()
        ->csv();

    expect($action->getDefaultFormat())->toBe('csv');
});

it('can set default format via method', function () {
    $action = ImportAction::make()
        ->defaultFormat('txt');

    expect($action->getDefaultFormat())->toBe('txt');
});

it('can set accepted file types', function () {
    $types = ['text/csv', 'application/json'];

    $action = ImportAction::make()
        ->acceptedFileTypes($types);

    expect($action->getAcceptedFileTypes())->toBe($types);
});

it('has default accepted file types', function () {
    $action = ImportAction::make();

    $types = $action->getAcceptedFileTypes();

    expect($types)->toContain('application/vnd.ms-excel');
    expect($types)->toContain('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    expect($types)->toContain('text/csv');
});

it('can be queued', function () {
    $action = ImportAction::make()
        ->queue();

    expect($action->shouldQueue())->toBeTrue();
});

it('is not queued by default', function () {
    $action = ImportAction::make();

    expect($action->shouldQueue())->toBeFalse();
});

it('can set disk', function () {
    $action = ImportAction::make()
        ->disk('imports');

    expect($action->getDisk())->toBe('imports');
});

it('can set chunk size', function () {
    $action = ImportAction::make()
        ->chunkSize(1000);

    expect($action->getChunkSize())->toBe(1000);
});

it('can set before import callback', function () {
    $action = ImportAction::make()
        ->beforeImport(function ($file) {
            // Validate file
        });

    expect($action->getBeforeImportCallback())->toBeInstanceOf(Closure::class);
});

it('can set after import callback', function () {
    $action = ImportAction::make()
        ->afterImport(function ($file) {
            // Clear cache
        });

    expect($action->getAfterImportCallback())->toBeInstanceOf(Closure::class);
});

it('includes import data in array', function () {
    $action = ImportAction::make()
        ->importer('App\\Imports\\PostsImport')
        ->acceptedFileTypes(['text/csv']);

    $array = $action->toArray();

    expect($array['acceptedFileTypes'])->toBe(['text/csv']);
    expect($array['importerClass'])->toBe('App\\Imports\\PostsImport');
});

it('can set model class', function () {
    $action = ImportAction::make()
        ->model('App\\Models\\Post');

    expect($action->getModelClass())->toBe('App\\Models\\Post');
});

it('can set column mapping', function () {
    $mapping = [
        'Title' => 'title',
        'Author' => 'author_id',
    ];

    $action = ImportAction::make()
        ->columnMapping($mapping);

    expect($action->getColumnMapping())->toBe($mapping);
});

it('can set required columns', function () {
    $action = ImportAction::make()
        ->requiredColumns(['title', 'email']);

    expect($action->getRequiredColumns())->toBe(['title', 'email']);
});

it('can set validation rules', function () {
    $rules = ['email' => 'required|email'];

    $action = ImportAction::make()
        ->validationRules($rules);

    expect($action->getValidationRules())->toBe($rules);
});

it('can set on duplicate behavior', function () {
    $action = ImportAction::make()
        ->onDuplicate('update');

    expect($action->getOnDuplicate())->toBe('update');
});

it('can set unique column for duplicate detection', function () {
    $action = ImportAction::make()
        ->uniqueColumn('email');

    expect($action->getUniqueColumn())->toBe('email');
});

it('can set csv delimiters', function () {
    $action = ImportAction::make()
        ->csvDelimiter(';')
        ->csvEnclosure("'")
        ->csvEscape('\\');

    expect($action->getCsvDelimiter())->toBe(';');
    expect($action->getCsvEnclosure())->toBe("'");
    expect($action->getCsvEscape())->toBe('\\');
});

it('can set plain text delimiters', function () {
    $action = ImportAction::make()
        ->plainTextRowDelimiter("\r\n")
        ->plainTextColumnDelimiter('|');

    expect($action->getPlainTextRowDelimiter())->toBe("\r\n");
    expect($action->getPlainTextColumnDelimiter())->toBe('|');
});

it('can set example data for download', function () {
    $example = [
        ['Title', 'Email'],
        ['Example', 'example@test.com'],
    ];

    $action = ImportAction::make()
        ->exampleData($example)
        ->exampleFileName('import-template.xlsx');

    expect($action->getExampleData())->toBe($example);
    expect($action->getExampleFileName())->toBe('import-template.xlsx');
});

it('can set job configuration', function () {
    $action = ImportAction::make()
        ->queue()
        ->job('App\\Jobs\\ImportJob')
        ->onQueue('imports')
        ->onConnection('redis')
        ->queueBatchSize(500);

    expect($action->getJobClass())->toBe('App\\Jobs\\ImportJob');
    expect($action->getQueueName())->toBe('imports');
    expect($action->getQueueConnection())->toBe('redis');
    expect($action->getQueueBatchSize())->toBe(500);
});

it('can set header row number', function () {
    $action = ImportAction::make()
        ->headerRowNumber(2);

    expect($action->getHeaderRowNumber())->toBe(2);
});

it('can set start row', function () {
    $action = ImportAction::make()
        ->startRow(3);

    expect($action->getStartRow())->toBe(3);
});
