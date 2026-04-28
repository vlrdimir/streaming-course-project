<?php

namespace App\Models;

use CodeIgniter\Model;

class CoursePaymentTransactionModel extends Model
{
    private array $heldCheckoutLocks = [];

    protected $table = 'course_payment_transactions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'course_id',
        'granted_enrollment_id',
        'reference_code',
        'provider',
        'status',
        'xendit_status',
        'xendit_invoice_id',
        'xendit_external_id',
        'xendit_invoice_url',
        'success_redirect_url',
        'failure_redirect_url',
        'amount',
        'currency',
        'customer_email',
        'customer_name',
        'customer_phone',
        'expires_at',
        'paid_at',
        'expired_at',
        'cancelled_at',
        'granted_at',
        'last_webhook_at',
        'failure_code',
        'failure_message',
        'checkout_url',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function findActivePendingTransaction(int $userId, int $courseId): ?array
    {
        $now = date('Y-m-d H:i:s');

        $transaction = $this->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'pending')
            ->groupStart()
                ->where('expires_at', null)
                ->orWhere('expires_at >', $now)
            ->groupEnd()
            ->orderBy('created_at', 'DESC')
            ->first();

        return $transaction ?: null;
    }

    public function acquireCheckoutLock(int $userId, int $courseId): void
    {
        $this->db->query('SELECT pg_advisory_lock(?, ?)', [$userId, $courseId]);
        $this->heldCheckoutLocks[$this->getCheckoutLockKey($userId, $courseId)] = true;
    }

    public function releaseCheckoutLock(int $userId, int $courseId): void
    {
        $lockKey = $this->getCheckoutLockKey($userId, $courseId);

        if (!isset($this->heldCheckoutLocks[$lockKey])) {
            return;
        }

        try {
            $this->db->query('SELECT pg_advisory_unlock(?, ?)', [$userId, $courseId]);
        } finally {
            unset($this->heldCheckoutLocks[$lockKey]);
        }
    }

    public function expireStalePendingTransactions(int $userId, int $courseId): int
    {
        $now = date('Y-m-d H:i:s');

        $this->db->query(
            'UPDATE course_payment_transactions
            SET status = ?,
                expired_at = COALESCE(expired_at, expires_at, ?),
                updated_at = ?
            WHERE user_id = ?
              AND course_id = ?
              AND status = ?
              AND expires_at IS NOT NULL
              AND expires_at <= ?',
            ['expired', $now, $now, $userId, $courseId, 'pending', $now]
        );

        return $this->db->affectedRows();
    }

    public function countHistoricalAttempts(int $userId, int $courseId): int
    {
        return $this->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->countAllResults();
    }

    public function findByWebhookPayload(array $payload): ?array
    {
        $invoiceId = $this->extractIdentifier($payload, ['id', 'invoice_id', 'payment_id']);
        $externalId = $this->extractIdentifier($payload, ['external_id', 'reference_code']);

        if ($invoiceId !== null) {
            $transaction = $this->where('xendit_invoice_id', $invoiceId)->first();

            if ($transaction) {
                return $transaction;
            }
        }

        if ($externalId !== null) {
            $transaction = $this->groupStart()
                ->where('xendit_external_id', $externalId)
                ->orWhere('reference_code', $externalId)
                ->groupEnd()
                ->orderBy('created_at', 'DESC')
                ->first();

            if ($transaction) {
                return $transaction;
            }
        }

        return null;
    }

    public function findReturnTransactionForUser(int $userId, array $identifiers = []): ?array
    {
        $builder = $this->select('course_payment_transactions.*, courses.title AS course_title, courses.slug AS course_slug')
            ->join('courses', 'courses.id = course_payment_transactions.course_id', 'left')
            ->where('course_payment_transactions.user_id', $userId)
            ->orderBy('course_payment_transactions.created_at', 'DESC');

        $invoiceId = $this->extractIdentifier($identifiers, ['id', 'invoice_id', 'payment_id']);
        $externalId = $this->extractIdentifier($identifiers, ['external_id', 'reference_code']);
        $courseId = isset($identifiers['course_id']) && is_numeric($identifiers['course_id'])
            ? (int) $identifiers['course_id']
            : null;

        if ($invoiceId !== null) {
            $builder->where('course_payment_transactions.xendit_invoice_id', $invoiceId);
        } elseif ($externalId !== null) {
            $builder->groupStart()
                ->where('course_payment_transactions.xendit_external_id', $externalId)
                ->orWhere('course_payment_transactions.reference_code', $externalId)
                ->groupEnd();
        } elseif ($courseId !== null) {
            $builder->where('course_payment_transactions.course_id', $courseId);
        }

        $transaction = $builder->first();

        return $transaction ?: null;
    }

    public function findRecentTransactionsForUser(int $userId, int $limit = 5): array
    {
        return $this->select('course_payment_transactions.*, courses.title AS course_title, courses.slug AS course_slug')
            ->join('courses', 'courses.id = course_payment_transactions.course_id', 'left')
            ->where('course_payment_transactions.user_id', $userId)
            ->orderBy('course_payment_transactions.created_at', 'DESC')
            ->findAll($limit);
    }

    public function findTransactionsForUser(int $userId): array
    {
        return $this->select('course_payment_transactions.*, courses.title AS course_title, courses.slug AS course_slug')
            ->join('courses', 'courses.id = course_payment_transactions.course_id', 'left')
            ->where('course_payment_transactions.user_id', $userId)
            ->orderBy('course_payment_transactions.created_at', 'DESC')
            ->findAll();
    }

    public function countTransactionsByStatusForUser(int $userId, string $status): int
    {
        return $this->where('user_id', $userId)
            ->where('status', $status)
            ->countAllResults();
    }

    private function extractIdentifier(array $payload, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = trim((string) ($payload[$key] ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function getCheckoutLockKey(int $userId, int $courseId): string
    {
        return $userId . ':' . $courseId;
    }
}
