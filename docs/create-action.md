# CreateAction

The `CreateAction` is a preset action designed for creating new records. It comes pre-configured with sensible defaults for "create" operations.

## Basic Usage

```php
use Accelade\Actions\CreateAction;

$action = CreateAction::make()
    ->url(route('posts.create'));
```

## Default Configuration

CreateAction comes with these defaults:

| Property | Default |
|----------|---------|
| Name | `create` |
| Label | "Create" (translated) |
| Icon | `plus` |
| Color | `primary` (indigo) |
| HTTP Method | `POST` |

## Customization

Override any default as needed:

```php
CreateAction::make()
    ->label('Add New Post')
    ->icon('file-plus')
    ->color('success')
    ->url(route('posts.create'));
```

## Server-Side Action

Execute PHP code when the button is clicked:

```php
CreateAction::make()
    ->action(function ($record, $data) {
        $post = Post::create([
            'title' => 'New Post',
            'author_id' => auth()->id(),
        ]);

        return [
            'message' => 'Post created successfully!',
            'redirect' => route('posts.edit', $post),
        ];
    });
```

## With Form Schema

Add form fields to collect data before creation:

```php
use Accelade\Forms\Fields\TextInput;
use Accelade\Forms\Fields\Textarea;
use Accelade\Forms\Fields\Select;

CreateAction::make()
    ->label('Create Post')
    ->modalHeading('Create New Post')
    ->modalDescription('Fill in the details below to create a new post.')
    ->schema([
        TextInput::make('title')
            ->label('Title')
            ->required()
            ->placeholder('Enter post title'),
        Textarea::make('content')
            ->label('Content')
            ->rows(5),
        Select::make('category')
            ->label('Category')
            ->options([
                'news' => 'News',
                'blog' => 'Blog',
                'tutorial' => 'Tutorial',
            ]),
    ])
    ->action(function ($record, $data) {
        $post = Post::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'category' => $data['category'],
            'author_id' => auth()->id(),
        ]);

        return [
            'message' => "Created: {$post->title}",
            'redirect' => route('posts.show', $post),
        ];
    });
```

## Common Patterns

### Table Header Button

Place in a table header for creating new records:

```blade
<div class="flex justify-between items-center mb-4">
    <h2>Posts</h2>
    <x-accelade::action :action="$createAction" />
</div>
```

### With Authorization

Only show to authorized users:

```php
CreateAction::make()
    ->url(route('posts.create'))
    ->authorize(fn () => auth()->user()->can('create', Post::class))
    ->hidden(fn () => ! auth()->user()->can('create', Post::class));
```

### Modal Form for Quick Create

Create without leaving the page:

```php
CreateAction::make()
    ->label('Quick Create')
    ->modalHeading('Quick Create Post')
    ->schema([
        TextInput::make('title')->required(),
    ])
    ->action(function ($record, $data) {
        Post::create($data + ['author_id' => auth()->id()]);

        return ['message' => 'Post created!'];
    });
```

## Rendering

```blade
@php
use Accelade\Actions\CreateAction;

$action = CreateAction::make()
    ->url(route('posts.create'));
@endphp

<x-accelade::action :action="$action" />
```

## See Also

- [EditAction](edit-action) - Edit existing records
- [DeleteAction](delete-action) - Delete records
- [ViewAction](view-action) - View record details
- [Confirmation Modals](action-modals) - Add confirmations
