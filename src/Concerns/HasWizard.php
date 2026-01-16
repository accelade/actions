<?php

declare(strict_types=1);

namespace Accelade\Actions\Concerns;

use Closure;

trait HasWizard
{
    /**
     * @var array<array<mixed>>|Closure|null
     */
    protected array|Closure|null $wizardSteps = null;

    protected bool $skippableWizardSteps = false;

    protected ?int $startWizardStep = null;

    /**
     * Define wizard steps for multi-step forms.
     * Each step should have a 'label', optional 'description', 'icon', and 'schema' (form fields).
     *
     * @param  array<array<mixed>>|Closure  $steps
     */
    public function steps(array|Closure $steps): static
    {
        $this->wizardSteps = $steps;

        // Enable modal if steps are set
        if (method_exists($this, 'modal')) {
            $this->modal(true);
        }

        return $this;
    }

    /**
     * Allow users to skip wizard steps.
     */
    public function skippableSteps(bool $condition = true): static
    {
        $this->skippableWizardSteps = $condition;

        return $this;
    }

    /**
     * Set the starting step (1-indexed).
     */
    public function startOnStep(int $step): static
    {
        $this->startWizardStep = $step;

        return $this;
    }

    /**
     * Check if this action has wizard steps.
     */
    public function hasWizardSteps(): bool
    {
        return $this->wizardSteps !== null;
    }

    /**
     * Get the wizard steps.
     *
     * @return array<array<mixed>>
     */
    public function getWizardSteps(mixed $record = null): array
    {
        if ($this->wizardSteps === null) {
            return [];
        }

        if ($this->wizardSteps instanceof Closure) {
            return call_user_func($this->wizardSteps, $record) ?? [];
        }

        return $this->wizardSteps;
    }

    /**
     * Check if wizard steps can be skipped.
     */
    public function areWizardStepsSkippable(): bool
    {
        return $this->skippableWizardSteps;
    }

    /**
     * Get the starting step number.
     */
    public function getStartWizardStep(): ?int
    {
        return $this->startWizardStep;
    }

    /**
     * Serialize wizard steps to array for JavaScript.
     *
     * @return array<array<string, mixed>>
     */
    public function getWizardStepsArray(mixed $record = null): array
    {
        $steps = [];

        foreach ($this->getWizardSteps($record) as $index => $step) {
            $stepData = [
                'index' => $index,
                'label' => $step['label'] ?? null,
                'description' => $step['description'] ?? null,
                'icon' => $step['icon'] ?? null,
            ];

            // Process schema fields if present
            if (isset($step['schema']) && is_array($step['schema'])) {
                $schemaFields = [];
                foreach ($step['schema'] as $field) {
                    if (method_exists($field, 'toArray')) {
                        $schemaFields[] = $field->toArray();
                    }
                }
                $stepData['schema'] = $schemaFields;
            }

            $steps[] = $stepData;
        }

        return $steps;
    }
}
