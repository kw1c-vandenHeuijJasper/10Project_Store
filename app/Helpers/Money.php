<?php

namespace App\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Money
{
    /**
     * Formats int or string to a standard money format
     */
    public static function format(string|int $input): string
    {
        $input == null ? $input = '0' : $input;
        $inputWithoutSpaces = (string) str($input)->remove(' ');

        $cents = substr($inputWithoutSpaces, -2);
        $cents = strlen($cents) == 1 ? 0 .$cents : $cents;

        $trimmedInput = $inputWithoutSpaces[0] === '0' ? ltrim($inputWithoutSpaces, '0') : $inputWithoutSpaces;

        $euros = (string) Str::of($trimmedInput)->chopEnd($cents);
        $euros = $euros == '' || $euros == $inputWithoutSpaces ? '0' : $euros;

        $eurosWithCommas = number_format($euros);

        $output = $eurosWithCommas.'.'.$cents;

        return $output;
    }

    /**
     * Prefixes any string or int with €
     */
    public static function prefix(string|int|null $input = null): string
    {
        return '€'.$input;
    }

    /**
     * Prefixes and formats
     */
    public static function prefixFormat(string|int $input = ''): string
    {
        return self::prefix(self::format($input));
    }

    /**
     * Converts already formatted prices to integers
     */
    public static function toInteger(string $input): int
    {
        $withoutCommas = Str::of($input)
            ->explode(',')
            ->toArray();
        $withoutDots = Str::of(implode($withoutCommas))
            ->explode('.')
            ->toArray();

        return round(implode($withoutDots), 0, PHP_ROUND_HALF_EVEN);
    }

    /**
     * Returns standard widget htmlstring styling
     */
    public static function HtmlString(string|int $input, bool $prefixed = false): HtmlString
    {
        $prefixed = $prefixed == true ? Money::prefix() : false;

        return new HtmlString(
            '<span style=color:lime;>'.$prefixed.'</span>'.
                '<span style=color:lime;text-decoration:underline;>'.$input.'</span>'
        );
    }
}
