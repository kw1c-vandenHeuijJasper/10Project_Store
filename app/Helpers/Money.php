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
     * 
     * @param string|integer $input
     * @return string
     */
    public static function format(string|int $input): string
    {
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
        $output = $partone . ',' . $parttwo;
        if (strlen($input) == 1) {
            $output = '0,0' . $input;
        }

        return $output;
    }

    /**
     * Prefixes any string or int with €
     *
     * @param string|integer $input
     * @return string
     */
    public static function prefix(string|int $input): string
    {
        return '€ ' . $input;
    }
}
