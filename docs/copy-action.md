# Copy Action

CopyAction provides a pre-configured action for copying values to the clipboard. It shows a notification on success or failure.

## Basic Usage

```php
use Accelade\Actions\CopyAction;

// Copy a static value
CopyAction::make()
    ->copyValue('Hello, World!');
```

## Copy Modes

### Copy Static Value

```php
CopyAction::make()
    ->copyValue('https://example.com');

// Alternative syntax
CopyAction::make()
    ->value('DISCOUNT20');
```

### Copy Record Attribute

```php
CopyAction::make()
    ->copyAttribute('email');

// Alternative syntax
CopyAction::make()
    ->attribute('api_token');

// Nested attributes (dot notation)
CopyAction::make()
    ->copyAttribute('address.postal_code');
```

### Copy Multiple Attributes

```php
CopyAction::make()
    ->copyAttributes(['name', 'email', 'phone'])
    ->attributeSeparator("\n");  // Each on new line

// With custom separator
CopyAction::make()
    ->attributes(['first_name', 'last_name'])
    ->attributeSeparator(' ');  // Space between
```

### Copy Element Content

```php
CopyAction::make()
    ->copyElement('#code-block');

// Alternative syntax
CopyAction::make()
    ->element('.api-key-display');
```

### Copy Text Selection

```php
CopyAction::make()
    ->copySelection();

// Alternative syntax
CopyAction::make()
    ->selection();
```

## Dynamic Values

### Using Closures

```php
CopyAction::make()
    ->copyValue(fn ($record) => route('posts.show', $record) . '?ref=share');

// Complex formatting
CopyAction::make()
    ->copyValue(fn ($user) => "Name: {$user->name}\nEmail: {$user->email}");
```

### Format Value

```php
// Format phone number
CopyAction::make()
    ->copyAttribute('phone')
    ->formatValue(fn ($value) => preg_replace('/[^0-9]/', '', $value));

// Uppercase
CopyAction::make()
    ->copyAttribute('code')
    ->formatValue(fn ($value) => strtoupper($value));
```

## Copy Format

### As Plain Text (Default)

```php
CopyAction::make()
    ->asText();
```

### As HTML

```php
CopyAction::make()
    ->copyValue('<strong>Bold Text</strong>')
    ->asHtml();
```

### As JSON

```php
CopyAction::make()
    ->copyValue(fn ($record) => $record->toArray())
    ->asJson();
```

## Notifications

### Custom Messages

```php
CopyAction::make()
    ->successMessage('API key copied securely!')
    ->failureMessage('Failed to copy. Please try again.');

// Dynamic messages
CopyAction::make()
    ->successMessage(fn ($record) => "{$record->name}'s email copied!");
```

### Notification Duration

```php
CopyAction::make()
    ->notificationDuration(3000);  // 3 seconds
```

### Hide Notifications

```php
CopyAction::make()
    ->hideNotification();

// Or
CopyAction::make()
    ->showNotification(false);
```

## Callbacks

```php
CopyAction::make()
    ->copyAttribute('api_token')
    ->beforeCopy(function ($record, $data) {
        // Log the access
        activity()
            ->performedOn($record)
            ->log('API token copied');
    })
    ->afterCopy(function ($record, $data) {
        // Track analytics
        Analytics::track('api_token_copied', [
            'user_id' => auth()->id(),
        ]);
    });
```

## Button Variants

```php
CopyAction::make()->button();      // Standard button
CopyAction::make()->link();        // Link style
CopyAction::make()->iconButton();  // Icon only
CopyAction::make()->outlined();    // Outlined button
```

## Common Use Cases

### API Key Display

```blade
<div class="flex items-center gap-2">
    <code class="flex-1 font-mono">{{ $apiKey }}</code>
    <x-accelade::action :action="CopyAction::make()
        ->iconButton()
        ->tooltip('Copy API Key')
        ->copyAttribute('api_key')
        ->successMessage('API key copied!')" :record="$user" />
</div>
```

### Share Link

```php
CopyAction::make()
    ->label('Copy Link')
    ->icon('heroicon-o-link')
    ->copyValue(fn ($record) => route('posts.show', $record))
    ->successMessage('Link copied to clipboard!');
```

### Copy Email from Table

```php
CopyAction::make()
    ->iconButton()
    ->icon('heroicon-o-clipboard-document')
    ->tooltip('Copy email')
    ->copyAttribute('email');
```

### Copy vCard

```php
CopyAction::make()
    ->label('Copy Contact')
    ->copyValue(fn ($contact) => "BEGIN:VCARD
VERSION:3.0
FN:{$contact->name}
EMAIL:{$contact->email}
TEL:{$contact->phone}
END:VCARD");
```

## Complete Example

```php
CopyAction::make()
    ->label('Copy Invoice Details')
    ->icon('heroicon-o-clipboard-document')
    ->copyValue(fn ($invoice) => "Invoice: {$invoice->number}\n" .
        "Date: {$invoice->date->format('Y-m-d')}\n" .
        "Amount: \${$invoice->total}\n" .
        "Status: {$invoice->status}")
    ->successMessage(fn ($invoice) => "Invoice #{$invoice->number} details copied!")
    ->failureMessage('Could not copy invoice details')
    ->notificationDuration(2000)
    ->beforeCopy(fn ($invoice) =>
        activity()->performedOn($invoice)->log('Invoice details copied')
    );
```

## API Reference

| Method | Description |
|--------|-------------|
| `copyValue(string\|Closure $value)` | Set the value to copy |
| `value(string\|Closure $value)` | Alias for copyValue |
| `copyAttribute(string $attribute)` | Copy a record attribute |
| `attribute(string $attribute)` | Alias for copyAttribute |
| `copyAttributes(array $attributes)` | Copy multiple attributes |
| `attributes(array $attributes)` | Alias for copyAttributes |
| `attributeSeparator(string $separator)` | Separator for multiple attributes |
| `copyElement(string $selector)` | Copy element content by selector |
| `element(string $selector)` | Alias for copyElement |
| `copySelection()` | Copy current text selection |
| `selection()` | Alias for copySelection |
| `formatValue(Closure $callback)` | Transform value before copying |
| `asText()` | Copy as plain text (default) |
| `asHtml()` | Copy as HTML |
| `asJson()` | Copy as JSON |
| `copyAs(string $format)` | Set copy format |
| `showNotification(bool $show)` | Show/hide notification |
| `hideNotification()` | Hide notification |
| `successMessage(string\|Closure $message)` | Success notification message |
| `failureMessage(string\|Closure $message)` | Failure notification message |
| `notificationDuration(int $ms)` | Notification duration in ms |
| `beforeCopy(Closure $callback)` | Callback before copying |
| `afterCopy(Closure $callback)` | Callback after copying |
