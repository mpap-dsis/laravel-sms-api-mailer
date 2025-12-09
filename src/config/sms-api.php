<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS API URL
    |--------------------------------------------------------------------------
    |
    | A URL da API de envio de e-mails do sistema SMS do MPAP.
    |
    */

    'api_url' => env('SMSAPI_URL', 'http://mp-app-sms.mpap.private/email'),

    /*
    |--------------------------------------------------------------------------
    | SMS API Token
    |--------------------------------------------------------------------------
    |
    | Token de autenticação para a API do SMS. Este token é usado no
    | cabeçalho Authorization como Basic Auth.
    |
    */

    'token' => env('SMSAPI_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Sistema ID
    |--------------------------------------------------------------------------
    |
    | Identificador único do sistema que está enviando o e-mail.
    | Este ID é registrado na API do SMS para rastreamento.
    |
    */

    'sistema' => env('SMSAPI_SISTEMA'),

];
