<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPrizesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('prizes', [
            'lottery_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => true,
                'after' => 'raffle_id',
            ],
            'prize_type' => [
                'type' => "ENUM('PRIZE','MONEY')",
                'null' => false,
                'default' => 'PRIZE',
                'after' => 'lottery_id',
            ],
            'recurrence_days' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'prize_type',
            ],
            'draw_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'recurrence_days',
            ],
        ]);

        $this->forge->addKey('lottery_id');
        $this->forge->addForeignKey('lottery_id', 'lotteries', 'id', 'CASCADE', 'SET NULL', 'fk_prizes_lottery');
    }

    public function down()
    {
        $this->forge->dropForeignKey('prizes', 'fk_prizes_lottery');
        $this->forge->dropColumn('prizes', ['lottery_id', 'prize_type', 'recurrence_days', 'draw_date']);
    }
}
