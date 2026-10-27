<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $templates = [
            [
                'uuid' => $this->generateUuid(),
                'company_id' => 1,
                'name' => 'Bienvenida de Participante',
                'slug' => 'welcome_participant',
                'subject' => 'Bienvenido al sistema de rifas',
                'body_html' => '<p>Hola <strong>{name}</strong>,</p><p>Bienvenido a nuestro sistema de rifas. Ya puedes empezar a participar.</p>',
                'body_text' => 'Hola {name}, bienvenido al sistema de rifas. Ya puedes empezar a participar.',
                'variables' => json_encode(['name']),
                'is_global' => 1,
                'status' => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUuid(),
                'company_id' => 1,
                'name' => 'Confirmación de Venta',
                'slug' => 'sale_confirmation',
                'subject' => 'Gracias por tu compra',
                'body_html' => '<p>Hola <strong>{name}</strong>,</p><p>Tu compra ha sido realizada con éxito.</p><p>Número de venta: <strong>{sale_code}</strong></p><p>Total pagado: <strong>{total}</strong></p>',
                'body_text' => 'Hola {name}, tu compra ha sido realizada con éxito. Número de venta: {sale_code}. Total pagado: {total}.',
                'variables' => json_encode(['name', 'sale_code', 'total']),
                'is_global' => 1,
                'status' => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUuid(),
                'company_id' => 1,
                'name' => 'Notificación de Premio',
                'slug' => 'winner_notification',
                'subject' => '¡Felicidades! Has ganado',
                'body_html' => '<p>Hola <strong>{name}</strong>,</p><p>¡Felicidades! Has ganado el premio <strong>{prize_name}</strong> en la rifa <strong>{raffle_name}</strong>.</p><p>Tu número de boleta: <strong>{ticket_number}</strong></p><p>Fecha del sorteo: <strong>{draw_date}</strong></p><p>Sigue las instrucciones para reclamar tu premio.</p>',
                'body_text' => 'Hola {name}, ¡felicidades! Has ganado el premio {prize_name} en la rifa {raffle_name}. Tu número: {ticket_number}. Fecha sorteo: {draw_date}.',
                'variables' => json_encode(['name', 'prize_name', 'raffle_name', 'ticket_number', 'draw_date']),
                'is_global' => 1,
                'status' => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'uuid' => $this->generateUuid(),
                'company_id' => 1,
                'name' => 'Recordatorio de Reserva',
                'slug' => 'reservation_reminder',
                'subject' => 'Tu boleta está por expirar',
                'body_html' => '<p>Hola <strong>{name}</strong>,</p><p>Tu reserva de boleta está por expirar. Por favor completa tu compra o será liberada.</p><p>Código de reserva: <strong>{sale_code}</strong></p><p>Expira en: <strong>{minutes}</strong> minutos</p>',
                'body_text' => 'Hola {name}, tu reserva está por expirar. Código: {sale_code}. Expira en {minutes} minutos.',
                'variables' => json_encode(['name', 'sale_code', 'minutes']),
                'is_global' => 0,
                'status' => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($templates as $template) {
            $exists = $db->table('email_templates')
                ->where('company_id', $template['company_id'])
                ->where('slug', $template['slug'])
                ->countAllResults();
            if ($exists > 0) {
                continue;
            }
            $template['created_at'] = date('Y-m-d H:i:s');
            $template['updated_at'] = date('Y-m-d H:i:s');
            $db->table('email_templates')->insert($template);
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