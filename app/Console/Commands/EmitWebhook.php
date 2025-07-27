<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class EmitWebhook extends Command
{
    protected $signature = 'webhook:emit';
    protected $description = 'Emite um webhook para a própria aplicação';

    public function handle()
    {
        $this->info('Enviando webhook para a própria API...');

        $data = [
            'evento' => 'novo_usuario',
            'nome' => 'Leandro Adrian',
            'email' => 'leandro@example.com',
        ];

        $secret = env('WEBHOOK_SECRET');
        $payload = json_encode($data);
        $signature = hash_hmac('sha256', $payload, $secret);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Signature' => $signature,
        ])->post('http://127.0.0.1:8000/api/webhook', $data);

        $this->info('Resposta do servidor: ' . $response->body());
    }
}
