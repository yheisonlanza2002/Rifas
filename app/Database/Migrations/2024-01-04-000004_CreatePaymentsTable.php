<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
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
            'sale_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'gateway_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
            ],
            'fee_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00',
            ],
            'net_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => false,
                'default' => '0.00',
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => false,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
                'default' => 'PENDING',
            ],
            'transaction_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'gateway' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
            ],
            'raw_request' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'raw_response' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'paid_at' => [
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
        $this->forge->addKey('sale_id', false, false, 'idx_payments_sale');
        $this->forge->addKey('gateway_id');
        $this->forge->addKey(['company_id', 'status'], false, false, 'idx_payments_company_status');
        $this->forge->addKey(['company_id', 'paid_at'], false, false, 'idx_payments_paid');
        $this->forge->addKey(['gateway', 'transaction_reference'], false, false, 'idx_payments_gateway_reference');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'RESTRICT', 'fk_payments_company');
        $this->forge->addForeignKey('sale_id', 'sales', 'id', 'CASCADE', 'RESTRICT', 'fk_payments_sale');
        $this->forge->addForeignKey('gateway_id', 'payment_gateways', 'id', 'CASCADE', 'SET NULL', 'fk_payments_gateway');
        $this->forge->createTable('payments', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}