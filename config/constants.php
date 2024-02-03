<?php

return [
    'siteStatus' => [
        'development' => 'Development',
        'production' => 'Production'
    ],


    'developerUnChangableStatus' => [
        0,
        1,
        4,
    ],

    'testerUnChangableStatus' => [

        2,
        5,
        3,
    ],

    'ticketStatus' => [
        1 => 'Open',
        2 => 'In Progress',
        3 => 'Fixed',
        4 => 'Re Opened',
        5 => 'Intended',
        0 => 'Closed',
    ],

    'isActiveStatus' => [
        0 => 'Inactive',
        1 => 'Active'
    ],

    'priorityTypes' => [
        1 => 'LOW',
        2 => 'MEDIUM',
        3 => 'HIGH',
        4 => 'CRITICAL',
    ],

    'taskTypes' => [
        1 => 'UIUX',
        2 => 'FUNCTIONAL',
        3 => 'SUGGESTION',
    ]
];
