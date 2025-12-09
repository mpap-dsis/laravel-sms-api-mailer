# Guia de Instalação

Este guia fornece instruções detalhadas para instalar e configurar o Laravel SMS API Mailer em seus projetos.

## Método 1: Via Repositório Git (Recomendado para MPAP)

### 1. Configurar Repositório no composer.json

Adicione o repositório no `composer.json` do seu projeto:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mpap-dsis/laravel-sms-api-mailer"
        }
    ]
}
```

### 2. Instalar o Pacote

```bash
composer require mpap/laravel-sms-api-mailer
```

## Método 2: Via Path (Desenvolvimento Local)

Se você tem o pacote localmente no mesmo workspace:

### 1. Configurar Path no composer.json

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../laravel-sms-api-mailer"
        }
    ]
}
```

### 2. Instalar o Pacote

```bash
composer require mpap/laravel-sms-api-mailer
```

## Método 3: Via Packagist Privado

Se sua organização usa Packagist privado ou Satis:

```bash
composer config repositories.mpap composer https://packages.mpap.mp.br
composer require mpap/laravel-sms-api-mailer
```

## Configuração Pós-Instalação

### 1. Publicar Configuração (Opcional)

```bash
php artisan vendor:publish --tag=sms-api-config
```

Isso criará o arquivo `config/sms-api.php`.

### 2. Configurar Variáveis de Ambiente

Adicione ao arquivo `.env`:

```env
# SMS API Configuration
SMSAPI_URL=http://mp-app-sms.mpap.private/email
SMSAPI_TOKEN=seu-token-de-autenticacao
SMSAPI_SISTEMA=uuid-do-seu-sistema

# Opcional: definir como mailer padrão
MAIL_MAILER=smsapi
```

### 3. Adicionar Mailer ao config/mail.php

Abra `config/mail.php` e adicione:

```php
'mailers' => [
    // ... outros mailers

    'smsapi' => [
        'transport' => 'smsapi',
    ],
],
```

### 4. Limpar Cache

```bash
php artisan config:clear
php artisan cache:clear
```

## Teste de Funcionamento

Crie um comando de teste para verificar se tudo está funcionando:

```bash
php artisan make:command TesteSmsApi
```

Em `app/Console/Commands/TesteSmsApi.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TesteSmsApi extends Command
{
    protected $signature = 'teste:sms-api';
    protected $description = 'Testa o envio via SMS API';

    public function handle(): void
    {
        Mail::mailer('smsapi')
            ->to('seu-email@example.com', 'Seu Nome')
            ->send(new \App\Mail\ExemploMail());

        $this->info('E-mail enviado com sucesso!');
    }
}
```

Execute o teste:

```bash
php artisan teste:sms-api
```

## Atualização do Pacote

Para atualizar para a versão mais recente:

```bash
composer update mpap/laravel-sms-api-mailer
```

Para uma versão específica:

```bash
composer require mpap/laravel-sms-api-mailer:^1.0
```

## Resolução de Problemas

### Erro: "Unsupported mail transport"

**Solução**: Limpe o cache de configuração:
```bash
php artisan config:clear
```

### Erro: "Class not found"

**Solução**: Recrie o autoload:
```bash
composer dump-autoload
```

### Provider não registrado

**Solução**: O provider deve ser auto-descoberto. Se não for, adicione manualmente em `config/app.php`:
```php
'providers' => [
    // ...
    Mpap\LaravelSmsApiMailer\SmsApiServiceProvider::class,
],
```

## Uso em Produção

### Recomendações

1. **Use filas** para envio assíncrono:
```php
Mail::to($user)->queue(new MinhaNotificacao());
```

2. **Configure retry** para falhas:
```php
Mail::to($user)
    ->later(now()->addMinutes(5), new MinhaNotificacao());
```

3. **Log de erros**: Configure logging adequado no Laravel.

4. **Rate limiting**: Implemente throttling se necessário.

### Variáveis de Ambiente Recomendadas

```env
# Produção
SMSAPI_URL=https://sms-api.mpap.mp.br/email
SMSAPI_TOKEN=${SMSAPI_TOKEN} # Use secrets manager
SMSAPI_SISTEMA=${SISTEMA_UUID}

# Queue para e-mails
QUEUE_CONNECTION=redis
```

## Suporte

Para problemas ou dúvidas:
- Abra uma issue no repositório
- Entre em contato: dsis@mpap.mp.br
