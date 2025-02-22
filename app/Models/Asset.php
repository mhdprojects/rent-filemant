<?php

namespace App\Models;

use App\Helper\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends BaseModel {
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'brand_id',
        'type_model_id',
        'warna',
        'tahun',
        'is_partner',
        'contact_id',
        'images',
        'description',
        'is_active',
        'stock',
    ];

    protected $casts = [
        'images'    => 'array',
    ];

    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Brand::class);
    }

    public function typeModel(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(TypeModel::class);
    }

    public function contact(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Contact::class);
    }

    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany{
        return $this->hasMany(AssetVariant::class);
    }
}
