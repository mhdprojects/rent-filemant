<?php

namespace App\Models;

use App\Helper\BaseModel;
use App\Observers\OrderPaymentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(OrderPaymentObserver::class)]
class OrderPayment extends BaseModel{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'tgl',
        'number',
        'payment_method_id',
        'nominal',
        'description',
    ];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(PaymentMethod::class);
    }
}
