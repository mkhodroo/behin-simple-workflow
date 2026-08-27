<?php

return [
    'caseStartValue' => 1000,
    'patterns' => [
        'customer_fullname',
        'device_name', 
        'device_serial_no',
        'creator',
        'case_name'
    ],
    'caseNumberingPerCategory' => true,
    'caseNumberingPerProcess' => false,
    'inboxStatus' => [
        'new' => ['label' => 'new', 'color' => 'primary', 'type' => 'open'],
        'opened' => ['label' => 'opened', 'color' => 'secondary', 'type' => 'open'],
        'inProgress' => ['label' => 'inProgress', 'color' => 'warning', 'type' => 'open'],
        'draft' => ['label' => 'draft', 'color' => 'info', 'type' => 'open'],
        'canceled' => ['label' => 'canceled', 'color' => 'danger', 'type' => 'close'],
        'done' => ['label' => 'done', 'color' => 'success', 'type' => 'close'],
        'doneByOther' => ['label' => 'doneByOther', 'color' => 'success', 'type' => 'close'],
        'doneBySystem' => ['label' => 'doneBySystem', 'color' => 'success', 'type' => 'close'],
    ],
    'caseStatus' => [
        'inProgress' => ['label' => 'inProgress', 'color' => 'primary', 'key' => 'inProgress'],
        'done' => ['label' => 'done', 'color' => 'warning', 'key' => 'done'],
        'canceled' => ['label' => 'canceled', 'color' => 'danger', 'key' => 'canceled'],
    ]
];
