# ExportAction

The `ExportAction` exports data to various formats like XLSX or CSV. It integrates with Laravel Excel or can be used with custom exporters.

## Basic Usage

```php
use Accelade\Actions\ExportAction;

$action = ExportAction::make();
```

## Default Configuration

| Property | Default |
|----------|---------|
| Name | `export` |
| Label | "Export" (translated) |
| Icon | `heroicon-o-arrow-down-tray` |
| Color | `gray` |
| Format | `xlsx` |

## With Exporter Class

Use a Laravel Excel exporter class:

```php
ExportAction::make()
    ->exporter(PostsExport::class);
```

## Export Formats

### XLSX (Default)

```php
ExportAction::make()
    ->xlsx()
    ->fileName('posts-export.xlsx');
```

### CSV

```php
ExportAction::make()
    ->csv()
    ->fileName('posts-export.csv');
```

### Multiple Formats

```php
ExportAction::make()
    ->formats(['xlsx', 'csv']);
```

## Custom Filename

```php
ExportAction::make()
    ->fileName('posts-' . now()->format('Y-m-d') . '.xlsx');
```

## Columns and Headings

Define columns and headings for simple exports:

```php
ExportAction::make()
    ->columns(['id', 'title', 'author', 'created_at'])
    ->headings(['ID', 'Title', 'Author', 'Created At']);
```

## Query Modification

Modify the query before exporting:

```php
ExportAction::make()
    ->modifyQueryUsing(function ($query) {
        return $query
            ->where('status', 'published')
            ->orderBy('created_at', 'desc');
    });
```

## Queued Exports

For large datasets, queue the export:

```php
ExportAction::make()
    ->queue()
    ->disk('exports'); // Store to specific disk
```

## Complete Example

```php
ExportAction::make()
    ->label('Export Posts')
    ->icon('heroicon-o-document-arrow-down')
    ->exporter(PostsExport::class)
    ->fileName('posts-' . now()->format('Y-m-d-His') . '.xlsx')
    ->xlsx()
    ->modifyQueryUsing(fn ($query) => $query->where('status', 'published'));
```

## Creating an Exporter

Create a Laravel Excel exporter:

```php
// app/Exports/PostsExport.php
namespace App\Exports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PostsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Post::query()->where('status', 'published');
    }

    public function headings(): array
    {
        return ['ID', 'Title', 'Author', 'Published At', 'Views'];
    }

    public function map($post): array
    {
        return [
            $post->id,
            $post->title,
            $post->author->name,
            $post->published_at->format('Y-m-d'),
            $post->views_count,
        ];
    }
}
```

## In Table Header

```blade
@php
use Accelade\Actions\ExportAction;

$exportAction = ExportAction::make()
    ->exporter(App\Exports\PostsExport::class)
    ->fileName('posts.xlsx');
@endphp

<div class="flex justify-between mb-4">
    <h1>Posts</h1>
    <x-accelade::action :action="$exportAction" />
</div>
```

## With Filters

Export with applied filters:

```php
ExportAction::make()
    ->modifyQueryUsing(function ($query) use ($filters) {
        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['date_from'] ?? null) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        return $query;
    });
```

## Download vs Store

By default, exports are downloaded. To store instead:

```php
ExportAction::make()
    ->disk('exports')
    ->queue(); // Queued exports are stored, not downloaded
```

## See Also

- [ImportAction](import-action) - Import data from files
- [Laravel Excel Documentation](https://docs.laravel-excel.com/)
