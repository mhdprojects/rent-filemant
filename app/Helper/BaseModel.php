<?php

namespace App\Helper;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaseModel extends Model{

    use HasFactory, HasUuids;

    public function tenant(): BelongsTo{
        return $this->belongsTo(Tenant::class);
    }
}
