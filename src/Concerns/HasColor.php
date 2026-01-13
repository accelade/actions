<?php

namespace Accelade\Actions\Concerns;

trait HasColor
{
    protected ?string $color = null;

    public function color(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Shorthand for color('primary').
     */
    public function primary(): static
    {
        return $this->color('primary');
    }

    /**
     * Shorthand for color('secondary').
     */
    public function secondary(): static
    {
        return $this->color('secondary');
    }

    /**
     * Shorthand for color('success').
     */
    public function success(): static
    {
        return $this->color('success');
    }

    /**
     * Shorthand for color('danger').
     */
    public function danger(): static
    {
        return $this->color('danger');
    }

    /**
     * Shorthand for color('warning').
     */
    public function warning(): static
    {
        return $this->color('warning');
    }

    /**
     * Shorthand for color('info').
     */
    public function info(): static
    {
        return $this->color('info');
    }

    public function getColor(): ?string
    {
        return $this->color;
    }
}
