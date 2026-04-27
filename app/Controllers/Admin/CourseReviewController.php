<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\CourseReviewModel;
use App\Models\UserModel;

class CourseReviewController extends BaseController
{
    protected $courseModel;
    protected $courseReviewModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->courseReviewModel = new CourseReviewModel();
        $this->userModel = new UserModel();
    }
    
    public function index($courseId)
    {
        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            return redirect()->to('/admin/course')->with('error', 'Course not found');
        }
        
        // Mendapatkan review untuk course ini
        $reviews = $this->courseReviewModel->getCourseReviews($courseId);
        
        // Mendapatkan rata-rata rating
        $averageRating = $this->courseReviewModel->getAverageRating($courseId) ?? 0;
        
        // Mendapatkan distribusi rating
        $ratingDistribution = $this->courseReviewModel->getRatingDistribution($courseId);
        
        // Menghitung total review
        $totalReviews = count($reviews);
        
        // Menambahkan informasi user ke setiap review
        foreach ($reviews as &$review) {
            $user = $this->userModel->find($review['user_id']);
            if ($user) {
                $review['username'] = $user['username'];
                $review['full_name'] = $user['full_name'] ?? $user['username'];
                $review['email'] = $user['email'];
            } else {
                $review['username'] = 'Unknown User';
                $review['full_name'] = 'Unknown User';
                $review['email'] = '';
            }
        }
        
        $data = [
            'course' => $course,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'ratingDistribution' => $ratingDistribution,
            'totalReviews' => $totalReviews
        ];
        
        return view('admin/course/reviews', $data);
    }
    
    public function delete($courseId, $reviewId)
    {
        $review = $this->courseReviewModel->find($reviewId);
        
        if (!$review || $review['course_id'] != $courseId) {
            return redirect()->to('/admin/course/' . $courseId . '/reviews')->with('error', 'Review not found');
        }
        
        // Hapus review
        $this->courseReviewModel->delete($reviewId);
        
        return redirect()->to('/admin/course/' . $courseId . '/reviews')->with('message', 'Review deleted successfully');
    }
} 