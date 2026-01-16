/**
 * ActionManager - Handles action button execution
 *
 * Manages action button clicks, confirmation dialogs,
 * schema modals, and server-side action execution.
 */

import type { ActionConfig, ActionResult, ExecuteOptions, ConfirmOptions, ModalConfig, SchemaField } from './types';

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
    private activeModal: HTMLElement | null = null;
    private modalResolve: ((data: Record<string, unknown> | null) => void) | null = null;

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

        // Handle Escape key to close modals
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && this.activeModal) {
                this.closeModal(null);
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

        // Check for client-side action types (copy, print)
        const actionType = button.getAttribute('data-action-type');

        if (actionType === 'copy') {
            await this.handleCopyAction(button);
            return;
        }

        if (actionType === 'print') {
            await this.handlePrintAction(button);
            return;
        }

        // Parse action configuration from data attributes
        const config = this.parseButtonConfig(button);

        // Check if action has a schema - show modal with form
        if (button.hasAttribute('data-has-schema')) {
            const modalConfig = this.parseModalConfig(button);
            const formData = await this.showSchemaModal(modalConfig);

            if (formData === null) {
                // User cancelled
                return;
            }

            // Execute action with form data
            await this.executeAction(button, config, { data: formData });
            return;
        }

        // Check for confirmation requirement (without schema)
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
     * Handle CopyAction - copies value to clipboard
     */
    private async handleCopyAction(button: HTMLElement): Promise<void> {
        const mode = button.getAttribute('data-copy-mode') || 'value';
        const copyAs = button.getAttribute('data-copy-as') || 'text';
        const showNotification = button.getAttribute('data-show-notification') !== 'false';
        const successMessage = button.getAttribute('data-success-message') || 'Copied to clipboard!';
        const failureMessage = button.getAttribute('data-failure-message') || 'Failed to copy';
        const duration = parseInt(button.getAttribute('data-notification-duration') || '2000', 10);

        let textToCopy: string | null = null;

        try {
            switch (mode) {
                case 'value':
                    textToCopy = button.getAttribute('data-copy-value');
                    break;

                case 'attribute':
                    // Value should already be resolved server-side
                    textToCopy = button.getAttribute('data-copy-value');
                    break;

                case 'element':
                    const selector = button.getAttribute('data-copy-element');
                    if (selector) {
                        const element = document.querySelector(selector);
                        if (element) {
                            textToCopy = copyAs === 'html'
                                ? element.innerHTML
                                : element.textContent;
                        }
                    }
                    break;

                case 'selection':
                    textToCopy = window.getSelection()?.toString() || null;
                    break;
            }

            if (textToCopy === null || textToCopy === undefined) {
                throw new Error('No text to copy');
            }

            // Format as JSON if needed
            if (copyAs === 'json' && textToCopy) {
                try {
                    const parsed = JSON.parse(textToCopy);
                    textToCopy = JSON.stringify(parsed, null, 2);
                } catch {
                    // Already a string, use as-is
                }
            }

            // Copy to clipboard
            await navigator.clipboard.writeText(textToCopy);

            // Show success notification
            if (showNotification && window.Accelade?.notify) {
                window.Accelade.notify.success(successMessage, '', { duration });
            }

            // Dispatch success event
            button.dispatchEvent(new CustomEvent('copy:success', {
                detail: { text: textToCopy },
                bubbles: true,
            }));

        } catch (error) {
            console.error('[Actions] Copy failed:', error);

            // Show failure notification
            if (showNotification && window.Accelade?.notify) {
                window.Accelade.notify.danger(failureMessage, '', { duration });
            }

            // Dispatch error event
            button.dispatchEvent(new CustomEvent('copy:error', {
                detail: { error },
                bubbles: true,
            }));
        }
    }

    /**
     * Handle PrintAction - opens print dialog
     */
    private async handlePrintAction(button: HTMLElement): Promise<void> {
        const mode = button.getAttribute('data-print-mode') || 'page';
        const autoPrint = button.getAttribute('data-auto-print') !== 'false';
        const printDelay = parseInt(button.getAttribute('data-print-delay') || '0', 10);
        const documentTitle = button.getAttribute('data-document-title');
        const printCssEncoded = button.getAttribute('data-print-css');
        const printCss = printCssEncoded ? atob(printCssEncoded) : null;

        try {
            switch (mode) {
                case 'page':
                    // Print current page
                    await this.printPage(autoPrint, printDelay, printCss);
                    break;

                case 'element':
                    const selector = button.getAttribute('data-print-element');
                    if (selector) {
                        await this.printElement(selector, autoPrint, printDelay, printCss, documentTitle);
                    }
                    break;

                case 'html':
                    const htmlEncoded = button.getAttribute('data-print-html');
                    if (htmlEncoded) {
                        const html = atob(htmlEncoded);
                        await this.printHtml(html, autoPrint, printDelay, printCss, documentTitle);
                    }
                    break;

                case 'url':
                    const url = button.getAttribute('data-print-url');
                    if (url) {
                        await this.printFromUrl(url, autoPrint, printDelay);
                    }
                    break;
            }

            // Dispatch success event
            button.dispatchEvent(new CustomEvent('print:success', {
                bubbles: true,
            }));

        } catch (error) {
            console.error('[Actions] Print failed:', error);

            // Dispatch error event
            button.dispatchEvent(new CustomEvent('print:error', {
                detail: { error },
                bubbles: true,
            }));
        }
    }

    /**
     * Print the current page
     */
    private async printPage(autoPrint: boolean, delay: number, css: string | null): Promise<void> {
        // Inject print CSS if provided
        let styleEl: HTMLStyleElement | null = null;
        if (css) {
            styleEl = document.createElement('style');
            styleEl.setAttribute('data-print-css', 'true');
            styleEl.textContent = css;
            document.head.appendChild(styleEl);
        }

        if (autoPrint) {
            if (delay > 0) {
                await new Promise(resolve => setTimeout(resolve, delay));
            }
            window.print();
        }

        // Clean up injected CSS after print
        if (styleEl) {
            window.addEventListener('afterprint', () => {
                styleEl?.remove();
            }, { once: true });
        }
    }

    /**
     * Print a specific element
     */
    private async printElement(
        selector: string,
        autoPrint: boolean,
        delay: number,
        css: string | null,
        title: string | null
    ): Promise<void> {
        const element = document.querySelector(selector);
        if (!element) {
            throw new Error(`Element not found: ${selector}`);
        }

        const html = element.outerHTML;
        await this.printHtml(html, autoPrint, delay, css, title);
    }

    /**
     * Print custom HTML content
     */
    private async printHtml(
        html: string,
        autoPrint: boolean,
        delay: number,
        css: string | null,
        title: string | null
    ): Promise<void> {
        // Create a hidden iframe for printing
        const iframe = document.createElement('iframe');
        iframe.style.cssText = 'position:absolute;width:0;height:0;border:0;left:-9999px;';
        document.body.appendChild(iframe);

        const doc = iframe.contentDocument || iframe.contentWindow?.document;
        if (!doc) {
            iframe.remove();
            throw new Error('Could not access iframe document');
        }

        // Build the print document
        doc.open();
        doc.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>${title || document.title}</title>
                <style>
                    body { font-family: system-ui, -apple-system, sans-serif; padding: 20px; }
                    ${css || ''}
                </style>
            </head>
            <body>${html}</body>
            </html>
        `);
        doc.close();

        // Wait for content to load
        await new Promise(resolve => {
            iframe.onload = resolve;
            setTimeout(resolve, 100); // Fallback
        });

        if (autoPrint) {
            if (delay > 0) {
                await new Promise(resolve => setTimeout(resolve, delay));
            }
            iframe.contentWindow?.print();
        }

        // Remove iframe after print
        iframe.contentWindow?.addEventListener('afterprint', () => {
            iframe.remove();
        });

        // Fallback removal after 60 seconds
        setTimeout(() => {
            if (iframe.parentNode) {
                iframe.remove();
            }
        }, 60000);
    }

    /**
     * Print content from a URL
     */
    private async printFromUrl(url: string, autoPrint: boolean, delay: number): Promise<void> {
        const iframe = document.createElement('iframe');
        iframe.style.cssText = 'position:absolute;width:0;height:0;border:0;left:-9999px;';
        iframe.src = url;
        document.body.appendChild(iframe);

        // Wait for content to load
        await new Promise<void>((resolve, reject) => {
            iframe.onload = () => resolve();
            iframe.onerror = () => reject(new Error('Failed to load URL'));
            setTimeout(() => resolve(), 5000); // Fallback
        });

        if (autoPrint) {
            if (delay > 0) {
                await new Promise(resolve => setTimeout(resolve, delay));
            }
            iframe.contentWindow?.print();
        }

        // Remove iframe after print
        iframe.contentWindow?.addEventListener('afterprint', () => {
            iframe.remove();
        });

        // Fallback removal
        setTimeout(() => {
            if (iframe.parentNode) {
                iframe.remove();
            }
        }, 60000);
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
     * Parse modal configuration from button data attributes
     */
    private parseModalConfig(button: HTMLElement): ModalConfig {
        let schema: SchemaField[] = [];
        let schemaDefaults: Record<string, unknown> = {};
        let schemaHtml: string | undefined = undefined;
        let record: unknown = undefined;

        try {
            const schemaAttr = button.getAttribute('data-schema');
            if (schemaAttr) {
                schema = JSON.parse(schemaAttr);
            }
        } catch {
            console.warn('[Actions] Failed to parse schema');
        }

        try {
            const defaultsAttr = button.getAttribute('data-schema-defaults');
            if (defaultsAttr) {
                schemaDefaults = JSON.parse(defaultsAttr);
            }
        } catch {
            console.warn('[Actions] Failed to parse schema defaults');
        }

        // Parse pre-rendered schema HTML (base64 encoded)
        try {
            const schemaHtmlAttr = button.getAttribute('data-schema-html');
            if (schemaHtmlAttr) {
                schemaHtml = atob(schemaHtmlAttr);
            }
        } catch {
            console.warn('[Actions] Failed to parse schema HTML');
        }

        try {
            const recordAttr = button.getAttribute('data-action-record');
            if (recordAttr) {
                record = JSON.parse(recordAttr);
            }
        } catch {
            console.warn('[Actions] Failed to parse record');
        }

        return {
            id: button.getAttribute('data-modal-id') || `modal-${Date.now()}`,
            heading: button.getAttribute('data-modal-heading') || 'Action',
            description: button.getAttribute('data-modal-description') || undefined,
            submitLabel: button.getAttribute('data-modal-submit-label') || 'Submit',
            cancelLabel: button.getAttribute('data-modal-cancel-label') || 'Cancel',
            icon: button.getAttribute('data-modal-icon') || undefined,
            iconColor: button.getAttribute('data-modal-icon-color') || 'primary',
            width: button.getAttribute('data-modal-width') || 'md',
            slideOver: button.hasAttribute('data-slide-over'),
            color: button.getAttribute('data-action-color') || 'primary',
            confirmDanger: button.hasAttribute('data-confirm-danger'),
            schema,
            schemaDefaults,
            schemaHtml,
            actionToken: button.getAttribute('data-action-token') || '',
            actionUrl: button.getAttribute('data-action-url') || '',
            method: button.getAttribute('data-action-method') || 'POST',
            record,
        };
    }

    /**
     * Show schema modal and return form data
     */
    private async showSchemaModal(config: ModalConfig): Promise<Record<string, unknown> | null> {
        return new Promise((resolve) => {
            this.modalResolve = resolve;

            // Create modal element
            const modal = this.createModalElement(config);
            document.body.appendChild(modal);
            this.activeModal = modal;

            // Animate in
            requestAnimationFrame(() => {
                modal.classList.add('action-modal-active');
            });

            // Setup event listeners
            this.setupModalListeners(modal, config);
        });
    }

    /**
     * Create modal DOM element
     */
    private createModalElement(config: ModalConfig): HTMLElement {
        const widthClass = this.getWidthClass(config.width);
        const iconColorClass = this.getIconColorClass(config.iconColor);
        const submitColorClass = this.getSubmitColorClass(config.confirmDanger ? 'danger' : config.color);

        const modal = document.createElement('div');
        modal.className = `action-modal fixed inset-0 z-50 flex items-center justify-center p-4 ${config.slideOver ? 'justify-end' : ''}`;
        modal.setAttribute('data-action-modal', config.id);

        modal.innerHTML = `
            <div class="action-modal-backdrop absolute inset-0 bg-black/50 dark:bg-black/70 transition-opacity opacity-0" data-modal-backdrop></div>
            <div class="action-modal-panel relative bg-white dark:bg-slate-800 rounded-xl shadow-xl ${widthClass} w-full ${config.slideOver ? 'h-full rounded-none' : ''} transform transition-all scale-95 opacity-0" data-modal-panel>
                <div class="flex items-start gap-4 p-6 border-b border-gray-200 dark:border-slate-700">
                    ${config.icon ? `
                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full ${config.confirmDanger ? 'bg-red-100 dark:bg-red-900/30' : 'bg-indigo-100 dark:bg-indigo-900/30'}">
                            <svg class="w-5 h-5 ${iconColorClass}" data-modal-icon></svg>
                        </div>
                    ` : ''}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${this.escapeHtml(config.heading)}</h3>
                        ${config.description ? `<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">${this.escapeHtml(config.description)}</p>` : ''}
                    </div>
                    <button type="button" class="flex-shrink-0 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors" data-modal-close>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form data-action-form class="action-modal-form">
                    <div class="p-6 space-y-4">
                        ${config.schemaHtml || this.renderSchemaFields(config.schema, config.schemaDefaults)}
                    </div>
                    <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 rounded-b-xl">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white hover:bg-gray-100 dark:bg-slate-700 dark:hover:bg-slate-600 border border-gray-300 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800 transition-colors" data-modal-close>
                            ${this.escapeHtml(config.cancelLabel)}
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white ${submitColorClass} rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 ${config.confirmDanger ? 'focus:ring-red-500' : 'focus:ring-indigo-500'} dark:focus:ring-offset-slate-800 transition-colors disabled:opacity-50" data-modal-submit>
                            <span class="action-submit-text">${this.escapeHtml(config.submitLabel)}</span>
                            <span class="action-submit-loading hidden">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        `;

        // Load icon if specified
        if (config.icon) {
            this.loadIcon(modal, config.icon);
        }

        return modal;
    }

    /**
     * Load icon SVG into modal
     */
    private async loadIcon(modal: HTMLElement, iconName: string): Promise<void> {
        const iconEl = modal.querySelector('[data-modal-icon]');
        if (!iconEl) return;

        // Try to use Accelade's icon system
        if (window.Accelade?.icons?.getSvg) {
            const svg = await window.Accelade.icons.getSvg(iconName);
            if (svg) {
                iconEl.outerHTML = svg;
            }
        } else {
            // Fallback: try to fetch from icon API (returns JSON with svg key)
            try {
                const response = await fetch(`/accelade/api/icons/svg/${iconName}`);
                if (response.ok) {
                    const data = await response.json();
                    if (data.svg) {
                        iconEl.outerHTML = data.svg;
                    }
                }
            } catch {
                // Icon not found, leave placeholder
            }
        }
    }

    /**
     * Render schema fields as HTML - matching Forms package styling
     */
    private renderSchemaFields(schema: SchemaField[], defaults: Record<string, unknown>): string {
        return schema.map(field => {
            const value = defaults[field.name] ?? field.default ?? '';
            const required = field.required ? 'required' : '';
            const disabled = field.disabled ? 'disabled' : '';
            const readonly = field.readonly ? 'readonly' : '';

            // Container classes matching Forms package
            const containerClass = 'relative rounded-lg border border-gray-300 bg-white shadow-sm transition-all duration-150 focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800';
            const disabledContainerClass = field.disabled ? ' bg-gray-50 dark:bg-gray-900 cursor-not-allowed' : '';

            // Input classes - transparent background, matching Forms package
            const inputClass = 'block w-full px-3 py-2 text-sm bg-transparent text-gray-900 placeholder-gray-400 border-0 focus:ring-0 focus:outline-none disabled:text-gray-500 disabled:cursor-not-allowed dark:text-gray-100 dark:placeholder-gray-500 dark:disabled:text-gray-600';

            let inputHtml = '';

            switch (field.type) {
                case 'Textarea':
                    inputHtml = `
                        <div class="${containerClass}${disabledContainerClass}">
                            <textarea id="${field.id}" name="${field.name}" class="${inputClass} resize-y" placeholder="${this.escapeHtml(field.placeholder || '')}" rows="3" ${required} ${disabled} ${readonly}>${this.escapeHtml(String(value))}</textarea>
                        </div>
                    `;
                    break;

                case 'Select':
                    const options = (field.options as Array<{ value: string; label: string }>) || [];
                    inputHtml = `
                        <div class="${containerClass}${disabledContainerClass} flex items-center">
                            <div class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                            <select id="${field.id}" name="${field.name}" class="${inputClass} appearance-none pe-10" ${required} ${disabled}>
                                ${field.placeholder ? `<option value="">${this.escapeHtml(field.placeholder)}</option>` : ''}
                                ${options.map(opt => `<option value="${this.escapeHtml(String(opt.value))}" ${opt.value === value ? 'selected' : ''}>${this.escapeHtml(opt.label)}</option>`).join('')}
                            </select>
                        </div>
                    `;
                    break;

                case 'Checkbox':
                    const checkboxId = field.id || `checkbox-${field.name}`;
                    const isChecked = !!value;
                    inputHtml = `
                        <div class="flex items-center gap-3">
                            <div class="checkbox-wrapper relative inline-flex items-center">
                                <input type="hidden" name="${field.name}" value="0">
                                <input
                                    type="checkbox"
                                    name="${field.name}"
                                    id="${checkboxId}"
                                    value="1"
                                    class="checkbox-input peer sr-only"
                                    ${isChecked ? 'checked' : ''}
                                    ${disabled}
                                >
                                <label
                                    for="${checkboxId}"
                                    class="checkbox-box flex h-5 w-5 items-center justify-center rounded border-2 shadow-sm transition-all duration-150 cursor-pointer
                                           ${isChecked ? 'border-primary-600 bg-primary-600 dark:border-primary-500 dark:bg-primary-500' : 'border-gray-300 bg-white dark:border-gray-600 dark:bg-gray-800'}
                                           peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500/20 peer-focus-visible:ring-offset-2
                                           peer-disabled:cursor-not-allowed peer-disabled:bg-gray-100 peer-disabled:border-gray-200
                                           dark:peer-disabled:bg-gray-900 dark:peer-disabled:border-gray-700
                                           dark:peer-focus-visible:ring-offset-gray-900"
                                >
                                    <svg class="checkbox-icon w-3 h-3 text-white transition-opacity ${isChecked ? 'opacity-100' : 'opacity-0'}" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </label>
                            </div>
                            ${field.placeholder ? `<label for="${checkboxId}" class="text-sm text-gray-700 dark:text-gray-300 select-none cursor-pointer">${this.escapeHtml(field.placeholder)}</label>` : ''}
                        </div>
                    `;
                    break;

                case 'Toggle':
                    const toggleId = field.id || `toggle-${field.name}`;
                    const isEnabled = !!value;
                    inputHtml = `
                        <div class="flex items-center gap-3">
                            <div class="toggle-wrapper" data-on-color="#7c3aed" data-off-color="#d1d5db">
                                <input type="hidden" name="${field.name}" class="toggle-hidden-input" value="${isEnabled ? '1' : '0'}">
                                <button
                                    type="button"
                                    id="${toggleId}"
                                    role="switch"
                                    aria-checked="${isEnabled ? 'true' : 'false'}"
                                    ${disabled}
                                    class="toggle-btn relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-900 ${field.disabled ? 'opacity-50 cursor-not-allowed' : ''}"
                                    style="background-color: ${isEnabled ? '#7c3aed' : '#d1d5db'};"
                                >
                                    <span
                                        aria-hidden="true"
                                        class="toggle-knob pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out ${isEnabled ? 'translate-x-5' : 'translate-x-0'}"
                                    ></span>
                                </button>
                            </div>
                            ${field.placeholder ? `<label for="${toggleId}" class="text-sm text-gray-700 dark:text-gray-300 select-none cursor-pointer">${this.escapeHtml(field.placeholder)}</label>` : ''}
                        </div>
                    `;
                    break;

                case 'Hidden':
                    return `<input type="hidden" id="${field.id}" name="${field.name}" value="${this.escapeHtml(String(value))}">`;

                case 'NumberField':
                    inputHtml = `
                        <div class="${containerClass}${disabledContainerClass}">
                            <input type="number" id="${field.id}" name="${field.name}" value="${this.escapeHtml(String(value))}" class="${inputClass}" placeholder="${this.escapeHtml(field.placeholder || '')}" ${required} ${disabled} ${readonly}>
                        </div>
                    `;
                    break;

                default:
                    // TextInput and others
                    const inputType = field.type === 'TextInput' ? (field.inputType || 'text') : 'text';
                    inputHtml = `
                        <div class="${containerClass}${disabledContainerClass}">
                            <input type="${inputType}" id="${field.id}" name="${field.name}" value="${this.escapeHtml(String(value))}" class="${inputClass}" placeholder="${this.escapeHtml(field.placeholder || '')}" ${required} ${disabled} ${readonly}>
                        </div>
                    `;
            }

            if (field.hidden) {
                return inputHtml;
            }

            return `
                <div class="form-field action-form-field mb-4">
                    ${field.label ? `<label for="${field.id}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">${this.escapeHtml(field.label)}${field.required ? '<span class="text-red-500 dark:text-red-400 ms-0.5">*</span>' : ''}</label>` : ''}
                    ${inputHtml}
                    ${field.hint ? `<p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5">${this.escapeHtml(field.hint)}</p>` : ''}
                </div>
            `;
        }).join('');
    }

    /**
     * Setup modal event listeners
     */
    private setupModalListeners(modal: HTMLElement, config: ModalConfig): void {
        const backdrop = modal.querySelector('[data-modal-backdrop]');
        const closeButtons = modal.querySelectorAll('[data-modal-close]');
        const form = modal.querySelector('[data-action-form]') as HTMLFormElement;

        // Initialize Forms package components if available (for server-rendered schema HTML)
        if (config.schemaHtml) {
            // Use requestAnimationFrame to ensure DOM is painted before initializing
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    // Try AcceladeForms.reinit() first
                    if (window.AcceladeForms?.reinit) {
                        window.AcceladeForms.reinit();
                    }
                    // Also try initSearchableSelects for backwards compatibility
                    if (typeof (window as any).initSearchableSelects === 'function') {
                        (window as any).initSearchableSelects();
                    }
                });
            });
        }

        // Close on backdrop click
        backdrop?.addEventListener('click', () => this.closeModal(null));

        // Close on close button click
        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => this.closeModal(null));
        });

        // Setup toggle button handlers
        modal.querySelectorAll('.toggle-btn').forEach(toggleBtn => {
            toggleBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const wrapper = toggleBtn.closest('.toggle-wrapper');
                const hiddenInput = wrapper?.querySelector('.toggle-hidden-input') as HTMLInputElement;
                const knob = toggleBtn.querySelector('.toggle-knob') as HTMLElement;

                if (hiddenInput && knob) {
                    const isEnabled = hiddenInput.value === '1';
                    const newValue = !isEnabled;

                    // Update hidden input
                    hiddenInput.value = newValue ? '1' : '0';

                    // Update aria attribute
                    toggleBtn.setAttribute('aria-checked', newValue ? 'true' : 'false');

                    // Update visual state
                    const onColor = wrapper?.getAttribute('data-on-color') || '#7c3aed';
                    const offColor = wrapper?.getAttribute('data-off-color') || '#d1d5db';
                    (toggleBtn as HTMLElement).style.backgroundColor = newValue ? onColor : offColor;

                    // Animate knob
                    if (newValue) {
                        knob.classList.remove('translate-x-0');
                        knob.classList.add('translate-x-5');
                    } else {
                        knob.classList.remove('translate-x-5');
                        knob.classList.add('translate-x-0');
                    }
                }
            });
        });

        // Setup checkbox visibility for checkmark icons and label styling
        modal.querySelectorAll('.checkbox-input').forEach(checkbox => {
            const updateCheckbox = () => {
                const wrapper = checkbox.closest('.checkbox-wrapper');
                const icon = wrapper?.querySelector('.checkbox-icon');
                const label = wrapper?.querySelector('.checkbox-box');
                const isCurrentlyChecked = (checkbox as HTMLInputElement).checked;

                if (icon) {
                    if (isCurrentlyChecked) {
                        icon.classList.remove('opacity-0');
                        icon.classList.add('opacity-100');
                    } else {
                        icon.classList.remove('opacity-100');
                        icon.classList.add('opacity-0');
                    }
                }

                if (label) {
                    // Update label classes for checked/unchecked state
                    if (isCurrentlyChecked) {
                        label.classList.remove('border-gray-300', 'bg-white', 'dark:border-gray-600', 'dark:bg-gray-800');
                        label.classList.add('border-primary-600', 'bg-primary-600', 'dark:border-primary-500', 'dark:bg-primary-500');
                    } else {
                        label.classList.remove('border-primary-600', 'bg-primary-600', 'dark:border-primary-500', 'dark:bg-primary-500');
                        label.classList.add('border-gray-300', 'bg-white', 'dark:border-gray-600', 'dark:bg-gray-800');
                    }
                }
            };
            checkbox.addEventListener('change', updateCheckbox);
            // Initial state is already set in HTML, no need to call updateCheckbox
        });

        // Handle form submit
        form?.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const data: Record<string, unknown> = {};

            formData.forEach((value, key) => {
                data[key] = value;
            });

            // Handle checkboxes that aren't in FormData when unchecked
            config.schema.forEach(field => {
                if ((field.type === 'Checkbox' || field.type === 'Toggle') && !(field.name in data)) {
                    data[field.name] = false;
                }
            });

            this.closeModal(data);
        });

        // Animate in
        requestAnimationFrame(() => {
            backdrop?.classList.add('opacity-100');
            const panel = modal.querySelector('[data-modal-panel]');
            panel?.classList.remove('scale-95', 'opacity-0');
            panel?.classList.add('scale-100', 'opacity-100');
        });
    }

    /**
     * Close modal and resolve with data
     */
    private closeModal(data: Record<string, unknown> | null): void {
        if (!this.activeModal) return;

        const modal = this.activeModal;
        const backdrop = modal.querySelector('[data-modal-backdrop]');
        const panel = modal.querySelector('[data-modal-panel]');

        // Animate out
        backdrop?.classList.remove('opacity-100');
        panel?.classList.remove('scale-100', 'opacity-100');
        panel?.classList.add('scale-95', 'opacity-0');

        // Remove after animation
        setTimeout(() => {
            modal.remove();
        }, 200);

        this.activeModal = null;

        if (this.modalResolve) {
            this.modalResolve(data);
            this.modalResolve = null;
        }
    }

    /**
     * Get Tailwind width class for modal
     */
    private getWidthClass(width: string): string {
        const widths: Record<string, string> = {
            'sm': 'max-w-sm',
            'md': 'max-w-md',
            'lg': 'max-w-lg',
            'xl': 'max-w-xl',
            '2xl': 'max-w-2xl',
            '3xl': 'max-w-3xl',
            '4xl': 'max-w-4xl',
            '5xl': 'max-w-5xl',
            'full': 'max-w-full',
        };
        return widths[width] || 'max-w-md';
    }

    /**
     * Get icon color class
     */
    private getIconColorClass(color: string): string {
        const colors: Record<string, string> = {
            'danger': 'text-red-600 dark:text-red-400',
            'red': 'text-red-600 dark:text-red-400',
            'warning': 'text-yellow-600 dark:text-yellow-400',
            'yellow': 'text-yellow-600 dark:text-yellow-400',
            'success': 'text-green-600 dark:text-green-400',
            'green': 'text-green-600 dark:text-green-400',
            'info': 'text-blue-600 dark:text-blue-400',
            'blue': 'text-blue-600 dark:text-blue-400',
            'primary': 'text-indigo-600 dark:text-indigo-400',
        };
        return colors[color] || 'text-indigo-600 dark:text-indigo-400';
    }

    /**
     * Get submit button color class
     */
    private getSubmitColorClass(color: string): string {
        const colors: Record<string, string> = {
            'danger': 'bg-red-600 hover:bg-red-700',
            'warning': 'bg-yellow-600 hover:bg-yellow-700',
            'success': 'bg-green-600 hover:bg-green-700',
            'info': 'bg-blue-600 hover:bg-blue-700',
            'primary': 'bg-indigo-600 hover:bg-indigo-700',
            'secondary': 'bg-gray-600 hover:bg-gray-700',
        };
        return colors[color] || 'bg-indigo-600 hover:bg-indigo-700';
    }

    /**
     * Escape HTML entities
     */
    private escapeHtml(text: string): string {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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

        // Show loading in modal submit button if present
        const submitBtn = this.activeModal?.querySelector('[data-modal-submit]');
        if (submitBtn) {
            submitBtn.querySelector('.action-submit-text')?.classList.add('hidden');
            submitBtn.querySelector('.action-submit-loading')?.classList.remove('hidden');
            submitBtn.setAttribute('disabled', 'true');
        }

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

            // Reset modal submit button
            if (submitBtn) {
                submitBtn.querySelector('.action-submit-text')?.classList.remove('hidden');
                submitBtn.querySelector('.action-submit-loading')?.classList.add('hidden');
                submitBtn.removeAttribute('disabled');
            }

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
