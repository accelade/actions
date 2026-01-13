@php
    $assetMode = config('actions.asset_mode', 'route');

    if ($assetMode === 'published') {
        $scriptUrl = asset('vendor/actions/actions.js');
    } else {
        $scriptUrl = route('actions.js');
    }

    // Add cache buster based on file modification time
    $distPath = __DIR__ . '/../../../dist/actions.js';
    if (file_exists($distPath)) {
        $scriptUrl .= '?v=' . filemtime($distPath);
    }
@endphp

<script src="{{ $scriptUrl }}" defer></script>
