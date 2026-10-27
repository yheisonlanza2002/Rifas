<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalesTable extends Migration
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
            'participant_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'sale_code' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => false,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
                'default' => 'PENDING',
            ],
            'quantity' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
                'default' => 0,
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00',
            ],
            'discount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00',
            ],
            'tax_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00',
            ],
            'commission_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00',
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00',
            ],
            'paid_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00',
            ],
            'pending_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00',
            ],
            'currency' => [
                'type' => 'CHAR',
                'constraint' => 3,
                'null' => false,
                'default' => 'COP',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sold_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expires_at' => [
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
            'cancelled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey(['company_id', 'sale_code'], 'uq_sales_company_code');
        $this->forge->addKey('participant_id', false, false, 'idx_sales_participant');
        $this->forge->addKey(['company_id', 'status'], false, false, 'idx_sales_company_status');
        $this->forge->addKey(['company_id', 'created_at'], false, false, 'idx_sales_created');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'RESTRICT', 'fk_sales_company');
        $this->forge->addForeignKey('participant_id', 'participants', 'id', 'CASCADE', 'RESTRICT', 'fk_sales_participant');
        $this->forge->createTable('sales', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('sales');
    }
}