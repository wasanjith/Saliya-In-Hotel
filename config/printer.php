<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Thermal Printer Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for the 80mm thermal receipt printer
    | YHD-80E model
    |
    */

    'thermal' => [
        'width' => 32, // Characters per line for 80mm printer
        'paper_width' => 80, // Paper width in mm
        'company_name' => 'SALIYA INN HOTEL',
        'company_address' => 'No. 123, Main Street',
        'company_city' => 'Colombo, Sri Lanka',
        'company_phone' => '+94 11 234 5678',
        'company_email' => 'info@saliyainn.com',
        'company_website' => 'www.saliyainn.com',
        'currency' => 'Rs.',
        'tax_rate' => 0.10, // 10% tax rate
        'footer_message' => 'Thank you for dining with us!',
        'powered_by' => 'Powered by Saliya Inn POS',
    ],

    /*
    |--------------------------------------------------------------------------
    | Print Options
    |--------------------------------------------------------------------------
    |
    | Available printing options and their settings
    |
    */

    'options' => [
        'thermal' => [
            'enabled' => true,
            'auto_cut' => true,
            'auto_feed' => true,
        ],
        'web' => [
            'enabled' => true,
            'auto_print' => false,
            'show_preview' => true,
        ],
        'download' => [
            'enabled' => true,
            'format' => 'txt',
            'include_timestamp' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Templates
    |--------------------------------------------------------------------------
    |
    | Template settings for different invoice types
    |
    */

    'templates' => [
        'takeaway' => [
            'title' => 'TAKEAWAY ORDER',
            'show_customer' => true,
            'show_table' => false,
        ],
        'dine_in' => [
            'title' => 'DINE-IN ORDER',
            'show_customer' => true,
            'show_table' => true,
        ],
        'delivery' => [
            'title' => 'DELIVERY ORDER',
            'show_customer' => true,
            'show_table' => false,
            'show_address' => true,
        ],
    ],
]; 