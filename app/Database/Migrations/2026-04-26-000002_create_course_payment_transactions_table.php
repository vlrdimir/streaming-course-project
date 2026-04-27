<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursePaymentTransactionsTable extends Migration
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
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'granted_enrollment_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'reference_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'provider' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'xendit',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'pending',
            ],
            'xendit_status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'xendit_invoice_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'xendit_external_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'xendit_invoice_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'success_redirect_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'failure_redirect_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'currency' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'IDR',
            ],
            'customer_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'customer_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'paid_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expired_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'cancelled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'granted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_webhook_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'failure_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'failure_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
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
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('reference_code');
        $this->forge->addUniqueKey('xendit_invoice_id');
        $this->forge->addUniqueKey('xendit_external_id');
        $this->forge->addUniqueKey('granted_enrollment_id');
        $this->forge->addKey(['user_id', 'course_id']);
        $this->forge->addKey(['user_id', 'course_id', 'status']);
        $this->forge->addKey('status');
        $this->forge->addKey('expires_at');
        $this->forge->addKey('paid_at');
        $this->forge->addKey('last_webhook_at');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('granted_enrollment_id', 'enrollments', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('course_payment_transactions');
    }

    public function down()
    {
        $this->forge->dropTable('course_payment_transactions');
    }
}
