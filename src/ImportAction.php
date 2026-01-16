<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Closure;

/**
 * ImportAction - Pre-configured action for importing data from files.
 *
 * Supports CSV, Excel (xlsx, xls), and PlainText formats.
 * PlainText imports by splitting rows with \n and columns with , (configurable delimiters).
 *
 * Compatible with Laravel Excel for spreadsheet imports and provides
 * a flexible API for custom import processing.
 */
class ImportAction extends Action
{
    // ============================================
    // IMPORTER CONFIGURATION
    // ============================================

    protected ?string $importerClass = null;

    /** @var class-string|null */
    protected ?string $modelClass = null;

    // ============================================
    // FORMAT CONFIGURATION
    // ============================================

    protected string $defaultFormat = 'xlsx';

    /** @var array<string> */
    protected array $formats = ['xlsx', 'csv', 'txt'];

    protected bool $csvEnabled = true;

    protected bool $xlsxEnabled = true;

    protected bool $txtEnabled = true;

    // ============================================
    // PLAIN TEXT CONFIGURATION
    // ============================================

    protected string $plainTextRowDelimiter = "\n";

    protected string $plainTextColumnDelimiter = ',';

    protected bool $plainTextTrimValues = true;

    protected bool $plainTextSkipEmptyRows = true;

    // ============================================
    // CSV CONFIGURATION
    // ============================================

    protected string $csvDelimiter = ',';

    protected string $csvEnclosure = '"';

    protected string $csvEscape = '\\';

    // ============================================
    // FILE CONFIGURATION
    // ============================================

    protected ?string $disk = null;

    protected ?string $directory = null;

    protected ?int $maxSize = null; // In KB

