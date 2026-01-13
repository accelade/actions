@php
    $assetMode = config('actions.asset_mode', 'route');

    if ($assetMode === 'published') {
        $scriptUrl = asset('vendor/actions/actions.js');
    } else {
        $scriptUrl = route('actions.js');
    }
@endphp

<script src="{{ $scriptUrl }}" defer></script>
