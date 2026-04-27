<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\CoursePaymentTransactionModel;
use App\Models\EnrollmentModel;
use App\Models\LessonProgressModel;
use DateTimeImmutable;

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
}
