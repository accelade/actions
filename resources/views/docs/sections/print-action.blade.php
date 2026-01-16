<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    <div class="space-y-8">
        {{-- Basic PrintAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic PrintAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                PrintAction opens the browser print dialog. By default, it prints the current page.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicPrint = \Accelade\Actions\PrintAction::make()
                        ->action(fn () => \Accelade\Facades\Notify::success('Print dialog opened'));
                @endphp
                <x-accelade::action :action="$basicPrint" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\PrintAction;

// Print the entire page
PrintAction::make();
            </x-accelade::code-block>
        </div>

        {{-- Print Element --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Print Specific Element</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Print only a specific element by CSS selector.
            </p>

            <div id="printable-content" class="p-4 border border-slate-200 dark:border-slate-700 rounded-lg mb-4">
                <h4 class="font-medium text-slate-900 dark:text-white">Invoice #12345</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400">This content will be printed.</p>
                <table class="min-w-full mt-2 text-sm">
                    <tr><td>Item 1</td><td class="text-end">$100.00</td></tr>
                    <tr><td>Item 2</td><td class="text-end">$50.00</td></tr>
                    <tr class="font-bold"><td>Total</td><td class="text-end">$150.00</td></tr>
                </table>
            </div>

            <div class="flex flex-wrap gap-4">
                @php
                    $elementPrint = \Accelade\Actions\PrintAction::make('print-invoice')
                        ->label('Print Invoice')
                        ->printElement('#printable-content')
                        ->action(fn () => \Accelade\Facades\Notify::success('Printing invoice...'));
                @endphp
                <x-accelade::action :action="$elementPrint" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
PrintAction::make()
    ->label('Print Invoice')
    ->printElement('#invoice-container');

// Alternative syntax
PrintAction::make()
    ->selector('#invoice-container');
            </x-accelade::code-block>
        </div>

        {{-- Print Custom HTML --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Print Custom HTML</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Print dynamically generated HTML content.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $htmlPrint = \Accelade\Actions\PrintAction::make('print-html')
                        ->label('Print Custom HTML')
                        ->printHtml('<h1>Hello World</h1><p>This is custom HTML content to print.</p>')
                        ->action(fn () => \Accelade\Facades\Notify::success('Custom HTML print dialog opened'));
                @endphp
                <x-accelade::action :action="$htmlPrint" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
PrintAction::make()
    ->label('Print Report')
    ->printHtml('<h1>Sales Report</h1><p>Generated: ' . now()->format('Y-m-d') . '</p>');

// With closure for dynamic content
PrintAction::make()
    ->html(fn ($record) => "
        <h1>Order #{$record->id}</h1>
        <p>Customer: {$record->customer->name}</p>
        <p>Total: \${$record->total}</p>
    ");
            </x-accelade::code-block>
        </div>

        {{-- Print View --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Print Blade View</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Render and print a Blade view with data.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
// Print a Blade view
PrintAction::make()
    ->label('Print Receipt')
    ->printView('receipts.print', ['order' => $order]);

// With closure for dynamic data
PrintAction::make()
    ->view('invoices.print', fn ($record) => [
        'invoice' => $record,
        'company' => config('app.company'),
    ]);
            </x-accelade::code-block>
        </div>

        {{-- Page Settings --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Page Settings</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Configure page size, orientation, and margins for printing.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $landscapePrint = \Accelade\Actions\PrintAction::make('print-landscape')
                        ->label('Print Landscape')
                        ->landscape()
                        ->action(fn () => \Accelade\Facades\Notify::success('Landscape print'));

                    $a4Print = \Accelade\Actions\PrintAction::make('print-a4')
                        ->label('Print A4')
                        ->a4()
                        ->portrait()
                        ->action(fn () => \Accelade\Facades\Notify::success('A4 portrait print'));
                @endphp
                <x-accelade::action :action="$landscapePrint" />
                <x-accelade::action :action="$a4Print" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
PrintAction::make()
    ->landscape()  // or ->portrait()
    ->a4()         // or ->letter() or ->legal()
    ->margins('1in');  // All sides

// Custom margins
PrintAction::make()
    ->pageSize('Letter')
    ->orientation('landscape')
    ->margins([
        'top' => '0.5in',
        'right' => '1in',
        'bottom' => '0.5in',
        'left' => '1in',
    ]);
            </x-accelade::code-block>
        </div>

        {{-- Custom Print CSS --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Custom Print Styles</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Add custom CSS that only applies when printing.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
PrintAction::make()
    ->printCss('
        body { font-size: 12pt; }
        .no-print { display: none !important; }
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #000; padding: 8px; }
    ');
            </x-accelade::code-block>
        </div>

        {{-- Hide/Show Elements --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Hide/Show Elements</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Control which elements appear in the printed output.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $hidePrint = \Accelade\Actions\PrintAction::make('print-hide')
                        ->label('Print (Hide Navigation)')
                        ->hideWhenPrinting(['.navbar', '.sidebar', '.footer'])
                        ->action(fn () => \Accelade\Facades\Notify::success('Navigation hidden in print'));
                @endphp
                <x-accelade::action :action="$hidePrint" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
PrintAction::make()
    ->hideWhenPrinting([
        '.navbar',
        '.sidebar',
        '.print-button',
        '.pagination',
    ])
    ->removeBackgrounds()  // Remove background colors/images
    ->printLinks();        // Show URLs after links

// Multiple hide selectors
PrintAction::make()
    ->hideWhenPrinting('.no-print')
    ->hideWhenPrinting('.actions-column');
            </x-accelade::code-block>
        </div>

        {{-- Document Title --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Document Title</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Set the document title that appears in the print dialog and saved PDF.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $titlePrint = \Accelade\Actions\PrintAction::make('print-title')
                        ->label('Print Report')
                        ->documentTitle('Monthly Sales Report - ' . now()->format('F Y'))
                        ->action(fn () => \Accelade\Facades\Notify::success('Report with custom title'));
                @endphp
                <x-accelade::action :action="$titlePrint" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
PrintAction::make()
    ->documentTitle('Invoice #12345 - Acme Corp');

// Dynamic title from record
PrintAction::make()
    ->title(fn ($record) => "Order #{$record->id} - {$record->customer->name}");
            </x-accelade::code-block>
        </div>

        {{-- Print Delay --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Print Delay</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Add a delay before the print dialog opens (useful for content that needs to load).
            </p>

            <x-accelade::code-block language="php" class="mt-4">
PrintAction::make()
    ->printDelay(500);  // 500ms delay

// Disable auto-print (just prepare content, don't open dialog)
PrintAction::make()
    ->autoPrint(false);
            </x-accelade::code-block>
        </div>

        {{-- Callbacks --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Before & After Callbacks</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Execute code before and after printing.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
PrintAction::make()
    ->beforePrint(function ($record) {
        // Log the print action
        activity()
            ->performedOn($record)
            ->log('Document printed');
    })
    ->afterPrint(function ($record) {
        // Mark as printed
        $record->update(['printed_at' => now()]);
    });
            </x-accelade::code-block>
        </div>

        {{-- Button Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                PrintAction in different button styles.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @php
                    $buttonPrint = \Accelade\Actions\PrintAction::make('print-button')
                        ->button()
                        ->action(fn () => \Accelade\Facades\Notify::success('Button variant'));

                    $linkPrint = \Accelade\Actions\PrintAction::make('print-link')
                        ->link()
                        ->action(fn () => \Accelade\Facades\Notify::success('Link variant'));

                    $iconPrint = \Accelade\Actions\PrintAction::make('print-icon')
                        ->iconButton()
                        ->tooltip('Print this page')
                        ->action(fn () => \Accelade\Facades\Notify::success('Icon variant'));

                    $outlinedPrint = \Accelade\Actions\PrintAction::make('print-outlined')
                        ->outlined()
                        ->action(fn () => \Accelade\Facades\Notify::success('Outlined variant'));
                @endphp

                <div class="text-center">
                    <x-accelade::action :action="$buttonPrint" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Button</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$linkPrint" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Link</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$iconPrint" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$outlinedPrint" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Outlined</p>
                </div>
            </div>
        </div>

        {{-- Real World Example --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Real World Example: Invoice Printing</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Complete example for printing invoices with professional formatting.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
// In your controller or component
PrintAction::make()
    ->label('Print Invoice')
    ->icon('heroicon-o-printer')
    ->printView('invoices.print', fn ($invoice) => [
        'invoice' => $invoice,
        'company' => Company::first(),
        'showLogo' => true,
    ])
    ->documentTitle(fn ($invoice) => "Invoice #{$invoice->number}")
    ->a4()
    ->portrait()
    ->margins([
        'top' => '0.75in',
        'right' => '0.5in',
        'bottom' => '0.75in',
        'left' => '0.5in',
    ])
    ->printCss('
        .invoice-header { border-bottom: 2px solid #333; }
        .invoice-total { font-size: 18pt; font-weight: bold; }
        .payment-terms { color: #666; font-size: 10pt; }
    ')
    ->beforePrint(function ($invoice) {
        $invoice->increment('print_count');
    });
            </x-accelade::code-block>
        </div>
    </div>
</x-accelade::layouts.docs>
