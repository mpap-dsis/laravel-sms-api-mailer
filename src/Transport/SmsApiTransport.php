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
            'data_envio' => now()->format('Y-m-d'),  // Formato: YYYY-MM-DD conforme documentação
            'sistema' => $this->sistema,
            'destinatario' => $to->getName() ?: $to->getAddress(),
            'email' => $to->getAddress(),
            'assunto' => $email->getSubject(),
            'mensagem' => $email->getHtmlBody() ?: $email->getTextBody(),
        ];

        // Processar anexos
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $filename = null;
            
            // Tentar getFilename()
            if (method_exists($attachment, 'getFilename')) {
                $filename = $attachment->getFilename();
            }
            
            // Fallback: tentar getName()
            if (!$filename && method_exists($attachment, 'getName')) {
                $filename = $attachment->getName();
            }
            
            // Formato esperado pela API do SMS: ["nome_arquivo.ext", "base64"]
            $attachments[] = [
                $filename ?: 'attachment.pdf',
                base64_encode($attachment->getBody())
            ];
        }

        if (!empty($attachments)) {
            $body['anexos'] = $attachments;
        }

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
