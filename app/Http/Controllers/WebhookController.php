<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = env('WEBHOOK_SECRET');
        $payload = $request->getContent();
        $signature = $request->header('X-Signature');

        if (!$signature || !hash_equals(hash_hmac('sha256', $payload, $secret), $signature)) {
            Log::warning('Webhook com assinatura inválida', [
                'signature' => $signature,
                'expected' => hash_hmac('sha256', $payload, $secret),
            ]);

            return response()->json(['erro' => 'Assinatura inválida'], Response::HTTP_UNAUTHORIZED);
        }

        // Processa os dados como necessário 
        $data = $request->json()->all();

        $logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($data) . PHP_EOL;
        Storage::append('webhook_logs.txt', $logEntry);

        Log::info('Webhook recebido e salvo', $data);

        return response()->json(['status' => 'ok']);
    }
}
