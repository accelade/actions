<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;

/**
 * PrintAction - Action for printing the current screen, a view, or custom HTML.
 *
 * Opens the browser print dialog directly when clicked.
 * Supports printing the current page, a specific element by selector,
 * custom HTML content, or rendering a Blade view.
 */
class PrintAction extends Action
{
    // ============================================
    // PRINT MODE CONFIGURATION
    // ============================================

    /**
     * Print mode: 'page', 'element', 'html', 'view', 'url'
     */
    protected string $printMode = 'page';

    /**
     * CSS selector for element to print (when mode is 'element')
     */
    protected ?string $selector = null;

    /**
     * Custom HTML content to print (when mode is 'html')
     */
    protected string|Closure|null $htmlContent = null;

    /**
     * Blade view to render and print (when mode is 'view')
     */
    protected ?string $viewName = null;

    /**
     * Data to pass to the view
     *
     * @var array<string, mixed>|Closure|null
     */
    protected array|Closure|null $viewData = null;

    /**
     * URL to print (when mode is 'url')
     */
    protected string|Closure|null $printUrl = null;

    // ============================================
    // STYLING CONFIGURATION
    // ============================================

    /**
     * Custom CSS to inject for print styling
     */
    protected string|Closure|null $printCss = null;

    /**
     * Page size: 'A4', 'Letter', 'Legal', etc.
     */
    protected string $pageSize = 'A4';

    /**
     * Page orientation: 'portrait' or 'landscape'
     */
    protected string $orientation = 'portrait';

    /**
     * Page margins
     *
     * @var array{top?: string, right?: string, bottom?: string, left?: string}|null
     */
    protected ?array $margins = null;

    /**
     * Whether to include page headers/footers
     */
    protected bool $includeHeaders = false;

    /**
     * Whether to include page numbers
     */
    protected bool $includePageNumbers = false;

    /**
     * Custom header HTML
     */
    protected ?string $headerHtml = null;

    /**
     * Custom footer HTML
     */
    protected ?string $footerHtml = null;

    // ============================================
    // BEHAVIOR CONFIGURATION
    // ============================================

    /**
     * Document title for the print job
     */
    protected string|Closure|null $documentTitle = null;

    /**
     * Whether to open print dialog immediately
     */
    protected bool $autoPrint = true;

    /**
     * Delay before print dialog opens (ms)
     */
    protected int $printDelay = 0;

    /**
     * Elements to hide when printing
     *
     * @var array<string>
     */
    protected array $hideSelectors = [];

    /**
     * Elements to show only when printing
     *
     * @var array<string>
     */
    protected array $showOnlySelectors = [];

    /**
     * Whether to remove backgrounds when printing
     */
    protected bool $removeBackgrounds = false;

    /**
     * Whether to print images
     */
    protected bool $printImages = true;

    /**
     * Whether to print links (show URLs)
     */
    protected bool $printLinks = false;

    // ============================================
    // CALLBACKS
    // ============================================

    /**
     * Callback before printing
     */
    protected ?Closure $beforePrint = null;

    /**
     * Callback after printing
     */
    protected ?Closure $afterPrint = null;

    // ============================================
    // SETUP
    // ============================================

    protected function setUp(): void
    {
        parent::setUp();

        $this->name ??= 'print';
        $this->label(__('actions::actions.buttons.print'));
        $this->icon('heroicon-o-printer');
        $this->color('gray');

        // PrintAction should NOT require confirmation by default
        // It should open print dialog directly on click
        $this->requiresConfirmation(false);
    }

    // ============================================
    // PRINT MODE METHODS
    // ============================================

    /**
     * Print the entire current page.
     */
    public function printPage(): static
    {
        $this->printMode = 'page';

        return $this;
    }

    /**
     * Print a specific element by CSS selector.
     */
    public function printElement(string $selector): static
    {
        $this->printMode = 'element';
        $this->selector = $selector;

        return $this;
    }

    /**
     * Print custom HTML content.
     */
    public function printHtml(string|Closure $html): static
    {
        $this->printMode = 'html';
        $this->htmlContent = $html;

        return $this;
    }

