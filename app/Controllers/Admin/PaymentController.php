<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CoursePaymentTransactionModel;
use DateTimeImmutable;

class PaymentController extends BaseController
{
    protected CoursePaymentTransactionModel $paymentTransactionModel;

    public function __construct()
    {
        $this->paymentTransactionModel = new CoursePaymentTransactionModel();
    }

    public function index()
    {
        $transactions = $this->paymentTransactionModel
            ->select('course_payment_transactions.*, users.username, users.email, users.full_name, courses.title AS course_title')
            ->join('users', 'users.id = course_payment_transactions.user_id', 'left')
            ->join('courses', 'courses.id = course_payment_transactions.course_id', 'left')
            ->orderBy('course_payment_transactions.created_at', 'DESC')
            ->findAll();

        foreach ($transactions as &$transaction) {
            $transaction['status_meta'] = $this->buildStatusMeta($transaction['status'] ?? null);
        }
        unset($transaction);

        return view('admin/payments/index', [
            'transactions' => $transactions,
        ]);
    }

    public function show($id)
    {
        $transaction = $this->paymentTransactionModel
            ->select('course_payment_transactions.*, users.username, users.email, users.full_name, users.role AS user_role, courses.title AS course_title, courses.slug AS course_slug, enrollments.enrolled_at AS enrollment_enrolled_at, enrollments.completed_at AS enrollment_completed_at, enrollments.progress_percentage AS enrollment_progress_percentage, enrollments.is_active AS enrollment_is_active')
            ->join('users', 'users.id = course_payment_transactions.user_id', 'left')
            ->join('courses', 'courses.id = course_payment_transactions.course_id', 'left')
            ->join('enrollments', 'enrollments.id = course_payment_transactions.granted_enrollment_id', 'left')
            ->where('course_payment_transactions.id', $id)
            ->first();

        if (!$transaction) {
            return redirect()->to('/admin/payments')->with('error', 'Payment transaction not found');
        }

        return view('admin/payments/show', [
            'transaction' => $transaction,
            'statusMeta' => $this->buildStatusMeta($transaction['status'] ?? null),
            'userFacts' => $this->filterFacts([
                ['label' => 'Username', 'value' => $transaction['username'] ?? null],
                ['label' => 'Email', 'value' => $transaction['email'] ?? null],
                ['label' => 'Full Name', 'value' => $transaction['full_name'] ?? 'Not provided'],
                ['label' => 'Role', 'value' => $this->formatRoleLabel($transaction['user_role'] ?? null)],
            ]),
            'courseFacts' => $this->filterFacts([
                ['label' => 'Course', 'value' => $transaction['course_title'] ?? null],
                ['label' => 'Slug', 'value' => $transaction['course_slug'] ?? null],
                ['label' => 'Enrollment Link', 'value' => $transaction['granted_enrollment_id'] ? site_url('admin/enrollments/' . $transaction['granted_enrollment_id']) : null, 'is_link' => true],
                ['label' => 'Enrollment Progress', 'value' => $transaction['enrollment_progress_percentage'] !== null ? round((float) $transaction['enrollment_progress_percentage']) . '%' : null],
                ['label' => 'Enrollment Status', 'value' => $transaction['granted_enrollment_id'] ? (!empty($transaction['enrollment_is_active']) ? 'Active' : 'Inactive') : null],
            ]),
            'paymentFacts' => $this->filterFacts([
                ['label' => 'Reference Code', 'value' => $transaction['reference_code'] ?? null],
                ['label' => 'Provider', 'value' => $transaction['provider'] ?? null],
                ['label' => 'Amount', 'value' => $this->formatAmount($transaction['currency'] ?? 'IDR', $transaction['amount'] ?? 0)],
                ['label' => 'Internal Status', 'value' => $this->formatStatusLabel($transaction['status'] ?? null)],
                ['label' => 'Provider Status', 'value' => $transaction['xendit_status'] ?? null],
                ['label' => 'Invoice ID', 'value' => $transaction['xendit_invoice_id'] ?? null],
                ['label' => 'External ID', 'value' => $transaction['xendit_external_id'] ?? null],
                ['label' => 'Failure Code', 'value' => $transaction['failure_code'] ?? null],
                ['label' => 'Failure Message', 'value' => $transaction['failure_message'] ?? null],
            ]),
            'timelineFacts' => $this->filterFacts([
                ['label' => 'Created At', 'value' => $this->formatTimestamp($transaction['created_at'] ?? null)],
                ['label' => 'Updated At', 'value' => $this->formatTimestamp($transaction['updated_at'] ?? null)],
                ['label' => 'Expires At', 'value' => $this->formatTimestamp($transaction['expires_at'] ?? null)],
                ['label' => 'Paid At', 'value' => $this->formatTimestamp($transaction['paid_at'] ?? null)],
                ['label' => 'Expired At', 'value' => $this->formatTimestamp($transaction['expired_at'] ?? null)],
                ['label' => 'Cancelled At', 'value' => $this->formatTimestamp($transaction['cancelled_at'] ?? null)],
                ['label' => 'Granted At', 'value' => $this->formatTimestamp($transaction['granted_at'] ?? null)],
                ['label' => 'Last Webhook At', 'value' => $this->formatTimestamp($transaction['last_webhook_at'] ?? null)],
                ['label' => 'Enrollment Created At', 'value' => $this->formatTimestamp($transaction['enrollment_enrolled_at'] ?? null)],
                ['label' => 'Enrollment Completed At', 'value' => $this->formatTimestamp($transaction['enrollment_completed_at'] ?? null)],
            ]),
            'linkFacts' => $this->filterFacts([
                ['label' => 'Checkout URL', 'value' => $transaction['checkout_url'] ?? null, 'is_link' => true],
                ['label' => 'Xendit Invoice URL', 'value' => $transaction['xendit_invoice_url'] ?? null, 'is_link' => true],
                ['label' => 'Success Redirect URL', 'value' => $transaction['success_redirect_url'] ?? null, 'is_link' => true],
                ['label' => 'Failure Redirect URL', 'value' => $transaction['failure_redirect_url'] ?? null, 'is_link' => true],
            ]),
        ]);
    }

    private function buildStatusMeta(?string $status): array
    {
        return match ($status) {
            'paid' => ['label' => 'Paid', 'class' => 'bg-success'],
            'failed' => ['label' => 'Failed', 'class' => 'bg-danger'],
            'expired' => ['label' => 'Expired', 'class' => 'bg-secondary'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-dark'],
            default => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
        };
    }

    private function filterFacts(array $facts): array
    {
        return array_values(array_filter($facts, static function (array $fact): bool {
            return isset($fact['value']) && $fact['value'] !== '';
        }));
    }

    private function formatAmount(string $currency, $amount): string
    {
        return strtoupper($currency) . ' ' . number_format((int) $amount);
    }

    private function formatStatusLabel(?string $status): string
    {
        return $status === null ? '-' : ucfirst($status);
    }

    private function formatRoleLabel(?string $role): string
    {
        if ($role === null || trim($role) === '') {
            return '-';
        }

        return ucwords(str_replace('_', ' ', $role));
    }

    private function formatTimestamp(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return (new DateTimeImmutable($value))->format('M d, Y H:i');
        } catch (\Exception) {
            return $value;
        }
    }

}
