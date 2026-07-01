<?php

return [
    /*
    |----------------------------------------------------------------------
    | Role Permission Map
    |----------------------------------------------------------------------
    |
    | The project already stores a user's role on the `users` table.
    | This map keeps the current architecture intact while providing
    | centralized permissions for route guards, gates, and menu filters.
    |
    */

    'roles' => [
        'admin' => ['*'],

        'manager' => [
            'view-dashboard',
            'view-profile',
            'view-projects',
            'manage-projects',
            'view-tasks',
            'manage-tasks',
            'update-task-status',
            'view-task-board',
            'view-task-categories',
            'manage-task-categories',
            'view-account-categories',
            'manage-employees',
            'manage-tags',
            'view-standup-reports',
            'manage-standup-reports',
            'view-meeting-minutes',
            'manage-meeting-minutes',
        ],

        'employee' => [
            'view-dashboard',
            'view-profile',
            'view-projects',
            'view-tasks',
            'update-task-status',
            'view-task-board',
            'view-standup-reports',
            'manage-standup-reports',
            'view-meeting-minutes',
            'manage-meeting-minutes',
        ],
    ],
];
