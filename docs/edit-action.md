# EditAction

The `EditAction` is a preset action designed for editing existing records. It comes pre-configured with sensible defaults for "edit" operations.

## Basic Usage

```php
use Accelade\Actions\EditAction;

$action = EditAction::make()
    ->url(fn ($record) => route('posts.edit', $record));
```

## Default Configuration

EditAction comes with these defaults:

| Property | Default |
|----------|---------|
| Name | `edit` |
| Label | "Edit" (translated) |
| Icon | `pencil` |
| Color | `primary` (indigo) |
| HTTP Method | `POST` |

## Customization

Override any default as needed:

```php
EditAction::make()
    ->label('Modify')
    ->icon('edit-2')
    ->color('warning')
    ->url(fn ($record) => route('posts.edit', $record));
```

## Server-Side Action

Execute PHP code when the button is clicked:

```php
EditAction::make()
    ->action(function ($record, $data) {
        $record->update(['status' => 'draft']);

        return ['message' => 'Post reverted to draft!'];
    });
```

## With Form Schema

Add form fields for inline editing:

```php
use Accelade\Forms\Fields\TextInput;
use Accelade\Forms\Fields\Toggle;
use Accelade\Forms\Fields\Select;

EditAction::make()
    ->modalHeading(fn ($record) => "Edit: {$record->title}")
    ->modalDescription('Update the record details below.')
    ->schema([
        TextInput::make('title')
            ->label('Title')
            ->required(),
        Select::make('status')
            ->label('Status')
            ->options([
                'draft' => 'Draft',
                'published' => 'Published',
                'archived' => 'Archived',
            ]),
        Toggle::make('featured')
            ->label('Featured Post'),
    ])
    ->fillForm(fn ($record) => [
        'title' => $record->title,
        'status' => $record->status,
        'featured' => $record->featured,
    ])
    ->action(function ($record, $data) {
        $record->update($data);

        return ['message' => 'Post updated successfully!'];
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
            <x-accelade::action :action="$editAction" :record="$post" />
        </td>
    </tr>
@endforeach
```

### Icon-Only Button

Compact icon button for tables:

```php
EditAction::make()
    ->iconButton()
    ->tooltip('Edit this record')
    ->url(fn ($record) => route('posts.edit', $record));
```

### Quick Edit Modal

Edit without leaving the page:

```php
EditAction::make()
    ->label('Quick Edit')
    ->modalHeading('Quick Edit')
    ->schema([
        TextInput::make('title')->required(),
        Select::make('category')->options($categories),
    ])
    ->fillForm(fn ($record) => $record->only(['title', 'category']))
    ->action(function ($record, $data) {
        $record->update($data);

        return ['message' => 'Updated!'];
    });
```

### With Confirmation

Require confirmation for edits:

```php
EditAction::make()
    ->requiresConfirmation()
    ->modalHeading('Confirm Edit')
    ->modalDescription('Are you sure you want to modify this record?')
    ->action(function ($record, $data) {
        $record->update($data);
        return ['message' => 'Record updated!'];
    });
```

### Conditional Visibility

Only show for editable records:

```php
EditAction::make()
    ->url(fn ($record) => route('posts.edit', $record))
    ->hidden(fn ($record) => $record->is_locked)
    ->authorize(fn ($record) => auth()->user()->can('update', $record));
```

## Rendering

```blade
@php
use Accelade\Actions\EditAction;

$action = EditAction::make()
    ->url(fn ($record) => route('posts.edit', $record));
@endphp

<x-accelade::action :action="$action" :record="$post" />
```

## See Also

- [CreateAction](create-action) - Create new records
- [DeleteAction](delete-action) - Delete records
- [ViewAction](view-action) - View record details
- [Form Schema](action-modals#form-schema) - Add form fields to modals
