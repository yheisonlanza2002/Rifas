<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPrizesAddImage extends Migration
{
    public function up()
    {
        $this->forge->addColumn('prizes', [
            'prize_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'image',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('prizes', 'prize_image');
    }
}
