<?php

declare(strict_types=1);

namespace Accelade\Actions\Concerns;

use Closure;

trait HasSuccessHandling
{
    protected bool $hasSuccessNotification = true;

    protected string|Closure|null $successNotificationTitle = null;

    protected string|Closure|null $successNotificationBody = null;

    protected string|Closure|null $successRedirectUrl = null;

    protected string|Closure|null $failureNotificationTitle = null;

    protected string|Closure|null $failureNotificationBody = null;

    protected string|Closure|null $failureRedirectUrl = null;

    /**
     * Enable or disable success notifications.
     */
    public function successNotification(bool $condition = true): static
    {
        $this->hasSuccessNotification = $condition;

        return $this;
    }

    /**
     * Set the success notification title.
     */
    public function successNotificationTitle(string|Closure|null $title): static
    {
        $this->successNotificationTitle = $title;
        $this->hasSuccessNotification = true;

        return $this;
    }

    /**
     * Set the success notification body.
     */
    public function successNotificationBody(string|Closure|null $body): static
    {
        $this->successNotificationBody = $body;

        return $this;
    }

    /**
     * Set the URL to redirect to after success.
     */
    public function successRedirectUrl(string|Closure|null $url): static
    {
        $this->successRedirectUrl = $url;

        return $this;
    }

    /**
     * Set the failure notification title.
     */
    public function failureNotificationTitle(string|Closure|null $title): static
    {
        $this->failureNotificationTitle = $title;

        return $this;
    }

    /**
     * Set the failure notification body.
     */
    public function failureNotificationBody(string|Closure|null $body): static
    {
        $this->failureNotificationBody = $body;

        return $this;
    }

    /**
     * Set the URL to redirect to after failure.
     */
    public function failureRedirectUrl(string|Closure|null $url): static
    {
        $this->failureRedirectUrl = $url;

        return $this;
    }

    public function hasSuccessNotification(): bool
    {
        return $this->hasSuccessNotification;
    }

    public function getSuccessNotificationTitle(mixed $record = null): ?string
    {
        if ($this->successNotificationTitle === null) {
            return null;
        }

        if ($this->successNotificationTitle instanceof Closure) {
            return ($this->successNotificationTitle)($record);
        }

        return $this->successNotificationTitle;
    }

    public function getSuccessNotificationBody(mixed $record = null): ?string
    {
        if ($this->successNotificationBody === null) {
            return null;
        }

        if ($this->successNotificationBody instanceof Closure) {
            return ($this->successNotificationBody)($record);
        }

        return $this->successNotificationBody;
    }

    public function getSuccessRedirectUrl(mixed $record = null): ?string
    {
        if ($this->successRedirectUrl === null) {
            return null;
        }

        if ($this->successRedirectUrl instanceof Closure) {
            return ($this->successRedirectUrl)($record);
        }

        return $this->successRedirectUrl;
    }

    public function getFailureNotificationTitle(mixed $record = null): ?string
    {
        if ($this->failureNotificationTitle === null) {
            return null;
        }

        if ($this->failureNotificationTitle instanceof Closure) {
            return ($this->failureNotificationTitle)($record);
        }

        return $this->failureNotificationTitle;
    }

    public function getFailureNotificationBody(mixed $record = null): ?string
    {
        if ($this->failureNotificationBody === null) {
            return null;
        }

        if ($this->failureNotificationBody instanceof Closure) {
            return ($this->failureNotificationBody)($record);
        }

        return $this->failureNotificationBody;
    }

    public function getFailureRedirectUrl(mixed $record = null): ?string
    {
        if ($this->failureRedirectUrl === null) {
            return null;
        }

        if ($this->failureRedirectUrl instanceof Closure) {
            return ($this->failureRedirectUrl)($record);
        }

        return $this->failureRedirectUrl;
    }
}
