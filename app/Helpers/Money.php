<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class Money
{
    public function __construct()
    {
        //
    }

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

    public static function toInteger(string $input): int
    {

        $withoutCommas = Str::of($input)
            ->explode(
                ','
            )->toArray();
        $withoutDots = Str::of(implode($withoutCommas))
            ->explode('.')
            ->toArray();

        return implode($withoutDots);
    }
}
