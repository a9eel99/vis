<?php
return [
    'scoring' => [
        'excellent' => env('SCORE_EXCELLENT', 90),
        'good' => env('SCORE_GOOD', 75),
        'needs_attention' => env('SCORE_NEEDS_ATTENTION', 50),
    ],
    'upload' => [
        'max_image_size' => env('UPLOAD_MAX_IMAGE_SIZE', 5120),
        'allowed_image_types' => env('UPLOAD_ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,webp'),
    ],
    'company' => [
        'name_ar' => env('COMPANY_NAME_AR', 'مركز فحص المركبات'),
        'name_en' => env('COMPANY_NAME_EN', 'Vehicle Inspection Center'),
        'logo' => env('COMPANY_LOGO', null),
        'address_ar' => env('COMPANY_ADDRESS_AR', ''),
        'address_en' => env('COMPANY_ADDRESS_EN', ''),
        'phone' => env('COMPANY_PHONE', ''),
        'email' => env('COMPANY_EMAIL', ''),
        'website' => env('COMPANY_WEBSITE', ''),
        'tax_number' => env('COMPANY_TAX_NUMBER', ''),
        'notes_ar' => env('COMPANY_PDF_NOTES_AR', ''),
        'notes_en' => env('COMPANY_PDF_NOTES_EN', ''),
    ],
    'puppeteer' => [
    'node_path' => env('NODE_PATH', 'node'),
],
];