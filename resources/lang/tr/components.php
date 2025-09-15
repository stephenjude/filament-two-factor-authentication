<?php

return [
    'enable' => [
        'header' => 'İki Faktörlü Kimlik Doğrulama Etkin Değil',
        'description' => 'İki faktörlü kimlik doğrulama etkinleştirildiğinde, oturum açarken güvenli ve rastgele oluşturulmuş bir doğrulama kodu girmeniz istenir. Bu kodu, telefonunuzdaki Google Authenticator veya benzeri bir doğrulama uygulamasından alabilirsiniz.',
    ],
    'logout' => [
        'button' => 'Çıkış Yap',
    ],
    'enabled' => [
        'header' => 'İki Faktörlü Kimlik Doğrulama Etkinleştirildi',
        'description' => 'Bu kurtarma kodlarını güvenli bir parola yöneticisinde saklayın. İki faktörlü kimlik doğrulama cihazınızı kaybederseniz, hesabınıza erişimi geri kazanmak için bu kodları kullanabilirsiniz.',
    ],
    'setup_confirmation' => [
        'header' => 'İki Faktörlü Kimlik Doğrulamasını Tamamlayın',
        'description' => 'İki faktörlü kimlik doğrulama etkinleştirildiğinde, oturum açarken güvenli ve rastgele bir doğrulama kodu girmeniz gerekir. Bu kodu telefonunuzdaki doğrulama uygulamasından alabilirsiniz.',
        'scan_qr_code' => 'İki faktörlü kimlik doğrulamayı etkinleştirmek için, aşağıdaki QR kodunu telefonunuzdaki doğrulama uygulaması ile tarayın veya kurulum anahtarını girip oluşturulan doğrulama kodunu sağlayın.',
    ],
    'base' => [
        'wrong_user' => 'Kimliği doğrulanmış kullanıcı, profil sayfasını güncellemek için Filament Auth modeline ait olmalıdır.',
        'rate_limit_exceeded' => 'Çok fazla istek gönderildi.',
        'try_again' => ':seconds saniye içinde tekrar deneyin.',
    ],
    '2fa' => [
        'confirm' => 'Onayla',
        'cancel' => 'İptal',
        'enable' => 'Etkinleştir',
        'disable' => 'Devre Dışı Bırak',
        'confirm_password' => 'Şifreyi Onayla',
        'wrong_password' => 'Girilen şifre hatalı.',
        'code' => 'Doğrulama Kodu',
        'setup_key' => 'Kurulum Anahtarı: :setup_key',
        'current_password' => 'Mevcut Şifre',
        'regenerate_recovery_codes' => 'Yeni Kurtarma Kodları Oluştur',
    ],
    'passkey' => [
        'add' => 'Anahtar Oluştur',
        'name' => 'Ad',
        'added' => 'Anahtar başarıyla eklendi.',
        'login' => 'Anahtar ile Giriş Yap',
        'tootip' => 'Face ID, parmak izi veya PIN ile doğrulama yapın',
        'notice' => [
            'header' => 'Geçiş anahtarları, cihazınızın biyometrik doğrulamasını kullanan şifresiz bir giriş yöntemidir. Şifre yazmak yerine, güvenilir cihazınızda oturumu onaylarsınız.',
        ],
    ],
];
