<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey(['course_id', 'category_id'], true);
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('course_categories');
    }

    public function down()
    {
        $this->forge->dropTable('course_categories');
    }
} 