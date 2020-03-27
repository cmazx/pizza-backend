<?php


namespace App\Services;


class CurrencyService
{
    /**
     * Return converted value in cents
     *
     * @param string $curencyFrom
     * @param string $currencyTo
     * @param int $amount
     *
     * @return string
     */
    public function convert(string $curencyFrom, string $currencyTo, string $amount): string
    {
        return number_format(1.11 * $amount , 2, '.', '');
    }
}
