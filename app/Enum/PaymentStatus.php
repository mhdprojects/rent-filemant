<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus : string implements HasColor, HasIcon, HasLabel{

    case Unpaid = 'unpaid';
    case Partial = 'partial';
    case Paid = 'paid';
    case Refund = 'refund';

    public function getColor(): string|array|null{
        return match ($this) {
            self::Unpaid => 'danger',
            self::Partial => 'warning',
            self::Paid => 'success',
            self::Refund => 'orange',
        };
    }

    public function getIcon(): ?string{
        return match ($this) {
            self::Unpaid => 'heroicon-m-sparkles',
            self::Partial => 'heroicon-m-arrow-path',
            self::Paid => 'heroicon-m-check-badge',
            self::Refund => 'heroicon-m-x-circle',
        };
    }

    public function getLabel(): ?string{
        return match ($this) {
            self::Unpaid => 'Belum Bayar',
            self::Partial => 'Sebagian',
            self::Paid => 'Terbayar',
            self::Refund => 'Refund',
        };
    }
}
