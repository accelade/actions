<?php

use Accelade\Actions\PrintAction;

it('has print defaults', function () {
    $action = PrintAction::make();

    expect($action->getName())->toBe('print');
    expect($action->getIcon())->toBe('heroicon-o-printer');
    expect($action->getColor())->toBe('gray');
    expect($action->getRequiresConfirmation())->toBeFalse();
    expect($action->getPrintMode())->toBe('page');
    expect($action->shouldAutoPrint())->toBeTrue();
    expect($action->getPrintDelay())->toBe(0);
    expect($action->getPageSize())->toBe('A4');
    expect($action->getOrientation())->toBe('portrait');
});

it('can print current page', function () {
    $action = PrintAction::make()->printPage();

    expect($action->getPrintMode())->toBe('page');
});

it('can print specific element', function () {
    $action = PrintAction::make()
        ->printElement('#invoice');

    expect($action->getPrintMode())->toBe('element');
    expect($action->getSelector())->toBe('#invoice');
});

it('can use selector alias', function () {
    $action = PrintAction::make()
        ->selector('#receipt');

    expect($action->getPrintMode())->toBe('element');
    expect($action->getSelector())->toBe('#receipt');
});

it('can print custom html', function () {
    $html = '<h1>Test</h1>';
    $action = PrintAction::make()
        ->printHtml($html);

    expect($action->getPrintMode())->toBe('html');
    expect($action->getHtmlContent())->toBe($html);
});

it('can print html with closure', function () {
    $action = PrintAction::make()
        ->printHtml(fn ($record) => "<h1>{$record->title}</h1>");

    $record = (object) ['title' => 'My Report'];
    expect($action->getHtmlContent($record))->toBe('<h1>My Report</h1>');
});

it('can print blade view', function () {
    $action = PrintAction::make()
        ->printView('invoices.print', ['type' => 'detailed']);

    expect($action->getPrintMode())->toBe('view');
    expect($action->getViewName())->toBe('invoices.print');
    expect($action->getViewData())->toBe(['type' => 'detailed']);
});

it('can print from url', function () {
    $action = PrintAction::make()
        ->printFromUrl('/print/invoice/123');

    expect($action->getPrintMode())->toBe('url');
    expect($action->getPrintUrl())->toBe('/print/invoice/123');
});

it('can set landscape orientation', function () {
    $action = PrintAction::make()->landscape();

    expect($action->getOrientation())->toBe('landscape');
});

it('can set portrait orientation', function () {
    $action = PrintAction::make()->portrait();

    expect($action->getOrientation())->toBe('portrait');
});

it('can set page size to A4', function () {
    $action = PrintAction::make()->a4();

    expect($action->getPageSize())->toBe('A4');
});

it('can set page size to Letter', function () {
    $action = PrintAction::make()->letter();

    expect($action->getPageSize())->toBe('Letter');
});

it('can set page size to Legal', function () {
    $action = PrintAction::make()->legal();

    expect($action->getPageSize())->toBe('Legal');
});

it('can set custom page size', function () {
    $action = PrintAction::make()->pageSize('A3');

    expect($action->getPageSize())->toBe('A3');
});

it('can set margins as string', function () {
    $action = PrintAction::make()->margins('1in');

    expect($action->getMargins())->toBe([
        'top' => '1in',
        'right' => '1in',
        'bottom' => '1in',
        'left' => '1in',
    ]);
});

it('can set margins as array', function () {
    $margins = [
        'top' => '0.5in',
        'right' => '1in',
        'bottom' => '0.5in',
        'left' => '1in',
    ];
    $action = PrintAction::make()->margins($margins);

    expect($action->getMargins())->toBe($margins);
});

it('can set document title', function () {
    $action = PrintAction::make()
        ->documentTitle('Invoice #123');

    expect($action->getDocumentTitle())->toBe('Invoice #123');
});

it('can set document title with closure', function () {
    $action = PrintAction::make()
        ->title(fn ($record) => "Order #{$record->id}");

    $record = (object) ['id' => 456];
    expect($action->getDocumentTitle($record))->toBe('Order #456');
});

it('can disable auto print', function () {
    $action = PrintAction::make()->autoPrint(false);

    expect($action->shouldAutoPrint())->toBeFalse();
});

