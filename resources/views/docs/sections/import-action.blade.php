<x-accelade::layouts.docs
    :section="$section"
    :framework="$framework"
    :documentation="$documentation"
    :hasDemo="$hasDemo"
    :navigation="$navigation"
>
    <div class="space-y-8">
        {{-- Basic ImportAction Demo --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Basic ImportAction</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ImportAction provides a pre-configured action for importing data from files. Works great with Laravel Excel.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $basicImport = \Accelade\Actions\ImportAction::make()
                        ->action(fn () => \Accelade\Facades\Notify::success('Importing')->body('Processing import...'));
                @endphp
                <x-accelade::action :action="$basicImport" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
use Accelade\Actions\ImportAction;

ImportAction::make();
            </x-accelade::code-block>
        </div>

        {{-- Custom Import Class --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Custom Import Class</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Specify a Laravel Excel import class for the action.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
use App\Imports\UsersImport;

ImportAction::make()
    ->importer(UsersImport::class);

// With accepted file types
ImportAction::make()
    ->importer(UsersImport::class)
    ->acceptedFileTypes(['.xlsx', '.csv', '.xls']);
            </x-accelade::code-block>
        </div>

        {{-- File Upload Schema --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Import with File Upload</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Use a form schema to collect the file and import options.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $uploadImport = \Accelade\Actions\ImportAction::make('import-upload')
                        ->label('Import Users')
                        ->modalHeading('Import Users from File')
                        ->modalWidth('lg')
                        ->schema([
                            \Accelade\Forms\Components\TextInput::make('file')
                                ->label('Select File')
                                ->type('file')
                                ->required()
                                ->helperText('Accepted formats: .xlsx, .csv'),
                            \Accelade\Forms\Components\Checkbox::make('skip_header')
                                ->label('First row contains headers')
                                ->default(true),
                            \Accelade\Forms\Components\Select::make('on_duplicate')
                                ->label('On Duplicate')
                                ->options([
                                    'skip' => 'Skip',
                                    'update' => 'Update existing',
                                    'error' => 'Show error',
                                ])
                                ->default('skip'),
                        ])
                        ->action(fn ($data) => \Accelade\Facades\Notify::success('Importing')->body("Options: " . json_encode($data)));
                @endphp
                <x-accelade::action :action="$uploadImport" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
ImportAction::make()
    ->modalHeading('Import Users from File')
    ->schema([
        FileUpload::make('file')
            ->label('Select File')
            ->acceptedFileTypes(['.xlsx', '.csv'])
            ->required(),
        Checkbox::make('skip_header')
            ->label('First row contains headers')
            ->default(true),
        Select::make('on_duplicate')
            ->label('On Duplicate')
            ->options([
                'skip' => 'Skip',
                'update' => 'Update existing',
                'error' => 'Show error',
            ])
            ->default('skip'),
    ])
    ->action(function (array $data) {
        $import = new UsersImport(
            skipHeader: $data['skip_header'],
            onDuplicate: $data['on_duplicate']
        );

        Excel::import($import, $data['file']);

        Notify::success('Import Complete')
            ->body("{$import->getRowCount()} rows imported.");
    });
            </x-accelade::code-block>
        </div>

        {{-- Column Mapping --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Column Mapping</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Allow users to map file columns to database fields.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
ImportAction::make()
    ->modalHeading('Map Columns')
    ->modalWidth('xl')
    ->schema([
        FileUpload::make('file')
            ->label('Select File')
            ->required()
            ->live()
            ->afterStateUpdated(fn ($state, $set) =>
                // Parse headers and set column options
                $set('available_columns', $this->parseHeaders($state))
            ),

        Select::make('mapping.name')
            ->label('Name Column')
            ->options(fn ($get) => $get('available_columns') ?? [])
            ->required(),

        Select::make('mapping.email')
            ->label('Email Column')
            ->options(fn ($get) => $get('available_columns') ?? [])
            ->required(),

        Select::make('mapping.role')
            ->label('Role Column (Optional)')
            ->options(fn ($get) => $get('available_columns') ?? []),
    ])
    ->action(function (array $data) {
        $import = new UsersImport($data['mapping']);
        Excel::import($import, $data['file']);
    });
            </x-accelade::code-block>
        </div>

        {{-- Validation & Preview --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Validation & Preview</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Validate the import file and show a preview before processing.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
// Two-step import: validate first, then import
ImportAction::make()
    ->modalHeading('Import Preview')
    ->schema([
        FileUpload::make('file')
            ->label('Select File')
            ->required(),
    ])
    ->action(function (array $data, $action) {
        $import = new UsersImport();
        $import->setValidationMode(true);

        try {
            Excel::import($import, $data['file']);
        } catch (ValidationException $e) {
            // Show validation errors
            Notify::danger('Validation Failed')
                ->body($e->failures()->count() . ' rows have errors.');

            return ['errors' => $e->failures()];
        }

        // If validation passes, show preview and confirm
        return [
            'preview' => $import->getPreviewRows(),
            'total' => $import->getRowCount(),
        ];
    });
            </x-accelade::code-block>
        </div>

        {{-- Queued Import --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Queued Import (Large Files)</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                For large files, queue the import and notify the user when complete.
            </p>

            <x-accelade::code-block language="php" class="mt-4">
ImportAction::make()
    ->label('Import Large File')
    ->modalDescription('Large files will be processed in the background.')
    ->schema([
        FileUpload::make('file')
            ->label('Select File')
            ->maxSize(50 * 1024) // 50MB
            ->required(),
    ])
    ->action(function (array $data) {
        $user = auth()->user();
        $path = $data['file']->store('imports');

        // Queue the import job
        ImportUsersJob::dispatch($path, $user)
            ->onQueue('imports');

        Notify::success('Import Started')
            ->body('You will receive an email when the import is complete.');
    });

// The job handles the actual import
class ImportUsersJob implements ShouldQueue
{
    public function handle()
    {
        Excel::import(new UsersImport(), $this->path);

        // Notify user when complete
        $this->user->notify(new ImportCompleteNotification());
    }
}
            </x-accelade::code-block>
        </div>

        {{-- Download Template --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Download Template</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                Provide a template file for users to fill in before importing.
            </p>

            <div class="flex flex-wrap gap-4">
                @php
                    $templateAction = \Accelade\Actions\Action::make('download-template')
                        ->label('Download Template')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('secondary')
                        ->action(fn () => \Accelade\Facades\Notify::success('Downloading')->body('Template file downloading...'));
                @endphp
                <x-accelade::action :action="$templateAction" />
            </div>

            <x-accelade::code-block language="php" class="mt-4">
// Pair ImportAction with a template download action
$actions = [
    Action::make('download-template')
        ->label('Download Template')
        ->icon('heroicon-o-document-arrow-down')
        ->color('secondary')
        ->action(fn () => Excel::download(
            new UsersTemplateExport(),
            'users-template.xlsx'
        )),

    ImportAction::make()
        ->label('Import Users')
        ->importer(UsersImport::class),
];
            </x-accelade::code-block>
        </div>

        {{-- Button Variants --}}
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4 text-slate-900 dark:text-white">Button Variants</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                ImportAction in different button styles.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                @php
                    $buttonImport = \Accelade\Actions\ImportAction::make('import-button')
                        ->button()
                        ->action(fn () => \Accelade\Facades\Notify::success('Button variant'));

                    $linkImport = \Accelade\Actions\ImportAction::make('import-link')
                        ->link()
                        ->action(fn () => \Accelade\Facades\Notify::success('Link variant'));

                    $iconImport = \Accelade\Actions\ImportAction::make('import-icon')
                        ->iconButton()
                        ->tooltip('Import data')
                        ->action(fn () => \Accelade\Facades\Notify::success('Icon variant'));

                    $outlinedImport = \Accelade\Actions\ImportAction::make('import-outlined')
                        ->outlined()
                        ->action(fn () => \Accelade\Facades\Notify::success('Outlined variant'));
                @endphp

                <div class="text-center">
                    <x-accelade::action :action="$buttonImport" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Button</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$linkImport" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Link</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$iconImport" />
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Icon</p>
                </div>

                <div class="text-center">
                    <x-accelade::action :action="$outlinedImport" />
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

// app/Imports/UsersImport.php
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public function model(array $row)
    {
        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? Str::random(10)),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ];
    }
}

// In your action
ImportAction::make()
    ->schema([
        FileUpload::make('file')->required(),
    ])
    ->action(function (array $data) {
        $import = new UsersImport();
        Excel::import($import, $data['file']);

        $failures = $import->failures();
        if ($failures->isNotEmpty()) {
            Notify::warning('Import Complete with Errors')
                ->body("{$failures->count()} rows skipped due to errors.");
        } else {
            Notify::success('Import Complete');
        }
    });
            </x-accelade::code-block>
        </div>
    </div>
</x-accelade::layouts.docs>
