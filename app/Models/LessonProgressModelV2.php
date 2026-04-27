<!-- its not longer used -->
<!--</?php

namespace App\Models;

use CodeIgniter\Model;

 
class LessonProgressModelV2 extends Model
{
    protected $table = 'lesson_progress';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'user_id', 'lesson_id', 'status', 'progress_percentage',
        'started_at', 'completed_at', 'last_position', 'notes'
    ];
    
    protected $useTimestamps = false;
    
    protected $validationRules = [
        'user_id' => 'required|integer',
        'lesson_id' => 'required|integer',
        'status' => 'required|in_list[not_started,in_progress,completed]',
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
        ],
        'lesson_id' => [
            'required' => 'Lesson ID is required',
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Status must be one of: not_started, in_progress, completed',
        ],
    ];
    
    protected $skipValidation = false;
    
    /**
     * Record lesson progress for a user
     * 
     * @param int $userId User ID
     * @param int $lessonId Lesson ID
     * @param string $status Status of progress
     * @param float $progressPercentage Progress percentage (0-100)
     * @param int $lastPosition Video position in seconds
     * @param string $notes Optional notes
     * @return array|bool Progress record if successful, false if failed
     */
    public function recordProgress($userId, $lessonId, $status = 'in_progress', $progressPercentage = 0, $lastPosition = 0, $notes = null)
    {
        // Check if record already exists
        $existingRecord = $this->where('user_id', $userId)
                               ->where('lesson_id', $lessonId)
                               ->first();
        
        $now = date('Y-m-d H:i:s');
        
        if ($existingRecord) {
            // Update existing record
            $data = [
                'status' => $status,
                'progress_percentage' => $progressPercentage,
                'last_position' => $lastPosition,
            ];
            
            // Only set started_at if it's null
            if (empty($existingRecord['started_at'])) {
                $data['started_at'] = $now;
            }
            
            // Set completed_at only if status is 'completed' and it wasn't already completed
            if ($status === 'completed' && empty($existingRecord['completed_at'])) {
                $data['completed_at'] = $now;
            }
            
            // Update notes if provided
            if ($notes !== null) {
                $data['notes'] = $notes;
            }
            
            $this->update($existingRecord['id'], $data);
            return $this->find($existingRecord['id']);
        } else {
            // Create new record
            $data = [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'status' => $status,
                'progress_percentage' => $progressPercentage,
                'last_position' => $lastPosition,
                'started_at' => $now,
                'notes' => $notes,
            ];
            
            // Set completed_at if status is 'completed'
            if ($status === 'completed') {
                $data['completed_at'] = $now;
            }
            
            $this->insert($data);
            return $this->where('user_id', $userId)
                        ->where('lesson_id', $lessonId)
                        ->first();
        }
    }
    
    /**
     * Get progress for a specific user and lesson
     * 
     * @param int $userId User ID
     * @param int $lessonId Lesson ID
     * @return array|null Progress record or null if not found
     */
    public function getUserLessonProgress($userId, $lessonId)
    {
        return $this->where('user_id', $userId)
                    ->where('lesson_id', $lessonId)
                    ->first();
    }
    
    /**
     * Get all progress records for a user
     * 
     * @param int $userId User ID
     * @return array Progress records
     */
    public function getUserProgress($userId)
    {
        return $this->where('user_id', $userId)
                    ->findAll();
    }
    
    /**
     * Get all progress records for a user in a specific module
     * 
     * @param int $userId User ID
     * @param int $moduleId Module ID
     * @return array Progress records
     */
    public function getUserModuleProgress($userId, $moduleId)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('lesson_progress as lp');
        $builder->select('lp.*, l.title as lesson_title, l.module_id')
                ->join('lessons as l', 'l.id = lp.lesson_id')
                ->where('lp.user_id', $userId)
                ->where('l.module_id', $moduleId);
        
        return $builder->get()->getResultArray();
    }
}

-->