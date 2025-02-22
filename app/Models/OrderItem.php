<?php

namespace App\Models;

use App\Observers\OrderItemObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(OrderItemObserver::class)]
class OrderItem extends Model{
    use HasFactory, HasUuids;

    protected $fillable = [
        'order_id',
        'asset_id',
        'asset_variant_id',
        'qty',
        'price',
        'duration',
        'period_in',
        'subtotal',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'terlambat',
        'denda',
        'total_denda',
        'total',
        'sudah_kembali',
        'tgl_kembali',
        'sort',
    ];

    public function asset(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Asset::class);
    }

    public function assetVariant(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(AssetVariant::class);
    }
}
