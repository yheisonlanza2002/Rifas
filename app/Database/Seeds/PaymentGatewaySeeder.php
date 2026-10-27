<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $companyId = $db->table('companies')->where('slug', 'rifas-demo')->get()->getRow('id') ?? 1;

        $gateways = [
            [
                'name' => 'Wompi',
                'slug' => 'wompi',
                'driver' => 'wompi',
                'config' => json_encode([
                    'public_key' => env('WOMPI_PUBLIC_KEY', ''),
                    'private_key' => env('WOMPI_PRIVATE_KEY', ''),
                    'integrity_key' => env('WOMPI_INTEGRITY_KEY', ''),
                    'redirect_url' => env('WOMPI_REDIRECT_URL', ''),
                    'webhook_url' => env('WOMPI_WEBHOOK_URL', ''),
                    'currency' => 'COP',
                    'country' => 'CO',
                ]),
                'supported_methods' => json_encode(['CARD', 'NEQUI', 'BANCOLOMBIA', 'BANCO_DE_BOGOTA', 'DAVIPLATA', 'EFECTY', 'PSE']),
                'test_mode' => 1,
                'status' => 'ACTIVE',
                'sort_order' => 1,
            ],
            [
                'name' => 'Mercado Pago',
                'slug' => 'mercado_pago',
                'driver' => 'mercado_pago',
                'config' => json_encode([
                    'access_token' => env('MERCADO_PAGO_ACCESS_TOKEN', ''),
                    'public_key' => env('MERCADO_PAGO_PUBLIC_KEY', ''),
                    'webhook_secret' => env('MERCADO_PAGO_WEBHOOK_SECRET', ''),
                    'currency' => 'COP',
                    'country' => 'CO',
                ]),
                'supported_methods' => json_encode(['CARD', 'BALOTO', 'EFECTY', 'PSE', 'NEQUI', 'DAVIPLATA']),
                'test_mode' => 1,
                'status' => 'ACTIVE',
                'sort_order' => 2,
            ],
            [
                'name' => 'PayU',
                'slug' => 'payu',
                'driver' => 'payu',
                'config' => json_encode([
                    'api_key' => env('PAYU_API_KEY', ''),
                    'merchant_id' => env('PAYU_MERCHANT_ID', ''),
                    'account_id' => env('PAYU_ACCOUNT_ID', ''),
                    'currency' => 'COP',
                    'country' => 'CO',
                    'test_mode' => true,
                ]),
                'supported_methods' => json_encode(['CARD', 'PSE', 'EFECTY', 'BALOTO', 'NEQUI', 'DAVIPLATA']),
                'test_mode' => 1,
                'status' => 'INACTIVE',
                'sort_order' => 3,
            ],
            [
                'name' => 'Efecty (Manual)',
                'slug' => 'efecty_manual',
                'driver' => 'manual',
                'config' => json_encode([
                    'instructions' => 'Paga en cualquier punto Efecty con el código de referencia.',
                    'currency' => 'COP',
                ]),
                'supported_methods' => json_encode(['EFECTY']),
                'test_mode' => 0,
                'status' => 'ACTIVE',
                'sort_order' => 4,
            ],
            [
                'name' => 'Transferencia Bancaria (Manual)',
                'slug' => 'bank_transfer_manual',
                'driver' => 'manual',
                'config' => json_encode([
                    'instructions' => 'Transfiere a la cuenta bancaria indicada y envía el comprobante.',
                    'currency' => 'COP',
                    'bank_accounts' => [],
                ]),
                'supported_methods' => json_encode(['BANK_TRANSFER']),
                'test_mode' => 0,
                'status' => 'ACTIVE',
                'sort_order' => 5,
            ],
        ];

        foreach ($gateways as $gateway) {
            $exists = $db->table('payment_gateways')->where('slug', $gateway['slug'])->countAllResults();
            if ($exists > 0) {
                continue;
            }
            $gateway['company_id'] = $companyId;
            $gateway['uuid'] = $this->generateUuid();
            $gateway['created_at'] = date('Y-m-d H:i:s');
            $gateway['updated_at'] = date('Y-m-d H:i:s');
            $db->table('payment_gateways')->insert($gateway);
        }
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0x8000, 0xbfff),
            mt_rand(0x8000, 0xbfff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}