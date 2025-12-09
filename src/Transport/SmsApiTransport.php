<?php

namespace Mpap\LaravelSmsApiMailer\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class SmsApiTransport extends AbstractTransport
{
    /**
     * Create a new transport instance.
     */
    public function __construct(
        protected string $apiUrl,
        protected string $apiToken,
        protected string $sistema
    ) {
        parent::__construct();
    }

    /**
     * Send the given message.
     *
     * @throws GuzzleException
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $to = collect($email->getTo())->first();

        if (! $to) {
            return;
        }

        $body = [
            'data_envio' => now()->toIso8601String(),
            'sistema' => $this->sistema,
            'destinatario' => $to->getName() ?: $to->getAddress(),
            'email' => $to->getAddress(),
            'assunto' => $email->getSubject(),
            'mensagem' => $email->getHtmlBody() ?: $email->getTextBody(),
        ];

        $client = new Client();

        $client->post($this->apiUrl, [
            'headers' => [
                'Authorization' => 'Basic '.$this->apiToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => $body,
        ]);
    }

    /**
     * Get the string representation of the transport.
     */
    public function __toString(): string
    {
        return 'smsapi';
    }
}
