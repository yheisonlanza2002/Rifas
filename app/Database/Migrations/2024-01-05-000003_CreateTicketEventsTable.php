<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketEventsTable extends Migration
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
            'raffle_id' => [
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
                'null' => true,
            ],
            'event_type' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => false,
            ],
            'previous_status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'new_status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'reference_type' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
            ],
            'reference_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'metadata' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addKey(['ticket_id', 'created_at'], false, false, 'idx_ticket_events_ticket');
        $this->forge->addKey(['ticket_number_id', 'created_at'], false, false, 'idx_ticket_events_number');
        $this->forge->addKey(['raffle_id', 'created_at'], false, false, 'idx_ticket_events_raffle');
        $this->forge->addForeignKey('raffle_id', 'raffles', 'id', 'CASCADE', 'RESTRICT', 'fk_ticket_events_raffle');
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'RESTRICT', 'fk_ticket_events_ticket');
        $this->forge->addForeignKey('ticket_number_id', 'ticket_numbers', 'id', 'CASCADE', 'RESTRICT', 'fk_ticket_events_number');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'SET NULL', 'fk_ticket_events_created_by');
        $this->forge->createTable('ticket_events', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('ticket_events');
    }
}