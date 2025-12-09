# Laravel SMS API Mailer

[![Latest Version](https://img.shields.io/packagist/v/mpap/laravel-sms-api-mailer.svg)](https://packagist.org/packages/mpap/laravel-sms-api-mailer)
[![License](https://img.shields.io/packagist/l/mpap/laravel-sms-api-mailer.svg)](https://packagist.org/packages/mpap/laravel-sms-api-mailer)

Transport customizado do Laravel Mail para integração com a API de envio de e-mails do sistema SMS do MPAP.

## Requisitos

- PHP 8.1 ou superior
- Laravel 10.x ou 11.x
- GuzzleHTTP 7.x

## Instalação

### Via Repositório Git (Recomendado para MPAP)

Se o pacote ainda não está publicado no Packagist, você precisa configurar o repositório Git no `composer.json`:

**1. Adicione o repositório ao `composer.json`:**

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mpap-dsis/laravel-sms-api-mailer"
        }
    ],
    "require": {
        "mpap/laravel-sms-api-mailer": "dev-main"
    }
}
```

**2. Instale o pacote:**

```bash
composer require mpap/laravel-sms-api-mailer:dev-main
```

**Nota:** Use `dev-main` para instalar a partir da branch main, ou `^1.0` se houver uma versão com tag no repositório.

### Via Packagist (Após Publicação)

Quando o pacote estiver publicado no Packagist:

```bash
composer require mpap/laravel-sms-api-mailer
```

### Configuração

O Service Provider é auto-descoberto pelo Laravel. Opcionalmente, você pode publicar o arquivo de configuração:

```bash
php artisan vendor:publish --tag=sms-api-config
```

### Variáveis de Ambiente

Adicione as seguintes variáveis ao seu arquivo `.env`:

```env
SMSAPI_URL=http://mp-app-sms.mpap.private/email
SMSAPI_TOKEN=seu-token-aqui
SMSAPI_SISTEMA=seu-sistema-id-aqui
```

### Configuração do Mailer

Adicione a configuração do mailer no arquivo `config/mail.php`:

```php
'mailers' => [
    'smsapi' => [
        'transport' => 'smsapi',
    ],

    // outros mailers...
],
```

Ou configure com valores específicos:

```php
'mailers' => [
    'smsapi' => [
        'transport' => 'smsapi',
        'api_url' => env('SMSAPI_URL'),
        'token' => env('SMSAPI_TOKEN'),
        'sistema' => env('SMSAPI_SISTEMA'),
    ],
],
```

## Uso

### Definir como Mailer Padrão

Para usar o SMS API como mailer padrão, defina no `.env`:

```env
MAIL_MAILER=smsapi
```

Assim você pode usar o Mail facade normalmente:

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\MinhaNotificacao;

Mail::to('usuario@example.com', 'Nome do Usuário')
    ->send(new MinhaNotificacao());
```

### Usar Explicitamente

Você também pode especificar o mailer explicitamente:

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\MinhaNotificacao;

Mail::mailer('smsapi')
    ->to('usuario@example.com', 'Nome do Usuário')
    ->send(new MinhaNotificacao());
```

### Exemplo de Mailable

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MinhaNotificacao extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $titulo,
        public string $mensagem
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.minha-notificacao',
        );
    }
}
```

### Uso em Filas

O transport funciona perfeitamente com o sistema de filas do Laravel:

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\MinhaNotificacao;

Mail::to('usuario@example.com')
    ->queue(new MinhaNotificacao());
```

### Uso em Notificações

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MinhaNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->mailer('smsapi')
            ->subject('Assunto da Notificação')
            ->line('Conteúdo da notificação');
    }
}
```

### Envio com Anexos

O pacote suporta envio de anexos através da classe `Attachment`:

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacaoComAnexo extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $arquivoPath
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'E-mail com Anexo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notificacao',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->arquivoPath),
        ];
    }
}
```

**Enviando o e-mail com anexo:**

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacaoComAnexo;

$arquivoPath = storage_path('app/documentos/relatorio.pdf');

Mail::mailer('smsapi')
    ->to('usuario@example.com', 'Nome do Usuário')
    ->send(new NotificacaoComAnexo($arquivoPath));
```

**Múltiplos anexos:**

```php
public function attachments(): array
{
    return [
        Attachment::fromPath(storage_path('app/documento1.pdf')),
        Attachment::fromPath(storage_path('app/documento2.pdf'))
            ->as('relatorio-final.pdf')
            ->withMime('application/pdf'),
    ];
}
```

**Anexo a partir de dados:**

```php
public function attachments(): array
{
    return [
        Attachment::fromData(fn () => $this->pdf, 'relatorio.pdf')
            ->withMime('application/pdf'),
    ];
}
```

## Funcionamento

O transport converte automaticamente os dados do Laravel Mail para o formato esperado pela API do SMS:

```json
{
    "data_envio": "2024-01-15T10:30:00+00:00",
    "sistema": "uuid-do-sistema",
    "destinatario": "Nome do Destinatário",
    "email": "email@example.com",
    "assunto": "Assunto do E-mail",
    "mensagem": "<html>Conteúdo do e-mail...</html>"
}
```

## Testes

```bash
composer test
```

## Segurança

Se você descobrir alguma vulnerabilidade de segurança, por favor envie um e-mail para dsis@mpap.mp.br.

## Licença

The MIT License (MIT). Consulte o arquivo [LICENSE](LICENSE) para mais informações.

## Créditos

- [MPAP - DSIS](https://github.com/mpap-dsis)
- [Todos os Contribuidores](../../contributors)

## Suporte

Para suporte, abra uma issue no repositório ou entre em contato com a equipe DSIS do MPAP.
