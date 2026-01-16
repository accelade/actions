# ReplicateAction

The `ReplicateAction` creates a copy of an existing record. It's useful for duplicating content, templates, or any record that can serve as a starting point.

## Basic Usage

```php
use Accelade\Actions\ReplicateAction;

$action = ReplicateAction::make();
```

## Default Configuration

| Property | Default |
|----------|---------|
| Name | `replicate` |
| Label | "Replicate" (translated) |
| Icon | `heroicon-o-document-duplicate` |
| Color | `gray` |
| Requires Confirmation | Yes |

## Excluding Attributes

Specify attributes that should not be copied to the replica:

```php
ReplicateAction::make()
    ->excludeAttributes([
        'slug',
        'published_at',
        'views_count',
        'likes_count',
    ]);
```

## Lifecycle Callbacks

### Before Replica Saved

Modify the replica before it's saved:

```php
ReplicateAction::make()
    ->beforeReplicaSaved(function ($replica, $original, $data) {
        $replica->title = $original->title . ' (Copy)';
        $replica->slug = Str::slug($replica->title);
        $replica->published_at = null;
        $replica->status = 'draft';
    });
```

### After Replica Saved

Perform actions after the replica is saved:

```php
ReplicateAction::make()
    ->afterReplicaSaved(function ($replica, $original, $data) {
        // Copy relationships
        foreach ($original->tags as $tag) {
            $replica->tags()->attach($tag->id);
        }

        // Copy media
        foreach ($original->getMedia('images') as $media) {
            $media->copy($replica, 'images');
        }
    });
```

## Success Redirect

Redirect to a specific URL after replication:

```php
ReplicateAction::make()
    ->successRedirectUrl(function ($replica) {
        return route('posts.edit', $replica);
    });
```

## Complete Example

```php
ReplicateAction::make()
    ->label('Duplicate')
    ->icon('heroicon-o-square-2-stack')
    ->modalHeading('Duplicate Post')
    ->modalDescription('A copy of this post will be created as a draft.')
    ->excludeAttributes([
        'slug',
        'published_at',
        'views_count',
    ])
    ->beforeReplicaSaved(function ($replica, $original) {
        $replica->title = $original->title . ' (Copy)';
        $replica->slug = Str::slug($replica->title) . '-' . time();
        $replica->status = 'draft';
        $replica->author_id = auth()->id();
    })
    ->afterReplicaSaved(function ($replica, $original) {
        // Copy tags
        $replica->tags()->sync($original->tags->pluck('id'));

        // Notify
        activity()->log("Replicated post: {$original->title}");
    })
    ->successRedirectUrl(fn ($replica) => route('posts.edit', $replica));
```

## With Form Schema

Collect additional data during replication:

```php
use Accelade\Forms\Fields\TextInput;
use Accelade\Forms\Fields\Toggle;

ReplicateAction::make()
    ->schema([
        TextInput::make('new_title')
            ->label('New Title')
            ->default(fn ($record) => $record->title . ' (Copy)')
            ->required(),
        Toggle::make('copy_tags')
            ->label('Copy Tags')
            ->default(true),
    ])
    ->beforeReplicaSaved(function ($replica, $original, $data) {
        $replica->title = $data['new_title'];
        $replica->slug = Str::slug($data['new_title']);
    })
    ->afterReplicaSaved(function ($replica, $original, $data) {
        if ($data['copy_tags'] ?? false) {
            $replica->tags()->sync($original->tags->pluck('id'));
        }
    });
```

## In Table Actions

```blade
<x-accelade::action
    :action="Accelade\Actions\ReplicateAction::make()
        ->excludeAttributes(['slug', 'published_at'])"
    :record="$post"
/>
```

## Handling Unique Constraints

When replicating records with unique fields:

```php
ReplicateAction::make()
    ->excludeAttributes(['email']) // Exclude unique fields
    ->beforeReplicaSaved(function ($replica, $original) {
        // Generate a new unique email
        $replica->email = 'copy_' . time() . '_' . $original->email;
    });
```

## Replicating with Relationships

```php
ReplicateAction::make()
    ->afterReplicaSaved(function ($replica, $original) {
        // Replicate hasMany relationships
        foreach ($original->items as $item) {
            $newItem = $item->replicate();
            $newItem->order_id = $replica->id;
            $newItem->save();
        }

        // Sync belongsToMany relationships
        $replica->categories()->sync($original->categories->pluck('id'));
    });
```

## See Also

- [CreateAction](create-action) - Create new records
- [EditAction](edit-action) - Edit existing records
- [ViewAction](view-action) - View record details
