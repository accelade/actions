<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ExportAction extends Action
{
    protected ?string $exporterClass = null;

    protected string|Closure|null $fileName = null;

    protected string $defaultFormat = 'xlsx';

    protected array $allowedFormats = ['xlsx', 'csv', 'pdf'];

    protected ?Closure $queryModifier = null;

    protected array $columns = [];

    protected array $headings = [];

    protected string|Closure|null $disk = null;

    protected bool $queue = false;

    protected ?string $jobClass = null;

    protected ?string $jobQueue = null;

    protected ?string $jobConnection = null;

    protected int $chunkSize = 100;

    protected ?int $maxRows = null;

    protected string $csvDelimiter = ',';

    protected string $timeFormat = 'M_d_Y-H_i';

    protected string $pageOrientation = 'portrait';

    protected bool $useSnappy = false;

    protected bool $directDownload = false;

    protected bool $columnMapping = true;

    protected bool $filterColumns = true;

    protected bool $additionalColumns = true;

    protected bool $showFileName = true;

    protected bool $showFileNamePrefix = true;

    protected bool $showPreview = true;

    protected bool $withHiddenColumns = false;

    protected array $extraViewData = [];

    protected ?Closure $modifyExcelWriter = null;

    protected ?Closure $modifyPdfWriter = null;

    protected ?Closure $modifyCsvWriter = null;

    protected array $formatStates = [];

    protected array $xlsxCellStyle = [];

    protected array $xlsxHeaderCellStyle = [];

    protected string|Closure|null $directory = null;

    protected string $visibility = 'private';

    protected function setUp(): void
    {
        parent::setUp();

        $this->name ??= 'export';
        $this->label(__('actions::actions.buttons.export'));
        $this->icon('heroicon-o-arrow-down-tray');
        $this->color('gray');
    }

    /**
     * Set the exporter class to use.
     *
     * @param  class-string  $exporter
     */
    public function exporter(string $exporter): static
    {
        $this->exporterClass = $exporter;

        return $this;
    }

    /**
     * Set the export filename.
     */
    public function fileName(string|Closure $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Set the default export format.
     */
    public function defaultFormat(string $format): static
    {
        $this->defaultFormat = $format;

        return $this;
    }

    /**
     * Alias for defaultFormat.
     */
    public function format(string $format): static
    {
        return $this->defaultFormat($format);
    }

    /**
     * Set allowed export formats.
     */
    public function formats(array $formats): static
    {
        $this->allowedFormats = $formats;

        if (! empty($formats) && ! in_array($this->defaultFormat, $formats, true)) {
            $this->defaultFormat = $formats[0];
        }

        return $this;
    }

    /**
     * Disable PDF export option.
     */
    public function disablePdf(): static
    {
        $this->allowedFormats = array_filter($this->allowedFormats, fn ($f) => $f !== 'pdf');

        return $this;
    }

    /**
     * Disable XLSX export option.
     */
    public function disableXlsx(): static
    {
        $this->allowedFormats = array_filter($this->allowedFormats, fn ($f) => $f !== 'xlsx');

        return $this;
    }

    /**
     * Disable CSV export option.
     */
    public function disableCsv(): static
    {
        $this->allowedFormats = array_filter($this->allowedFormats, fn ($f) => $f !== 'csv');

        return $this;
    }

    /**
     * Export as XLSX.
     */
    public function xlsx(): static
    {
        $this->defaultFormat = 'xlsx';

        return $this;
    }

    /**
     * Export as CSV.
     */
    public function csv(): static
    {
        $this->defaultFormat = 'csv';

        return $this;
    }

    /**
     * Export as PDF.
     */
    public function pdf(): static
    {
        $this->defaultFormat = 'pdf';

        return $this;
    }

    /**
     * Modify the query before exporting.
     */
    public function modifyQueryUsing(?Closure $callback): static
    {
        $this->queryModifier = $callback;

        return $this;
    }

    /**
     * Set the columns to export.
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Alias for columns - add non-visible model columns.
     */
    public function withColumns(array $columns): static
    {
        return $this->columns($columns);
    }

    /**
     * Set the headings for the export.
     */
    public function headings(array $headings): static
    {
        $this->headings = $headings;

        return $this;
    }

    /**
     * Queue the export for background processing.
     */
    public function queue(bool $queue = true): static
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set a custom job class for processing exports.
     */
    public function job(string $jobClass): static
    {
        $this->jobClass = $jobClass;

        return $this;
    }

    /**
     * Set the queue name for the export job.
     */
    public function onQueue(string $queue): static
    {
        $this->jobQueue = $queue;

        return $this;
    }

    /**
     * Set the connection for the export job.
     */
    public function onConnection(string $connection): static
    {
        $this->jobConnection = $connection;

        return $this;
    }

    /**
     * Set the disk to store the export.
     */
    public function disk(string|Closure $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Alias for disk (Filament compatibility).
     */
    public function fileDisk(string|Closure $disk): static
    {
        return $this->disk($disk);
    }

    /**
     * Set the storage directory.
     */
    public function directory(string|Closure $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Set file visibility (public/private).
     */
    public function visibility(string $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Set the chunk size for processing.
     */
    public function chunkSize(int $size): static
    {
        $this->chunkSize = $size;

        return $this;
    }

    /**
     * Set maximum rows to export.
     */
    public function maxRows(int $rows): static
    {
        $this->maxRows = $rows;

        return $this;
    }

    /**
     * Set CSV delimiter.
     */
    public function csvDelimiter(string $delimiter): static
    {
        $this->csvDelimiter = $delimiter;

        return $this;
    }

    /**
     * Set time format for filename prefix.
     */
    public function timeFormat(string $format): static
    {
        $this->timeFormat = $format;

        return $this;
    }

    /**
     * Set default page orientation for PDF.
     */
    public function defaultPageOrientation(string $orientation): static
    {
        $this->pageOrientation = $orientation;

        return $this;
    }

    /**
     * Set page orientation to portrait.
     */
    public function portrait(): static
    {
        $this->pageOrientation = 'portrait';

        return $this;
    }

    /**
     * Set page orientation to landscape.
     */
    public function landscape(): static
    {
        $this->pageOrientation = 'landscape';

        return $this;
    }

    /**
     * Use Snappy instead of DomPDF for PDF generation.
     */
    public function snappy(bool $useSnappy = true): static
    {
        $this->useSnappy = $useSnappy;

        return $this;
    }

    /**
     * Skip modal and download immediately.
     */
    public function directDownload(bool $direct = true): static
    {
        $this->directDownload = $direct;

        return $this;
    }

    /**
     * Enable/disable column mapping interface.
     */
    public function columnMapping(bool $enabled = true): static
    {
        $this->columnMapping = $enabled;

        return $this;
    }

    /**
     * Disable column filter/selection.
     */
    public function disableFilterColumns(): static
    {
        $this->filterColumns = false;

        return $this;
    }

    /**
     * Disable additional columns feature.
     */
    public function disableAdditionalColumns(): static
    {
        $this->additionalColumns = false;

        return $this;
    }

    /**
     * Disable filename input.
     */
    public function disableFileName(): static
    {
        $this->showFileName = false;

        return $this;
    }

    /**
     * Disable filename prefix (timestamp).
     */
    public function disableFileNamePrefix(): static
    {
        $this->showFileNamePrefix = false;

        return $this;
    }

    /**
     * Disable preview functionality.
     */
    public function disablePreview(): static
    {
        $this->showPreview = false;

        return $this;
    }

    /**
     * Include toggled-hidden columns in export.
     */
    public function withHiddenColumns(bool $include = true): static
    {
        $this->withHiddenColumns = $include;

        return $this;
    }

    /**
     * Pass extra data to the export view/template.
     */
    public function extraViewData(array|Closure $data): static
    {
        $this->extraViewData = is_array($data) ? $data : [$data];

        return $this;
    }

    /**
     * Modify Excel writer before export.
     */
    public function modifyExcelWriter(?Closure $callback): static
    {
        $this->modifyExcelWriter = $callback;

        return $this;
    }

    /**
     * Modify PDF writer before export.
     */
    public function modifyPdfWriter(?Closure $callback): static
    {
        $this->modifyPdfWriter = $callback;

        return $this;
    }

    /**
     * Modify CSV writer before export.
     */
    public function modifyCsvWriter(?Closure $callback): static
    {
        $this->modifyCsvWriter = $callback;

        return $this;
    }

    /**
     * Format specific column states.
     */
    public function formatStates(array $formatters): static
    {
        $this->formatStates = $formatters;

        return $this;
    }

    /**
     * Set XLSX cell styling.
     */
    public function xlsxCellStyle(array $style): static
    {
        $this->xlsxCellStyle = $style;

        return $this;
    }

    /**
     * Set XLSX header cell styling.
     */
    public function xlsxHeaderCellStyle(array $style): static
    {
        $this->xlsxHeaderCellStyle = $style;

        return $this;
    }

    // Getters

    public function getExporterClass(): ?string
    {
        return $this->exporterClass;
    }

    public function getDefaultFormat(): string
    {
        return $this->defaultFormat;
    }

    public function getAllowedFormats(): array
    {
        return array_values($this->allowedFormats);
    }

    public function getFileName(mixed $record = null): string
    {
        if ($this->fileName) {
            $name = $this->fileName instanceof Closure
                ? call_user_func($this->fileName, $record)
                : $this->fileName;

            return $name;
        }

        $prefix = $this->showFileNamePrefix
            ? now()->format($this->timeFormat).'_'
            : '';

        return $prefix.'export.'.$this->defaultFormat;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getHeadings(): array
    {
        return $this->headings;
    }

    public function shouldQueue(): bool
    {
        return $this->queue;
    }

    public function getJobClass(): ?string
    {
        return $this->jobClass;
    }

    public function getJobQueue(): ?string
    {
        return $this->jobQueue;
    }

    public function getJobConnection(): ?string
    {
        return $this->jobConnection;
    }

    public function getDisk(mixed $record = null): ?string
    {
        if ($this->disk instanceof Closure) {
            return call_user_func($this->disk, $record);
        }

        return $this->disk;
    }

    public function getDirectory(mixed $record = null): ?string
    {
        if ($this->directory instanceof Closure) {
            return call_user_func($this->directory, $record);
        }

        return $this->directory;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    public function getMaxRows(): ?int
    {
        return $this->maxRows;
    }

    public function getCsvDelimiter(): string
    {
        return $this->csvDelimiter;
    }

    public function getTimeFormat(): string
    {
        return $this->timeFormat;
    }

    public function getPageOrientation(): string
    {
        return $this->pageOrientation;
    }

    public function shouldUseSnappy(): bool
    {
        return $this->useSnappy;
    }

    public function isDirectDownload(): bool
    {
        return $this->directDownload;
    }

    public function hasColumnMapping(): bool
    {
        return $this->columnMapping;
    }

    public function hasFilterColumns(): bool
    {
        return $this->filterColumns;
    }

    public function hasAdditionalColumns(): bool
    {
        return $this->additionalColumns;
    }

    public function showsFileName(): bool
    {
        return $this->showFileName;
    }

    public function showsFileNamePrefix(): bool
    {
        return $this->showFileNamePrefix;
    }

    public function showsPreview(): bool
    {
        return $this->showPreview;
    }

    public function includesHiddenColumns(): bool
    {
        return $this->withHiddenColumns;
    }

    public function getExtraViewData(): array
    {
        return $this->extraViewData;
    }

    public function getExcelWriterModifier(): ?Closure
    {
        return $this->modifyExcelWriter;
    }

    public function getPdfWriterModifier(): ?Closure
    {
        return $this->modifyPdfWriter;
    }

    public function getCsvWriterModifier(): ?Closure
    {
        return $this->modifyCsvWriter;
    }

    public function getFormatStates(): array
    {
        return $this->formatStates;
    }

    public function getXlsxCellStyle(): array
    {
        return $this->xlsxCellStyle;
    }

    public function getXlsxHeaderCellStyle(): array
    {
        return $this->xlsxHeaderCellStyle;
    }

    public function getQueryModifier(): ?Closure
    {
        return $this->queryModifier;
    }

    /**
     * Get the data to export.
     */
    public function getExportData(mixed $data = null): Collection
    {
        if ($data instanceof Collection) {
            return $data;
        }

        if ($data instanceof Builder) {
            $query = $data;

            if ($this->queryModifier) {
                $query = call_user_func($this->queryModifier, $query);
            }

            if ($this->maxRows) {
                $query = $query->limit($this->maxRows);
            }

            return $query->get();
        }

        return collect([$data])->filter();
    }

    /**
     * Format a record value using formatStates.
     */
    public function formatValue(string $column, mixed $record): mixed
    {
        if (isset($this->formatStates[$column])) {
            return call_user_func($this->formatStates[$column], $record);
        }

        if ($record instanceof Model) {
            return data_get($record, $column);
        }

        if (is_array($record) || $record instanceof \ArrayAccess) {
            return $record[$column] ?? null;
        }

        return $record->{$column} ?? null;
    }

    /**
     * Execute the export action.
     */
    public function execute(mixed $record = null, array $data = []): mixed
    {
        $format = $data['format'] ?? $this->defaultFormat;
        $fileName = $data['fileName'] ?? $this->getFileName($record);
        $selectedColumns = $data['columns'] ?? $this->columns;

        // Ensure filename has correct extension
        if (! Str::endsWith($fileName, '.'.$format)) {
            $fileName = Str::beforeLast($fileName, '.').'.'.$format;
        }

        return [
            'exporter' => $this->exporterClass,
            'fileName' => $fileName,
            'format' => $format,
            'columns' => $selectedColumns,
            'headings' => $this->headings,
            'queue' => $this->queue,
            'jobClass' => $this->jobClass,
            'jobQueue' => $this->jobQueue,
            'jobConnection' => $this->jobConnection,
            'disk' => $this->getDisk($record),
            'directory' => $this->getDirectory($record),
            'visibility' => $this->visibility,
            'chunkSize' => $this->chunkSize,
            'maxRows' => $this->maxRows,
            'csvDelimiter' => $this->csvDelimiter,
            'pageOrientation' => $this->pageOrientation,
            'useSnappy' => $this->useSnappy,
            'extraViewData' => $this->extraViewData,
            'formatStates' => $this->formatStates,
            'xlsxCellStyle' => $this->xlsxCellStyle,
            'xlsxHeaderCellStyle' => $this->xlsxHeaderCellStyle,
        ];
    }

    /**
     * Override toArrayWithRecord to include export-specific data.
     */
    public function toArrayWithRecord(mixed $record = null): array
    {
        $data = parent::toArrayWithRecord($record);

        $data['exporterClass'] = $this->exporterClass;
        $data['defaultFormat'] = $this->defaultFormat;
        $data['allowedFormats'] = $this->getAllowedFormats();
        $data['directDownload'] = $this->directDownload;
        $data['columnMapping'] = $this->columnMapping;
        $data['filterColumns'] = $this->filterColumns;
        $data['additionalColumns'] = $this->additionalColumns;
        $data['showFileName'] = $this->showFileName;
        $data['showPreview'] = $this->showPreview;
        $data['columns'] = $this->columns;
        $data['headings'] = $this->headings;
        $data['pageOrientation'] = $this->pageOrientation;

        return $data;
    }
}
