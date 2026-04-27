<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Models\CourseReviewModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class ReviewController extends BaseController
{
    protected $courseReviewModel;
    protected $courseModel;
    protected $enrollmentModel;
    protected $currentUser;

    public function __construct()
    {
        $this->courseReviewModel = new CourseReviewModel();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
        
        // Set currentUser jika user sudah login
        if (session()->has('id')) {
            $userModel = new \App\Models\UserModel();
            $this->currentUser = $userModel->find(session()->get('id'));
        }
    }

    protected function isLoggedIn()
    {
        return session()->has('id');
    }

    public function index($courseId)
    {
        if (!$this->isLoggedIn()) {
            log_message('error', 'User not logged in');
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return redirect()->back()->with('error', 'Kursus tidak ditemukan.');
        }

        $isEnrolled = $this->enrollmentModel->isEnrolled($this->currentUser['id'], $courseId);
        $reviews = $this->courseReviewModel->getCourseReviews($courseId);
        $averageRating = $this->courseReviewModel->getAverageRating($courseId);
        $ratingDistribution = $this->courseReviewModel->getRatingDistribution($courseId);
        $userReview = $this->courseReviewModel->getUserReview($courseId, $this->currentUser['id']);
        
        return view('user/course-reviews', [
            'course' => $course,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'ratingDistribution' => $ratingDistribution,
            'userReview' => $userReview,
            'user' => $this->currentUser,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    public function create($courseId)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            log_message('error', 'Course not found');
            return redirect()->back()->with('error', 'Kursus tidak ditemukan.');
        }

        // Cek apakah pengguna sudah terdaftar di kursus
        $isEnrolled = $this->enrollmentModel->isEnrolled($this->currentUser['id'], $courseId);
        if (!$isEnrolled) {
            return redirect()->to("/course/$courseId/reviews")->with('error', 'Anda harus terdaftar di kursus ini untuk memberikan ulasan.');
        }
       

        // Cek apakah pengguna sudah memberikan ulasan
        $hasReviewed = $this->courseReviewModel->hasUserReviewed($courseId, $this->currentUser['id']);
        if ($hasReviewed) {
            log_message('error', 'User already reviewed course');
            return redirect()->to("/course/$courseId/reviews")->with('error', 'Anda sudah memberikan ulasan untuk kursus ini.');
        }

        return view('user/create-review', [
            'course' => $course,
            'user' => $this->currentUser,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    public function store($courseId)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return redirect()->back()->with('error', 'Kursus tidak ditemukan.');
        }

        // Cek apakah pengguna sudah terdaftar di kursus
        $isEnrolled = $this->enrollmentModel->isEnrolled($this->currentUser['id'], $courseId);
        if (!$isEnrolled) {
            return redirect()->back()->with('error', 'Anda harus terdaftar di kursus ini untuk memberikan ulasan.');
        }

        // Cek apakah pengguna sudah memberikan ulasan
        $hasReviewed = $this->courseReviewModel->hasUserReviewed($courseId, $this->currentUser['id']);
        if ($hasReviewed) {
            return redirect()->to("/course/$courseId/reviews")->with('error', 'Anda sudah memberikan ulasan untuk kursus ini.');
        }

        $rules = [
            'rating' => 'required|numeric|min_length[1]|max_length[1]|in_list[1,2,3,4,5]',
            'review' => 'required|min_length[10]|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'course_id' => $courseId,
            'user_id' => $this->currentUser['id'],
            'rating' => $this->request->getPost('rating'),
            'review' => $this->request->getPost('review'),
        ];

        $this->courseReviewModel->insert($data);

        return redirect()->to("/course/$courseId/reviews")->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function edit($courseId)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            return redirect()->back()->with('error', 'Kursus tidak ditemukan.');
        }

        $userReview = $this->courseReviewModel->getUserReview($courseId, $this->currentUser['id']);
        if (!$userReview) {
            return redirect()->to("/course/$courseId/reviews/create")->with('info', 'Anda belum memberikan ulasan untuk kursus ini.');
        }

        return view('user/edit-review', [
            'course' => $course,
            'review' => $userReview,
            'user' => $this->currentUser,
        ]);
    }

    public function update($courseId)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userReview = $this->courseReviewModel->getUserReview($courseId, $this->currentUser['id']);
        if (!$userReview) {
            return redirect()->to("/course/$courseId/reviews/create")->with('info', 'Anda belum memberikan ulasan untuk kursus ini.');
        }

        $rules = [
            'rating' => 'required|numeric|min_length[1]|max_length[1]|in_list[1,2,3,4,5]',
            'review' => 'required|min_length[10]|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'rating' => $this->request->getPost('rating'),
            'review' => $this->request->getPost('review'),
        ];

        $this->courseReviewModel->update($userReview['id'], $data);

        return redirect()->to("/course/$courseId/reviews")->with('success', 'Ulasan Anda berhasil diperbarui!');
    }

    public function delete($courseId)
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userReview = $this->courseReviewModel->getUserReview($courseId, $this->currentUser['id']);
        if (!$userReview) {
            return redirect()->to("/course/$courseId/reviews")->with('error', 'Ulasan tidak ditemukan.');
        }

        $this->courseReviewModel->delete($userReview['id']);

        return redirect()->to("/course/$courseId/reviews")->with('success', 'Ulasan Anda berhasil dihapus!');
    }
} 
