<?php

namespace App\Models;

use App\Helper\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends BaseModel {
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'image',
    ];
}
