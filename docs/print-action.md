# Print Action

PrintAction provides a pre-configured action for printing the current page, specific elements, or custom content. It opens the browser's print dialog directly when clicked.

## Basic Usage

```php
use Accelade\Actions\PrintAction;

// Print the entire page
PrintAction::make();
```

## Print Modes

### Print Current Page

```php
PrintAction::make()
    ->printPage();  // Default behavior
```

### Print Specific Element

```php
PrintAction::make()
    ->printElement('#invoice-container');

// Alternative syntax
PrintAction::make()
    ->selector('.printable-content');
```

### Print Custom HTML

```php
PrintAction::make()
    ->printHtml('<h1>Hello World</h1><p>Custom content</p>');

// With closure for dynamic content
PrintAction::make()
    ->html(fn ($record) => "<h1>Invoice #{$record->id}</h1>");
```

### Print Blade View

```php
PrintAction::make()
    ->printView('invoices.print', ['invoice' => $invoice]);

// With closure for dynamic data
PrintAction::make()
    ->view('receipts.print', fn ($record) => [
        'order' => $record,
        'company' => config('app.company'),
    ]);
```

### Print from URL

```php
PrintAction::make()
    ->printFromUrl(route('invoice.print', $id));
```

## Page Settings

### Page Size

```php
PrintAction::make()
    ->a4()       // A4 size
    ->letter()   // Letter size
    ->legal()    // Legal size
    ->pageSize('A3');  // Custom size
```

### Orientation

```php
PrintAction::make()
    ->portrait()   // Portrait orientation
    ->landscape(); // Landscape orientation

// Or explicitly
PrintAction::make()
    ->orientation('landscape');
```

### Margins

```php
// All sides same margin
PrintAction::make()
    ->margins('1in');

// Custom margins
PrintAction::make()
    ->margins([
        'top' => '0.5in',
        'right' => '1in',
        'bottom' => '0.5in',
        'left' => '1in',
    ]);
```

## Styling

### Custom Print CSS

```php
PrintAction::make()
    ->printCss('
        body { font-size: 12pt; }
        .no-print { display: none !important; }
        table { border-collapse: collapse; }
    ');
```

### Hide Elements When Printing

```php
PrintAction::make()
    ->hideWhenPrinting([
        '.navbar',
        '.sidebar',
        '.footer',
        '.print-button',
    ]);
```

### Remove Backgrounds

```php
PrintAction::make()
    ->removeBackgrounds();  // Remove all background colors/images
```

### Print Links with URLs

```php
PrintAction::make()
    ->printLinks();  // Show URLs after links: "Click here (https://example.com)"
```

### Control Images

```php
PrintAction::make()
    ->printImages(false);  // Hide all images when printing
```

## Document Settings

### Document Title

```php
PrintAction::make()
    ->documentTitle('Invoice #12345');

// Dynamic title
PrintAction::make()
    ->title(fn ($record) => "Order #{$record->id}");
```

### Headers and Footers

```php
PrintAction::make()
    ->includeHeaders()
    ->includePageNumbers()
    ->headerHtml('<div>Company Name</div>')
    ->footerHtml('<div>Page {page} of {pages}</div>');
```

## Behavior

### Print Delay

```php
PrintAction::make()
    ->printDelay(500);  // 500ms delay before print dialog
```

### Disable Auto Print

```php
PrintAction::make()
    ->autoPrint(false);  // Prepare content but don't open dialog
```

## Callbacks

```php
PrintAction::make()
    ->beforePrint(function ($record, $data) {
        // Log the print action
        activity()->performedOn($record)->log('Document printed');
    })
    ->afterPrint(function ($record, $data) {
        // Mark as printed
        $record->update(['printed_at' => now()]);
    });
```

## Button Variants

```php
PrintAction::make()->button();      // Standard button
PrintAction::make()->link();        // Link style
PrintAction::make()->iconButton();  // Icon only
PrintAction::make()->outlined();    // Outlined button
```

## Complete Example

```php
PrintAction::make()
    ->label('Print Invoice')
    ->icon('heroicon-o-printer')
    ->printView('invoices.print', fn ($invoice) => [
        'invoice' => $invoice,
        'company' => Company::first(),
    ])
    ->documentTitle(fn ($invoice) => "Invoice #{$invoice->number}")
    ->a4()
    ->portrait()
    ->margins(['top' => '0.75in', 'right' => '0.5in', 'bottom' => '0.75in', 'left' => '0.5in'])
    ->hideWhenPrinting(['.actions', '.pagination'])
    ->printCss('
        .invoice-header { border-bottom: 2px solid #333; }
        .invoice-total { font-size: 18pt; font-weight: bold; }
    ')
    ->beforePrint(fn ($invoice) => $invoice->increment('print_count'));
```

## API Reference

| Method | Description |
|--------|-------------|
| `printPage()` | Print the entire page (default) |
| `printElement(string $selector)` | Print specific element by CSS selector |
| `selector(string $selector)` | Alias for printElement |
| `printHtml(string\|Closure $html)` | Print custom HTML content |
| `html(string\|Closure $html)` | Alias for printHtml |
| `printView(string $view, array\|Closure $data)` | Print rendered Blade view |
| `view(string $view, array\|Closure $data)` | Alias for printView |
| `printFromUrl(string\|Closure $url)` | Print content from URL |
| `pageSize(string $size)` | Set page size |
| `a4()` | Set A4 page size |
| `letter()` | Set Letter page size |
| `legal()` | Set Legal page size |
| `orientation(string $orientation)` | Set page orientation |
| `portrait()` | Set portrait orientation |
| `landscape()` | Set landscape orientation |
| `margins(array\|string $margins)` | Set page margins |
| `printCss(string\|Closure $css)` | Add custom print CSS |
| `documentTitle(string\|Closure $title)` | Set document title |
| `title(string\|Closure $title)` | Alias for documentTitle |
| `autoPrint(bool $auto)` | Enable/disable auto print dialog |
| `printDelay(int $ms)` | Delay before print dialog (ms) |
| `hideWhenPrinting(array\|string $selectors)` | Hide elements when printing |
| `showOnlyWhenPrinting(array\|string $selectors)` | Show only these elements |
| `removeBackgrounds(bool $remove)` | Remove background colors/images |
| `printImages(bool $print)` | Enable/disable printing images |
| `printLinks(bool $print)` | Show URLs after links |
| `includeHeaders(bool $include)` | Include headers/footers |
| `includePageNumbers(bool $include)` | Include page numbers |
| `headerHtml(string $html)` | Custom header HTML |
| `footerHtml(string $html)` | Custom footer HTML |
| `beforePrint(Closure $callback)` | Callback before printing |
| `afterPrint(Closure $callback)` | Callback after printing |
