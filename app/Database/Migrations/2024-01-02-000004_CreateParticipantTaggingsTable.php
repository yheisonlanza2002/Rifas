<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParticipantTaggingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'participant_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'tag_id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['participant_id', 'tag_id'], 'uq_participant_tagging');
        $this->forge->addForeignKey('participant_id', 'participants', 'id', 'CASCADE', 'CASCADE', 'fk_participant_taggings_participant');
        $this->forge->addForeignKey('tag_id', 'participant_tags', 'id', 'CASCADE', 'CASCADE', 'fk_participant_taggings_tag');
        $this->forge->createTable('participant_taggings', true, ['ENGINE' => 'InnoDB', 'CHARSET' => 'utf8mb4', 'COLLATE' => 'utf8mb4_unicode_ci']);
    }

    public function down()
    {
        $this->forge->dropTable('participant_taggings');
    }
}