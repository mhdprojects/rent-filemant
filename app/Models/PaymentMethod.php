<?php

namespace App\Models;

use App\Helper\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends BaseModel {
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'is_default',
    ];
}
