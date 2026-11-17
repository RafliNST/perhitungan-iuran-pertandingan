<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PAID => 'Paid',
        };
    }
}