    /**
     * Print a rendered Blade view.
     *
     * @param  array<string, mixed>|Closure|null  $data
     */
    public function printView(string $view, array|Closure|null $data = null): static
    {
        $this->printMode = 'view';
        $this->viewName = $view;
        $this->viewData = $data;

        return $this;
    }

    /**
     * Print content from a URL.
     */
    public function printFromUrl(string|Closure $url): static
    {
        $this->printMode = 'url';
        $this->printUrl = $url;

        return $this;
    }

    /**
     * Alias for printElement.
     */
    public function selector(string $selector): static
    {
        return $this->printElement($selector);
    }

    /**
     * Alias for printHtml.
     */
    public function html(string|Closure $html): static
    {
        return $this->printHtml($html);
    }

    /**
     * Alias for printView.
     *
     * @param  array<string, mixed>|Closure|null  $data
     */
    public function view(string $view, array|Closure|null $data = null): static
    {
        return $this->printView($view, $data);
    }

    // ============================================
    // STYLING METHODS
    // ============================================

    /**
     * Set custom print CSS.
     */
    public function printCss(string|Closure $css): static
    {
        $this->printCss = $css;

        return $this;
    }

    /**
     * Set the page size.
     */
    public function pageSize(string $size): static
    {
        $this->pageSize = $size;

        return $this;
    }

    /**
     * Set page to A4 size.
     */
    public function a4(): static
    {
        $this->pageSize = 'A4';

        return $this;
    }

    /**
     * Set page to Letter size.
     */
    public function letter(): static
    {
        $this->pageSize = 'Letter';

        return $this;
    }

    /**
     * Set page to Legal size.
     */
    public function legal(): static
    {
        $this->pageSize = 'Legal';

        return $this;
    }

    /**
     * Set page orientation.
     */
    public function orientation(string $orientation): static
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * Set portrait orientation.
     */
    public function portrait(): static
    {
        $this->orientation = 'portrait';

        return $this;
    }

    /**
     * Set landscape orientation.
     */
    public function landscape(): static
    {
        $this->orientation = 'landscape';

        return $this;
    }

    /**
     * Set page margins.
     *
     * @param  array{top?: string, right?: string, bottom?: string, left?: string}|string  $margins
     */
    public function margins(array|string $margins): static
    {
        if (is_string($margins)) {
            $this->margins = [
                'top' => $margins,
                'right' => $margins,
                'bottom' => $margins,
                'left' => $margins,
            ];
        } else {
            $this->margins = $margins;
        }

        return $this;
    }

    /**
     * Include headers and footers.
     */
    public function includeHeaders(bool $include = true): static
    {
        $this->includeHeaders = $include;

        return $this;
    }

    /**
     * Include page numbers.
     */
    public function includePageNumbers(bool $include = true): static
    {
        $this->includePageNumbers = $include;

        return $this;
    }

    /**
     * Set custom header HTML.
     */
    public function headerHtml(string $html): static
    {
        $this->headerHtml = $html;
        $this->includeHeaders = true;

        return $this;
    }

    /**
     * Set custom footer HTML.
     */
    public function footerHtml(string $html): static
    {
        $this->footerHtml = $html;
        $this->includeHeaders = true;

        return $this;
    }

    // ============================================
    // BEHAVIOR METHODS
    // ============================================

    /**
     * Set the document title for the print job.
     */
    public function documentTitle(string|Closure $title): static
    {
        $this->documentTitle = $title;

        return $this;
    }

    /**
     * Alias for documentTitle.
     */
    public function title(string|Closure $title): static
    {
        return $this->documentTitle($title);
    }

    /**
     * Set whether to auto-open print dialog.
     */
    public function autoPrint(bool $auto = true): static
    {
        $this->autoPrint = $auto;

        return $this;
    }

    /**
     * Delay before print dialog opens (milliseconds).
     */
    public function printDelay(int $ms): static
    {
        $this->printDelay = $ms;

        return $this;
    }

    /**
     * Hide elements when printing.
     *
     * @param  array<string>|string  $selectors
     */
    public function hideWhenPrinting(array|string $selectors): static
    {
        if (is_string($selectors)) {
            $selectors = [$selectors];
        }

        $this->hideSelectors = array_merge($this->hideSelectors, $selectors);

        return $this;
    }

