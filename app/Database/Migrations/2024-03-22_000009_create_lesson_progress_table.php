<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLessonProgressTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'lesson_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'not_started',
            ],
            'progress_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'lesson_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('lesson_id', 'lessons', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lesson_progress');
    }

    public function down()
    {
        $this->forge->dropTable('lesson_progress');
    }
} 
