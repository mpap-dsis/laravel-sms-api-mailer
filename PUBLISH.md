# Guia de Publicação do Pacote

Este guia fornece instruções para publicar e versionar o pacote Laravel SMS API Mailer.

## Preparação para Publicação

### 1. Inicializar Git no Pacote

```bash
cd packages/mpap/laravel-sms-api-mailer
git init
git add .
git commit -m "Initial commit - Laravel SMS API Mailer v1.0.0"
```

### 2. Criar Repositório no GitHub/GitLab

Crie um novo repositório em:
- **GitHub**: https://github.com/mpap-dsis/laravel-sms-api-mailer
- **GitLab MPAP**: https://gitlab.mpap.mp.br/dsis/laravel-sms-api-mailer

### 3. Adicionar Remote e Push

```bash
# Para GitHub
git remote add origin git@github.com:mpap-dsis/laravel-sms-api-mailer.git

# Ou para GitLab MPAP
git remote add origin git@gitlab.mpap.mp.br:dsis/laravel-sms-api-mailer.git

git branch -M main
git push -u origin main
```

## Versionamento

O pacote segue [Semantic Versioning](https://semver.org/):

- **MAJOR** (1.0.0): Mudanças incompatíveis
- **MINOR** (0.1.0): Novas funcionalidades compatíveis
- **PATCH** (0.0.1): Correções de bugs

### Criar uma Nova Versão

```bash
# Atualizar CHANGELOG.md com as mudanças
nano CHANGELOG.md

# Commitar mudanças
git add .
git commit -m "Release v1.0.0"

# Criar tag
git tag -a v1.0.0 -m "Release version 1.0.0"

# Push com tags
git push origin main --tags
```

## Publicação via Packagist (Público)

### 1. Criar Conta no Packagist

Acesse https://packagist.org e crie uma conta.

### 2. Submeter Pacote

1. Clique em "Submit"
2. Cole a URL do repositório Git
3. Click em "Check"

### 3. Configurar Auto-Update

Configure webhook no GitHub/GitLab para atualizar automaticamente no Packagist.

## Publicação via Satis (Privado)

Para uso interno do MPAP com Satis:

### 1. Configurar Satis

Adicione ao `satis.json` do servidor:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://gitlab.mpap.mp.br/dsis/laravel-sms-api-mailer"
        }
    ],
    "require": {
        "mpap/laravel-sms-api-mailer": "*"
    }
}
```

### 2. Rebuild Satis

```bash
php bin/satis build satis.json public/
```

### 3. Uso nos Projetos

No `composer.json` dos projetos:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.mpap.mp.br"
        }
    ],
    "require": {
        "mpap/laravel-sms-api-mailer": "^1.0"
    }
}
```

## Instalação via Path (Desenvolvimento)

Para desenvolvimento local sem publicar:

### No Projeto Principal

```bash
# No composer.json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/mpap/laravel-sms-api-mailer",
            "options": {
                "symlink": true
            }
        }
    ]
}

# Instalar
composer require mpap/laravel-sms-api-mailer @dev
```

## Workflow de Desenvolvimento

### 1. Branch para Features

```bash
git checkout -b feature/nova-funcionalidade
# ... fazer alterações
git commit -m "feat: adiciona nova funcionalidade"
git push origin feature/nova-funcionalidade
```

### 2. Pull Request

Crie PR para review antes de merge na main.

### 3. Release

Após merge na main:

```bash
git checkout main
git pull
git tag -a v1.1.0 -m "Release v1.1.0"
git push --tags
```

## CI/CD Recomendado

### GitHub Actions

Crie `.github/workflows/tests.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        php: [8.1, 8.2, 8.3]
        laravel: [10.*, 11.*]

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}

      - name: Install dependencies
        run: composer install

      - name: Run tests
        run: vendor/bin/phpunit
```

## Documentação

Mantenha atualizado:
- **README.md**: Visão geral e uso básico
- **INSTALL.md**: Instruções detalhadas de instalação
- **CHANGELOG.md**: Histórico de versões
- **PUBLISH.md**: Este arquivo

## Manutenção

### Issues e Pull Requests

1. Configure templates no GitHub/GitLab
2. Use labels para categorizar issues
3. Responda issues em até 48h
4. Review PRs semanalmente

### Atualização de Dependências

```bash
composer update
vendor/bin/phpunit
```

Se tudo passar, commit e release patch.

## Checklist de Release

- [ ] Atualizar CHANGELOG.md
- [ ] Executar testes: `vendor/bin/phpunit`
- [ ] Verificar formatação: `vendor/bin/pint`
- [ ] Atualizar versão no composer.json (se necessário)
- [ ] Commit das mudanças
- [ ] Criar tag: `git tag -a vX.Y.Z -m "Release vX.Y.Z"`
- [ ] Push: `git push origin main --tags`
- [ ] Criar release no GitHub/GitLab
- [ ] Atualizar Packagist/Satis

## Contato

Para dúvidas sobre publicação:
- Email: dsis@mpap.mp.br
- Interno: Slack #dev-packages
