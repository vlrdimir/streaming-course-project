<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniquePendingConstraintToCoursePaymentTransactions extends Migration
{
    private const UNIQUE_PENDING_INDEX = 'course_payment_transactions_pending_user_course_unique';

    public function up()
    {
        $this->db->query(
            "UPDATE course_payment_transactions
            SET status = 'expired',
                expired_at = COALESCE(expired_at, expires_at, CURRENT_TIMESTAMP),
                updated_at = CURRENT_TIMESTAMP
            WHERE status = 'pending'
              AND expires_at IS NOT NULL
              AND expires_at <= CURRENT_TIMESTAMP"
        );

        $this->db->query(
            "WITH ranked_pending AS (
                SELECT id,
                       ROW_NUMBER() OVER (
                           PARTITION BY user_id, course_id
                           ORDER BY created_at DESC NULLS LAST, id DESC
                       ) AS pending_rank
                FROM course_payment_transactions
                WHERE status = 'pending'
            )
            UPDATE course_payment_transactions AS transactions
            SET status = 'expired',
                expired_at = COALESCE(transactions.expired_at, transactions.expires_at, CURRENT_TIMESTAMP),
                updated_at = CURRENT_TIMESTAMP
            FROM ranked_pending
            WHERE transactions.id = ranked_pending.id
              AND ranked_pending.pending_rank > 1"
        );

        $this->db->query(
            "CREATE UNIQUE INDEX IF NOT EXISTS " . self::UNIQUE_PENDING_INDEX . "
            ON course_payment_transactions (user_id, course_id)
            WHERE status = 'pending'"
        );
    }

    public function down()
    {
        $this->db->query('DROP INDEX IF EXISTS ' . self::UNIQUE_PENDING_INDEX);
    }
}
