<?php

namespace App\Support;

/**
 * Montos en centavos para no sumar con float.
 * decimal(10,2) cabe en un entero de centavos sin perder el centavo.
 */
final class Money
{
    public static function cents(mixed $value): int
    {
        if (! is_numeric($value)) {
            return 0;
        }

        return (int) round(((float) $value) * 100);
    }

    public static function format(int $cents): string
    {
        $sign = $cents < 0 ? '-' : '';
        $cents = abs($cents);

        return $sign.intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function add(mixed $left, mixed $right): string
    {
        return self::format(self::cents($left) + self::cents($right));
    }
}
