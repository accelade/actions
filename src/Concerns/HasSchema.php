<?php

declare(strict_types=1);

namespace Accelade\Actions\Concerns;

use Accelade\Forms\Field;
use Closure;

trait HasSchema
{
    /**
     * @var array<Field>|Closure|null
     */
    protected array|Closure|null $schema = null;

    /**
     * @var array<string, mixed>
     */
    protected array $schemaDefaults = [];

    /**
     * Set the form schema for the action modal.
     *
     * @param  array<Field>|Closure  $schema
     */
    public function schema(array|Closure $schema): static
    {
        $this->schema = $schema;

        // Enable modal if schema is set
        $this->modal(true);

        return $this;
    }

    /**
     * Alias for schema() - Filament compatibility.
     *
     * @param  array<Field>|Closure  $schema
     */
    public function form(array|Closure $schema): static
    {
        return $this->schema($schema);
    }

    /**
     * Set default values for schema fields.
     *
     * @param  array<string, mixed>  $defaults
     */
    public function schemaDefaults(array $defaults): static
    {
        $this->schemaDefaults = $defaults;

        return $this;
    }

    /**
     * Alias for schemaDefaults() - Filament compatibility.
     *
     * @param  array<string, mixed>  $data
     */
    public function fillForm(array $data): static
    {
        return $this->schemaDefaults($data);
    }

    /**
     * Check if action has a schema.
     */
    public function hasSchema(): bool
    {
        return $this->schema !== null;
    }

    /**
     * Get the schema fields.
     *
     * @return array<Field>
     */
    public function getSchema(mixed $record = null): array
    {
        if ($this->schema === null) {
            return [];
        }

        if ($this->schema instanceof Closure) {
            return call_user_func($this->schema, $record) ?? [];
        }

        return $this->schema;
    }

    /**
     * Get schema defaults.
     *
     * @return array<string, mixed>
     */
    public function getSchemaDefaults(mixed $record = null): array
    {
        $defaults = [];

        // Get defaults from schema fields (with record context for closures)
        foreach ($this->getSchema($record) as $field) {
            if ($field instanceof Field) {
                $fieldName = $field->getName();
                $default = $field->getDefaultWithRecord($record);
                if ($default !== null) {
                    $defaults[$fieldName] = $default;
                }
            }
        }

        // Merge with explicit schemaDefaults (explicit wins)
        return array_merge($defaults, $this->schemaDefaults);
    }

    /**
     * Get the validation rules from the schema.
     *
     * @return array<string, array<string>>
     */
    public function getSchemaRules(mixed $record = null): array
    {
        $rules = [];

        foreach ($this->getSchema($record) as $field) {
            if ($field instanceof Field) {
                $fieldRules = $field->getRules();
                if (! empty($fieldRules)) {
                    $rules[$field->getName()] = $fieldRules;
                }
            }
        }

        return $rules;
    }

    /**
     * Serialize schema to array for JavaScript.
     *
     * @return array<array<string, mixed>>
     */
    public function getSchemaArray(mixed $record = null): array
    {
        $schema = [];

        foreach ($this->getSchema($record) as $field) {
            if ($field instanceof Field) {
                // Use toArrayWithRecord to properly evaluate closure defaults
                $fieldData = $field->toArrayWithRecord($record);
                // Add default value from schemaDefaults if set (explicit wins)
                $fieldName = $field->getName();
                if (isset($this->schemaDefaults[$fieldName])) {
                    $fieldData['default'] = $this->schemaDefaults[$fieldName];
                }
                $schema[] = $fieldData;
            }
        }

        return $schema;
    }

    /**
     * Render schema fields to HTML.
     */
    public function renderSchema(mixed $record = null): string
    {
        $html = '';

        // Get all defaults including record data (for EditAction/ViewAction)
        $allDefaults = $this->getSchemaDefaults($record);

        foreach ($this->getSchema($record) as $field) {
            if ($field instanceof Field) {
                // Set default value if available
                $fieldName = $field->getName();
                if (isset($allDefaults[$fieldName]) && method_exists($field, 'default')) {
                    $field->default($allDefaults[$fieldName]);
                }

                $html .= $field->render();
            }
        }

        return $html;
    }
}
