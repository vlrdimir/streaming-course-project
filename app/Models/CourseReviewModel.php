<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseReviewModel extends Model
{
    protected $table            = 'course_reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['course_id', 'user_id', 'rating', 'review', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get course reviews with user information
     *
     * @param int $courseId
     * @return array
     */
    public function getCourseReviews($courseId)
    {
        return $this->select('course_reviews.*, users.full_name, users.profile_picture')
            ->join('users', 'users.id = course_reviews.user_id')
            ->where('course_id', $courseId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get average rating for a course
     *
     * @param int $courseId
     * @return float|null
     */
    public function getAverageRating($courseId)
    {
        $result = $this->selectAvg('rating')
            ->where('course_id', $courseId)
            ->first();
        
        return $result ? round((float)$result['rating'], 1) : null;
    }

    /**
     * Get rating distribution for a course
     *
     * @param int $courseId
     * @return array
     */
    public function getRatingDistribution($courseId)
    {
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $this->where('course_id', $courseId)
                ->where('rating', $i)
                ->countAllResults();
            $distribution[$i] = $count;
        }
        return $distribution;
    }

    /**
     * Check if user has already reviewed the course
     *
     * @param int $courseId
     * @param int $userId
     * @return bool
     */
    public function hasUserReviewed($courseId, $userId)
    {
        return $this->where('course_id', $courseId)
            ->where('user_id', $userId)
            ->countAllResults() > 0;
    }

    /**
     * Get user review for a course
     *
     * @param int $courseId
     * @param int $userId
     * @return array|null
     */
    public function getUserReview($courseId, $userId)
    {
        return $this->where('course_id', $courseId)
            ->where('user_id', $userId)
            ->first();
    }
} 