<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWebhooksTable extends Migration
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
            'gateway_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
            ],
            'gateway' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => false,
            ],
            'event_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'payload' => [
                'type' => 'JSON',
                'null' => false,
            ],
            'headers' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'RECEIVED',
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'retry_count' => [
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addKey('gateway_id');
        $this->forge->addKey('gateway');
        $this->forge->addKey('event_type');
        $this->forge->addKey('status');
        $this->forge->addKey('processed_at');
        $this->forge->addForeignKey('gateway_id', 'payment_gateways', 'id', 'CASCADE', 'SET NULL', 'fk_webhooks_gateway');
        $this->forge->createTable('webhooks', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('webhooks');
    }
}