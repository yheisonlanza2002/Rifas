<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReservationsTable extends Migration
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
            'company_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'raffle_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'participant_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'ticket_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'requested_number_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
                'default' => 'ACTIVE',
            ],
            'reserved_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'released_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'release_reason' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addKey('ticket_id', false, false, 'idx_reservations_ticket');
        $this->forge->addKey('participant_id', false, false, 'idx_reservations_participant');
        $this->forge->addKey(['status', 'expires_at'], false, false, 'idx_reservations_status_expiry');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'RESTRICT', 'fk_reservations_company');
        $this->forge->addForeignKey('raffle_id', 'raffles', 'id', 'CASCADE', 'RESTRICT', 'fk_reservations_raffle');
        $this->forge->addForeignKey('participant_id', 'participants', 'id', 'CASCADE', 'RESTRICT', 'fk_reservations_participant');
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'RESTRICT', 'fk_reservations_ticket');
        $this->forge->addForeignKey('requested_number_id', 'ticket_numbers', 'id', 'CASCADE', 'RESTRICT', 'fk_reservations_requested_number');
        $this->forge->createTable('reservations', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('reservations');
    }
}