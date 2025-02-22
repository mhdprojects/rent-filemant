<?php

namespace App\Models;

use App\Helper\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends BaseModel {
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'alamat',
        'image',
        'is_customer',
        'is_partner',
        'description',
    ];
}
