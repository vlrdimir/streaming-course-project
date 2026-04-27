<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Enrollments</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Enrollments</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-graduate me-1"></i>
            All Enrollments
        </div>
        <div class="card-body">
            <?php if (empty($enrollments)): ?>
                <div class="alert alert-info">
                    No enrollments found.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="enrollmentsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Course</th>
                                <th>Enrolled Date</th>
                                <th>Progress</th>
                                <th>Completed</th>

                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrollments as $enrollment): ?>
                                <tr>
                                    <td><?= $enrollment['id'] ?></td>
                                    <td>
                                        <div><?= $enrollment['username'] ?></div>
                                        <small class="text-muted"><?= $enrollment['email'] ?></small>
                                    </td>
                                    <td><?= $enrollment['course_title'] ?></td>
                                    <td><?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar <?= $enrollment['progress_percentage'] >= 100 ? 'bg-success' : 'bg-primary' ?>"
                                                role="progressbar"
                                                style="width: <?= $enrollment['progress_percentage'] ?>%;"
                                                aria-valuenow="<?= $enrollment['progress_percentage'] ?>"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                                <?= round($enrollment['progress_percentage']) ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($enrollment['completed_at']): ?>
                                            <span class="badge bg-success">Completed</span>
                                            <div><small><?= date('M d, Y', strtotime($enrollment['completed_at'])) ?></small></div>
                                        <?php else: ?>
                                            <span class="badge bg-warning">In Progress</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <a href="<?= site_url('admin/enrollments/' . $enrollment['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = new simpleDatatables.DataTable("#enrollmentsTable");
    });
</script>
<?= $this->endSection() ?>