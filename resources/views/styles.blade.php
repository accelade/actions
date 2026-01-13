{{-- Actions CSS - Minimal styling for actions (most styles come from Tailwind) --}}
<style>
    /* Action button loading state */
    .action-button[data-loading],
    [data-action-button][data-loading] {
        pointer-events: none;
        opacity: 0.75;
        cursor: wait;
    }

    /* Hide icon, show spinner when loading */
    .action-button[data-loading] .action-icon,
    [data-action-button][data-loading] .action-icon {
        display: none !important;
    }

    .action-button[data-loading] .action-spinner,
    [data-action-button][data-loading] .action-spinner {
        display: inline-flex !important;
    }

    /* Hide spinner by default */
    .action-button .action-spinner,
    [data-action-button] .action-spinner {
        display: none;
    }

    /* Spinner animation */
    @keyframes action-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .action-spinner svg {
        animation: action-spin 0.8s linear infinite;
    }

    /* RTL Support for dropdowns */
    [dir="rtl"] [data-action-dropdown] {
        transform-origin: top left;
    }

    /* Dark mode variables */
    :root {
        --actions-bg: #ffffff;
        --actions-text: #1f2937;
        --actions-border: #e5e7eb;
        --actions-hover: #f3f4f6;
    }

    .dark, [data-theme="dark"] {
        --actions-bg: #1e293b;
        --actions-text: #f1f5f9;
        --actions-border: #334155;
        --actions-hover: #334155;
    }

    /* Action dropdown transitions */
    [data-action-dropdown] {
        transform-origin: top right;
    }

    [data-action-dropdown].entering {
        animation: actions-dropdown-in 0.15s ease-out;
    }

    [data-action-dropdown].leaving {
        animation: actions-dropdown-out 0.1s ease-in;
    }

    @keyframes actions-dropdown-in {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes actions-dropdown-out {
        from {
            opacity: 1;
            transform: scale(1);
        }
        to {
            opacity: 0;
            transform: scale(0.95);
        }
    }
</style>
