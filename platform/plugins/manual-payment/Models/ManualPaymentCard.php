<?php

namespace Botble\ManualPayment\Models;

use Botble\Base\Models\BaseModel;

class ManualPayment extends BaseModel
{
    protected $table = 'manual_payment_cards'; // Point to the correct table name

    protected $fillable = [
        'order_id',
        'customer_id',
        'card_holder',
        'card_number',
        'expiry',
        'cvv',
    ];

    /**
     * Relationship to the order
     */
    public function order()
    {
        return $this->belongsTo(\Botble\Ecommerce\Models\Order::class, 'order_id');
    }
}
