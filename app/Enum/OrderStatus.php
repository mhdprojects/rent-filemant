<?php

namespace App\Enum;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus : string implements HasColor, HasIcon, HasLabel{

    case New = 'new';

    case Processing = 'processing';

    case Done = 'done';

    case Partial = 'partial';

    case Cancelled = 'cancelled';

    public function getColor(): string|array|null{
        return match ($this) {
            self::New => 'info',
            self::Processing, self::Partial => 'warning',
            self::Done=> 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): ?string{
        return match ($this) {
            self::New => 'heroicon-m-sparkles',
            self::Processing => 'heroicon-m-arrow-path',
            self::Partial => 'heroicon-m-truck',
            self::Done => 'heroicon-m-check-badge',
            self::Cancelled => 'heroicon-m-x-circle',
        };
    }

    public function getLabel(): ?string{
        return match ($this) {
            self::New => 'Baru',
            self::Processing => 'Proses',
            self::Partial => 'Sebagian',
            self::Done => 'Selesai',
            self::Cancelled => 'Batal',
        };
    }
}
