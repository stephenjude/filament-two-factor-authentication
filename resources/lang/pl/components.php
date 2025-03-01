<?php

return [
    'enable' => [
        'header' => 'Nie włączono dwuetapowego uwierzytelniania',
        'description' => 'Po włączeniu dwuetapowego uwierzytelniania podczas logowania zostaniesz poproszony o podanie bezpiecznego, losowego tokenu. Możesz go pobrać z aplikacji Google Authenticator na swoim telefonie.',
    ],
    'logout' => [
        'button' => 'Wyloguj się',
    ],
    'enabled' => [
        'header' => 'Włączono dwuetapowe uwierzytelnianie',
        'description' => 'Przechowuj te kody odzyskiwania w bezpiecznym menedżerze haseł. Mogą być użyte do odzyskania dostępu do Twojego konta, jeśli urządzenie do dwuetapowego uwierzytelniania zostanie utracone.',
    ],
    'setup_confirmation' => [
        'header' => 'Zakończ włączanie dwuetapowego uwierzytelniania',
        'description' => 'Po włączeniu dwuetapowego uwierzytelniania podczas logowania zostaniesz poproszony o podanie bezpiecznego, losowego tokenu. Możesz go pobrać z aplikacji Google Authenticator na swoim telefonie.',
        'scan_qr_code' => 'Aby zakończyć włączanie dwuetapowego uwierzytelniania, zeskanuj poniższy kod QR przy użyciu aplikacji uwierzytelniającej w telefonie lub wprowadź klucz konfiguracyjny i podaj wygenerowany kod OTP.',
    ],
    'base' => [
        'wrong_user' => 'Uwierzytelniony obiekt użytkownika musi być modelem Filament Auth, aby strona profilu mogła go aktualizować.',
        'rate_limit_exceeded' => 'Zbyt wiele żądań',
        'try_again' => 'Spróbuj ponownie za :seconds sekund',
    ],
    '2fa' => [
        'confirm' => 'Potwierdź',
        'cancel' => 'Anuluj',
        'enable' => 'Włącz dwuetapowe uwierzytelnianie',
        'disable' => 'Wyłącz dwuetapowe uwierzytelnianie',
        'confirm_password' => 'Potwierdź hasło',
        'wrong_password' => 'Podane hasło jest nieprawidłowe.',
        'code' => 'Kod',
        'setup_key' => 'Klucz konfiguracyjny: :setup_key.',
        'current_password' => 'Aktualne hasło',
        'regenerate_recovery_codes' => 'Wygeneruj nowe kody odzyskiwania',
    ],
];