it('can set print delay', function () {
    $action = PrintAction::make()->printDelay(500);

    expect($action->getPrintDelay())->toBe(500);
});

it('can hide elements when printing', function () {
    $action = PrintAction::make()
        ->hideWhenPrinting('.navbar')
        ->hideWhenPrinting(['.sidebar', '.footer']);

    expect($action->getHideSelectors())->toBe(['.navbar', '.sidebar', '.footer']);
});

it('can show only specific elements when printing', function () {
    $action = PrintAction::make()
        ->showOnlyWhenPrinting('.print-area');

    expect($action->getShowOnlySelectors())->toBe(['.print-area']);
});

it('can remove backgrounds', function () {
    $action = PrintAction::make()->removeBackgrounds();

    expect($action->shouldRemoveBackgrounds())->toBeTrue();
});

it('can disable printing images', function () {
    $action = PrintAction::make()->printImages(false);

    expect($action->shouldPrintImages())->toBeFalse();
});

it('can enable printing links', function () {
    $action = PrintAction::make()->printLinks();

    expect($action->shouldPrintLinks())->toBeTrue();
});

it('can set custom print css', function () {
    $css = 'body { font-size: 12pt; }';
    $action = PrintAction::make()->printCss($css);

    expect($action->getPrintCss())->toBe($css);
});

it('can include headers', function () {
    $action = PrintAction::make()->includeHeaders();

    expect($action->shouldIncludeHeaders())->toBeTrue();
});

it('can include page numbers', function () {
    $action = PrintAction::make()->includePageNumbers();

    expect($action->shouldIncludePageNumbers())->toBeTrue();
});

it('can set custom header html', function () {
    $action = PrintAction::make()
        ->headerHtml('<header>My Header</header>');

    expect($action->getHeaderHtml())->toBe('<header>My Header</header>');
    expect($action->shouldIncludeHeaders())->toBeTrue();
});

it('can set custom footer html', function () {
    $action = PrintAction::make()
        ->footerHtml('<footer>Page 1</footer>');

    expect($action->getFooterHtml())->toBe('<footer>Page 1</footer>');
    expect($action->shouldIncludeHeaders())->toBeTrue();
});

it('can set before print callback', function () {
    $action = PrintAction::make()
        ->beforePrint(fn ($record) => null);

    expect($action->getBeforePrintCallback())->not->toBeNull();
});

it('can set after print callback', function () {
    $action = PrintAction::make()
        ->afterPrint(fn ($record) => null);

    expect($action->getAfterPrintCallback())->not->toBeNull();
});

it('generates print css', function () {
    $action = PrintAction::make()
        ->a4()
        ->portrait()
        ->margins('1in')
        ->hideWhenPrinting('.no-print')
        ->removeBackgrounds();

    $css = $action->generatePrintCss();

    expect($css)->toContain('@media print');
    expect($css)->toContain('size: A4 portrait');
    expect($css)->toContain('margin: 1in 1in 1in 1in');
    expect($css)->toContain('.no-print { display: none !important; }');
    expect($css)->toContain('background: transparent !important');
});

it('executes with correct configuration', function () {
    $action = PrintAction::make()
        ->printElement('#invoice')
        ->documentTitle('Invoice')
        ->printDelay(100);

    $result = $action->execute();

    expect($result)->toBeArray();
    expect($result['mode'])->toBe('element');
    expect($result['selector'])->toBe('#invoice');
    expect($result['documentTitle'])->toBe('Invoice');
    expect($result['autoPrint'])->toBeTrue();
    expect($result['printDelay'])->toBe(100);
});

it('converts to array with record', function () {
    $action = PrintAction::make()
        ->landscape()
        ->letter();

    $data = $action->toArrayWithRecord();

    expect($data['printMode'])->toBe('page');
    expect($data['pageSize'])->toBe('Letter');
    expect($data['orientation'])->toBe('landscape');
    expect($data['autoPrint'])->toBeTrue();
});

it('can override defaults', function () {
    $action = PrintAction::make('print-invoice')
        ->label('Print Invoice')
        ->icon('heroicon-o-document')
        ->color('primary');

    expect($action->getName())->toBe('print-invoice');
    expect($action->getLabel())->toBe('Print Invoice');
    expect($action->getIcon())->toBe('heroicon-o-document');
    expect($action->getColor())->toBe('primary');
});
