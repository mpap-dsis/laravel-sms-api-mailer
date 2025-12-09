# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [1.0.0] - 2024-01-XX

### Adicionado
- Implementação inicial do Laravel SMS API Mailer
- Transport customizado para integração com API do SMS do MPAP
- Service Provider com auto-descoberta
- Configuração publicável
- Suporte para Laravel 10.x e 11.x
- Suporte para PHP 8.1, 8.2 e 8.3
- Documentação completa no README
- Testes básicos com PHPUnit
- Licença MIT

### Características
- Conversão automática de Laravel Mail para formato da API SMS
- Suporte completo para filas e notificações do Laravel
- Configuração via arquivo .env
- Headers HTTP customizados (Authorization, Content-Type, Accept)
- Envio de data/hora em formato ISO 8601

[1.0.0]: https://github.com/mpap-dsis/laravel-sms-api-mailer/releases/tag/v1.0.0
