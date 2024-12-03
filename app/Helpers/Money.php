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
        //TODO improve code
        $input = str($input)->remove(' ')->toString();
        (string) $parttwo = substr($input, -2);

        if ($input[0] === '0') {
            $trimmed_input = ltrim($input, '0');
        } else {
            $trimmed_input = $input;
        }
        $partone = Str::of($trimmed_input)->chopEnd($parttwo);
        if ($partone == '' || $partone == $input) {
            $partone = '0';
        }
        $output = $partone.','.$parttwo;
        if (strlen($input) == 1) {
            $output = '0,0'.$input;
        }

        return $output;
    }

    /**
     * Prefixes any string or int with €
     */
    public static function prefix(string|int|null $input = null): string
    {
        return '€ '.$input;
    }
}
