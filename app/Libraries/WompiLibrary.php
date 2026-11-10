<?php

namespace App\Libraries;

class WompiLibrary
{
    private const SANDBOX_URL    = 'https://sandbox.wompi.co/v1';
    private const PRODUCTION_URL = 'https://production.wompi.co/v1';

    /**
     * Genera la firma de integridad para el Checkout Widget de Wompi.
     * Fórmula: SHA256(reference + amountInCents + currency + integritySecret)
     */
    public static function generateIntegritySignature(string $reference, int $amountInCents, string $currency, string $integritySecret): string
    {
        return hash('sha256', $reference . $amountInCents . $currency . $integritySecret);
    }

    /**
     * Valida la firma enviada por el Webhook de Wompi.
     * Wompi envía en signature.properties las claves a concatenar en orden,
     * seguido de timestamp y events_key.
     */
    public static function verifyWebhookSignature(array $payload, string $eventsKey): bool
    {
        if (empty($eventsKey)) {
            // Si no se ha configurado llave de eventos, no se puede verificar
            return true;
        }

        $checksum       = $payload['signature']['checksum'] ?? '';
        $properties     = $payload['signature']['properties'] ?? [];
        $timestamp      = $payload['timestamp'] ?? '';

        if (empty($checksum) || empty($properties)) {
            return false;
        }

        $concatenated = '';
        foreach ($properties as $prop) {
            $value = self::getNestedProperty($payload['data'] ?? [], $prop);
            $concatenated .= (string) $value;
        }

        $calculated = hash('sha256', $concatenated . $timestamp . $eventsKey);

        return hash_equals(strtolower($calculated), strtolower($checksum));
    }

    /**
     * Consulta una transacción directamente en el API de Wompi para verificación redundante.
     */
    public static function getTransaction(string $transactionId, string $prvKey, bool $testMode = true): ?array
    {
        $baseUrl = $testMode ? self::SANDBOX_URL : self::PRODUCTION_URL;
        $url     = $baseUrl . '/transactions/' . urlencode($transactionId);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $prvKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && is_string($response)) {
            $json = json_decode($response, true);
            return $json['data'] ?? null;
        }

        return null;
    }

    /**
     * Extrae un valor usando dot notation (ej: 'transaction.id').
     */
    private static function getNestedProperty(array $data, string $key)
    {
        $parts = explode('.', $key);
        $current = $data;

        foreach ($parts as $part) {
            if (!is_array($current) || !array_key_exists($part, $current)) {
                return '';
            }
            $current = $current[$part];
        }

        return $current;
    }
}

