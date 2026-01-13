/**
 * ActionManager - Handles action button execution
 *
 * Manages action button clicks, confirmation dialogs,
 * and server-side action execution.
 */

import type { ActionConfig, ActionResult, ExecuteOptions, ConfirmOptions } from './types';

/**
 * Default confirmation dialog options
 */
const defaultConfirmOptions: ConfirmOptions = {
    text: 'Are you sure you want to continue?',
    confirmButton: 'Confirm',
    cancelButton: 'Cancel',
    danger: false,
};

/**
 * ActionManager class
 */
export class ActionManager {
    private static instance: ActionManager | null = null;

    private constructor() {
        // Initialize event listeners
        this.initEventListeners();
    }

    /**
     * Get singleton instance
     */
    public static getInstance(): ActionManager {
        if (!ActionManager.instance) {
            ActionManager.instance = new ActionManager();
        }
        return ActionManager.instance;
    }

    /**
     * Initialize global event listeners
     */
    private initEventListeners(): void {
        // Delegate click events for action buttons
        document.addEventListener('click', (event) => {
            const target = event.target as HTMLElement;
            const button = target.closest('[data-action-button]') as HTMLElement;

            if (button) {
                event.preventDefault();
                void this.handleButtonClick(button);
            }
        });
    }

    /**
     * Handle action button click
     */
    private async handleButtonClick(button: HTMLElement): Promise<void> {
        // Check if already loading
        if (button.hasAttribute('data-loading')) {
            return;
        }

        // Parse action configuration from data attributes
        const config = this.parseButtonConfig(button);

        // Check for confirmation requirement
        if (config.requiresConfirmation) {
            const confirmed = await this.showConfirmDialog({
                title: config.modalHeading,
                text: config.modalDescription || defaultConfirmOptions.text,
                confirmButton: config.modalSubmitActionLabel || defaultConfirmOptions.confirmButton,
                cancelButton: config.modalCancelActionLabel || defaultConfirmOptions.cancelButton,
                danger: config.confirmDanger || false,
            });

            if (!confirmed) {
                return;
            }
        }

        // Execute the action
        await this.executeAction(button, config);
    }

    /**
     * Parse action configuration from button data attributes
     */
    private parseButtonConfig(button: HTMLElement): ActionConfig {
        // Parse record from data attribute if present
        let record: unknown = undefined;
        const recordAttr = button.getAttribute('data-action-record');
        if (recordAttr) {
            try {
                record = JSON.parse(recordAttr);
            } catch {
                console.warn('[Actions] Failed to parse record data');
            }
        }

        return {
            name: button.getAttribute('data-action-name') || 'action',
            actionUrl: button.getAttribute('data-action-url') || undefined,
            actionToken: button.getAttribute('data-action-token') || undefined,
            method: (button.getAttribute('data-action-method') as ActionConfig['method']) || 'POST',
            requiresConfirmation: button.hasAttribute('data-confirm'),
            modalHeading: button.getAttribute('data-confirm-title') || undefined,
            modalDescription: button.getAttribute('data-confirm') || undefined,
            modalSubmitActionLabel: button.getAttribute('data-confirm-button') || undefined,
            modalCancelActionLabel: button.getAttribute('data-cancel-button') || undefined,
            confirmDanger: button.hasAttribute('data-confirm-danger'),
            preserveScroll: button.hasAttribute('data-preserve-scroll'),
            preserveState: button.hasAttribute('data-preserve-state'),
            hasAction: !!button.getAttribute('data-action-token'),
            record,
        };
    }

