<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tenant extends Model{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'logo',
    ];

    public function members(): BelongsToMany{
        return $this->belongsToMany(User::class);
    }

    public function users(): BelongsToMany{
        return $this->belongsToMany(User::class);
    }

    public function brands(): BelongsToMany{
        return $this->belongsToMany(Brand::class);
    }

    public function typeModels(): BelongsToMany{
        return $this->belongsToMany(TypeModel::class);
    }

    public function contacts(): BelongsToMany{
        return $this->belongsToMany(Contact::class);
    }

    public function assets(): BelongsToMany{
        return $this->belongsToMany(Asset::class);
    }

    public function orders(): BelongsToMany{
        return $this->belongsToMany(Order::class);
    }

    public function paymentMethods(): BelongsToMany{
        return $this->belongsToMany(PaymentMethod::class);
    }

    public function orderPayments(): BelongsToMany{
        return $this->belongsToMany(OrderPayment::class);
    }

    public function expenseCategories(): BelongsToMany{
        return $this->belongsToMany(ExpenseCategory::class);
    }

    public function expenses(): BelongsToMany{
        return $this->belongsToMany(Expense::class);
    }
}
