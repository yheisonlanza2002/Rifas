<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrizesTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'position' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'quantity' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
                'default' => 1,
            ],
            'value' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'currency' => [
                'type' => 'CHAR',
                'constraint' => 3,
                'null' => false,
                'default' => 'COP',
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'odds' => [
                'type' => 'DECIMAL',
                'constraint' => '8,4',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'ACTIVE',
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
        $this->forge->addUniqueKey(['raffle_id', 'position'], 'uq_prizes_raffle_position');
        $this->forge->addKey('raffle_id', false, false, 'idx_prizes_raffle');
        $this->forge->addKey('status');
        $this->forge->addKey('deleted_at');
        $this->forge->addForeignKey('raffle_id', 'raffles', 'id', 'CASCADE', 'RESTRICT', 'fk_prizes_raffle');
        $this->forge->createTable('prizes', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('prizes');
    }
}