<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDrawsTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false,
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'executed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
                'default' => 'SCHEDULED',
            ],
            'method' => [
                'type' => "ENUM('MANUAL','RANDOM','EXTERNAL_API','BLOCKCHAIN')",
                'null' => true,
            ],
            'seed' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'result_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'external_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
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
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addKey('raffle_id', false, false, 'idx_draws_raffle');
        $this->forge->addKey('status', false, false, 'idx_draws_status');
        $this->forge->addKey(['company_id', 'scheduled_at'], false, false, 'idx_draws_scheduled');
        $this->forge->addForeignKey('company_id', 'companies', 'id', 'CASCADE', 'RESTRICT', 'fk_draws_company');
        $this->forge->addForeignKey('raffle_id', 'raffles', 'id', 'CASCADE', 'RESTRICT', 'fk_draws_raffle');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'SET NULL', 'fk_draws_created_by');
        $this->forge->createTable('draws', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('draws');
    }
}