<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Enrollment Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/enrollments') ?>">Enrollments</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
    
    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Enrollment Information
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="border-bottom pb-2">User Information</h5>
                        <p class="mb-1"><strong>Username:</strong> <?= $enrollment['username'] ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?= $enrollment['email'] ?></p>
                        <p class="mb-1"><strong>Full Name:</strong> <?= $enrollment['full_name'] ?? 'Not provided' ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="border-bottom pb-2">Course Information</h5>
                        <p class="mb-1"><strong>Course:</strong> <?= $enrollment['course_title'] ?></p>
                        <p class="mb-1"><strong>Slug:</strong> <?= $enrollment['course_slug'] ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="border-bottom pb-2">Enrollment Status</h5>
                        <p class="mb-1"><strong>Enrolled Date:</strong> <?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?></p>
                        <p class="mb-1">
                            <strong>Completion Status:</strong> 
                            <?php if ($enrollment['completed_at']): ?>
                                <span class="badge bg-success">Completed</span>
                            <?php else: ?>
                                <span class="badge bg-warning">In Progress</span>
                            <?php endif; ?>
                        </p>
                        <?php if ($enrollment['completed_at']): ?>
                            <p class="mb-1"><strong>Completion Date:</strong> <?= date('M d, Y', strtotime($enrollment['completed_at'])) ?></p>
                        <?php endif; ?>
                        <p class="mb-1"><strong>Progress:</strong> <?= round($enrollment['progress_percentage']) ?>%</p>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar <?= $enrollment['progress_percentage'] >= 100 ? 'bg-success' : 'bg-primary' ?>" 
                                 role="progressbar" 
                                 style="width: <?= $enrollment['progress_percentage'] ?>%;" 
                                 aria-valuenow="<?= $enrollment['progress_percentage'] ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?= round($enrollment['progress_percentage']) ?>%
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tasks me-1"></i>
                    Lesson Progress
                </div>
                <div class="card-body">
                    <?php if (empty($lessonProgress)): ?>
                        <div class="alert alert-info">
                            No lesson progress data available.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="lessonProgressTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>Lesson</th>
                                        <th>Status</th>
                                        <th>Started</th>
                                        <th>Completed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $currentModule = '';
                                    foreach ($lessonProgress as $progress): 
                                        $isNewModule = $currentModule !== $progress['module_title'];
                                        $currentModule = $progress['module_title'];
                                    ?>
                                    <tr <?= $isNewModule ? 'class="table-light"' : '' ?>>
                                        <td><?= $isNewModule ? $progress['module_title'] : '' ?></td>
                                        <td><?= $progress['lesson_title'] ?></td>
                                        <td>
                                            <?php if ($progress['status'] === 'completed'): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php elseif ($progress['status'] === 'in_progress'): ?>
                                                <span class="badge bg-warning">In Progress</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Not Started</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($progress['started_at']): ?>
                                                <?= date('M d, Y', strtotime($progress['started_at'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($progress['completed_at']): ?>
                                                <?= date('M d, Y', strtotime($progress['completed_at'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i>
                    Progress Timeline
                </div>
                <div class="card-body">
                    <canvas id="progressChart" width="100%" height="40"></canvas>
                </div>
            </div> -->
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = new simpleDatatables.DataTable("#lessonProgressTable");
        
        // Create a simple progress chart (placeholder)
        const ctx = document.getElementById('progressChart');
        const progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                datasets: [{
                    label: 'Progress Percentage',
                    data: [0, 15, 30, 45, 60, 75, <?= round($enrollment['progress_percentage']) ?>],
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Progress (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>

