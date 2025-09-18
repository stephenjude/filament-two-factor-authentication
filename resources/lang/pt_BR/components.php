<?php

return [
    'enable' => [
        'header' => 'Você não habilitou a autenticação de dois fatores.',
        'description' => 'Quando a autenticação de dois fatores estiver habilitada, você será solicitado a inserir um token seguro e aleatório durante a autenticação. Você pode obter este token do aplicativo Google Authenticator do seu celular.',
    ],
    'logout' => [
        'button' => 'Sair',
    ],
    'enabled' => [
        'header' => 'Você habilitou a autenticação de dois fatores.',
        'description' => 'Armazene estes códigos de recuperação em um gerenciador de senhas seguro. Eles podem ser usados para recuperar o acesso à sua conta se seu dispositivo de autenticação de dois fatores for perdido.',
    ],
    'setup_confirmation' => [
        'header' => 'Conclua a habilitação da autenticação de dois fatores.',
        'description' => 'Quando a autenticação de dois fatores estiver habilitada, você será solicitado a inserir um token seguro e aleatório durante a autenticação. Você pode obter este token do aplicativo Google Authenticator do seu celular.',
        'scan_qr_code' => 'Para concluir a habilitação da autenticação de dois fatores, escaneie o código QR a seguir usando o aplicativo autenticador do seu celular ou insira a chave de configuração e forneça o código OTP gerado.',
    ],
    'base' => [
        'wrong_user' => 'O objeto de usuário autenticado deve ser um modelo Filament Auth para permitir que a página de perfil o atualize.',
        'rate_limit_exceeded' => 'Muitas solicitações',
        'try_again' => 'Tente novamente em :seconds segundos',
    ],
    '2fa' => [
        'confirm' => 'Confirmar',
        'cancel' => 'Cancelar',
        'enable' => 'Habilitar',
        'disable' => 'Desabilitar',
        'confirm_password' => 'Confirmar Senha',
        'wrong_password' => 'A senha fornecida estava incorreta.',
        'code' => 'Código',
        'setup_key' => 'Chave de Configuração: :setup_key.',
        'current_password' => 'Senha Atual',
        'regenerate_recovery_codes' => 'Gerar Novos Códigos de Recuperação',
    ],
    'passkey' => [
        'add' => 'Criar Passkey',
        'name' => 'Nome',
        'added' => 'Passkey adicionada com sucesso.',
        'login' => 'Entrar com Passkey',
        'tootip' => 'Use Face ID, impressão digital ou PIN',
        'error' => 'Erro de autenticação: :message',
        'never_used' => 'Nunca usado',
        'notice' => [
            'header' => 'Passkeys são um método de login sem senha usando a autenticação biométrica do seu dispositivo. Em vez de digitar uma senha, você aprova o login no seu dispositivo confiável.',
        ],
    ],
];
