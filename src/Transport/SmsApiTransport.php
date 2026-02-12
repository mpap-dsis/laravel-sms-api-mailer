<?php

namespace Mpap\LaravelSmsApiMailer\Transport;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
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

        // Processar anexos
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $filename = null;
            
            // Debug: Log da classe do anexo
            Log::debug('Attachment Class: ' . get_class($attachment));
            
            // Método 1: Tentar getFilename()
            if (method_exists($attachment, 'getFilename')) {
                $filename = $attachment->getFilename();
                Log::debug('getFilename(): ' . ($filename ?: 'null'));
            }
            
            // Método 2: Tentar pegar do Content-Disposition header
            if (!$filename && method_exists($attachment, 'getPreparedHeaders')) {
                $headers = $attachment->getPreparedHeaders();
                $disposition = $headers->get('Content-Disposition');
                if ($disposition) {
                    Log::debug('Content-Disposition exists');
                    if (method_exists($disposition, 'getParameter')) {
                        $filename = $disposition->getParameter('filename');
                        Log::debug('Disposition filename: ' . ($filename ?: 'null'));
                    }
                    // Tentar getBodyAsString também
                    if (!$filename && method_exists($disposition, 'getBodyAsString')) {
                        $dispString = $disposition->getBodyAsString();
                        Log::debug('Disposition string: ' . $dispString);
                    }
                }
            }
            
            // Método 3: Tentar getName()
            if (!$filename && method_exists($attachment, 'getName')) {
                $filename = $attachment->getName();
                Log::debug('getName(): ' . ($filename ?: 'null'));
            }
            
            // Método 4: Listar todos os headers disponíveis
            if (!$filename && method_exists($attachment, 'getPreparedHeaders')) {
                $headers = $attachment->getPreparedHeaders();
                Log::debug('All headers: ' . json_encode($headers->toString()));
            }
            
            $attachments[] = [
                'filename' => $filename ?: 'attachment.pdf',
                'content' => base64_encode($attachment->getBody()),
                'mime_type' => $attachment->getContentType(),
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
