<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Course Reviews</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course') ?>">Courses</a></li>
        <li class="breadcrumb-item active">Reviews</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-star me-1"></i>
                    Reviews for: <?= $course['title'] ?>
                </div>
                <div>
                    <a href="<?= site_url('admin/course') ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Courses
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h2 class="display-1"><?= number_format($averageRating, 1) ?></h2>
                            <div class="mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= round($averageRating)): ?>
                                        <i class="fas fa-star fa-2x text-warning"></i>
                                    <?php else: ?>
                                        <i class="far fa-star fa-2x text-warning"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <p class="lead"><?= $totalReviews ?> Reviews</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Rating Distribution</h5>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <?php 
                                    $count = $ratingDistribution[$i] ?? 0;
                                    $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                ?>
                                <div class="d-flex align-items-center mb-2">
                                    <div style="width: 60px;">
                                        <?= $i ?> <i class="fas fa-star text-warning"></i>
                                    </div>
                                    <div class="progress flex-grow-1" style="height: 20px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: <?= $percentage ?>%;" 
                                             aria-valuenow="<?= $percentage ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?= $count ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (empty($reviews)): ?>
                <div class="alert alert-info">
                    No reviews found for this course.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="reviewsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?= $review['id'] ?></td>
                                <td>
                                    <div><?= $review['full_name'] ?></div>
                                    <small class="text-muted"><?= $review['email'] ?></small>
                                </td>
                                <td class="text-center">
                                    <div>
                                        <span class="badge bg-warning"><?= $review['rating'] ?>/5</span>
                                    </div>
                                    <div class="mt-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $review['rating']): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-warning"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td><?= nl2br(esc($review['review'])) ?></td>
                                <td><?= date('M d, Y', strtotime($review['created_at'])) ?></td>
                                <td>
                                    <a href="<?= site_url('admin/course/' . $course['id'] . '/reviews/' . $review['id'] . '/delete') ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this review?')">
                                        <i class="fas fa-trash"></i> Delete
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
        const table = new simpleDatatables.DataTable("#reviewsTable");
    });
</script>
<?= $this->endSection() ?> 