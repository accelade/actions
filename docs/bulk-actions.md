# Bulk Actions

Bulk actions allow users to perform operations on multiple selected records at once. They're commonly used in tables with checkboxes for record selection.

## BulkAction

The `BulkAction` class is the base class for all bulk actions. It extends `Action` with additional support for handling multiple records.

### Basic Usage

```php
use Accelade\Actions\BulkAction;

$action = BulkAction::make('archive')
    ->label('Archive Selected')
    ->icon('heroicon-o-archive-box')
    ->action(function (array $recordIds, array $data) {
        Post::whereIn('id', $recordIds)->update(['archived' => true]);

        return count($recordIds) . ' posts archived';
    });
```

### Key Methods

| Method | Description |
|--------|-------------|
| `deselectRecordsAfterCompletion()` | Deselect records after action completes |
| `executeForRecords($records, $data)` | Execute the action for given records |
| `isBulkAction()` | Always returns `true` |

### With Confirmation

```php
BulkAction::make('delete')
    ->label('Delete Selected')
    ->icon('heroicon-o-trash')
    ->color('danger')
    ->requiresConfirmation()
    ->modalHeading('Delete Selected Records')
    ->modalDescription('Are you sure you want to delete the selected records?')
    ->deselectRecordsAfterCompletion()
    ->action(function (array $ids) {
        return Post::whereIn('id', $ids)->delete();
    });
```

## BulkActionGroup

Group multiple bulk actions into a dropdown menu or button group.

### Basic Usage

```php
use Accelade\Actions\BulkActionGroup;
use Accelade\Actions\DeleteBulkAction;
use Accelade\Actions\RestoreBulkAction;

$group = BulkActionGroup::make([
    DeleteBulkAction::make()->model(Post::class),
    RestoreBulkAction::make()->model(Post::class),
])
    ->label('Bulk Actions')
    ->icon('heroicon-o-chevron-down');
```

### As Button Group

Display actions as horizontal buttons instead of dropdown:

```php
BulkActionGroup::make([...])
    ->button(); // Horizontal button group instead of dropdown
```

### Styling

```php
BulkActionGroup::make([...])
    ->label('Actions')
    ->icon('heroicon-o-cog-6-tooth')
    ->color('primary');
```

## DeleteBulkAction

Pre-configured bulk action for deleting multiple records.

### Basic Usage

```php
use Accelade\Actions\DeleteBulkAction;

$action = DeleteBulkAction::make()
    ->model(Post::class);
```

### Default Configuration

| Property | Default |
|----------|---------|
| Name | `delete` |
| Label | "Delete" (translated) |
| Icon | `heroicon-o-trash` |
| Color | `danger` |
| Requires Confirmation | Yes |
| Deselect After | Yes |

### Custom Delete Logic

```php
DeleteBulkAction::make()
    ->action(function (array $ids) {
        // Custom deletion logic
        $posts = Post::whereIn('id', $ids)->get();

        foreach ($posts as $post) {
            // Delete related files
            Storage::delete($post->attachment_path);
            $post->delete();
        }

        return count($posts);
    });
```

## ForceDeleteBulkAction

Permanently delete soft-deleted records in bulk.

```php
use Accelade\Actions\ForceDeleteBulkAction;

$action = ForceDeleteBulkAction::make()
    ->model(Post::class);
```

This action is automatically configured to:
- Only appear when viewing trashed records
- Use `withTrashed()` to find soft-deleted records
- Call `forceDelete()` to permanently remove records

## RestoreBulkAction

Restore soft-deleted records in bulk.

```php
use Accelade\Actions\RestoreBulkAction;

$action = RestoreBulkAction::make()
    ->model(Post::class);
```

This action is automatically configured to:
- Only appear when viewing trashed records
- Use `withTrashed()` to find soft-deleted records
- Call `restore()` to recover records

## Blade Component Usage

```blade
@php
use Accelade\Actions\BulkActionGroup;
use Accelade\Actions\DeleteBulkAction;

$bulkActions = BulkActionGroup::make([
    DeleteBulkAction::make()->model(App\Models\Post::class),
]);
@endphp

{{-- In your table header --}}
<x-accelade::action-group :actions="$bulkActions" />
```

## JavaScript Integration

Bulk actions expect an array of record IDs to be passed when executed:

```javascript
// When submitting a bulk action
AcceladeActions.trigger('[data-action="delete"]', {
    data: {
        ids: [1, 2, 3, 4, 5] // Selected record IDs
    }
});
```

## See Also

- [DeleteAction](delete-action) - Delete single records
- [ForceDeleteAction](force-delete-action) - Permanently delete single records
- [RestoreAction](restore-action) - Restore single records
- [Action Groups](action-groups) - Group regular actions
