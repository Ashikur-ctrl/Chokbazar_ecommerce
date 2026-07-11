<?php

if (! function_exists('taka')) {
    function taka(float|int|string|null $amount): string
    {
        return '৳' . number_format((float) $amount, 2);
    }
}
