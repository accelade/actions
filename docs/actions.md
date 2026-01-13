# Actions

Actions are the core building blocks of this package. Each action represents a button, link, or interactive element that can navigate to URLs, open modals, or execute server-side code.

## Creating Actions

Use the static `make()` method to create an action:

```php
use Accelade\Actions\Action;

$action = Action::make('edit')
    ->label('Edit Post')
    ->icon('pencil')
    ->color('primary')
    ->url(route('posts.edit', $post));
```

## Action Properties

### Label

```php
->label('Click Me')
```

If not specified, the label is generated from the action name.

### Icon

```php
->icon('check')
->icon('pencil', 'after')  // Icon position
->iconPosition('before')   // or 'after'
```

Available icons: `check`, `x`, `pencil`, `trash-2`, `eye`, `plus`, `refresh-cw`, `download`, `upload`, `copy`, `settings`, `external-link`, `alert-circle`, `more-vertical`, `more-horizontal`, `chevron-down`, `chevron-up`, `chevron-left`, `chevron-right`.

### Color

```php
->color('primary')   // Indigo
->color('secondary') // Gray
->color('success')   // Green
->color('danger')    // Red
->color('warning')   // Yellow
->color('info')      // Cyan

// Shorthand methods
->primary()
->secondary()
->success()
->danger()
->warning()
->info()
```

### Size

```php
->size('xs')  // Extra small
->size('sm')  // Small
->size('md')  // Medium (default)
->size('lg')  // Large
->size('xl')  // Extra large
```

### Variant

```php
->button()      // Standard button (default)
->link()        // Text link style
->iconButton()  // Icon-only button
->outlined()    // Outlined style
```

## URL Actions

Navigate to a URL when clicked:

```php
$action = Action::make('view')
    ->label('View Post')
    ->url(route('posts.show', $post));

// Open in new tab
$action = Action::make('external')
    ->label('Visit Website')
    ->url('https://example.com', true);
// or
    ->openUrlInNewTab();
```

### Dynamic URLs

Use closures for record-based URLs:

```php
$action = Action::make('edit')
    ->url(fn ($record) => route('posts.edit', $record));
```

### HTTP Methods

```php
->method('GET')     // Default
->method('POST')
->method('PUT')
->method('PATCH')
->method('DELETE')
```

## Server-Side Actions

Execute PHP code when the action is triggered:

```php
$action = Action::make('approve')
    ->label('Approve')
    ->icon('check')
    ->color('success')
    ->action(function ($record, $data) {
        $record->update(['approved' => true]);

        return [
            'message' => 'Record approved!',
        ];
    });
```

### Return Values

Your action closure can return:

```php
// Success message
return ['message' => 'Done!'];

// Redirect
return ['redirect' => '/dashboard'];

// Both
return [
    'message' => 'Saved!',
    'redirect' => route('posts.index'),
];

// Just a string
return 'Action completed!';
```

## Visibility & Authorization

### Hiding Actions

```php
// Always hidden
->hidden()

// Conditionally hidden
->hidden(fn ($record) => $record->is_locked)

// Visible when condition is true
->visible(fn ($record) => $record->is_editable)
```

### Authorization

```php
->authorize(fn ($record) => auth()->user()->can('update', $record))
```

## Disabling Actions

```php
->disabled()
->disabled(fn ($record) => $record->is_processing)
```

## Extra Attributes

Add custom HTML attributes:

```php
->extraAttributes([
    'data-turbo' => 'false',
    'x-on:click' => 'handleClick',
])
```

## Tooltip

```php
->tooltip('Click to edit this post')
```

## Built-in Action Types

### ViewAction

```php
use Accelade\Actions\ViewAction;

ViewAction::make()
    ->url(fn ($record) => route('posts.show', $record));
```

### EditAction

```php
use Accelade\Actions\EditAction;

EditAction::make()
    ->url(fn ($record) => route('posts.edit', $record));
```

### CreateAction

```php
use Accelade\Actions\CreateAction;

CreateAction::make()
    ->url(route('posts.create'));
```

### DeleteAction

```php
use Accelade\Actions\DeleteAction;

DeleteAction::make()
    ->url(fn ($record) => route('posts.destroy', $record));
```

Includes built-in confirmation dialog with danger styling.

## Rendering Actions

Use the Blade component:

```blade
<x-actions::action :action="$action" :record="$record" />
```

The `:record` prop is optional and used for dynamic URLs and visibility checks.
