<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Convert INR decimal amount into words using the Indian numbering system.
     */
    public static function inWords(float $amount): string
    {
        $amount = round($amount, 2);
        $number = (int) floor($amount);
        $fraction = (int) round(($amount - $number) * 100);

        $words = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
            5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
            14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
            18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
            80 => 'Eighty', 90 => 'Ninety'
        ];

        $crore = floor($number / 10000000);
        $number %= 10000000;
        $lakh = floor($number / 100000);
        $number %= 100000;
        $thousand = floor($number / 1000);
        $number %= 1000;
        $hundred = floor($number / 100);
        $rest = $number % 100;

        $str = [];
        if ($crore > 0) {
            $str[] = self::twoDigitsToWords((int)$crore, $words) . ' Crore';
        }
        if ($lakh > 0) {
            $str[] = self::twoDigitsToWords((int)$lakh, $words) . ' Lakh';
        }
        if ($thousand > 0) {
            $str[] = self::twoDigitsToWords((int)$thousand, $words) . ' Thousand';
        }
        if ($hundred > 0) {
            $str[] = $words[$hundred] . ' Hundred';
        }
        if ($rest > 0) {
            $str[] = self::twoDigitsToWords((int)$rest, $words);
        }

        $res = !empty($str) ? 'Rupees ' . implode(' ', $str) : 'Rupees Zero';

        if ($fraction > 0) {
            $res .= ' and ' . self::twoDigitsToWords((int)$fraction, $words) . ' Paise';
        }

        return $res . ' Only';
    }

    private static function twoDigitsToWords(int $num, array $words): string
    {
        if ($num < 20) {
            return $words[$num] ?? '';
        }
        $tens = (int) (floor($num / 10) * 10);
        $units = $num % 10;
        return trim(($words[$tens] ?? '') . ' ' . ($words[$units] ?? ''));
    }
}