    /** @var array<string> */
    protected array $acceptedFileTypes = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
        'text/plain',
        '.xlsx',
        '.xls',
        '.csv',
        '.txt',
    ];

    // ============================================
    // HEADER CONFIGURATION
    // ============================================

    protected bool $hasHeaderRow = true;

    protected int $headerRowNumber = 1;

    protected int $startRow = 2;

    // ============================================
    // COLUMN MAPPING
    // ============================================

    /** @var array<string, string|int>|Closure|null Column mapping [db_column => file_column] */
    protected array|Closure|null $columnMapping = null;

    /** @var array<string>|Closure|null Required columns */
    protected array|Closure|null $requiredColumns = null;

    /** @var array<string, string>|null Column labels for UI */
    protected ?array $columnLabels = null;

    // ============================================
    // DUPLICATE HANDLING
    // ============================================

    protected string $onDuplicate = 'skip'; // 'skip', 'update', 'error'

    /** @var string|array<string>|null */
    protected string|array|null $uniqueColumn = null;

    // ============================================
    // VALIDATION & TRANSFORMATION
    // ============================================

    /** @var Closure|null Validate each row */
    protected ?Closure $validateRow = null;

    /** @var array<string, string>|null Laravel validation rules */
    protected ?array $validationRules = null;

    /** @var array<string, string>|null Custom validation messages */
    protected ?array $validationMessages = null;

    /** @var Closure|null Transform each row before processing */
    protected ?Closure $transformRow = null;

    /** @var Closure|null Create record from row */
    protected ?Closure $createRecord = null;

    /** @var Closure|null Update record from row */
    protected ?Closure $updateRecord = null;

    // ============================================
    // QUEUE CONFIGURATION
    // ============================================

    protected bool $queue = false;

    /** @var class-string|null */
    protected ?string $jobClass = null;

    protected ?string $queueName = null;

    protected ?string $queueConnection = null;

    protected int $queueBatchSize = 1000;

    // ============================================
    // CHUNK CONFIGURATION
    // ============================================

    protected ?int $chunkSize = null;

    // ============================================
    // EXAMPLE DATA CONFIGURATION
    // ============================================

    /** @var array<array<string, mixed>>|Closure|null */
    protected array|Closure|null $exampleData = null;

    protected ?string $exampleFileName = null;

    protected bool $downloadableExample = true;

    // ============================================
    // CALLBACKS
    // ============================================

    protected ?Closure $beforeImport = null;

    protected ?Closure $afterImport = null;

    protected ?Closure $onError = null;

    protected ?Closure $onProgress = null;

    // ============================================
    // SETUP
    // ============================================

    protected function setUp(): void
    {
        parent::setUp();

        $this->name ??= 'import';
        $this->label(__('actions::actions.buttons.import'));
        $this->icon('heroicon-o-arrow-up-tray');
        $this->color('gray');
        $this->requiresConfirmation();
        $this->modalHeading(__('actions::actions.modal.import_title'));
        $this->modalDescription(__('actions::actions.modal.import_description'));
    }

    // ============================================
    // IMPORTER CONFIGURATION METHODS
    // ============================================

    /**
     * Set the importer class to use (Laravel Excel compatible).
     *
     * @param  class-string  $importer
     */
    public function importer(string $importer): static
    {
        $this->importerClass = $importer;

        return $this;
    }

    /**
     * Set the model class for creating/updating records.
     *
     * @param  class-string  $model
     */
    public function model(string $model): static
    {
        $this->modelClass = $model;

        return $this;
    }

    // ============================================
    // FORMAT METHODS
    // ============================================

    /**
     * Set the default import format.
     */
    public function defaultFormat(string $format): static
    {
        $this->defaultFormat = $format;

        return $this;
    }

    /**
     * Set available formats for import.
     *
     * @param  array<string>  $formats
     */
    public function formats(array $formats): static
    {
        $this->formats = $formats;

        return $this;
    }

    /**
     * Shorthand for XLSX format.
     */
    public function xlsx(): static
    {
        $this->defaultFormat = 'xlsx';

        return $this;
    }

    /**
     * Shorthand for CSV format.
     */
    public function csv(): static
    {
        $this->defaultFormat = 'csv';

        return $this;
    }

    /**
     * Shorthand for plain text format.
     */
    public function plainText(): static
    {
        $this->defaultFormat = 'txt';

        return $this;
    }

    /**
     * Disable CSV format.
     */
    public function disableCsv(bool $condition = true): static
    {
        $this->csvEnabled = ! $condition;

        return $this;
    }

    /**
     * Disable XLSX format.
     */
    public function disableXlsx(bool $condition = true): static
    {
        $this->xlsxEnabled = ! $condition;

        return $this;
    }

    /**
     * Disable plain text format.
     */
    public function disablePlainText(bool $condition = true): static
    {
        $this->txtEnabled = ! $condition;

        return $this;
    }

    // ============================================
    // PLAIN TEXT CONFIGURATION METHODS
    // ============================================

    /**
     * Set the row delimiter for plain text imports (default: \n).
     */
    public function plainTextRowDelimiter(string $delimiter): static
    {
        $this->plainTextRowDelimiter = $delimiter;

        return $this;
    }

    /**
     * Set the column delimiter for plain text imports (default: ,).
     */
    public function plainTextColumnDelimiter(string $delimiter): static
    {
        $this->plainTextColumnDelimiter = $delimiter;

        return $this;
    }

    /**
     * Set whether to trim values in plain text imports.
     */
    public function plainTextTrimValues(bool $trim = true): static
    {
        $this->plainTextTrimValues = $trim;

        return $this;
    }

    /**
     * Set whether to skip empty rows in plain text imports.
     */
    public function plainTextSkipEmptyRows(bool $skip = true): static
    {
        $this->plainTextSkipEmptyRows = $skip;

        return $this;
    }

    // ============================================
    // CSV CONFIGURATION METHODS
    // ============================================

    /**
     * Set the CSV delimiter.
     */
    public function csvDelimiter(string $delimiter): static
    {
        $this->csvDelimiter = $delimiter;

        return $this;
    }

    /**
     * Set the CSV enclosure character.
     */
    public function csvEnclosure(string $enclosure): static
    {
        $this->csvEnclosure = $enclosure;

        return $this;
    }

    /**
     * Set the CSV escape character.
     */
    public function csvEscape(string $escape): static
    {
        $this->csvEscape = $escape;

        return $this;
    }

    // ============================================
    // FILE CONFIGURATION METHODS
    // ============================================

    /**
     * Set the disk to store/read files from.
     */
    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Set the directory for file storage.
     */
    public function directory(string $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Set the maximum file size in KB.
     */
    public function maxSize(int $kb): static
    {
        $this->maxSize = $kb;

        return $this;
    }

    /**
     * Set accepted file types for upload.
     *
     * @param  array<string>  $types
     */
    public function acceptedFileTypes(array $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    // ============================================
    // HEADER CONFIGURATION METHODS
    // ============================================

    /**
     * Set whether the file has a header row.
     */
    public function withHeaderRow(bool $hasHeader = true): static
    {
        $this->hasHeaderRow = $hasHeader;

        return $this;
    }

    /**
     * Set the header row number (1-indexed).
     */
    public function headerRowNumber(int $row): static
    {
        $this->headerRowNumber = $row;
        $this->startRow = $row + 1;

        return $this;
    }

    /**
     * Set the starting row for data (1-indexed).
     */
    public function startRow(int $row): static
    {
        $this->startRow = $row;

        return $this;
    }

    // ============================================
    // COLUMN MAPPING METHODS
    // ============================================

    /**
     * Set column mapping from file columns to database columns.
     *
     * @param  array<string, string|int>|Closure  $mapping  [db_column => file_column_name_or_index]
     */
    public function columnMapping(array|Closure $mapping): static
    {
        $this->columnMapping = $mapping;

        return $this;
    }

    /**
     * Set required columns that must exist in the file.
     *
     * @param  array<string>|Closure  $columns
     */
    public function requiredColumns(array|Closure $columns): static
    {
        $this->requiredColumns = $columns;

        return $this;
    }

    /**
     * Set column labels for the UI.
     *
     * @param  array<string, string>  $labels
     */
    public function columnLabels(array $labels): static
    {
        $this->columnLabels = $labels;

        return $this;
    }

    // ============================================
    // DUPLICATE HANDLING METHODS
    // ============================================

    /**
     * Set how to handle duplicate records.
     *
     * @param  string  $action  'skip', 'update', or 'error'
     */
    public function onDuplicate(string $action): static
    {
        $this->onDuplicate = $action;

        return $this;
    }

    /**
     * Set the unique column(s) for duplicate detection.
     *
     * @param  string|array<string>  $column
     */
    public function uniqueColumn(string|array $column): static
    {
        $this->uniqueColumn = $column;

        return $this;
    }

    /**
     * Skip duplicate records.
     */
    public function skipDuplicates(): static
    {
        $this->onDuplicate = 'skip';

        return $this;
    }

    /**
     * Update duplicate records.
     */
    public function updateDuplicates(): static
    {
        $this->onDuplicate = 'update';

        return $this;
    }

    /**
     * Throw error on duplicate records.
     */
    public function errorOnDuplicates(): static
    {
        $this->onDuplicate = 'error';

        return $this;
    }

    // ============================================
    // VALIDATION & TRANSFORMATION METHODS
    // ============================================

    /**
     * Set a callback to validate each row.
     * Return true/false or throw ValidationException.
     */
    public function validateRow(?Closure $callback): static
    {
        $this->validateRow = $callback;

        return $this;
    }

    /**
     * Set Laravel validation rules for each row.
     *
     * @param  array<string, string>  $rules
     */
    public function validationRules(array $rules): static
    {
        $this->validationRules = $rules;

        return $this;
    }

    /**
     * Set custom validation messages.
     *
     * @param  array<string, string>  $messages
     */
    public function validationMessages(array $messages): static
    {
        $this->validationMessages = $messages;

        return $this;
    }

    /**
     * Set a callback to transform each row before processing.
     */
    public function transformRow(?Closure $callback): static
    {
        $this->transformRow = $callback;

        return $this;
    }

    /**
     * Set a callback to create a record from a row.
     */
    public function createRecord(?Closure $callback): static
    {
        $this->createRecord = $callback;

        return $this;
    }

    /**
     * Set a callback to update a record from a row.
     */
    public function updateRecord(?Closure $callback): static
    {
        $this->updateRecord = $callback;

        return $this;
    }

    // ============================================
    // QUEUE CONFIGURATION METHODS
    // ============================================

    /**
     * Queue the import for background processing.
     */
    public function queue(bool $queue = true): static
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set the job class for queued imports.
     *
     * @param  class-string  $job
     */
    public function job(string $job): static
    {
        $this->jobClass = $job;
        $this->queue = true;

        return $this;
    }

    /**
     * Set the queue name.
     */
    public function onQueue(string $queue): static
    {
        $this->queueName = $queue;
        $this->queue = true;

        return $this;
    }

    /**
     * Set the queue connection.
     */
    public function onConnection(string $connection): static
    {
        $this->queueConnection = $connection;
        $this->queue = true;

        return $this;
    }

    /**
     * Set the batch size for queued imports.
     */
    public function queueBatchSize(int $size): static
    {
        $this->queueBatchSize = $size;

        return $this;
    }

    // ============================================
    // CHUNK CONFIGURATION METHODS
    // ============================================

    /**
     * Set the chunk size for importing.
     */
    public function chunkSize(int $size): static
    {
        $this->chunkSize = $size;

        return $this;
    }

    // ============================================
    // EXAMPLE DATA METHODS
    // ============================================

    /**
     * Set example data for the template download.
     *
     * @param  array<array<string, mixed>>|Closure  $data
     */
    public function exampleData(array|Closure $data): static
    {
        $this->exampleData = $data;

        return $this;
    }

    /**
     * Set the example file name.
     */
    public function exampleFileName(string $fileName): static
    {
        $this->exampleFileName = $fileName;

        return $this;
    }

    /**
     * Enable/disable downloadable example template.
     */
    public function downloadableExample(bool $downloadable = true): static
    {
        $this->downloadableExample = $downloadable;

        return $this;
    }

    // ============================================
    // CALLBACK METHODS
    // ============================================

    /**
     * Callback to run before importing.
     */
    public function beforeImport(?Closure $callback): static
    {
        $this->beforeImport = $callback;

        return $this;
    }

    /**
     * Callback to run after importing.
     */
    public function afterImport(?Closure $callback): static
    {
        $this->afterImport = $callback;

        return $this;
    }

    /**
     * Callback to run on error.
     */
    public function onError(?Closure $callback): static
    {
        $this->onError = $callback;

        return $this;
    }

    /**
     * Callback to run on progress (for queued imports).
     */
    public function onProgress(?Closure $callback): static
    {
        $this->onProgress = $callback;

        return $this;
    }

    // ============================================
    // GETTER METHODS
    // ============================================

    public function getImporterClass(): ?string
    {
        return $this->importerClass;
    }

    public function getModelClass(): ?string
    {
        return $this->modelClass;
    }

    public function getDefaultFormat(): string
    {
        return $this->defaultFormat;
    }

    /**
     * @return array<string>
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * @return array<string>
     */
    public function getEnabledFormats(): array
    {
        $formats = [];

        if ($this->xlsxEnabled && in_array('xlsx', $this->formats, true)) {
            $formats[] = 'xlsx';
        }

        if ($this->csvEnabled && in_array('csv', $this->formats, true)) {
            $formats[] = 'csv';
        }

        if ($this->txtEnabled && in_array('txt', $this->formats, true)) {
            $formats[] = 'txt';
        }

        return $formats;
    }

    public function getPlainTextRowDelimiter(): string
    {
        return $this->plainTextRowDelimiter;
    }

    public function getPlainTextColumnDelimiter(): string
    {
        return $this->plainTextColumnDelimiter;
    }

    public function shouldTrimPlainTextValues(): bool
    {
        return $this->plainTextTrimValues;
    }

    public function shouldSkipEmptyPlainTextRows(): bool
    {
        return $this->plainTextSkipEmptyRows;
    }

    public function getCsvDelimiter(): string
    {
        return $this->csvDelimiter;
    }

    public function getCsvEnclosure(): string
    {
        return $this->csvEnclosure;
    }

    public function getCsvEscape(): string
    {
        return $this->csvEscape;
    }

    /**
     * @return array<string>
     */
    public function getAcceptedFileTypes(): array
    {
        return $this->acceptedFileTypes;
    }

    public function getDisk(): ?string
    {
        return $this->disk;
    }

    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    public function getMaxSize(): ?int
    {
        return $this->maxSize;
    }

    public function hasHeaderRow(): bool
    {
        return $this->hasHeaderRow;
    }

    public function getHeaderRowNumber(): int
    {
        return $this->headerRowNumber;
    }

    public function getStartRow(): int
    {
        return $this->startRow;
    }

    /**
     * @return array<string, string|int>|null
     */
    public function getColumnMapping(): ?array
    {
        if ($this->columnMapping instanceof Closure) {
            return call_user_func($this->columnMapping);
        }

        return $this->columnMapping;
    }

    /**
     * @return array<string>|null
     */
    public function getRequiredColumns(): ?array
    {
        if ($this->requiredColumns instanceof Closure) {
            return call_user_func($this->requiredColumns);
        }

        return $this->requiredColumns;
    }

    /**
     * @return array<string, string>|null
     */
    public function getColumnLabels(): ?array
    {
        return $this->columnLabels;
    }

    public function getOnDuplicate(): string
    {
        return $this->onDuplicate;
    }

    /**
     * @return string|array<string>|null
     */
    public function getUniqueColumn(): string|array|null
    {
        return $this->uniqueColumn;
    }

    public function getValidateRowCallback(): ?Closure
    {
        return $this->validateRow;
    }

    /**
     * @return array<string, string>|null
     */
    public function getValidationRules(): ?array
    {
        return $this->validationRules;
    }

    /**
     * @return array<string, string>|null
     */
    public function getValidationMessages(): ?array
    {
        return $this->validationMessages;
    }

    public function getTransformRowCallback(): ?Closure
    {
        return $this->transformRow;
    }

    public function getCreateRecordCallback(): ?Closure
    {
        return $this->createRecord;
    }

    public function getUpdateRecordCallback(): ?Closure
    {
        return $this->updateRecord;
    }

    public function shouldQueue(): bool
    {
        return $this->queue;
    }

    public function getJobClass(): ?string
    {
        return $this->jobClass;
    }

    public function getQueueName(): ?string
    {
        return $this->queueName;
    }

    public function getQueueConnection(): ?string
    {
        return $this->queueConnection;
    }

    public function getQueueBatchSize(): int
    {
        return $this->queueBatchSize;
    }

    public function getChunkSize(): ?int
    {
        return $this->chunkSize;
    }

    /**
     * @return array<array<string, mixed>>|null
     */
    public function getExampleData(): ?array
    {
        if ($this->exampleData instanceof Closure) {
            return call_user_func($this->exampleData);
        }

        return $this->exampleData;
    }

    public function getExampleFileName(): ?string
    {
        return $this->exampleFileName;
    }

    public function hasDownloadableExample(): bool
    {
        return $this->downloadableExample && $this->exampleData !== null;
    }

    public function getBeforeImportCallback(): ?Closure
    {
        return $this->beforeImport;
    }

    public function getAfterImportCallback(): ?Closure
    {
        return $this->afterImport;
    }

    public function getOnErrorCallback(): ?Closure
    {
        return $this->onError;
    }

    public function getOnProgressCallback(): ?Closure
    {
        return $this->onProgress;
    }

    // ============================================
    // PLAIN TEXT PARSING
    // ============================================

    /**
     * Parse plain text content into rows and columns.
     *
     * @return array<int, array<int|string, string>>
     */
    public function parsePlainText(string $content): array
    {
        $rows = [];
        $headers = [];

        // Split by row delimiter
        $lines = explode($this->plainTextRowDelimiter, $content);

        foreach ($lines as $index => $line) {
            // Skip empty rows if configured
            if ($this->plainTextSkipEmptyRows && trim($line) === '') {
                continue;
            }

            // Split by column delimiter
            $columns = explode($this->plainTextColumnDelimiter, $line);

            // Trim values if configured
            if ($this->plainTextTrimValues) {
                $columns = array_map('trim', $columns);
            }

            // First row as headers if configured
            if ($this->hasHeaderRow && empty($headers)) {
                $headers = $columns;

                continue;
            }

            // Create associative array if we have headers
            if (! empty($headers)) {
                $row = [];
                foreach ($headers as $headerIndex => $header) {
                    $row[$header] = $columns[$headerIndex] ?? '';
                }
                $rows[] = $row;
            } else {
                $rows[] = $columns;
            }
        }

        return $rows;
    }

    // ============================================
    // EXECUTION
    // ============================================

    /**
     * Execute the import action.
     */
    public function execute(mixed $record = null, array $data = []): mixed
    {
        $file = $data['file'] ?? null;

        if (! $file) {
            throw new \InvalidArgumentException('No file provided for import.');
        }

        // Run before callback
        if ($this->beforeImport) {
            call_user_func($this->beforeImport, $file, $data);
        }

        // Determine format from file extension or data
        $format = $data['format'] ?? $this->defaultFormat;

        // Build result
        $result = [
            'importer' => $this->importerClass,
            'model' => $this->modelClass,
            'file' => $file,
            'format' => $format,
            'queue' => $this->queue,
            'disk' => $this->disk,
            'directory' => $this->directory,
            'chunkSize' => $this->chunkSize,
            'hasHeaderRow' => $this->hasHeaderRow,
            'headerRowNumber' => $this->headerRowNumber,
            'startRow' => $this->startRow,
            'columnMapping' => $this->getColumnMapping(),
            'onDuplicate' => $this->onDuplicate,
            'uniqueColumn' => $this->uniqueColumn,
            'validationRules' => $this->validationRules,
        ];

        // Run after callback
        if ($this->afterImport) {
            call_user_func($this->afterImport, $file, $data, $result);
        }

        return $result;
    }

    /**
     * Override toArrayWithRecord to include import-specific data.
     */
    public function toArrayWithRecord(mixed $record = null): array
    {
        $data = parent::toArrayWithRecord($record);

        $data['acceptedFileTypes'] = $this->acceptedFileTypes;
        $data['importerClass'] = $this->importerClass;
        $data['modelClass'] = $this->modelClass;
        $data['defaultFormat'] = $this->defaultFormat;
        $data['enabledFormats'] = $this->getEnabledFormats();
        $data['hasHeaderRow'] = $this->hasHeaderRow;
        $data['maxSize'] = $this->maxSize;
        $data['hasDownloadableExample'] = $this->hasDownloadableExample();
        $data['exampleFileName'] = $this->exampleFileName;
        $data['onDuplicate'] = $this->onDuplicate;
        $data['plainTextRowDelimiter'] = $this->plainTextRowDelimiter;
        $data['plainTextColumnDelimiter'] = $this->plainTextColumnDelimiter;

        return $data;
    }
}
