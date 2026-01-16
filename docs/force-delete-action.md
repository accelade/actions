# ForceDeleteAction

The `ForceDeleteAction` permanently deletes soft-deleted records. It only appears for records that have been soft-deleted (trashed).

## Basic Usage

```php
use Accelade\Actions\ForceDeleteAction;

$action = ForceDeleteAction::make();
```

## Default Configuration

| Property | Default |
|----------|---------|
| Name | `force-delete` |
| Label | "Force Delete" (translated) |
| Icon | `heroicon-o-trash` |
| Color | `danger` |
| Requires Confirmation | Yes |
| Visibility | Only for trashed records |

## With Model

```php
ForceDeleteAction::make()
    ->model(Post::class);
```

## Custom Force Delete Logic

```php
ForceDeleteAction::make()
    ->forceDeleteUsing(function ($record) {
        // Delete related files first
        Storage::delete($record->attachments->pluck('path')->toArray());

        // Then permanently delete the record
        return $record->forceDelete();
    });
```

Or use the `using()` alias:

```php
ForceDeleteAction::make()
    ->using(function ($record) {
        return $record->forceDelete();
    });
```

## Lifecycle Hooks

```php
ForceDeleteAction::make()
    ->before(function ($data, $record) {
        // Log the force deletion
        activity()->log("Force deleting {$record->title}");
        return $data;
    })
    ->after(function ($data, $record, $result) {
        // Clear cache
        Cache::forget("post.{$record->id}");
    });
```

## Customization

```php
ForceDeleteAction::make()
    ->label('Delete Permanently')
    ->icon('heroicon-o-x-circle')
    ->modalHeading('Permanently Delete Record')
    ->modalDescription('This record will be permanently removed and cannot be recovered. Are you absolutely sure?')
    ->modalSubmitActionLabel('Yes, delete forever');
```

## Visibility Control

By default, `ForceDeleteAction` is only visible for soft-deleted (trashed) records:

```php
// Override visibility
ForceDeleteAction::make()
    ->visible(function ($record) {
        return $record->trashed() && auth()->user()->can('forceDelete', $record);
    });
```

## In Tables

```blade
@foreach($trashedPosts as $post)
    <tr>
        <td>{{ $post->title }}</td>
        <td>
            <x-accelade::action
                :action="Accelade\Actions\ForceDeleteAction::make()"
                :record="$post"
            />
        </td>
    </tr>
@endforeach
```

## With RestoreAction

Typically used alongside `RestoreAction` for managing trashed records:

```php
$actions = [
    RestoreAction::make(),
    ForceDeleteAction::make(),
];
```

## See Also

- [DeleteAction](delete-action) - Soft delete records
- [RestoreAction](restore-action) - Restore soft-deleted records
- [ForceDeleteBulkAction](bulk-actions#forcedeletebulkaction) - Force delete multiple records
