<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\CoursePaymentTransactionModel;
use App\Models\EnrollmentModel;
use App\Models\LessonProgressModel;
use Config\Dompdf as DompdfConfig;
use DateTimeImmutable;
use Dompdf\Dompdf;
use Dompdf\Options;

class DashboardController extends BaseController
{
    protected $courseModel;
    protected $enrollmentModel;
    protected $lessonProgressModel;
    protected $paymentTransactionModel;
    protected $currentUser;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->lessonProgressModel = new LessonProgressModel();
        $this->paymentTransactionModel = new CoursePaymentTransactionModel();
        
        // Get user from session using the helper method
        $this->currentUser = $this->getCurrentUser();
    }
    
    public function index()
    {
        $userId = $this->currentUser['id'];

       
        log_message('info', json_encode(session()->get('username')));
        
        // $userId = 1;
        
        // Get user's enrolled courses
        $enrolledCourses = $this->enrollmentModel->getUserEnrollments($userId);
        
        // Filter courses that are not completed (progress < 100%) for continue learning section
        $continueLearningCourses = array_filter($enrolledCourses, function($course) {
            return $course['progress_percentage'] < 100;
        });
        
        // Get only 3 courses for continue learning section
        $recentCourses = array_slice($continueLearningCourses, 0, 3);
        
        // Count completed lessons
        $completedLessons = $this->lessonProgressModel
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->countAllResults();
        
        // Count in-progress courses
        $inProgressCount = 0;
        $completedCount = 0;
        
        foreach ($enrolledCourses as $course) {
            if ($course['progress_percentage'] == 100) {
                $completedCount++;
            } else if ($course['progress_percentage'] > 0) {
                $inProgressCount++;
            }
        }

        return view('user/dashboard', [
            'user' => $this->currentUser,
            'enrolledCourses' => $enrolledCourses,
            'recentCourses' => $recentCourses,
            'completedLessons' => $completedLessons,
            'inProgressCount' => $inProgressCount,
            'completedCount' => $completedCount,
            'totalEnrolled' => count($enrolledCourses),
        ]);
    }

    public function paymentHistory()
    {
        $userId = (int) $this->currentUser['id'];
        $paymentTransactions = $this->decoratePaymentTransactions(
            $this->paymentTransactionModel->findTransactionsForUser($userId)
        );

        return view('user/payment_history', [
            'user' => $this->currentUser,
            'paymentTransactions' => $paymentTransactions,
            'paidTransactionCount' => $this->paymentTransactionModel->countTransactionsByStatusForUser($userId, 'paid'),
            'pendingTransactionCount' => $this->paymentTransactionModel->countTransactionsByStatusForUser($userId, 'pending'),
            'totalTransactionCount' => count($paymentTransactions),
        ]);
    }

    public function invoice(int $transactionId)
    {
        $userId = (int) $this->currentUser['id'];
        $transaction = $this->paymentTransactionModel
            ->select('course_payment_transactions.*, courses.title AS course_title, courses.slug AS course_slug')
            ->join('courses', 'courses.id = course_payment_transactions.course_id', 'left')
            ->where('course_payment_transactions.id', $transactionId)
            ->where('course_payment_transactions.user_id', $userId)
            ->first();

        if (!$transaction) {
            return redirect()->to(site_url('user/payment-history'))->with('error', 'Invoice pembayaran tidak ditemukan.');
        }

        $decoratedTransactions = $this->decoratePaymentTransactions([$transaction]);

        return $this->streamInvoicePdf($decoratedTransactions[0]);
    }

    private function decoratePaymentTransactions(array $paymentTransactions): array
    {
        foreach ($paymentTransactions as &$transaction) {
            $transaction['status_meta'] = $this->buildPaymentStatusMeta($transaction['status'] ?? null);
            $transaction['timeline_label'] = $this->formatTransactionTimelineLabel($transaction);
            $transaction['timeline_value'] = $this->formatTransactionTimelineValue($transaction);
        }

        unset($transaction);

        return $paymentTransactions;
    }

    private function buildPaymentStatusMeta(?string $status): array
    {
        return match ($status) {
            'paid' => [
                'label' => 'Paid',
                'class' => 'bg-green-100 text-green-800 border-green-200',
            ],
            'failed' => [
                'label' => 'Failed',
                'class' => 'bg-red-100 text-red-800 border-red-200',
            ],
            'expired' => [
                'label' => 'Expired',
                'class' => 'bg-slate-200 text-slate-700 border-slate-300',
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'class' => 'bg-slate-800 text-white border-slate-800',
            ],
            default => [
                'label' => 'Pending',
                'class' => 'bg-amber-100 text-amber-800 border-amber-200',
            ],
        };
    }

    private function formatTransactionTimelineLabel(array $transaction): string
    {
        return match ($transaction['status'] ?? null) {
            'paid' => 'Dibayar',
            'expired' => 'Kedaluwarsa',
            'cancelled' => 'Dibatalkan',
            default => 'Dibuat',
        };
    }

    private function formatTransactionTimelineValue(array $transaction): string
    {
        $timestamp = match ($transaction['status'] ?? null) {
            'paid' => $transaction['paid_at'] ?? null,
            'expired' => $transaction['expired_at'] ?? null,
            'cancelled' => $transaction['cancelled_at'] ?? null,
            default => $transaction['created_at'] ?? null,
        };

        return $this->formatTimestamp($timestamp) ?? '-';
    }

    private function formatTimestamp(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return (new DateTimeImmutable($value))->format('d M Y H:i');
        } catch (\Exception) {
            return $value;
        }
    }

    private function streamInvoicePdf(array $transaction)
    {
        try {
            $config = new DompdfConfig();
            $options = new Options();

            foreach ($config->options as $key => $value) {
                $options->set($key, $value);
            }

            $options->set('isRemoteEnabled', true);

            if (!is_dir($config->options['font_dir'])) {
                mkdir($config->options['font_dir'], 0755, true);
            }

            if (!is_dir($config->options['temp_dir'])) {
                mkdir($config->options['temp_dir'], 0755, true);
            }

            $dompdf = new Dompdf($options);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->loadHtml(view('user/invoice_template', $this->buildInvoiceViewData($transaction)));
            $dompdf->render();

            $referenceCode = trim((string) ($transaction['reference_code'] ?? 'invoice-' . $transaction['id']));
            $safeFilename = preg_replace('/[^A-Za-z0-9\-_]+/', '-', strtolower($referenceCode));

            return $dompdf->stream(($safeFilename ?: 'invoice') . '.pdf', [
                'Attachment' => false,
            ]);
        } catch (\Throwable $exception) {
            log_message('error', 'Failed to generate payment invoice PDF: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->to(site_url('user/payment-history'))->with('error', 'Gagal membuat invoice PDF. Silakan coba lagi.');
        }
    }

    private function buildInvoiceViewData(array $transaction): array
    {
        $customerName = trim((string) ($transaction['customer_name'] ?? ''));

        if ($customerName === '') {
            $customerName = trim((string) ($this->currentUser['full_name'] ?? $this->currentUser['username'] ?? 'Peserta Kursus'));
        }

        $courseTitle = trim((string) ($transaction['course_title'] ?? 'Kursus Premium'));
        $statusLabel = (string) ($transaction['status_meta']['label'] ?? ucfirst((string) ($transaction['status'] ?? 'pending')));
        $statusClass = match ($transaction['status'] ?? null) {
            'paid' => 'paid',
            'failed' => 'failed',
            'expired' => 'expired',
            'cancelled' => 'cancelled',
            default => 'pending',
        };

        return [
            'transaction' => $transaction,
            'invoiceTitle' => 'Invoice Pembayaran Kursus',
            'invoiceNumber' => trim((string) ($transaction['reference_code'] ?? ('INV-' . $transaction['id']))),
            'customerName' => $customerName,
            'customerEmail' => trim((string) ($transaction['customer_email'] ?? $this->currentUser['email'] ?? '-')),
            'courseTitle' => $courseTitle,
            'courseUrl' => site_url('user/view-course/' . $transaction['course_id']),
            'issuedAt' => $this->formatTimestamp($transaction['created_at'] ?? null) ?? '-',
            'paidAt' => $this->formatTimestamp($transaction['paid_at'] ?? null),
            'expiresAt' => $this->formatTimestamp($transaction['expires_at'] ?? null),
            'statusLabel' => $statusLabel,
            'statusClass' => $statusClass,
            'amountLabel' => strtoupper((string) ($transaction['currency'] ?? 'IDR')) . ' ' . number_format((int) ($transaction['amount'] ?? 0)),
            'providerLabel' => strtoupper((string) ($transaction['provider'] ?? 'xendit')),
            'invoiceUrl' => trim((string) ($transaction['xendit_invoice_url'] ?? $transaction['checkout_url'] ?? '')),
            'failureMessage' => trim((string) ($transaction['failure_message'] ?? '')),
        ];
    }
}
