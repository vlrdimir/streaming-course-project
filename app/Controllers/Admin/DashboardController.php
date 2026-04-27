<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CourseModel;
use App\Models\CoursePaymentTransactionModel;
use App\Models\ModuleModel;
use App\Models\LessonModel;
use App\Models\EnrollmentModel;

class DashboardController extends BaseController
{
    protected $userModel;
    protected $courseModel;
    protected $moduleModel;
    protected $lessonModel;
    protected $enrollmentModel;
    protected $paymentTransactionModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->courseModel = new CourseModel();
        $this->moduleModel = new ModuleModel();
        $this->lessonModel = new LessonModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->paymentTransactionModel = new CoursePaymentTransactionModel();
    }
    
    public function index()
    {
        $premiumStats = $this->getPremiumSalesStats();

        // Get statistics for dashboard
        $data = [
            'totalUsers' => $this->userModel->countAll(),
            'totalCourses' => $this->courseModel->countAll(),
            'totalModules' => $this->moduleModel->countAll(),
            'totalLessons' => $this->lessonModel->countAll(),
            'totalEnrollments' => $this->enrollmentModel->countAll(),
            'premiumStats' => $premiumStats,
            'recentCourses' => $this->courseModel->orderBy('created_at', 'DESC')->findAll(5),
            'recentUsers' => $this->userModel->orderBy('created_at', 'DESC')->findAll(5)
        ];
        
        // Calculate course status counts
        $data['publishedCourses'] = $this->courseModel->where('status', 'published')->countAllResults();
        $data['privateCourses'] = $this->courseModel->where('status', 'private')->countAllResults();
        $data['draftCourses'] = $this->courseModel->where('status', 'draft')->countAllResults();
        
        return view('admin/dashboard', $data);
    }

    private function getPremiumSalesStats(): array
    {
        $summary = $this->paymentTransactionModel
            ->select([
                'COUNT(course_payment_transactions.id) AS total_transactions',
                'COUNT(DISTINCT course_payment_transactions.course_id) AS unique_courses',
                'COUNT(DISTINCT course_payment_transactions.user_id) AS unique_buyers',
            ])
            ->join('courses', 'courses.id = course_payment_transactions.course_id', 'inner')
            ->where('course_payment_transactions.status', 'paid')
            ->where('courses.is_premium', true)
            ->first() ?? [];

        $currencyTotals = $this->paymentTransactionModel
            ->select('course_payment_transactions.currency, SUM(course_payment_transactions.amount) AS total_amount')
            ->join('courses', 'courses.id = course_payment_transactions.course_id', 'inner')
            ->where('course_payment_transactions.status', 'paid')
            ->where('courses.is_premium', true)
            ->groupBy('course_payment_transactions.currency')
            ->orderBy('total_amount', 'DESC')
            ->findAll();

        $formattedRevenue = 'IDR 0';
        $revenueNote = 'Belum ada transaksi premium yang selesai.';

        if ($currencyTotals !== []) {
            $formattedRevenueParts = [];

            foreach ($currencyTotals as $currencyTotal) {
                $formattedRevenueParts[] = $this->formatAmount(
                    (string) ($currencyTotal['currency'] ?? 'IDR'),
                    (int) ($currencyTotal['total_amount'] ?? 0)
                );
            }

            $formattedRevenue = implode(' • ', $formattedRevenueParts);
            $revenueNote = count($formattedRevenueParts) > 1
                ? 'Total omzet premium dari checkout berbayar, digabung per mata uang.'
                : 'Total omzet premium dari checkout berbayar yang sudah berhasil.';
        }

        return [
            'revenueDisplay' => $formattedRevenue,
            'revenueNote' => $revenueNote,
            'totalTransactions' => (int) ($summary['total_transactions'] ?? 0),
            'uniqueCourses' => (int) ($summary['unique_courses'] ?? 0),
            'uniqueBuyers' => (int) ($summary['unique_buyers'] ?? 0),
        ];
    }

    private function formatAmount(string $currency, int $amount): string
    {
        return strtoupper($currency) . ' ' . number_format($amount);
    }
}

