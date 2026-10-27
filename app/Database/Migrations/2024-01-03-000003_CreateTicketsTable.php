<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketsTable extends Migration
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
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => false,
            ],
            'serial_prefix' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'AVAILABLE',
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
            ],
            'number_quantity' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'bundle_quantity' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
                'default' => 1,
            ],
            'reserved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reservation_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'sold_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'cancelled_at' => [
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
        $this->forge->addUniqueKey(['raffle_id', 'code'], 'uq_tickets_raffle_code');
        $this->forge->addKey(['company_id', 'status'], false, false, 'idx_tickets_company_status');
        $this->forge->addKey(['raffle_id', 'status'], false, false, 'idx_tickets_raffle_status');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'RESTRICT', 'fk_tickets_company');
        $this->forge->addForeignKey('raffle_id', 'raffles', 'id', 'CASCADE', 'RESTRICT', 'fk_tickets_raffle');
        $this->forge->createTable('tickets', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('tickets');
    }
}