    /**
     * Show only specific elements when printing.
     *
     * @param  array<string>|string  $selectors
     */
    public function showOnlyWhenPrinting(array|string $selectors): static
    {
        if (is_string($selectors)) {
            $selectors = [$selectors];
        }

        $this->showOnlySelectors = array_merge($this->showOnlySelectors, $selectors);

        return $this;
    }

    /**
     * Remove background colors/images when printing.
     */
    public function removeBackgrounds(bool $remove = true): static
    {
        $this->removeBackgrounds = $remove;

        return $this;
    }

    /**
     * Set whether to print images.
     */
    public function printImages(bool $print = true): static
    {
        $this->printImages = $print;

        return $this;
    }

    /**
     * Set whether to print link URLs.
     */
    public function printLinks(bool $print = true): static
    {
        $this->printLinks = $print;

        return $this;
    }

    // ============================================
    // CALLBACK METHODS
    // ============================================

    /**
     * Callback to run before printing.
     */
    public function beforePrint(?Closure $callback): static
    {
        $this->beforePrint = $callback;

        return $this;
    }

    /**
     * Callback to run after printing.
     */
    public function afterPrint(?Closure $callback): static
    {
        $this->afterPrint = $callback;

        return $this;
    }

    // ============================================
    // GETTER METHODS
    // ============================================

    public function getPrintMode(): string
    {
        return $this->printMode;
    }

    public function getSelector(): ?string
    {
        return $this->selector;
    }

    public function getHtmlContent(mixed $record = null): ?string
    {
        if ($this->htmlContent instanceof Closure) {
            return call_user_func($this->htmlContent, $record);
        }

        return $this->htmlContent;
    }

