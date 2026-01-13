{{-- Actions CSS - Minimal styling for actions (most styles come from Tailwind) --}}
<style>
    /* Action button loading state */
    [data-action-button][data-loading] {
        pointer-events: none;
        opacity: 0.7;
    }

    [data-action-button][data-loading]::after {
        content: '';
        display: inline-block;
        width: 1em;
        height: 1em;
        margin-left: 0.5em;
        border: 2px solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: actions-spin 0.6s linear infinite;
    }

    @keyframes actions-spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Dark mode variables */
    :root {
        --actions-bg: #ffffff;
        --actions-text: #1f2937;
        --actions-border: #e5e7eb;
        --actions-hover: #f3f4f6;
    }

    .dark, [data-theme="dark"] {
        --actions-bg: #1f2937;
        --actions-text: #f9fafb;
        --actions-border: #374151;
        --actions-hover: #374151;
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
