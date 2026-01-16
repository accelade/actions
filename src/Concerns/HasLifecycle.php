<?php

declare(strict_types=1);

namespace Accelade\Actions\Concerns;

use Closure;

trait HasLifecycle
{
    protected ?Closure $before = null;

    protected ?Closure $after = null;

    protected ?Closure $beforeFormFilled = null;

    protected ?Closure $afterFormFilled = null;

    protected ?Closure $beforeFormValidated = null;

    protected ?Closure $afterFormValidated = null;

    /**
     * Register a callback to run before the action executes.
     * The callback receives: function(array $data, $record = null)
     */
    public function before(?Closure $callback): static
    {
        $this->before = $callback;

        return $this;
    }

    /**
     * Register a callback to run after the action executes.
     * The callback receives: function(array $data, $record = null, mixed $result = null)
     */
    public function after(?Closure $callback): static
    {
        $this->after = $callback;

        return $this;
    }

    /**
     * Register a callback to run before the form is filled with data.
     * The callback receives: function(array $data)
     */
    public function beforeFormFilled(?Closure $callback): static
    {
        $this->beforeFormFilled = $callback;

        return $this;
    }

    /**
     * Register a callback to run after the form is filled with data.
     * The callback receives: function(array $data)
     */
    public function afterFormFilled(?Closure $callback): static
    {
        $this->afterFormFilled = $callback;

        return $this;
    }

    /**
     * Register a callback to run before form validation.
     * The callback receives: function(array $data)
     */
    public function beforeFormValidated(?Closure $callback): static
    {
        $this->beforeFormValidated = $callback;

        return $this;
    }

    /**
     * Register a callback to run after form validation passes.
     * The callback receives: function(array $data)
     */
    public function afterFormValidated(?Closure $callback): static
    {
        $this->afterFormValidated = $callback;

        return $this;
    }

    /**
     * Execute the before hook.
     */
    public function runBefore(array $data, mixed $record = null): array
    {
        if ($this->before !== null) {
            $result = call_user_func($this->before, $data, $record);
            if (is_array($result)) {
                return $result;
            }
        }

        return $data;
    }

    /**
     * Execute the after hook.
     */
    public function runAfter(array $data, mixed $record = null, mixed $result = null): void
    {
        if ($this->after !== null) {
            call_user_func($this->after, $data, $record, $result);
        }
    }

    /**
     * Execute the before form filled hook.
     */
    public function runBeforeFormFilled(array $data): array
    {
        if ($this->beforeFormFilled !== null) {
            $result = call_user_func($this->beforeFormFilled, $data);
            if (is_array($result)) {
                return $result;
            }
        }

        return $data;
    }

    /**
     * Execute the after form filled hook.
     */
    public function runAfterFormFilled(array $data): array
    {
        if ($this->afterFormFilled !== null) {
            $result = call_user_func($this->afterFormFilled, $data);
            if (is_array($result)) {
                return $result;
            }
        }

        return $data;
    }

    /**
     * Execute the before form validated hook.
     */
    public function runBeforeFormValidated(array $data): array
    {
        if ($this->beforeFormValidated !== null) {
            $result = call_user_func($this->beforeFormValidated, $data);
            if (is_array($result)) {
                return $result;
            }
        }

        return $data;
    }

    /**
     * Execute the after form validated hook.
     */
    public function runAfterFormValidated(array $data): array
    {
        if ($this->afterFormValidated !== null) {
            $result = call_user_func($this->afterFormValidated, $data);
            if (is_array($result)) {
                return $result;
            }
        }

        return $data;
    }

    public function hasBefore(): bool
    {
        return $this->before !== null;
    }

    public function hasAfter(): bool
    {
        return $this->after !== null;
    }

    public function hasBeforeFormFilled(): bool
    {
        return $this->beforeFormFilled !== null;
    }

    public function hasAfterFormFilled(): bool
    {
        return $this->afterFormFilled !== null;
    }

    public function hasBeforeFormValidated(): bool
    {
        return $this->beforeFormValidated !== null;
    }

    public function hasAfterFormValidated(): bool
    {
        return $this->afterFormValidated !== null;
    }
}
