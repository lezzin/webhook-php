# Laravel Webhook Demo

Uma aplicação Laravel que implementa um sistema de webhook com validação por assinatura HMAC.

## 🚀 Funcionalidades

-   **Endpoint seguro**: Rota `/api/webhook` com validação HMAC
-   **Validação de assinatura**: Verificação automática do header `X-Signature`
-   **Comando Artisan**: `webhook:emit` para testes internos

## 📦 Instalação

### 1. Clone e configure o projeto

```bash
git clone https://github.com/lezzin/webhook-php
cd webhook-php
```

### 2. Instale as dependências

```bash
composer install
```

### 3. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure a chave do webhook

Adicione ao seu arquivo `.env`:

```env
WEBHOOK_SECRET=sua_chave_secreta_aqui
```

> **💡 Dica**: Use `php artisan key:generate --show` para gerar uma chave segura

### 5. Inicie o servidor

```bash
php artisan serve
```

A aplicação estará disponível em `http://127.0.0.1:8000`

## 🔧 Uso

### Método 1: Comando Artisan (Recomendado para testes)

```bash
php artisan webhook:emit
```

#### 📖 Documentação detalhada do comando `webhook:emit`

O comando `webhook:emit` é uma ferramenta customizada do Laravel que simula o envio de webhooks para a própria aplicação, facilitando testes e demonstrações do sistema.

**Localização**: `app/Console/Commands/EmitirWebhook.php`

**Como funciona:**

1. Cria um payload JSON predefinido
2. Calcula a assinatura HMAC SHA-256 usando `WEBHOOK_SECRET`
3. Envia uma requisição POST para `/api/webhook` da própria aplicação
4. Exibe a resposta no terminal

**Payload padrão enviado:**

```json
{
    "evento": "novo_usuario",
    "nome": "Leandro Adrian",
    "email": "leandro@example.com"
}
```

**Saída esperada:**

```
Enviando webhook para a própria API...
Resposta do servidor: {"status":"ok","message":"Webhook recebido com sucesso"}
```

### Método 2: Curl (Para integração externa)

```bash
#!/bin/bash
payload='{"evento":"novo_usuario","nome":"Leandro Adrian","email":"leandro@example.com"}'
secret='sua_chave_secreta_aqui'
signature=$(echo -n "$payload" | openssl dgst -sha256 -hmac "$secret" | sed 's/^.* //')

curl -X POST http://127.0.0.1:8000/api/webhook \
  -H "Content-Type: application/json" \
  -H "X-Signature: $signature" \
  -d "$payload"
```

### Método 3: Usando PHP

```php
<?php
$payload = json_encode([
    'evento' => 'novo_usuario',
    'nome' => 'Leandro Adrian',
    'email' => 'leandro@example.com'
]);

$secret = 'sua_chave_secreta_aqui';
$signature = hash_hmac('sha256', $payload, $secret);

$ch = curl_init('http://127.0.0.1:8000/api/webhook');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Signature: ' . $signature
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);
```

### Método 4: Usando Laravel

```php
<?php

public function emit()
{
    $url = 'http://127.0.0.1:8000/api/webhook';
    $secret = 'sua_chave_secreta_aqui';

    $data = [
        'evento' => 'novo_usuario',
        'nome' => 'Leandro Adrian',
        'email' => 'leandro@example.com',
    ];

    $payload = json_encode($data);
    $signature = hash_hmac('sha256', $payload, $secret);

    try {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Signature' => $signature,
        ])->post($url, $data);

        Log::info('Webhook enviado com sucesso', ['resposta' => $response->body()]);

        return response()->json([
            'mensagem' => 'Webhook enviado!',
            'resposta' => $response->json()
        ]);
    } catch (RequestException $e) {
        Log::error('Erro ao enviar webhook', ['erro' => $e->getMessage()]);
        return response()->json(['erro' => 'Falha ao enviar webhook'], 500);
    }
}
```

## 📋 Estrutura do Payload

O webhook aceita qualquer JSON válido. Exemplo:

```json
{
    "evento": "novo_usuario",
    "nome": "Leandro Adrian",
    "email": "leandro@example.com",
    "timestamp": "2025-01-15T10:30:00Z"
}
```

## 🔒 Segurança

### Validação HMAC

-   **Algoritmo**: SHA-256
-   **Header**: `X-Signature`
-   **Chave**: Definida em `WEBHOOK_SECRET`
-   **Formato**: Hash hexadecimal do payload

## 📝 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 👨‍💻 Autor

**Leandro Adrian**

-   GitHub: [@lezzin](https://github.com/lezzin)
-   LinkedIn: [Leandro Adrian](https://linkedin.com/in/leandro-adrian)
