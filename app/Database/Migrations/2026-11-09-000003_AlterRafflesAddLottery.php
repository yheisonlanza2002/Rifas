<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterRafflesAddLottery extends Migration
{
    public function up()
    {
        $this->forge->addColumn('raffles', [
            'lottery_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => true,
                'after'    => 'code',
            ],
        ]);

        $this->db->query('ALTER TABLE `raffles` ADD INDEX `idx_raffles_lottery` (`lottery_id`)');
        $this->db->query('ALTER TABLE `raffles` ADD CONSTRAINT `fk_raffles_lottery` FOREIGN KEY (`lottery_id`) REFERENCES `lotteries` (`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `raffles` DROP FOREIGN KEY `fk_raffles_lottery`');
        $this->forge->dropColumn('raffles', 'lottery_id');
    }
}
