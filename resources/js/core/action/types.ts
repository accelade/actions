/**
 * Action Types
 *
 * Type definitions for the Actions package.
 */

/**
 * HTTP methods supported by actions
 */
export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';

/**
 * Action button configuration
 */
export interface ActionConfig {
    /** Action name/identifier */
    name: string;

    /** Display label */
    label?: string;

    /** Color scheme */
    color?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info';

    /** Icon name */
    icon?: string;

    /** Icon position relative to label */
    iconPosition?: 'before' | 'after';

    /** Target URL for navigation */
    url?: string;

    /** Open URL in new tab */
    openUrlInNewTab?: boolean;

    /** Requires user confirmation before execution */
    requiresConfirmation?: boolean;

    /** Has a modal dialog */
    hasModal?: boolean;

    /** Modal heading text */
    modalHeading?: string;

    /** Modal description text */
    modalDescription?: string;

    /** Modal submit button label */
    modalSubmitActionLabel?: string;

    /** Modal cancel button label */
    modalCancelActionLabel?: string;

    /** Modal icon */
    modalIcon?: string;

    /** Modal icon color */
    modalIconColor?: string;

    /** Modal width */
    modalWidth?: string;

    /** Use danger styling for confirmation */
    confirmDanger?: boolean;

    /** Display as slide-over panel */
    slideOver?: boolean;

    /** Whether action is hidden */
    isHidden?: boolean;

    /** Whether action is disabled */
    isDisabled?: boolean;

    /** Use outlined button style */
    isOutlined?: boolean;

    /** Button size */
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';

    /** Button variant */
    variant?: 'button' | 'link' | 'icon';

    /** Tooltip text */
    tooltip?: string;

    /** Extra HTML attributes */
    extraAttributes?: Record<string, string>;

    /** Has server-side action closure */
    hasAction?: boolean;

    /** Action execution URL */
    actionUrl?: string;

    /** Encrypted action token */
    actionToken?: string;

    /** Preserve component state after action */
    preserveState?: boolean;

    /** Preserve scroll position after action */
    preserveScroll?: boolean;

    /** HTTP method for action */
    method?: HttpMethod;

    /** Use SPA navigation */
    spa?: boolean;

    /** Record data parsed from button data attribute */
    record?: unknown;
}

/**
 * Notification data from action response
 */
export interface ActionNotification {
    /** Notification ID */
    id?: string;

    /** Notification title */
    title?: string;

    /** Notification body/message */
    body?: string;
    message?: string;

    /** Notification type/status */
    status?: 'success' | 'info' | 'warning' | 'danger';
    type?: 'success' | 'info' | 'warning' | 'danger';
}

/**
 * Action execution result
 */
export interface ActionResult {
    /** Whether execution was successful */
    success: boolean;

    /** Result message */
    message?: string;

    /** Redirect URL */
    redirect?: string;

    /** Notifications to display */
    notifications?: ActionNotification[];

    /** Additional data */
    data?: Record<string, unknown>;
}

/**
 * Confirmation dialog options
 */
export interface ConfirmOptions {
    /** Dialog title */
    title?: string;

    /** Dialog message */
    text: string;

    /** Confirm button label */
    confirmButton: string;

    /** Cancel button label */
    cancelButton: string;

    /** Use danger styling */
    danger: boolean;
}

/**
 * Action execution options
 */
export interface ExecuteOptions {
    /** Record data to pass to action */
    record?: unknown;

    /** Additional form data */
    data?: Record<string, unknown>;

    /** Custom headers */
    headers?: Record<string, string>;

    /** Callback on success */
    onSuccess?: (result: ActionResult) => void;

    /** Callback on error */
    onError?: (error: Error) => void;

    /** Callback when complete (success or error) */
    onComplete?: () => void;
}

/**
 * Schema field definition
 */
export interface SchemaField {
    /** Field type */
    type: string;

    /** Field name */
    name: string;

    /** Field ID */
    id: string;

    /** Display label */
    label?: string;

    /** Placeholder text */
    placeholder?: string;

    /** Hint/helper text */
    hint?: string;

    /** Default value */
    default?: unknown;

    /** Is required */
    required?: boolean;

    /** Is disabled */
    disabled?: boolean;

    /** Is readonly */
    readonly?: boolean;

    /** Is hidden */
    hidden?: boolean;

    /** Validation rules */
    rules?: string[];

    /** Additional field-specific options */
    [key: string]: unknown;
}

/**
 * Modal configuration parsed from button
 */
export interface ModalConfig {
    /** Modal ID */
    id: string;

    /** Modal heading */
    heading: string;

    /** Modal description */
    description?: string;

    /** Submit button label */
    submitLabel: string;

    /** Cancel button label */
    cancelLabel: string;

    /** Modal icon */
    icon?: string;

    /** Icon color */
    iconColor: string;

    /** Modal width */
    width: string;

    /** Show as slide-over */
    slideOver: boolean;

    /** Action color */
    color: string;

    /** Use danger styling */
    confirmDanger: boolean;

    /** Schema fields */
    schema: SchemaField[];

    /** Default values for schema fields */
    schemaDefaults: Record<string, unknown>;

    /** Pre-rendered schema HTML (server-side rendered using Forms package) */
    schemaHtml?: string;

    /** Action token */
    actionToken: string;

    /** Action URL */
    actionUrl: string;

    /** HTTP method */
    method: string;

    /** Record data */
    record?: unknown;
}
