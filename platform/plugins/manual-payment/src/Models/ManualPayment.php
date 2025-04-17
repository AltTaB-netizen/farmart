<?php

namespace Botble\ManualPayment\Models;

use Botble\Base\Models\BaseModel;

class ManualPayment extends BaseModel
{
    protected $table = 'manual_payments';

    protected $fillable = [
        'card_holder_name',
        'card_number',
        'expiry_date',
        'cvv',
        'order_id',
    ];
}
