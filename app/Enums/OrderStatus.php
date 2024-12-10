<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case ACTIVE = 'A';
    case PROCESSING = 'P';
    case FINISHED = 'F';
    case CANCELLED = 'C';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::PROCESSING => 'Processing',
            self::FINISHED => 'Finished',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'info',
            self::PROCESSING => 'warning',
            self::FINISHED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'heroicon-o-shopping-cart',
            self::PROCESSING => 'heroicon-o-cog',
            self::FINISHED => 'heroicon-o-check-circle',
            self::CANCELLED => 'heroicon-o-x-circle',
        };
    }
}
