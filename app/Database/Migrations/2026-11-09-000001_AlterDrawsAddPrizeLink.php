<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterDrawsAddPrizeLink extends Migration
{
    public function up()
    {
        $this->forge->addColumn('draws', [
            'prize_id' => [
                'type'       => 'BIGINT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'raffle_id',
            ],
            'lottery_result' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'seed',
            ],
        ]);

        $this->db->query(
            'ALTER TABLE `draws`
             ADD INDEX `idx_draws_prize` (`prize_id`),
             ADD CONSTRAINT `fk_draws_prize` FOREIGN KEY (`prize_id`) REFERENCES `prizes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE'
        );
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `draws` DROP FOREIGN KEY `fk_draws_prize`');
        $this->db->query('ALTER TABLE `draws` DROP INDEX `idx_draws_prize`');
        $this->forge->dropColumn('draws', 'prize_id');
        $this->forge->dropColumn('draws', 'lottery_result');
    }
}
