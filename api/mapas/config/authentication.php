<?php
return [
    // 'auth.provider' => 'Fake',
    // 'auth.config' => [],

    // https://github.com/kterva/MultipleLocalAuth
    'auth.provider' => '\MultipleLocalAuth\Provider',
    'auth.redirect_path' => '/edital/4',
    'auth.config' => [
        'salt' => env('AUTH_SALT', null),
        'urlImageToUseInEmails' => 'https://iniciativas-mapadasperiferias.cidades.gov.br/files/opportunity/4/file/30/logo-ppv2024-f33ccbf81a4f55ec30ccb6c03264c7a7-23.jpg',
        'timeout' => '24 hours',
        'enableLoginByCPF' => false,
        'loginOnRegister' => true,
        'statusCreateAgent' => '1',
        'strategies' => [
            'Facebook' => [
                'app_id' => env('AUTH_FACEBOOK_APP_ID', null),
                'app_secret' => env('AUTH_FACEBOOK_APP_SECRET', null),
                'scope' => env('AUTH_FACEBOOK_SCOPE', 'email'),
            ],
            'LinkedIn' => [
                'api_key' => env('AUTH_LINKEDIN_API_KEY', null),
                'secret_key' => env('AUTH_LINKEDIN_SECRET_KEY', null),
                'redirect_uri' => '/autenticacao/linkedin/oauth2callback',
                'scope' => env('AUTH_LINKEDIN_SCOPE', 'r_emailaddress')
            ],
            'Google' => [
                'client_id' => env('AUTH_GOOGLE_CLIENT_ID', null),
                'client_secret' => env('AUTH_GOOGLE_CLIENT_SECRET', null),
                'redirect_uri' => '/autenticacao/google/oauth2callback',
                'scope' => env('AUTH_GOOGLE_SCOPE', 'email'),
            ],
            'Twitter' => [
                'app_id' => env('AUTH_TWITTER_APP_ID', null),
                'app_secret' => env('AUTH_TWITTER_APP_SECRET', null),
            ],
        ]
    ]

    // //Example Authentik
    // auth.provider' => 'MapasCulturais\AuthProviders\OpauthAuthentik',
    // 'auth.config' => [
    //     'salt' => env('AUTH_SALT', 'SECURITY_SALT'),
    //     'timeout' => '24 hours',
    //     'client_id' => env('AUTH_AUTHENTIK_APP_ID', ''),
    //     'client_secret' => env('AUTH_AUTHENTIK_APP_SECRET', ''),
    //     'scope' => env('AUTH_AUTHENTIK_SCOPE', 'openid profile email'),
    //     'login_url' => env('AUTH_AUTHENTIK_LOGIN_URL', ''),
    //     'login_url' => env('AUTH_AUTHENTIK_LOGOUT_URL', ''),
    // ]
];
