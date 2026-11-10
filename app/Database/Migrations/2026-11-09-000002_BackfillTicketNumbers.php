<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillTicketNumbers extends Migration
{
    private function uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0x8000, 0xbfff), mt_rand(0x8000, 0xbfff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function up()
    {
        $raffles = $this->db->table('raffles')->get()->getResultArray();

        foreach ($raffles as $raffle) {
            $raffleId = (int) $raffle['id'];
            $longitud = (int) ($raffle['number_length'] ?? 4);
            $porBoleta = (int) ($raffle['numbers_per_ticket'] ?? 1);
            $inicio = (int) ($raffle['number_start'] ?? 0);
            $fin = (int) ($raffle['number_end'] ?? (pow(10, $longitud) - 1));

            $tickets = $this->db->table('tickets')
                ->select('tickets.id')
                ->where('tickets.raffle_id', $raffleId)
                ->whereNotIn('tickets.id', function ($builder) use ($raffleId) {
                    $builder->select('ticket_id')->from('ticket_numbers')->where('raffle_id', $raffleId);
                })
                ->orderBy('tickets.id', 'ASC')
                ->get()
                ->getResultArray();

            if (empty($tickets)) {
                continue;
            }

            $maxExistente = $this->db->table('ticket_numbers')
                ->where('raffle_id', $raffleId)
                ->selectMax('number', 'max_num')
                ->get()
                ->getRowArray();

            $cursor = $inicio;
            if (!empty($maxExistente['max_num'])) {
                $cursor = max($inicio, ((int) $maxExistente['max_num']) + 1);
            }
            $filas = [];

            foreach ($tickets as $ticket) {
                for ($n = 0; $n < $porBoleta; $n++) {
                    if ($cursor > $fin) {
                        break 2;
                    }

                    $numero = str_pad($cursor, $longitud, '0', STR_PAD_LEFT);
                    $filas[] = [
                        'uuid'           => $this->uuid(),
                        'raffle_id'      => $raffleId,
                        'ticket_id'      => (int) $ticket['id'],
                        'number'         => $numero,
                        'display_number' => $numero,
                        'is_winner'      => 0,
                        'created_at'     => date('Y-m-d H:i:s'),
                    ];
                    $cursor++;
                }
            }

            if (!empty($filas)) {
                $this->db->table('ticket_numbers')->insertBatch($filas);
            }
        }
    }

    public function down()
    {
        $this->db->query('DELETE FROM `ticket_numbers`');
    }
}
