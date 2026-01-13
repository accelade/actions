# Action Groups

Action groups allow you to combine multiple actions into a dropdown menu, saving space and organizing related actions together.

## Creating an Action Group

Use the `ActionGroup` class to create a group of actions:

```php
use Accelade\Actions\ActionGroup;
use Accelade\Actions\ViewAction;
use Accelade\Actions\EditAction;
use Accelade\Actions\DeleteAction;

$group = ActionGroup::make([
    ViewAction::make()->url('/view'),
    EditAction::make()->url('/edit'),
    DeleteAction::make()->url('/delete'),
]);
```

## Rendering Action Groups

Use the `action-group` component to render the group:

```blade
<x-accelade::action-group :group="$group" />
```

## Customizing the Trigger Button

You can customize the dropdown trigger button:

```php
$group = ActionGroup::make([/* actions */])
    ->label('Actions')
    ->icon('settings')
    ->color('primary')
    ->tooltip('More options');
```

## Inline Mode

By default, action groups render as dropdowns. You can disable this to show actions inline:

```php
$group = ActionGroup::make([/* actions */])
    ->dropdown(false);
```

Then render each action individually:

```blade
@foreach($group->getVisibleActions() as $action)
    <x-accelade::action :action="$action" />
@endforeach
```

## Filtering Hidden Actions

Action groups automatically filter out hidden actions. Use `getVisibleActions()` to get only visible actions:

```php
$group = ActionGroup::make([
    Action::make('visible')->label('Visible'),
    Action::make('hidden')->label('Hidden')->hidden(),
]);

$group->getVisibleActions(); // Returns only 'visible'
```

## Available Methods

| Method | Description |
|--------|-------------|
| `make(array $actions)` | Create a new action group |
| `label(string $label)` | Set the trigger button label |
| `icon(string $icon)` | Set the trigger button icon |
| `color(string $color)` | Set the trigger button color |
| `tooltip(string $tooltip)` | Set the trigger button tooltip |
| `size(string $size)` | Set the trigger button size |
| `dropdown(bool $dropdown)` | Enable/disable dropdown mode |
| `getActions()` | Get all actions |
| `getVisibleActions()` | Get only visible actions |
| `isDropdown()` | Check if dropdown mode is enabled |
