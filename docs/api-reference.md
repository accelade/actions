# API Reference

Complete reference for all Accelade Actions classes and methods.

## Action Class

```php
use Accelade\Actions\Action;
```

### Creating Actions

```php
Action::make(?string $name = null): static
```

### Properties

| Method | Description |
|--------|-------------|
| `name(string $name)` | Set action name/identifier |
| `label(string $label)` | Set display label |
| `icon(string $icon, ?string $position = null)` | Set icon name and optional position |
| `iconPosition(string $position)` | Set icon position ('before' or 'after') |
| `color(string $color)` | Set color scheme |
| `size(string $size)` | Set button size ('xs', 'sm', 'md', 'lg', 'xl') |
| `tooltip(string $tooltip)` | Set tooltip text |
| `extraAttributes(array $attrs)` | Add custom HTML attributes |

### Color Shortcuts

| Method | Color |
|--------|-------|
| `primary()` | Indigo |
| `secondary()` | Gray |
| `success()` | Green |
| `danger()` | Red |
| `warning()` | Yellow |
| `info()` | Cyan |

### Variants

| Method | Description |
|--------|-------------|
| `button()` | Standard button (default) |
| `link()` | Text link style |
| `iconButton()` | Icon-only button |
| `outlined()` | Outlined style |

### URLs

| Method | Description |
|--------|-------------|
| `url(string\|Closure $url, bool $newTab = false)` | Set target URL |
| `openUrlInNewTab(bool $condition = true)` | Open URL in new tab |
| `method(string $method)` | Set HTTP method |
| `spa(bool $condition = true)` | Enable/disable SPA navigation |
| `preserveState(bool $preserve = true)` | Preserve component state |
| `preserveScroll(bool $preserve = true)` | Preserve scroll position |

### Server Actions

| Method | Description |
|--------|-------------|
| `action(Closure $callback)` | Set server-side action closure |

The closure receives `($record, $data)` parameters.

### Visibility

| Method | Description |
|--------|-------------|
| `hidden(bool\|Closure $condition = true)` | Hide the action |
| `visible(bool\|Closure $condition = true)` | Show the action when true |
| `disabled(bool $condition = true)` | Disable the action |

### Authorization

| Method | Description |
|--------|-------------|
| `authorize(Closure $callback)` | Set authorization closure |

### Modal/Confirmation

| Method | Description |
|--------|-------------|
| `requiresConfirmation(bool $condition = true)` | Require user confirmation |
| `modalHeading(string $heading)` | Set modal title |
| `modalDescription(string $description)` | Set modal message |
| `modalSubmitActionLabel(string $label)` | Set confirm button label |
| `modalCancelActionLabel(string $label)` | Set cancel button label |
| `modalWidth(string $width)` | Set modal width |
| `confirmDanger(bool $condition = true)` | Use danger styling |
| `slideOver(bool $condition = true)` | Display as slide-over |

### Getters

| Method | Returns |
|--------|---------|
| `getName()` | `?string` |
| `getLabel()` | `?string` |
| `getIcon()` | `?string` |
| `getIconPosition()` | `string` |
| `getColor()` | `?string` |
| `getSize()` | `?string` |
| `getVariant()` | `?string` |
| `getUrl(?$record = null)` | `?string` |
| `getMethod()` | `string` |
| `isHidden(?$record = null)` | `bool` |
| `isVisible(?$record = null)` | `bool` |
| `isDisabled()` | `bool` |
| `canAuthorize(?$record = null)` | `bool` |
| `hasModal()` | `bool` |
| `toArray()` | `array` |
| `toArrayWithRecord($record)` | `array` |

---

## DeleteAction Class

```php
use Accelade\Actions\DeleteAction;
```

Pre-configured action with:
- Name: `delete`
- Icon: `trash-2`
- Color: `danger`
- Requires confirmation with danger styling

---

## ViewAction Class

```php
use Accelade\Actions\ViewAction;
```

Pre-configured action with:
- Name: `view`
- Icon: `eye`
- Color: `secondary`
- Method: `GET`

---

## EditAction Class

```php
use Accelade\Actions\EditAction;
```

Pre-configured action with:
- Name: `edit`
- Icon: `pencil`
- Color: `primary`
- Method: `GET`

---

## CreateAction Class

```php
use Accelade\Actions\CreateAction;
```

Pre-configured action with:
- Name: `create`
- Icon: `plus`
- Color: `primary`
- Method: `GET`

---

## ActionGroup Class

```php
use Accelade\Actions\ActionGroup;
```

### Creating Groups

```php
ActionGroup::make(array $actions): static
```

### Properties

| Method | Description |
|--------|-------------|
| `label(string $label)` | Set dropdown button label |
| `icon(string $icon)` | Set dropdown button icon |
| `color(string $color)` | Set dropdown button color |
| `tooltip(string $tooltip)` | Set tooltip text |
| `size(string $size)` | Set button size |
| `dropdown(bool $condition = true)` | Enable dropdown mode |
| `iconButton()` | Use icon-only trigger |

### Getters

| Method | Returns |
|--------|---------|
| `getActions()` | `array<Action>` |
| `getVisibleActions(?$record = null)` | `array<Action>` |
| `toArray()` | `array` |
| `toArrayWithRecord($record)` | `array` |

---

## Blade Components

### action

```blade
<x-actions::action
    :action="$action"
    :record="$record"
    as="button"
/>
```

| Prop | Type | Description |
|------|------|-------------|
| `action` | `Action\|array` | Action instance or array |
| `record` | `mixed` | Optional record for dynamic URLs |
| `as` | `string` | Element type ('button', 'submit') |

### action-group

```blade
<x-actions::action-group
    :group="$group"
    :record="$record"
    align="right"
/>
```

| Prop | Type | Description |
|------|------|-------------|
| `group` | `ActionGroup\|array` | Group instance or array |
| `record` | `mixed` | Optional record for actions |
| `align` | `string` | Dropdown alignment ('left', 'right', 'center') |

### icon

```blade
<x-actions::icon name="check" class="w-4 h-4" />
```

---

## JavaScript API

### Global Object

```javascript
window.AcceladeActions = {
    manager: ActionManager,
    trigger: (selector, options) => Promise<ActionResult>,
    version: string,
};
```

### Triggering Actions Programmatically

```javascript
await window.AcceladeActions.trigger('[data-action-button]', {
    record: { id: 1 },
    data: { reason: 'Testing' },
    onSuccess: (result) => console.log('Success:', result),
    onError: (error) => console.error('Error:', error),
});
```

### Events

| Event | Detail |
|-------|--------|
| `actions:ready` | `{ manager: ActionManager }` |
| `action:success` | `ActionResult` |
| `action:error` | `ActionResult` |
