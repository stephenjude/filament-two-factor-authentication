<?php

return [
    'enable' => [
        'header' => 'You have not enabled two factor authentication.',
        'description' => 'When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.',
    ],
    'logout' => [
        'button' => 'Logout',
    ],
    'recovery_codes' => [
        'header' => 'You have enabled two factor authentication.',
        'description' => 'Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.',
    ],
    'setup-confirmation' => [
        'header' => 'Finish enabling two factor authentication.',
        'description' => 'When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.',
        'scan_qr_code' => 'To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.'
    ],
    'base' => [
        'wrong_user' => 'The authenticated user object must be a Filament Auth model to allow the profile page to update it.',
        'rate_limit_exceeded' => 'Too many requests',
        'try_again' => 'Please try again in :seconds seconds',
    ],
    '2fa' => [
        'confirm' => 'Confirm',
        'cancel' => 'Cancel',
        'enable' => 'Enable',
        'disable' => 'Disable',
        'confirm_password' => 'Confirm Password',
        'wrong_password' => 'The provided password was incorrect.',
        'code' => 'Code',
        'setup_key' => 'Setup Key :setup_key.',
        'current_password' => 'Current Password',
        'regenerate_recovery_codes' => 'Generate New Recovery Codes',
    ]
];
