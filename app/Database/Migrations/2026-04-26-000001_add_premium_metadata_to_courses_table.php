<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPremiumMetadataToCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'is_premium' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'price_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'price_currency' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'IDR',
            ],
            'is_purchasable' => [
                'type' => 'BOOLEAN',
                'default' => true,
            ],
        ]);

        $this->forge->addKey('is_premium');
        $this->forge->addKey('is_purchasable');
        $this->forge->processIndexes('courses');
    }

    public function down()
    {
        $this->forge->dropColumn('courses', [
            'is_premium',
            'price_amount',
            'price_currency',
            'is_purchasable',
        ]);
    }
}
