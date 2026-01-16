<?php

use Accelade\Actions\ExportAction;

it('has export defaults', function () {
    $action = ExportAction::make();

    expect($action->getName())->toBe('export');
    expect($action->getIcon())->toBe('heroicon-o-arrow-down-tray');
    expect($action->getColor())->toBe('gray');
    expect($action->getDefaultFormat())->toBe('xlsx');
    expect($action->getAllowedFormats())->toBe(['xlsx', 'csv', 'pdf']);
});

it('can set exporter class', function () {
    $action = ExportAction::make()
        ->exporter('App\\Exports\\PostsExport');

    expect($action->getExporterClass())->toBe('App\\Exports\\PostsExport');
});

it('can set filename', function () {
    $action = ExportAction::make()
        ->fileName('posts-export.xlsx');

    expect($action->getFileName())->toBe('posts-export.xlsx');
});

it('can set xlsx as default format', function () {
    $action = ExportAction::make()
        ->xlsx();

    expect($action->getDefaultFormat())->toBe('xlsx');
});

it('can set csv as default format', function () {
    $action = ExportAction::make()
        ->csv();

    expect($action->getDefaultFormat())->toBe('csv');
});

it('can set pdf as default format', function () {
    $action = ExportAction::make()
        ->pdf();

    expect($action->getDefaultFormat())->toBe('pdf');
});

it('can set allowed formats', function () {
    $action = ExportAction::make()
        ->formats(['xlsx', 'csv']);

    expect($action->getAllowedFormats())->toBe(['xlsx', 'csv']);
});

it('can limit to single format', function () {
    $action = ExportAction::make()
        ->formats(['xlsx']);

    expect($action->getAllowedFormats())->toBe(['xlsx']);
});

it('can set columns', function () {
    $action = ExportAction::make()
        ->columns(['id', 'title', 'author']);

    expect($action->getColumns())->toBe(['id', 'title', 'author']);
});

it('can set headings', function () {
    $action = ExportAction::make()
        ->headings(['ID', 'Title', 'Author']);

    expect($action->getHeadings())->toBe(['ID', 'Title', 'Author']);
});

it('can be queued', function () {
    $action = ExportAction::make()
        ->queue();

    expect($action->shouldQueue())->toBeTrue();
});

it('is not queued by default', function () {
    $action = ExportAction::make();

    expect($action->shouldQueue())->toBeFalse();
});

it('can set disk', function () {
    $action = ExportAction::make()
        ->disk('exports');

    expect($action->getDisk())->toBe('exports');
});

it('can set query modifier', function () {
    $action = ExportAction::make()
        ->modifyQueryUsing(function ($query) {
            return $query->where('status', 'published');
        });

    expect($action->getQueryModifier())->toBeInstanceOf(Closure::class);
});

it('generates filename with format extension when not set', function () {
    $action = ExportAction::make();

    $filename = $action->getFileName();

    expect($filename)->toEndWith('.xlsx');
});

it('can set chunk size', function () {
    $action = ExportAction::make()
        ->chunkSize(500);

    expect($action->getChunkSize())->toBe(500);
});

it('can set max rows', function () {
    $action = ExportAction::make()
        ->maxRows(10000);

    expect($action->getMaxRows())->toBe(10000);
});

it('can set csv delimiter', function () {
    $action = ExportAction::make()
        ->csvDelimiter(';');

    expect($action->getCsvDelimiter())->toBe(';');
});

it('can set page orientation', function () {
    $action = ExportAction::make()
        ->landscape();

    expect($action->getPageOrientation())->toBe('landscape');
});

it('can set job configuration', function () {
    $action = ExportAction::make()
        ->queue()
        ->job('App\\Jobs\\CustomExportJob')
        ->onQueue('exports')
        ->onConnection('redis');

    expect($action->getJobClass())->toBe('App\\Jobs\\CustomExportJob');
    expect($action->getJobQueue())->toBe('exports');
    expect($action->getJobConnection())->toBe('redis');
});

it('changes default format when formats array excludes current default', function () {
    $action = ExportAction::make()
        ->formats(['csv', 'pdf']);

    expect($action->getDefaultFormat())->toBe('csv');
});
