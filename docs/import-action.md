# ImportAction

The `ImportAction` imports data from uploaded files like XLSX or CSV. It integrates with Laravel Excel or can be used with custom importers.

## Basic Usage

```php
use Accelade\Actions\ImportAction;

$action = ImportAction::make();
```

## Default Configuration

| Property | Default |
|----------|---------|
| Name | `import` |
| Label | "Import" (translated) |
| Icon | `heroicon-o-arrow-up-tray` |
| Color | `gray` |
| Format | `xlsx` |
| Requires Confirmation | Yes |

## With Importer Class

Use a Laravel Excel importer class:

```php
ImportAction::make()
    ->importer(PostsImport::class);
```

## Import Formats

### XLSX (Default)

```php
ImportAction::make()
    ->xlsx();
```

### CSV

```php
ImportAction::make()
    ->csv();
```

## Accepted File Types

```php
ImportAction::make()
    ->acceptedFileTypes([
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
    ]);
```

## Queued Imports

For large files, queue the import:

```php
ImportAction::make()
    ->queue()
    ->chunkSize(1000); // Process in chunks
```

## Lifecycle Callbacks

### Before Import

```php
ImportAction::make()
    ->beforeImport(function ($file, $data) {
        // Validate file
        // Log import start
        activity()->log('Starting import');
    });
```

### After Import

```php
ImportAction::make()
    ->afterImport(function ($file, $data) {
        // Send notification
        auth()->user()->notify(new ImportCompletedNotification());

        // Clear cache
        Cache::forget('posts.count');
    });
```

## Complete Example

```php
ImportAction::make()
    ->label('Import Posts')
    ->icon('heroicon-o-document-arrow-up')
    ->importer(PostsImport::class)
    ->modalHeading('Import Posts from File')
    ->modalDescription('Upload an Excel or CSV file with posts data. Download the template for the correct format.')
    ->acceptedFileTypes([
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
    ])
    ->beforeImport(function ($file) {
        Log::info('Starting posts import', ['file' => $file->getClientOriginalName()]);
    })
    ->afterImport(function ($file) {
        Cache::forget('posts.count');
        Log::info('Posts import completed');
    });
```

## Creating an Importer

Create a Laravel Excel importer:

```php
// app/Imports/PostsImport.php
namespace App\Imports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PostsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Post([
            'title' => $row['title'],
            'content' => $row['content'],
            'author_id' => auth()->id(),
            'status' => $row['status'] ?? 'draft',
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'nullable|in:draft,published',
        ];
    }
}
```

## With Chunk Reading

For large files:

```php
// app/Imports/PostsImport.php
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PostsImport implements ToModel, WithHeadingRow, WithChunkReading
{
    public function chunkSize(): int
    {
        return 1000;
    }

    // ...
}
```

## In Page Header

```blade
@php
use Accelade\Actions\ImportAction;

$importAction = ImportAction::make()
    ->importer(App\Imports\PostsImport::class);
@endphp

<div class="flex justify-between mb-4">
    <h1>Posts</h1>
    <div class="flex gap-2">
        <x-accelade::action :action="$importAction" />
        <x-accelade::action :action="$exportAction" />
    </div>
</div>
```

## Providing a Template

Offer users a download template:

```php
// Controller method for template download
public function downloadTemplate()
{
    return Excel::download(new PostsTemplateExport, 'posts-template.xlsx');
}

// In your view
<a href="{{ route('posts.template') }}">Download Template</a>
```

## Error Handling

Handle import errors gracefully:

```php
// app/Imports/PostsImport.php
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class PostsImport implements ToModel, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row)
    {
        // ...
    }
}

// Get errors after import
$import = new PostsImport;
Excel::import($import, $file);

foreach ($import->errors() as $error) {
    // Handle error
}
```

## See Also

- [ExportAction](export-action) - Export data to files
- [Laravel Excel Documentation](https://docs.laravel-excel.com/)
