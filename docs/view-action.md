# ViewAction

The `ViewAction` is a preset action designed for viewing record details. It comes pre-configured with sensible defaults for "view" operations.

## Basic Usage

```php
use Accelade\Actions\ViewAction;

$action = ViewAction::make()
    ->url(fn ($record) => route('posts.show', $record));
```

## Default Configuration

ViewAction comes with these defaults:

| Property | Default |
|----------|---------|
| Name | `view` |
| Label | "View" (translated) |
| Icon | `eye` |
| Color | `secondary` (gray) |
| HTTP Method | `POST` |

## Customization

Override any default as needed:

```php
ViewAction::make()
    ->label('Details')
    ->icon('info')
    ->color('info')
    ->url(fn ($record) => route('posts.show', $record));
```

## Server-Side Action

Execute PHP code when the button is clicked:

```php
ViewAction::make()
    ->action(function ($record) {
        // Log view event
        activity()->log("Viewed post: {$record->title}");

        // Show notification
        return ['message' => "Viewing: {$record->title}"];
    });
```

## With Modal Display

Show record details in a modal:

```php
use Accelade\Forms\Fields\Placeholder;

ViewAction::make()
    ->modalHeading(fn ($record) => $record->title)
    ->modalDescription('Record Details')
    ->schema([
        Placeholder::make('id')
            ->label('ID')
            ->content(fn ($record) => $record->id),
        Placeholder::make('title')
            ->label('Title')
            ->content(fn ($record) => $record->title),
        Placeholder::make('created_at')
            ->label('Created')
            ->content(fn ($record) => $record->created_at->diffForHumans()),
        Placeholder::make('status')
            ->label('Status')
            ->content(fn ($record) => ucfirst($record->status)),
    ])
    ->modalSubmitActionLabel('Close')
    ->action(fn () => null); // No action needed
```

## Common Patterns

### Table Row Action

Use in table rows with record context:

```blade
@foreach($posts as $post)
    <tr>
        <td>{{ $post->title }}</td>
        <td>
            <x-accelade::action :action="$viewAction" :record="$post" />
        </td>
    </tr>
@endforeach
```

### Icon-Only Button

Compact icon button for tables:

```php
ViewAction::make()
    ->iconButton()
    ->tooltip('View details')
    ->url(fn ($record) => route('posts.show', $record));
```

### Open in New Tab

View in a new browser tab:

```php
ViewAction::make()
    ->url(fn ($record) => route('posts.show', $record))
    ->openUrlInNewTab();
```

### Quick Preview Modal

Preview without leaving the page:

```php
ViewAction::make()
    ->label('Preview')
    ->icon('eye')
    ->modalHeading(fn ($record) => "Preview: {$record->title}")
    ->modalWidth('lg')
    ->schema([
        // Display record information
    ]);
```

### With Action Logging

Track who viewed what:

```php
ViewAction::make()
    ->action(function ($record) {
        // Log the view
        activity()
            ->performedOn($record)
            ->causedBy(auth()->user())
            ->log('viewed');

        // Increment view count
        $record->increment('views');

        return ['message' => 'Viewing record...'];
    })
    ->url(fn ($record) => route('posts.show', $record));
```

### Conditional Visibility

Only show for viewable records:

```php
ViewAction::make()
    ->url(fn ($record) => route('posts.show', $record))
    ->authorize(fn ($record) => auth()->user()->can('view', $record))
    ->hidden(fn ($record) => ! auth()->user()->can('view', $record));
```

### Link Variant

Display as a link instead of a button:

```php
ViewAction::make()
    ->label('Read more')
    ->link()
    ->url(fn ($record) => route('posts.show', $record));
```

## Rendering

```blade
@php
use Accelade\Actions\ViewAction;

$action = ViewAction::make()
    ->url(fn ($record) => route('posts.show', $record));
@endphp

<x-accelade::action :action="$action" :record="$post" />
```

## See Also

- [CreateAction](create-action) - Create new records
- [EditAction](edit-action) - Edit existing records
- [DeleteAction](delete-action) - Delete records
- [Actions](actions) - Base action documentation
