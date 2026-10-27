<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWinnersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'draw_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'raffle_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'prize_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'ticket_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'ticket_number_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'participant_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
                'default' => 'PENDING',
            ],
            'announced_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notification_sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'claim_deadline' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'claimed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey(['draw_id', 'ticket_number_id'], 'uq_winner_draw_number');
        $this->forge->addKey('prize_id');
        $this->forge->addKey('participant_id', false, false, 'idx_winners_participant');
        $this->forge->addKey('raffle_id', false, false, 'idx_winners_raffle');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('draw_id', 'draws', 'id', 'CASCADE', 'RESTRICT', 'fk_winners_draw');
        $this->forge->addForeignKey('raffle_id', 'raffles', 'id', 'CASCADE', 'RESTRICT', 'fk_winners_raffle');
        $this->forge->addForeignKey('prize_id', 'prizes', 'id', 'CASCADE', 'RESTRICT', 'fk_winners_prize');
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'RESTRICT', 'fk_winners_ticket');
        $this->forge->addForeignKey('ticket_number_id', 'ticket_numbers', 'id', 'CASCADE', 'RESTRICT', 'fk_winners_ticket_number');
        $this->forge->addForeignKey('participant_id', 'participants', 'id', 'CASCADE', 'RESTRICT', 'fk_winners_participant');
        $this->forge->createTable('winners', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('winners');
    }
}