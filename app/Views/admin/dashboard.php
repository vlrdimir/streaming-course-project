<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    
    <!-- Dashboard Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><?= $totalCourses ?></h5>
                            <div class="small">Total Courses</div>
                        </div>
                        <i class="fas fa-book-open fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= site_url('admin/course') ?>">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><?= $totalUsers ?></h5>
                            <div class="small">Total Users</div>
                        </div>
                        <i class="fas fa-users fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><?= $totalModules ?></h5>
                            <div class="small">Total Modules</div>
                        </div>
                        <i class="fas fa-folder fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><?= $totalEnrollments ?></h5>
                            <div class="small">Total Enrollments</div>
                        </div>
                        <i class="fas fa-user-graduate fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Course Status Chart -->
    <!-- <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Course Status
                </div>
                <div class="card-body">
                    <canvas id="courseStatusChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Recent Enrollments
                </div>
                <div class="card-body">
                    <canvas id="enrollmentsChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div> -->
    
    <!-- Recent Courses -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-book me-1"></i>
            Recent Courses
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Level</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentCourses as $course): ?>
                        <tr>
                            <td><?= $course['title'] ?></td>
                            <td>
                                <?php if ($course['status'] === 'published'): ?>
                                    <span class="badge bg-success">Published</span>
                                <?php elseif ($course['status'] === 'private'): ?>
                                    <span class="badge bg-warning">Private</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?= ucfirst($course['level']) ?></td>
                            <td><?= date('M d, Y', strtotime($course['created_at'])) ?></td>
                            <td>
                                <a href="<?= site_url('admin/course/' . $course['id'] . '/edit') ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="<?= site_url('admin/course/' . $course['id'] . '/modules') ?>" class="btn btn-sm btn-info">Modules</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Course Status Chart
    var ctx = document.getElementById("courseStatusChart");
    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["Published", "Private", "Draft"],
            datasets: [{
                data: [<?= $publishedCourses ?>, <?= $privateCourses ?>, <?= $draftCourses ?>],
                backgroundColor: ['#28a745', '#ffc107', '#6c757d'],
            }],
        },
    });
    
    // Enrollments Chart (placeholder data)
    var ctx2 = document.getElementById("enrollmentsChart");
    var myBarChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ["January", "February", "March", "April", "May", "June"],
            datasets: [{
                label: "Enrollments",
                backgroundColor: "rgba(2,117,216,1)",
                borderColor: "rgba(2,117,216,1)",
                data: [4215, 5312, 6251, 7841, 9821, 14984],
            }],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<?= $this->endSection() ?>

