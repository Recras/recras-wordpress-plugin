<?php
namespace Recras;

use NumberFormatter;

class Price
{
    /**
     * Format a price
     */
    public static function format(float $price): string
    {
        $currency = Settings::getCurrency();
        $fmt = new NumberFormatter(get_locale(), NumberFormatter::CURRENCY);
        return '<span class="recras-price">' . $fmt->formatCurrency($price, $currency) . '</span>';
    }
}
