<?php

return array(

    /**
     * Divide permissions in groups
     */
    'General' => array(

        // Viewing the disovery document
        'discovery.view' => 'View the discovery document',
        // Viewing datasets
        'dataset.view' => 'View datasets',
        // Viewing the info document
        'info.view' => 'View the info document',

    ),

    'Definitions' => array(
        // Definition permissions
        'definition.create' => 'Create a definition',
        'definition.view' => 'View a definition',
        'definition.update' => 'Update a definition',
        'definition.delete' => 'Delete a definition',
    ),

    /**
     * Admin permissions (UI)
     */

    'Admin: datasets' => array(
        // Dataset permissions
        'admin.dataset.view' => 'View datasets',
        'admin.dataset.create' => 'Add a dataset',
        'admin.dataset.update' => 'Update a dataset',
        'admin.dataset.delete' => 'Delete a dataset',
    ),

    'Admin: users' => array(
        // User permissions
        'admin.user.view' => 'View the users',
        'admin.user.create' => 'Add a user',
        'admin.user.update' => 'Update a user',
        'admin.user.delete' => 'Delete a user',
    ),

    'Admin: groups' => array(
        // Group permissions
        'admin.group.view' => 'View the groups',
        'admin.group.create' => 'Add a group',
        'admin.group.update' => 'Update a group',
        'admin.group.delete' => 'Delete a group',
    ),

);