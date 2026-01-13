# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Accelade Actions is a Laravel package that provides Filament-style action buttons and modals for Accelade. It extends Accelade's link component with a fluent PHP API for building interactive UI components.

## Common Commands

### Development & Building
```bash
npm run dev              # Start Vite dev server
npm run build            # Build JS bundle
npm run typecheck        # Run TypeScript type checking
```

### Testing
```bash
# PHP tests (Pest)
composer test                                    # Run all tests
vendor/bin/pest tests/Feature/ActionTest.php    # Run specific test file

# E2E tests (Playwright)
npm run test:e2e                                # Run all E2E tests
npm run test:e2e:ui                             # Run with interactive UI
```

### Code Quality
```bash
composer format              # Format PHP with Pint
composer analyse             # Run format check + Mago
composer ci                  # Full CI: analyse + test
```

## Architecture

### PHP Backend (src/)
- `Action.php` - Base action class with fluent API
- `DeleteAction.php`, `ViewAction.php`, etc. - Pre-configured action types
- `ActionGroup.php` - Dropdown menu of actions
- `Concerns/` - Reusable traits (HasLabel, HasColor, HasIcon, etc.)
- `Http/Controllers/ActionController.php` - Server-side action execution

### Blade Components (resources/views/components/)
- `action.blade.php` - Main action button component
- `action-group.blade.php` - Dropdown action group
- `icon.blade.php` - SVG icon component

### TypeScript (resources/js/)
- `index.ts` - Entry point and initialization
- `core/action/ActionManager.ts` - Handles button clicks and server execution

### Integration with Accelade
- Uses Accelade's `<x-accelade::link>` for SPA navigation
- Uses Accelade's `<x-accelade::toggle>` for dropdowns
- Uses Accelade's confirm dialog for confirmations
- Uses Accelade's notification system for success/error messages

## Key Patterns

### Action API
```php
Action::make('name')
    ->label('Label')
    ->icon('icon-name')
    ->color('primary')
    ->url('/path')
    ->requiresConfirmation()
    ->action(fn ($record, $data) => /* ... */);
```

### Server-Side Action Execution
1. Action closure is wrapped in `SerializableClosure` and stored in session
2. Encrypted token is generated and passed to frontend
3. Frontend sends token to `/actions/execute` endpoint
4. Controller retrieves closure from session and executes it

### Blade Component Usage
```blade
<x-actions::action :action="$action" :record="$record" />
<x-actions::action-group :group="$group" :record="$record" />
```

## Configuration

Key config options in `config/actions.php`:
- `asset_mode` - 'route' (serve via Laravel) or 'published' (public/vendor)
- `prefix` - URL prefix for action routes (default: 'actions')
- `colors` - Tailwind CSS color mappings
- `demo.enabled` - Enable demo routes

## Test Structure
- `tests/Feature/` - Feature tests for actions
- `tests/Unit/` - Unit tests for action classes
- `tests/e2e/` - Playwright E2E tests
