<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRafflesTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => false,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'DRAFT',
            ],
            'ticket_quantity' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'numbers_per_ticket' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
                'default' => 1,
            ],
            'number_start' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
            'number_end' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
            'number_length' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'number_assignment_mode' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'SEQUENTIAL',
            ],
            'number_selection_mode' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'BOTH',
            ],
            'ticket_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
            ],
            'currency' => [
                'type' => 'CHAR',
                'constraint' => 3,
                'null' => false,
                'default' => 'COP',
            ],
            'commission_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => false,
                'default' => '0.00',
            ],
            'reservation_minutes' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
                'default' => 15,
            ],
            'terms_conditions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'banner_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'gallery' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'settings' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'start_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'end_at' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey(['company_id', 'code'], 'uq_raffles_company_code');
        $this->forge->addKey(['company_id', 'status'], false, false, 'idx_raffles_company_status');
        $this->forge->addKey('deleted_at');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'RESTRICT', 'fk_raffles_company');
        $this->forge->createTable('raffles', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('raffles');
    }
}