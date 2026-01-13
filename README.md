# Accelade Actions

<p align="center">
<strong>Filament-Style Actions for Accelade</strong>
</p>

<p align="center">
<a href="https://github.com/accelade/actions/actions"><img src="https://github.com/accelade/actions/actions/workflows/tests.yml/badge.svg" alt="Tests"></a>
<a href="https://packagist.org/packages/accelade/actions"><img src="https://img.shields.io/packagist/v/accelade/actions" alt="Latest Version"></a>
<a href="https://packagist.org/packages/accelade/actions"><img src="https://img.shields.io/packagist/dt/accelade/actions" alt="Total Downloads"></a>
<a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"></a>
</p>

---

Build interactive action buttons with modals, confirmations, and server-side execution - all using Accelade's reactive Blade components.

```php
use Accelade\Actions\Action;
use Accelade\Actions\DeleteAction;

// Simple action button
Action::make('save')
    ->label('Save Changes')
    ->icon('check')
    ->color('success')
    ->url(route('posts.store'));

// Delete with confirmation
DeleteAction::make()
    ->url(fn ($record) => route('posts.destroy', $record))
    ->requiresConfirmation()
    ->modalHeading('Delete Post')
    ->modalDescription('Are you sure? This cannot be undone.');
```

---

## Features

- **Fluent API** — Filament-style action builder with chainable methods
- **Confirmation Modals** — Built-in confirmation dialogs with customizable text
- **Server Actions** — Execute closures on the server via secure AJAX
- **SPA Navigation** — Integrates with Accelade's SPA navigation system
- **Multiple Variants** — Button, link, and icon-only styles
- **Color Schemes** — Primary, secondary, success, danger, warning, info
- **Action Groups** — Dropdown menus for multiple actions
- **Authorization** — Closure-based authorization checks

---

## Installation

```bash
composer require accelade/actions
```

The package auto-registers with Laravel. To publish assets:

```bash
php artisan actions:install
```

Add to your layout:

```blade
<head>
    @actionsStyles
</head>
<body>
    {{ $slot }}

    @actionsScripts
</body>
```

---

## Quick Start

### Basic Action Button

```blade
@php
    $action = Action::make('edit')
        ->label('Edit')
        ->icon('pencil')
        ->color('primary')
        ->url(route('posts.edit', $post));
@endphp

<x-actions::action :action="$action" />
```

### Delete with Confirmation

```blade
@php
    $deleteAction = DeleteAction::make()
        ->url(route('posts.destroy', $post));
@endphp

<x-actions::action :action="$deleteAction" :record="$post" />
```

### Server-Side Action

```blade
@php
    $action = Action::make('publish')
        ->label('Publish')
        ->icon('check')
        ->color('success')
        ->requiresConfirmation()
        ->action(function ($record, $data) {
            $record->update(['published_at' => now()]);
            return ['message' => 'Post published!'];
        });
@endphp

<x-actions::action :action="$action" :record="$post" />
```

---

## Action Types

| Type | Description |
|------|-------------|
| `Action` | Standard action button |
| `ViewAction` | Navigate to view page |
| `EditAction` | Navigate to edit page |
| `CreateAction` | Navigate to create page |
| `DeleteAction` | Delete with confirmation |
| `ActionGroup` | Dropdown menu of actions |

---

## Colors

```php
->color('primary')   // Indigo
->color('secondary') // Gray
->color('success')   // Green
->color('danger')    // Red
->color('warning')   // Yellow
->color('info')      // Cyan
```

---

## Variants

```php
// Standard button (default)
->button()

// Text link style
->link()

// Icon-only button
->iconButton()

// Outlined style
->outlined()
```

---

## Sizes

```php
->size('xs')  // Extra small
->size('sm')  // Small
->size('md')  // Medium (default)
->size('lg')  // Large
->size('xl')  // Extra large
```

---

## Confirmation Modal

```php
Action::make('delete')
    ->requiresConfirmation()
    ->modalHeading('Delete Record')
    ->modalDescription('Are you sure you want to delete this?')
    ->modalSubmitActionLabel('Yes, Delete')
    ->modalCancelActionLabel('Cancel')
    ->confirmDanger();
```

---

## Action Groups

```blade
@php
    $group = ActionGroup::make([
        ViewAction::make()->url(route('posts.show', $post)),
        EditAction::make()->url(route('posts.edit', $post)),
        DeleteAction::make()->url(route('posts.destroy', $post)),
    ])->tooltip('Actions');
@endphp

<x-actions::action-group :group="$group" :record="$post" />
```

---

## Authorization

```php
Action::make('delete')
    ->authorize(fn ($record) => auth()->user()->can('delete', $record))
    ->hidden(fn ($record) => $record->is_protected);
```

---

## Documentation

| Guide | Description |
|-------|-------------|
| [Getting Started](docs/getting-started.md) | Installation and setup |
| [Actions](docs/actions.md) | Building action buttons |
| [Modals](docs/modals.md) | Confirmation dialogs |
| [API Reference](docs/api-reference.md) | Complete API docs |

---

## Requirements

- PHP 8.2+
- Laravel 11.x or 12.x
- Accelade 0.1+

---

## License

MIT License. See [LICENSE](LICENSE) for details.
