<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value from cache or return default
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        return cache()->get("settings.{$key}", $default);
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format a number as currency
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    function formatCurrency($amount, $currency = 'USD')
    {
        return number_format($amount, 2) . ' ' . $currency;
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format a date in a consistent format
     *
     * @param mixed $date
     * @param string $format
     * @return string
     */
    function formatDate($date, $format = 'M d, Y')
    {
        if (!$date) {
            return 'N/A';
        }
        
        return \Carbon\Carbon::parse($date)->format($format);
    }
}
