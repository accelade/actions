# RestoreAction

The `RestoreAction` restores soft-deleted records. It only appears for records that have been soft-deleted (trashed).

## Basic Usage

```php
use Accelade\Actions\RestoreAction;

$action = RestoreAction::make();
```

## Default Configuration

| Property | Default |
|----------|---------|
| Name | `restore` |
| Label | "Restore" (translated) |
| Icon | `heroicon-o-arrow-uturn-left` |
| Color | `success` |
| Requires Confirmation | Yes |
| Visibility | Only for trashed records |

## With Model

```php
RestoreAction::make()
    ->model(Post::class);
```

## Custom Restore Logic

```php
RestoreAction::make()
    ->restoreUsing(function ($record) {
        // Custom restore logic
        $record->restore();

        // Also restore related records
        $record->comments()->onlyTrashed()->restore();

        return true;
    });
```

Or use the `using()` alias:

```php
RestoreAction::make()
    ->using(function ($record) {
        return $record->restore();
    });
```

## Lifecycle Hooks

```php
RestoreAction::make()
    ->before(function ($data, $record) {
        // Log the restoration
        activity()->log("Restoring {$record->title}");
        return $data;
    })
    ->after(function ($data, $record, $result) {
        // Send notification
        $record->author->notify(new PostRestoredNotification($record));
    });
```

## Customization

```php
RestoreAction::make()
    ->label('Recover')
    ->icon('heroicon-o-arrow-path')
    ->modalHeading('Restore Record')
    ->modalDescription('This will restore the record and make it active again.')
    ->modalSubmitActionLabel('Yes, restore it');
```

## Visibility Control

By default, `RestoreAction` is only visible for soft-deleted (trashed) records:

```php
// Override visibility
RestoreAction::make()
    ->visible(function ($record) {
        return $record->trashed() && auth()->user()->can('restore', $record);
    });
```

## In Trash View

```blade
@foreach($trashedPosts as $post)
    <tr>
        <td>{{ $post->title }}</td>
        <td>{{ $post->deleted_at->diffForHumans() }}</td>
        <td>
            <x-accelade::action
                :action="Accelade\Actions\RestoreAction::make()"
                :record="$post"
            />
            <x-accelade::action
                :action="Accelade\Actions\ForceDeleteAction::make()"
                :record="$post"
            />
        </td>
    </tr>
@endforeach
```

## Common Patterns

### With Cascading Restore

Restore related records along with the main record:

```php
RestoreAction::make()
    ->using(function ($record) {
        // Restore in a transaction
        return DB::transaction(function () use ($record) {
            $record->restore();
            $record->comments()->onlyTrashed()->restore();
            $record->attachments()->onlyTrashed()->restore();
            return true;
        });
    });
```

### With Notification

```php
RestoreAction::make()
    ->after(function ($data, $record) {
        Notification::make()
            ->title('Post Restored')
            ->body("'{$record->title}' has been restored.")
            ->success()
            ->send();
    });
```

## See Also

- [DeleteAction](delete-action) - Soft delete records
- [ForceDeleteAction](force-delete-action) - Permanently delete records
- [RestoreBulkAction](bulk-actions#restorebulkaction) - Restore multiple records
