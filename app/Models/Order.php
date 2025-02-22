<?php

namespace App\Models;

use App\Enum\OrderStatus;
use App\Enum\PaymentStatus;
use App\Helper\BaseModel;
use App\Observers\OrderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(OrderObserver::class)]
class Order extends BaseModel{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'number',
        'tgl',
        'jam',
        'contact_id',
        'status',
        'subtotal',
        'description',
        'user_id',
    ];

    protected $casts = [
        'status'            => OrderStatus::class,
        'payment_status'    => PaymentStatus::class,
    ];

    public function contact(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Contact::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany{
        return $this->hasMany(OrderItem::class);
    }

    public function order_payments(): \Illuminate\Database\Eloquent\Relations\HasMany{
        return $this->hasMany(OrderPayment::class);
    }

    public function histories(): \Illuminate\Database\Eloquent\Relations\HasMany{
        return $this->hasMany(OrderHistory::class);
    }
}
