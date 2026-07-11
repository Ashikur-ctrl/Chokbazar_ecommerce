<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Facebook Pixel ID
    |--------------------------------------------------------------------------
    | Set your Facebook Pixel ID here or in your .env as FACEBOOK_PIXEL_ID.
    | Leave empty to disable the pixel.
    */
    'pixel_id' => env('FACEBOOK_PIXEL_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Facebook Page ID (for Messenger)
    |--------------------------------------------------------------------------
    | Set your Facebook Page ID here or in your .env as FACEBOOK_PAGE_ID.
    | Leave empty to disable Messenger chat.
    */
    'page_id' => env('FACEBOOK_PAGE_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Facebook Domain Verification
    |--------------------------------------------------------------------------
    | Some Facebook Business tools require a meta tag verification.
    | Copy the value from Facebook and set it in your .env as FACEBOOK_VERIFICATION_CODE.
    */
    'verification_code' => env('FACEBOOK_VERIFICATION_CODE', ''),
];
