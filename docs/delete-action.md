# DeleteAction

The `DeleteAction` is a preset action designed for deleting records. It comes pre-configured with confirmation dialogs and danger styling to prevent accidental deletions.

## Basic Usage

```php
use Accelade\Actions\DeleteAction;

$action = DeleteAction::make()
    ->url(fn ($record) => route('posts.destroy', $record));
```

## Default Configuration

DeleteAction comes with these defaults:

| Property | Default |
|----------|---------|
| Name | `delete` |
| Label | "Delete" (translated) |
| Icon | `trash-2` |
| Color | `danger` (red) |
| HTTP Method | `DELETE` |
| Requires Confirmation | Yes |
| Confirm Danger | Yes |
| Modal Heading | "Delete Record" |
| Modal Description | "Are you sure? This cannot be undone." |

## Customization

Override any default as needed:

```php
DeleteAction::make()
    ->label('Remove')
    ->icon('x-circle')
    ->modalHeading('Remove this post?')
    ->modalDescription('The post will be moved to trash.')
    ->modalSubmitActionLabel('Yes, remove it')
    ->url(fn ($record) => route('posts.destroy', $record));
```

## Server-Side Action

Execute PHP code when confirmed:

```php
DeleteAction::make()
    ->action(function ($record) {
        $record->delete();

        return [
            'message' => 'Post deleted successfully!',
            'redirect' => route('posts.index'),
        ];
    });
```

## With Form Schema

Add form fields for deletion options:

```php
use Accelade\Forms\Fields\Checkbox;
use Accelade\Forms\Fields\Textarea;

DeleteAction::make()
    ->modalHeading('Delete Post')
    ->modalDescription('Please provide a reason for deletion.')
    ->schema([
        Textarea::make('reason')
            ->label('Reason for deletion')
            ->placeholder('Why are you deleting this post?')
            ->rows(3),
        Checkbox::make('notify_author')
            ->label('Notify the author about this deletion'),
        Checkbox::make('permanent')
            ->label('Delete permanently (cannot be recovered)'),
    ])
    ->action(function ($record, $data) {
        if ($data['notify_author'] ?? false) {
            // Send notification to author
            $record->author->notify(new PostDeletedNotification($record, $data['reason']));
        }

        if ($data['permanent'] ?? false) {
            $record->forceDelete();
        } else {
            $record->delete();
        }

        return [
            'message' => 'Post deleted!',
            'redirect' => route('posts.index'),
        ];
    });
```

## Common Patterns

### Table Row Action

Use in table rows with record context:

```blade
@foreach($posts as $post)
    <tr>
        <td>{{ $post->title }}</td>
        <td>
            <x-accelade::action :action="$deleteAction" :record="$post" />
        </td>
    </tr>
@endforeach
```

### Icon-Only Button

Compact icon button for tables:

```php
DeleteAction::make()
    ->iconButton()
    ->tooltip('Delete this record');
```

### Soft Delete with Restore Option

```php
// Delete action
DeleteAction::make()
    ->hidden(fn ($record) => $record->trashed())
    ->action(function ($record) {
        $record->delete();
        return ['message' => 'Moved to trash'];
    });

// Restore action (use regular Action)
Action::make('restore')
    ->label('Restore')
    ->icon('refresh-cw')
    ->color('success')
    ->visible(fn ($record) => $record->trashed())
    ->action(function ($record) {
        $record->restore();
        return ['message' => 'Restored!'];
    });
```

### Bulk Delete

For deleting multiple selected records:

```php
DeleteAction::make('bulk-delete')
    ->label('Delete Selected')
    ->modalHeading('Delete Selected Records')
    ->modalDescription(fn () => 'Are you sure you want to delete ' . count($selected) . ' records?')
    ->action(function () use ($selected) {
        Post::whereIn('id', $selected)->delete();

        return [
            'message' => count($selected) . ' records deleted',
            'redirect' => route('posts.index'),
        ];
    });
```

### Force Delete (Permanent)

For permanently removing soft-deleted records:

```php
DeleteAction::make('force-delete')
    ->label('Delete Forever')
    ->icon('alert-triangle')
    ->modalHeading('Permanently Delete')
    ->modalDescription('This will permanently delete the record. This action CANNOT be undone.')
    ->modalSubmitActionLabel('Delete Forever')
    ->action(function ($record) {
        $record->forceDelete();

        return [
            'message' => 'Permanently deleted',
            'redirect' => route('posts.index'),
        ];
    });
```

### Conditional Authorization

Only allow deletion for authorized users:

```php
DeleteAction::make()
    ->authorize(fn ($record) => auth()->user()->can('delete', $record))
    ->hidden(fn ($record) => ! auth()->user()->can('delete', $record))
    ->action(function ($record) {
        $record->delete();
        return ['message' => 'Deleted!'];
    });
```

## Disabling Confirmation

If you need to skip confirmation (not recommended):

```php
DeleteAction::make()
    ->requiresConfirmation(false)
    ->action(fn ($record) => $record->delete());
```

## Rendering

```blade
@php
use Accelade\Actions\DeleteAction;

$action = DeleteAction::make()
    ->action(function ($record) {
        $record->delete();
        return ['redirect' => route('posts.index')];
    });
@endphp

<x-accelade::action :action="$action" :record="$post" />
```

## See Also

- [CreateAction](create-action) - Create new records
- [EditAction](edit-action) - Edit existing records
- [ViewAction](view-action) - View record details
- [Confirmation Modals](action-modals) - Customize confirmation dialogs