    /**
     * Execute an action
     */
    public async executeAction(
        button: HTMLElement,
        config: ActionConfig,
        options: ExecuteOptions = {}
    ): Promise<ActionResult> {
        if (!config.actionUrl || !config.actionToken) {
            console.warn('[Actions] Missing action URL or token');
            return { success: false, message: 'Missing action configuration' };
        }

        // Set loading state
        button.setAttribute('data-loading', 'true');

        try {
            // Get CSRF token
            const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ||
                document.querySelector<HTMLInputElement>('input[name="_token"]')?.value;

            // Build request
            const headers: Record<string, string> = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                ...(options.headers || {}),
            };

            const method = (config.method || 'POST').toUpperCase();
            const isBodylessMethod = method === 'GET' || method === 'HEAD';

            // Use record from options if provided, otherwise use record from config (parsed from data attribute)
            const record = options.record ?? config.record;

            let url = config.actionUrl;
            let body: string | undefined;

            if (isBodylessMethod) {
                // For GET/HEAD, send data as query parameters
                const params = new URLSearchParams();
                params.set('action_token', config.actionToken);
                if (record !== undefined) {
                    params.set('record', JSON.stringify(record));
                }
                if (options.data && Object.keys(options.data).length > 0) {
                    params.set('data', JSON.stringify(options.data));
                }
                const separator = url.includes('?') ? '&' : '?';
                url = `${url}${separator}${params.toString()}`;
            } else {
                // For POST/PUT/PATCH/DELETE, send as JSON body
                body = JSON.stringify({
                    action_token: config.actionToken,
                    record,
                    data: options.data || {},
                });
            }

            // Execute request
            const response = await fetch(url, {
                method,
                headers,
                ...(body ? { body } : {}),
            });

            const result: ActionResult = await response.json();

            // Handle success
            if (result.success) {
                // Handle redirect first - if redirecting, notifications will come from session
                if (result.redirect) {
                    if (window.Accelade?.router) {
                        window.Accelade.router.navigate(result.redirect, {
                            preserveScroll: config.preserveScroll,
                            preserveState: config.preserveState,
                        });
                    } else {
                        window.location.href = result.redirect;
                    }
                } else {
                    // Only show notifications if NOT redirecting (otherwise they come from session)
                    if (result.notifications && result.notifications.length > 0 && window.Accelade?.notify) {
                        for (const notif of result.notifications) {
                            const type = notif.status || notif.type || 'success';
                            const title = notif.title || '';
                            const message = notif.body || notif.message || '';
                            if (typeof window.Accelade.notify[type] === 'function') {
                                window.Accelade.notify[type](title, message);
                            } else {
                                window.Accelade.notify.success(title, message);
                            }
                        }
                    } else if (result.message && window.Accelade?.notify) {
                        // Fallback: show simple message notification
                        window.Accelade.notify.success('Success', result.message);
                    }
                }

                // Dispatch success event
                button.dispatchEvent(new CustomEvent('action:success', {
                    detail: result,
                    bubbles: true,
                }));

                options.onSuccess?.(result);
            } else {
                // Show error notification
                if (result.message && window.Accelade?.notify) {
                    window.Accelade.notify.danger('Error', result.message);
                }

                // Dispatch error event
                button.dispatchEvent(new CustomEvent('action:error', {
                    detail: result,
                    bubbles: true,
                }));

                options.onError?.(new Error(result.message || 'Action failed'));
            }

            return result;
        } catch (error) {
            console.error('[Actions] Execution failed:', error);

            // Show error notification
            if (window.Accelade?.notify) {
                window.Accelade.notify.danger('Error', 'An unexpected error occurred');
            }

            const errorResult: ActionResult = {
                success: false,
                message: error instanceof Error ? error.message : 'Unknown error',
            };

            options.onError?.(error instanceof Error ? error : new Error('Unknown error'));

            return errorResult;
        } finally {
            // Remove loading state
            button.removeAttribute('data-loading');
            options.onComplete?.();
        }
    }

    /**
     * Show confirmation dialog using Accelade's confirm system
     */
    private async showConfirmDialog(options: Partial<ConfirmOptions>): Promise<boolean> {
        const opts: ConfirmOptions = { ...defaultConfirmOptions, ...options };

        // Use Accelade's confirm dialog if available
        if (window.Accelade?.confirm) {
            return window.Accelade.confirm.show({
                title: opts.title,
                text: opts.text,
                confirmButton: opts.confirmButton,
                cancelButton: opts.cancelButton,
                danger: opts.danger,
            }).then((r: { confirmed: boolean }) => r.confirmed);
        }

        // Fallback to native confirm
        return window.confirm(opts.text);
    }

    /**
     * Programmatically trigger an action
     */
    public async trigger(
        selector: string | HTMLElement,
        options: ExecuteOptions = {}
    ): Promise<ActionResult> {
        const button = typeof selector === 'string'
            ? document.querySelector<HTMLElement>(selector)
            : selector;

        if (!button) {
            console.warn('[Actions] Button not found:', selector);
            return { success: false, message: 'Button not found' };
        }

        const config = this.parseButtonConfig(button);
        return this.executeAction(button, config, options);
    }
}

/**
 * Initialize the action manager
 */
export function initActions(): ActionManager {
    return ActionManager.getInstance();
}

export default ActionManager;
