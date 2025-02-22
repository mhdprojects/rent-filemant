<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetVariant extends Model{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'name',
        'price',
        'duration',
        'period_in',
        'sort',
    ];

    public function asset(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Asset::class);
    }
}
