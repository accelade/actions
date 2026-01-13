# Getting Started with Accelade Actions

Accelade Actions provides a Filament-style action API for building interactive buttons, links, and dropdown menus with Blade components.

## Installation

Install via Composer:

```bash
composer require accelade/actions
```

The package auto-registers with Laravel via package discovery.

## Quick Setup

Run the install command to publish assets:

```bash
php artisan actions:install
```

Add the directives to your layout:

```blade
<!DOCTYPE html>
<html>
<head>
    @acceladeStyles
    @actionsStyles
</head>
<body>
    {{ $slot }}

    @acceladeScripts
    @actionsScripts
</body>
</html>
```

## Your First Action

Create a simple action button:

```blade
@php
use Accelade\Actions\Action;

$action = Action::make('save')
    ->label('Save')
    ->icon('check')
    ->color('success')
    ->url(route('posts.store'));
@endphp

<x-actions::action :action="$action" />
```

## Action with Confirmation

Add a confirmation dialog:

```blade
@php
use Accelade\Actions\DeleteAction;

$action = DeleteAction::make()
    ->url(route('posts.destroy', $post));
@endphp

<x-actions::action :action="$action" :record="$post" />
```

## Server-Side Actions

Execute PHP code on button click:

```blade
@php
$action = Action::make('publish')
    ->label('Publish Post')
    ->icon('check')
    ->color('success')
    ->action(function ($record, $data) {
        $record->update(['published_at' => now()]);

        return [
            'message' => 'Post published successfully!',
            'redirect' => route('posts.show', $record),
        ];
    });
@endphp

<x-actions::action :action="$action" :record="$post" />
```

## Next Steps

- Learn about [Action Types](actions.md)
- Configure [Confirmation Modals](modals.md)
- Read the [API Reference](api-reference.md)
