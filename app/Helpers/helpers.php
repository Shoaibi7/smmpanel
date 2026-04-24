<?php

use App\Models\Setting;

if (!function_exists('get_currency_code')) {
    function get_currency_code() {
        return Setting::where('key', 'currency_code')->value('value') ?? 'USD';
    }
}

if (!function_exists('get_conversion_rate')) {
    function get_conversion_rate() {
        return Setting::where('key', 'usd_to_pkr_rate')->value('value') ?? 280;
    }
}

if (!function_exists('convert_currency')) {
    function convert_currency($amount, $to_currency = null) {
        $currency_code = $to_currency ?? get_currency_code();
        $rate = get_conversion_rate();

        if ($currency_code === 'PKR') {
            return (float)$amount * (float)$rate;
        }

        return (float)$amount;
    }
}

if (!function_exists('convert_to_usd')) {
    function convert_to_usd($amount, $from_currency = null) {
        $currency_code = $from_currency ?? get_currency_code();
        $rate = get_conversion_rate();

        if ($currency_code === 'PKR' && $rate > 0) {
            return (float)$amount / (float)$rate;
        }

        return (float)$amount;
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount, $to_currency = null) {
        $currency_code = $to_currency ?? get_currency_code();
        $converted = convert_currency($amount, $currency_code);

        if ($currency_code === 'PKR') {
            return 'Rs ' . number_format($converted, 2);
        }

        return '$' . number_format($converted, 2);
    }
}
