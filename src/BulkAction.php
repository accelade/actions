<?php

declare(strict_types=1);

namespace Accelade\Actions;

use Illuminate\Support\Collection;

class BulkAction extends Action
{
    protected array $selectedRecords = [];

    protected bool $deselectRecordsAfterCompletion = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Set default modal properties for bulk actions
        $this->requiresConfirmation();
    }

    /**
     * Set whether to deselect records after action completion.
     */
    public function deselectRecordsAfterCompletion(bool $condition = true): static
    {
        $this->deselectRecordsAfterCompletion = $condition;

        return $this;
    }

    /**
     * Get whether to deselect records after completion.
     */
    public function shouldDeselectRecordsAfterCompletion(): bool
    {
        return $this->deselectRecordsAfterCompletion;
    }

    /**
     * Execute the bulk action with selected records.
     */
    public function executeForRecords(array|Collection $records, array $data = []): mixed
    {
        $this->selectedRecords = $records instanceof Collection ? $records->all() : $records;

        if ($this->action === null) {
            return null;
        }

        // Execute the action closure with records
        return call_user_func($this->action, $this->selectedRecords, $data);
    }

    /**
     * Get the selected records.
     */
    public function getSelectedRecords(): array
    {
        return $this->selectedRecords;
    }

    /**
     * Check if this is a bulk action.
     */
    public function isBulkAction(): bool
    {
        return true;
    }

    /**
     * Override toArrayWithRecord to include bulk-specific properties.
     */
    public function toArrayWithRecord(mixed $record = null): array
    {
        $data = parent::toArrayWithRecord($record);

        $data['isBulkAction'] = true;
        $data['deselectRecordsAfterCompletion'] = $this->deselectRecordsAfterCompletion;

        return $data;
    }
}
