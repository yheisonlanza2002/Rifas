<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentGatewaysTable extends Migration
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
                'constraint' => 80,
                'null' => false,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => false,
            ],
            'driver' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'config' => [
                'type' => 'JSON',
                'null' => false,
            ],
            'supported_methods' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'test_mode' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 1,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'ACTIVE',
            ],
            'sort_order' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
                'default' => 0,
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
        $this->forge->addUniqueKey(['company_id', 'slug'], 'uq_payment_gateways_company_slug');
        $this->forge->addKey('company_id');
        $this->forge->addKey('driver');
        $this->forge->addKey('status');
        $this->forge->addKey('deleted_at');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'RESTRICT', 'fk_payment_gateways_company');
        $this->forge->createTable('payment_gateways', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('payment_gateways');
    }
}