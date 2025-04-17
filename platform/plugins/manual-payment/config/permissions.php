<?php

return [
    [
        'name' => 'Manual Payment',
        'flag' => 'manual-payment.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'manual-payment.create',
        'parent_flag' => 'manual-payment.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'manual-payment.edit',
        'parent_flag' => 'manual-payment.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'manual-payment.destroy',
        'parent_flag' => 'manual-payment.index',
    ],
];
