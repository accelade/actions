<?php

return [
    // Action labels
    'view' => 'View',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'create' => 'Create',
    'restore' => 'Restore',
    'force_delete' => 'Force Delete',
    'export' => 'Export',
    'import' => 'Import',

    // Modal strings
    'modal' => [
        'confirm_title' => 'Confirm Action',
        'confirm_action' => 'Confirm :action',
        'confirm_description' => 'Are you sure you want to proceed with this action?',
        'confirm' => 'Confirm',
        'cancel' => 'Cancel',
        'delete_heading' => 'Delete Record',
        'delete_description' => 'Are you sure you want to delete this record? This action cannot be undone.',
        'delete_confirm' => 'Delete',
    ],

    // Error messages
    'errors' => [
        'missing_token' => 'Action token is missing.',
        'invalid_token' => 'Invalid action token.',
        'invalid_payload' => 'Invalid action payload.',
        'action_expired' => 'This action has expired. Please refresh the page.',
        'execution_failed' => 'Action execution failed.',
        'unauthorized' => 'You are not authorized to perform this action.',
    ],

    // Success messages
    'success' => [
        'deleted' => 'Record deleted successfully.',
        'created' => 'Record created successfully.',
        'updated' => 'Record updated successfully.',
        'restored' => 'Record restored successfully.',
    ],
];
