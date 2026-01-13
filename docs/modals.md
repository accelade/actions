# Confirmation Modals

Actions can display confirmation dialogs before executing. This is useful for destructive operations like deleting records.

## Basic Confirmation

```php
Action::make('delete')
    ->label('Delete')
    ->color('danger')
    ->requiresConfirmation()
    ->url(route('posts.destroy', $post));
```

## Customizing the Modal

### Heading and Description

```php
->requiresConfirmation()
->modalHeading('Delete this post?')
->modalDescription('This action cannot be undone. All associated data will be permanently removed.')
```

### Button Labels

```php
->modalSubmitActionLabel('Yes, Delete')
->modalCancelActionLabel('No, Cancel')
```

### Danger Styling

Use danger styling for the confirm button (red):

```php
->confirmDanger()
```

### Complete Example

```php
$action = Action::make('force-delete')
    ->label('Permanently Delete')
    ->icon('trash-2')
    ->color('danger')
    ->requiresConfirmation()
    ->confirmDanger()
    ->modalHeading('Permanently Delete Record')
    ->modalDescription('This will permanently delete the record and all associated data. This action cannot be undone.')
    ->modalSubmitActionLabel('Delete Forever')
    ->modalCancelActionLabel('Keep It')
    ->action(function ($record) {
        $record->forceDelete();
        return ['redirect' => route('posts.index')];
    });
```

## Modal Appearance

### Width

```php
->modalWidth('sm')   // Small
->modalWidth('md')   // Medium (default)
->modalWidth('lg')   // Large
->modalWidth('xl')   // Extra large
->modalWidth('2xl')  // 2X large
```

### Slide-Over Panel

Display as a slide-over panel instead of a centered modal:

```php
->slideOver()
```

## How It Works

1. User clicks the action button
2. Accelade's confirm dialog appears
3. If confirmed, the action executes (URL navigation or server action)
4. If cancelled, nothing happens

The confirmation uses Accelade's built-in confirm dialog system, which:
- Supports dark mode
- Is keyboard accessible (Enter to confirm, Escape to cancel)
- Handles RTL layouts
- Animates smoothly

## JavaScript Events

You can listen for action events:

```javascript
document.addEventListener('action:success', (event) => {
    console.log('Action succeeded:', event.detail);
});

document.addEventListener('action:error', (event) => {
    console.log('Action failed:', event.detail);
});
```

## Best Practices

1. **Always use confirmation for destructive actions** - Deleting, force-deleting, and other irreversible actions should require confirmation.

2. **Be specific in your messaging** - Tell users exactly what will happen if they confirm.

3. **Use danger styling appropriately** - Only use `confirmDanger()` for truly destructive actions.

4. **Provide clear button labels** - "Delete" is better than "OK" for a delete confirmation.
