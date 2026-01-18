/**
 * Accelade Actions
 *
 * Action buttons and modals for Accelade.
 * Extends Accelade's link component with Filament-style action API.
 */

import { ActionManager, initActions } from './core/action';
import type { ActionConfig, ActionResult, ExecuteOptions } from './core/action';

// Extend window type for TypeScript
declare global {
    interface Window {
        Accelade?: {
            notify?: {
                success: (title: string, message?: string, options?: { duration?: number }) => void;
                danger: (title: string, message?: string, options?: { duration?: number }) => void;
                warning: (title: string, message?: string, options?: { duration?: number }) => void;
                info: (title: string, message?: string, options?: { duration?: number }) => void;
            };
            router?: {
                navigate: (url: string, options?: Record<string, unknown>) => Promise<boolean>;
            };
            confirm?: {
                show: (options: {
                    title?: string;
                    text: string;
                    confirmButton: string;
                    cancelButton: string;
                    danger: boolean;
                }) => Promise<{ confirmed: boolean }>;
            };
            icons?: {
                getSvg: (name: string) => Promise<string | null>;
            };
        };
        AcceladeActions?: {
            manager: ActionManager;
            trigger: (selector: string | HTMLElement, options?: ExecuteOptions) => Promise<ActionResult>;
            version: string;
        };
        AcceladeForms?: {
            init: () => void;
            reinit: () => void;
            Select: unknown;
            Toggle: unknown;
            [key: string]: unknown;
        };
    }
}

/**
 * Initialize Actions when DOM is ready
 * Uses a flag to prevent multiple initializations on SPA navigation
 */
function init(): void {
    // Prevent multiple initializations (important for SPA navigation)
    if (window.AcceladeActions) {
        return;
    }

    const checkAccelade = (): void => {
        const manager = initActions();

        // Expose global API
        window.AcceladeActions = {
            manager,
            trigger: (selector, options) => manager.trigger(selector, options),
            version: '0.1.0',
        };

        // Dispatch ready event
        document.dispatchEvent(new CustomEvent('actions:ready', {
            detail: { manager },
        }));
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkAccelade);
    } else {
        checkAccelade();
    }
}

// Auto-initialize
init();

// Export for module usage
export { ActionManager, initActions };
export type { ActionConfig, ActionResult, ExecuteOptions };
