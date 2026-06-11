<?php

namespace App\Services;

class NumberToWordsService
{
    private array $ones = [
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
    ];

    private array $tens = [
        2 => 'Twenty',
        3 => 'Thirty',
        4 => 'Forty',
        5 => 'Fifty',
        6 => 'Sixty',
        7 => 'Seventy',
        8 => 'Eighty',
        9 => 'Ninety',
    ];

    public function rupees(float $amount): string
    {
        $rupees = (int) round($amount);

        if ($rupees === 0) {
            return 'Rupees Zero Only';
        }

        return 'Rupees '.$this->convert($rupees).' Only';
    }

    private function convert(int $number): string
    {
        $parts = [];

        foreach ([
            10000000 => 'Crore',
            100000 => 'Lakh',
            1000 => 'Thousand',
            100 => 'Hundred',
        ] as $value => $label) {
            if ($number >= $value) {
                $parts[] = $this->convert((int) floor($number / $value)).' '.$label;
                $number %= $value;
            }
        }

        if ($number > 0) {
            if ($number < 20) {
                $parts[] = $this->ones[$number];
            } else {
                $parts[] = trim($this->tens[(int) floor($number / 10)].' '.$this->ones[$number % 10]);
            }
        }

        return trim(implode(' ', $parts));
    }
}
