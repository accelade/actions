<?php

return [
    // Action labels (legacy, kept for compatibility)
    'view' => 'View',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'create' => 'Create',
    'restore' => 'Restore',
    'force_delete' => 'Force Delete',
    'export' => 'Export',
    'import' => 'Import',

    // Button labels
    'buttons' => [
        'view' => 'View',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'create' => 'Create',
        'restore' => 'Restore',
        'force_delete' => 'Force Delete',
        'replicate' => 'Replicate',
        'export' => 'Export',
        'import' => 'Import',
        'print' => 'Print',
        'copy' => 'Copy',
    ],

    // Bulk actions
    'bulk_actions' => 'Bulk Actions',

    // Modal strings
    'modal' => [
        'confirm_title' => 'Confirm Action',
        'confirm_action' => 'Confirm :action',
        'confirm_description' => 'Are you sure you want to proceed with this action?',
        'confirm' => 'Confirm',
        'cancel' => 'Cancel',
        'close' => 'Close',
        'create' => 'Create',
        'save' => 'Save',

        // Delete
        'delete_heading' => 'Delete Record',
        'delete_description' => 'Are you sure you want to delete this record? This action cannot be undone.',
        'delete_confirm' => 'Delete',

        // Bulk Delete
        'bulk_delete_title' => 'Delete Selected Records',
        'bulk_delete_description' => 'Are you sure you want to delete the selected records? This action cannot be undone.',

        // Force Delete
        'force_delete_title' => 'Permanently Delete Record',
        'force_delete_description' => 'Are you sure you want to permanently delete this record? This action cannot be undone and the record cannot be recovered.',

        // Bulk Force Delete
        'bulk_force_delete_title' => 'Permanently Delete Selected Records',
        'bulk_force_delete_description' => 'Are you sure you want to permanently delete the selected records? This action cannot be undone and the records cannot be recovered.',

        // Restore
        'restore_title' => 'Restore Record',
        'restore_description' => 'Are you sure you want to restore this record?',

        // Bulk Restore
        'bulk_restore_title' => 'Restore Selected Records',
        'bulk_restore_description' => 'Are you sure you want to restore the selected records?',

        // Replicate
        'replicate_title' => 'Replicate Record',
        'replicate_description' => 'Are you sure you want to create a copy of this record?',

        // Import
        'import_title' => 'Import Data',
        'import_description' => 'Upload a file to import data.',
    ],

    // Notification strings
    'notifications' => [
        'created' => 'Created successfully',
        'updated' => 'Saved successfully',
        'deleted' => 'Deleted successfully',
        'restored' => 'Restored successfully',
        'replicated' => 'Replicated successfully',
        'force_deleted' => 'Permanently deleted',
        'bulk_deleted' => ':count record(s) deleted successfully',
        'bulk_restored' => ':count record(s) restored successfully',
        'bulk_force_deleted' => ':count record(s) permanently deleted',
        'exported' => 'Export completed',
        'imported' => 'Import completed',
        'copied' => 'Copied to clipboard',
        'copy_failed' => 'Failed to copy to clipboard',
        'print_started' => 'Print dialog opened',
    ],

    // Error messages
    'errors' => [
        'missing_token' => 'Action token is missing.',
        'invalid_token' => 'Invalid action token.',
        'invalid_payload' => 'Invalid action payload.',
        'action_expired' => 'This action has expired. Please refresh the page.',
        'execution_failed' => 'Action execution failed.',
        'unauthorized' => 'You are not authorized to perform this action.',
        'no_selection' => 'No records selected.',
    ],

    // Success messages
    'success' => [
        'deleted' => 'Record deleted successfully.',
        'created' => 'Record created successfully.',
        'updated' => 'Record updated successfully.',
        'restored' => 'Record restored successfully.',
        'replicated' => 'Record replicated successfully.',
        'force_deleted' => 'Record permanently deleted.',
    ],
];
