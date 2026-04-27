<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CorrectPaymentAmountColumnsAndGenericTransactionFields extends Migration
{
    public function up()
    {
        $this->db->query(
            "ALTER TABLE courses ALTER COLUMN price_amount TYPE INT USING CASE WHEN price_amount IS NULL THEN NULL ELSE ROUND(price_amount)::INTEGER END"
        );

        $this->db->query(
            "ALTER TABLE course_payment_transactions ALTER COLUMN amount TYPE INT USING ROUND(amount)::INTEGER"
        );

        $this->forge->addColumn('course_payment_transactions', [
            'checkout_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'status_payload_json' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->db->query(
            "UPDATE course_payment_transactions
            SET checkout_url = COALESCE(checkout_url, xendit_invoice_url),
                status_payload_json = COALESCE(status_payload_json, last_webhook_payload, response_payload, metadata_payload)"
        );
    }

    public function down()
    {
        $this->forge->dropColumn('course_payment_transactions', [
            'checkout_url',
            'status_payload_json',
        ]);

        $this->db->query(
            "ALTER TABLE course_payment_transactions ALTER COLUMN amount TYPE DECIMAL(12,2) USING amount::DECIMAL(12,2)"
        );

        $this->db->query(
            "ALTER TABLE courses ALTER COLUMN price_amount TYPE DECIMAL(12,2) USING CASE WHEN price_amount IS NULL THEN NULL ELSE price_amount::DECIMAL(12,2) END"
        );
    }
}
