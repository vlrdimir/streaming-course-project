<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropPayloadColumnsFromCoursePaymentTransactions extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('course_payment_transactions', [
            'metadata_payload',
            'request_payload',
            'response_payload',
            'last_webhook_payload',
            'status_payload_json',
        ]);
    }

    public function down()
    {
        $this->forge->addColumn('course_payment_transactions', [
            'metadata_payload' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'request_payload' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'response_payload' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'last_webhook_payload' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_payload_json' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }
}
