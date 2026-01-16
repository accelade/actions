<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    <div class="space-y-8">
        {{-- Basic ExportAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic ExportAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ExportAction provides a pre-configured action for exporting data. Works great with Laravel Excel.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicExport = \Accelade\Actions\ExportAction::make()
                        ->action(fn () => \Accelade\Facades\Notify::success('Exporting')->body('Starting export...'));
                @endphp
                <x-accelade::action :action="$basicExport" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\ExportAction;

ExportAction::make();
            </x-accelade::code-block>
        </div>

        {{-- Custom Export Class --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Custom Export Class</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Specify a Laravel Excel export class for the action.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
use App\Exports\UsersExport;

ExportAction::make()
    ->exporter(UsersExport::class);

// With custom filename
ExportAction::make()
    ->exporter(UsersExport::class)
    ->fileName('users-export.xlsx');

// With dynamic filename
ExportAction::make()
    ->exporter(UsersExport::class)
    ->fileName(fn () => 'users-' . now()->format('Y-m-d') . '.xlsx');
            </x-accelade::code-block>
        </div>

        {{-- Export Format Options --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Export Format Options</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Let users choose the export format with a form schema.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $formatExport = \Accelade\Actions\ExportAction::make('export-format')
                        ->label('Export Data')
                        ->modalHeading('Export Options')
                        ->schema([
                            \Accelade\Forms\Components\Select::make('format')
                                ->label('Export Format')
                                ->options([
                                    'xlsx' => 'Excel (.xlsx)',
                                    'csv' => 'CSV (.csv)',
                                    'pdf' => 'PDF (.pdf)',
                                ])
                                ->default('xlsx')
                                ->required(),
                            \Accelade\Forms\Components\Checkbox::make('include_headers')
                                ->label('Include column headers')
                                ->default(true),
                        ])
                        ->action(fn ($data) => \Accelade\Facades\Notify::success('Exporting')->body("Format: {$data['format']}"));
                @endphp
                <x-accelade::action :action="$formatExport" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ExportAction::make()
    ->modalHeading('Export Options')
    ->schema([
        Select::make('format')
            ->label('Export Format')
            ->options([
                'xlsx' => 'Excel (.xlsx)',
                'csv' => 'CSV (.csv)',
                'pdf' => 'PDF (.pdf)',
            ])
            ->default('xlsx')
            ->required(),
        Checkbox::make('include_headers')
            ->label('Include column headers')
            ->default(true),
    ])
    ->action(function (array $data) {
        $format = $data['format'];
        $export = new UsersExport($data['include_headers']);

        return Excel::download($export, "users.{$format}");
    });
            </x-accelade::code-block>
        </div>

        {{-- Column Selection --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Column Selection</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Allow users to choose which columns to export.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $columnExport = \Accelade\Actions\ExportAction::make('export-columns')
                        ->label('Custom Export')
                        ->modalHeading('Select Columns to Export')
                        ->modalWidth('lg')
                        ->schema([
                            \Accelade\Forms\Components\Checkbox::make('columns.id')
                                ->label('ID')
                                ->default(true),
                            \Accelade\Forms\Components\Checkbox::make('columns.name')
                                ->label('Name')
                                ->default(true),
                            \Accelade\Forms\Components\Checkbox::make('columns.email')
                                ->label('Email')
                                ->default(true),
                            \Accelade\Forms\Components\Checkbox::make('columns.created_at')
                                ->label('Created Date')
                                ->default(false),
                        ])
                        ->action(fn ($data) => \Accelade\Facades\Notify::success('Exporting')->body("Columns: " . json_encode($data['columns'] ?? [])));
                @endphp
                <x-accelade::action :action="$columnExport" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ExportAction::make()
    ->modalHeading('Select Columns to Export')
    ->schema([
        CheckboxList::make('columns')
            ->label('Columns')
            ->options([
                'id' => 'ID',
                'name' => 'Name',
                'email' => 'Email',
                'role' => 'Role',
                'created_at' => 'Created Date',
                'updated_at' => 'Last Updated',
            ])
            ->default(['id', 'name', 'email']),
    ])
    ->action(function (array $data) {
        return Excel::download(
            new UsersExport($data['columns']),
            'users.xlsx'
        );
    });
            </x-accelade::code-block>
        </div>

        {{-- Queued Export --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Queued Export (Large Datasets)</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                For large datasets, queue the export and notify the user when complete.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
ExportAction::make()
    ->label('Export All Data')
    ->modalHeading('Export Large Dataset')
    ->modalDescription('This export will be processed in the background. You will receive an email when it\'s ready.')
    ->action(function () {
        $user = auth()->user();

        // Queue the export job
        ExportUsersJob::dispatch($user)
            ->onQueue('exports');

        Notify::success('Export Started')
            ->body('You will receive an email when your export is ready.');

        return null; // No immediate download
    })
    ->successNotificationTitle('Export Queued')
    ->successNotificationBody('Check your email for the download link.');
            </x-accelade::code-block>
        </div>

        {{-- Filter Options --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Export with Filters</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Allow users to filter data before exporting.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $filterExport = \Accelade\Actions\ExportAction::make('export-filter')
                        ->label('Export with Filters')
                        ->modalHeading('Filter & Export')
                        ->modalWidth('lg')
                        ->schema([
                            \Accelade\Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    '' => 'All Statuses',
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'pending' => 'Pending',
                                ])
                                ->default(''),
                            \Accelade\Forms\Components\TextInput::make('date_from')
                                ->label('From Date')
                                ->type('date'),
                            \Accelade\Forms\Components\TextInput::make('date_to')
                                ->label('To Date')
                                ->type('date'),
                        ])
                        ->action(fn ($data) => \Accelade\Facades\Notify::success('Exporting')->body("Filters: " . json_encode($data)));
                @endphp
                <x-accelade::action :action="$filterExport" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ExportAction::make()
    ->modalHeading('Filter & Export')
    ->schema([
        Select::make('status')
            ->label('Status')
            ->options([
                '' => 'All',
                'active' => 'Active',
                'inactive' => 'Inactive',
            ]),
        DatePicker::make('date_from')
            ->label('From Date'),
        DatePicker::make('date_to')
            ->label('To Date'),
    ])
    ->action(function (array $data) {
        $query = User::query();

        if ($data['status']) {
            $query->where('status', $data['status']);
        }

        if ($data['date_from']) {
            $query->whereDate('created_at', '>=', $data['date_from']);
        }

        if ($data['date_to']) {
            $query->whereDate('created_at', '<=', $data['date_to']);
        }

        return Excel::download(
            new UsersExport($query),
            'filtered-users.xlsx'
        );
    });
            </x-accelade::code-block>
        </div>

        {{-- Button Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ExportAction in different button styles.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @php
                    $buttonExport = \Accelade\Actions\ExportAction::make('export-button')
                        ->button()
                        ->action(fn () => \Accelade\Facades\Notify::success('Button variant'));

                    $linkExport = \Accelade\Actions\ExportAction::make('export-link')
                        ->link()
                        ->action(fn () => \Accelade\Facades\Notify::success('Link variant'));

                    $iconExport = \Accelade\Actions\ExportAction::make('export-icon')
                        ->iconButton()
                        ->tooltip('Export data')
                        ->action(fn () => \Accelade\Facades\Notify::success('Icon variant'));

                    $outlinedExport = \Accelade\Actions\ExportAction::make('export-outlined')
                        ->outlined()
                        ->action(fn () => \Accelade\Facades\Notify::success('Outlined variant'));
                @endphp

                <div class="text-center">
                    <x-accelade::action :action="$buttonExport" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Button</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$linkExport" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Link</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$iconExport" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$outlinedExport" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Outlined</p>
                </div>
            </div>
        </div>

        {{-- Laravel Excel Integration --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Laravel Excel Integration</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Complete example with Laravel Excel package.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
// Install Laravel Excel: composer require maatwebsite/excel

// app/Exports/UsersExport.php
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected array $columns = ['id', 'name', 'email']
    ) {}

    public function query()
    {
        return User::query();
    }

    public function headings(): array
    {
        return array_map(fn ($col) => ucfirst($col), $this->columns);
    }

    public function map($user): array
    {
        return array_map(fn ($col) => $user->{$col}, $this->columns);
    }
}

// In your action
ExportAction::make()
    ->action(fn () => Excel::download(new UsersExport(), 'users.xlsx'));
            </x-accelade::code-block>
        </div>
    </div>
</x-accelade::layouts.docs>