    public function getViewName(): ?string
    {
        return $this->viewName;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getViewData(mixed $record = null): ?array
    {
        if ($this->viewData instanceof Closure) {
            return call_user_func($this->viewData, $record);
        }

        return $this->viewData;
    }

    public function getPrintUrl(mixed $record = null): ?string
    {
        if ($this->printUrl instanceof Closure) {
            return call_user_func($this->printUrl, $record);
        }

        return $this->printUrl;
    }

    public function getPrintCss(mixed $record = null): ?string
    {
        if ($this->printCss instanceof Closure) {
            return call_user_func($this->printCss, $record);
        }

        return $this->printCss;
    }

    public function getPageSize(): string
    {
        return $this->pageSize;
    }

    public function getOrientation(): string
    {
        return $this->orientation;
    }

    /**
     * @return array{top?: string, right?: string, bottom?: string, left?: string}|null
     */
    public function getMargins(): ?array
    {
        return $this->margins;
    }

    public function shouldIncludeHeaders(): bool
    {
        return $this->includeHeaders;
    }

    public function shouldIncludePageNumbers(): bool
    {
        return $this->includePageNumbers;
    }

    public function getHeaderHtml(): ?string
    {
        return $this->headerHtml;
    }

    public function getFooterHtml(): ?string
    {
        return $this->footerHtml;
    }

    public function getDocumentTitle(mixed $record = null): ?string
    {
        if ($this->documentTitle instanceof Closure) {
            return call_user_func($this->documentTitle, $record);
        }

        return $this->documentTitle;
    }

    public function shouldAutoPrint(): bool
    {
        return $this->autoPrint;
    }

    public function getPrintDelay(): int
    {
        return $this->printDelay;
    }

    /**
     * @return array<string>
     */
    public function getHideSelectors(): array
    {
        return $this->hideSelectors;
    }

    /**
     * @return array<string>
     */
    public function getShowOnlySelectors(): array
    {
        return $this->showOnlySelectors;
    }

    public function shouldRemoveBackgrounds(): bool
    {
        return $this->removeBackgrounds;
    }

    public function shouldPrintImages(): bool
    {
        return $this->printImages;
    }

    public function shouldPrintLinks(): bool
    {
        return $this->printLinks;
    }

    public function getBeforePrintCallback(): ?Closure
    {
        return $this->beforePrint;
    }

    public function getAfterPrintCallback(): ?Closure
    {
        return $this->afterPrint;
    }

    // ============================================
    // GENERATE PRINT CSS
    // ============================================

    /**
     * Generate the combined print CSS.
     */
    public function generatePrintCss(mixed $record = null): string
    {
        $css = "@media print {\n";

        // Page settings
        $css .= "@page {\n";
        $css .= "  size: {$this->pageSize} {$this->orientation};\n";

        if ($this->margins) {
            $margin = implode(' ', array_filter([
                $this->margins['top'] ?? null,
                $this->margins['right'] ?? null,
                $this->margins['bottom'] ?? null,
                $this->margins['left'] ?? null,
            ]));
            $css .= "  margin: {$margin};\n";
        }

        $css .= "}\n";

        // Hide elements
        foreach ($this->hideSelectors as $selector) {
            $css .= "{$selector} { display: none !important; }\n";
        }

        // Remove backgrounds
        if ($this->removeBackgrounds) {
            $css .= "* { background: transparent !important; background-image: none !important; }\n";
        }

        // Hide images
        if (! $this->printImages) {
            $css .= "img { display: none !important; }\n";
        }

        // Print links (show URLs)
        if ($this->printLinks) {
            $css .= "a[href]:after { content: \" (\" attr(href) \")\"; }\n";
        }

        // Custom CSS
        $customCss = $this->getPrintCss($record);
        if ($customCss) {
            $css .= $customCss."\n";
        }

        $css .= "}\n";

        return $css;
    }

    // ============================================
    // EXECUTION
    // ============================================

    /**
     * Execute the print action.
     * Returns configuration for the JavaScript print handler.
     */
    public function execute(mixed $record = null, array $data = []): mixed
    {
        // Run before callback
        if ($this->beforePrint) {
            call_user_func($this->beforePrint, $record, $data);
        }

        $result = [
            'mode' => $this->printMode,
            'selector' => $this->selector,
            'documentTitle' => $this->getDocumentTitle($record),
            'autoPrint' => $this->autoPrint,
            'printDelay' => $this->printDelay,
            'printCss' => $this->generatePrintCss($record),
        ];

        // Add mode-specific data
        if ($this->printMode === 'html') {
            $result['html'] = $this->getHtmlContent($record);
        } elseif ($this->printMode === 'view') {
            $viewData = $this->getViewData($record) ?? [];
            $viewData['record'] = $record;
            $result['html'] = view($this->viewName, $viewData)->render();
        } elseif ($this->printMode === 'url') {
            $result['url'] = $this->getPrintUrl($record);
        }

        // Run after callback (note: this runs before actual print in browser)
        if ($this->afterPrint) {
            call_user_func($this->afterPrint, $record, $data);
        }

        return $result;
    }

    /**
     * Override toArrayWithRecord to include print-specific data.
     */
    public function toArrayWithRecord(mixed $record = null): array
    {
        $data = parent::toArrayWithRecord($record);

        $data['printMode'] = $this->printMode;
        $data['selector'] = $this->selector;
        $data['documentTitle'] = $this->getDocumentTitle($record);
        $data['autoPrint'] = $this->autoPrint;
        $data['printDelay'] = $this->printDelay;
        $data['pageSize'] = $this->pageSize;
        $data['orientation'] = $this->orientation;
        $data['printCss'] = $this->generatePrintCss($record);
        $data['hideSelectors'] = $this->hideSelectors;
        $data['showOnlySelectors'] = $this->showOnlySelectors;
        $data['removeBackgrounds'] = $this->removeBackgrounds;
        $data['printImages'] = $this->printImages;
        $data['printLinks'] = $this->printLinks;

        // Add rendered content for html/view modes
        if ($this->printMode === 'html') {
            $data['htmlContent'] = $this->getHtmlContent($record);
        } elseif ($this->printMode === 'view') {
            $viewData = $this->getViewData($record) ?? [];
            $viewData['record'] = $record;
            $data['htmlContent'] = view($this->viewName, $viewData)->render();
        } elseif ($this->printMode === 'url') {
            $data['printUrl'] = $this->getPrintUrl($record);
        }

        return $data;
    }
}
