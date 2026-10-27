<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RaffleSettingSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $settings = [
            [
                'uuid' => $this->generateUuid(),
                'raffle_id' => 1,
                'key' => 'min_participants',
                'value' => '1',
                'type' => 'int',
                'description' => 'Mínimo de participantes para habilitar la rifa',
                'is_public' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUuid(),
                'raffle_id' => 1,
                'key' => 'max_participants',
                'value' => '1000',
                'type' => 'int',
                'description' => 'Máximo de participantes permitidos',
                'is_public' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUuid(),
                'raffle_id' => 1,
                'key' => 'enable_auto_draw',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Automatizar el sorteo al llegar al ticket_quantity',
                'is_public' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUuid(),
                'raffle_id' => 1,
                'key' => 'enable_email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enviar correos de notificación a ganadores',
                'is_public' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUuid(),
                'raffle_id' => 1,
                'key' => 'enable_sms_notifications',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Enviar notificaciones SMS a ganadores',
                'is_public' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUuid(),
                'raffle_id' => 1,
                'key' => 'terms_conditions_url',
                'value' => '',
                'type' => 'url',
                'description' => 'URL a los términos y condiciones de la rifa',
                'is_public' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUuid(),
                'raffle_id' => 1,
                'key' => 'currency_format',
                'value' => 'COP',
                'type' => 'string',
                'description' => 'Formato de moneda para mostrar precios',
                'is_public' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($settings as $setting) {
            $exists = $db->table('raffle_settings')
                ->where('raffle_id', $setting['raffle_id'])
                ->where('key', $setting['key'])
                ->countAllResults();
            if ($exists > 0) {
                continue;
            }
            $setting['uuid'] = $this->generateUuid();
            $setting['created_at'] = date('Y-m-d H:i:s');
            $setting['updated_at'] = date('Y-m-d H:i:s');
            $db->table('raffle_settings')->insert($setting);
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