<?php

namespace App\Models;

use App\Helper\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeModel extends BaseModel{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'tenant_id',
        'brand_id',
    ];

    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Brand::class);
    }
}
