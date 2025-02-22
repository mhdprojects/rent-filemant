<?php

namespace App\Models;

use App\Helper\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends BaseModel{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'number',
        'tgl',
        'contact_id',
        'asset_id',
        'expense_category_id',
        'nominal',
        'payment_method_id',
        'description',
        'images',
    ];

    protected $casts = [
        'images'    => 'array',
    ];

    public function contact(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Contact::class);
    }

    public function expenseCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function asset(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(Asset::class);
    }

    public function paymentMethod(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(PaymentMethod::class);
    }
}
