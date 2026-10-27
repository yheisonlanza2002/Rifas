<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketNumbersTable extends Migration
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
            'number' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
            'display_number' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
            'is_winner' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ],
            'won_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey(['raffle_id', 'number'], 'uq_ticket_numbers_raffle_number');
        $this->forge->addUniqueKey(['ticket_id', 'number'], 'uq_ticket_numbers_ticket_number');
        $this->forge->addKey('ticket_id', false, false, 'idx_ticket_numbers_ticket');
        $this->forge->addKey(['raffle_id', 'display_number'], false, false, 'idx_ticket_numbers_display');
        $this->forge->addKey('is_winner');
        $this->forge->addForeignKey('raffle_id', 'raffles', 'id', 'CASCADE', 'RESTRICT', 'fk_ticket_numbers_raffle');
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'RESTRICT', 'fk_ticket_numbers_ticket');
        $this->forge->createTable('ticket_numbers', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('ticket_numbers');
    }
}