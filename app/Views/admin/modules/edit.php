<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Module</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course') ?>">Courses</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course/' . $course['id'] . '/modules') ?>">Modules</a></li>
        <li class="breadcrumb-item active">Edit Module</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-edit me-1"></i>
                    Edit Module in: <?= $course['title'] ?>
                </div>
                <div>
                    <span class="badge bg-<?= $course['status'] === 'published' ? 'success' : ($course['status'] === 'private' ? 'warning' : 'secondary') ?>">
                        <?= ucfirst($course['status']) ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="<?= site_url('admin/course/' . $course['id'] . '/modules/' . $module['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Module Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= old('title', $module['title']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= old('description', $module['description']) ?></textarea>
                    <div class="form-text">Provide a brief description of what this module covers.</div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="<?= site_url('admin/course/' . $course['id'] . '/modules') ?>" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Module</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-list me-1"></i>
            Module Lessons
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Lessons in this Module</h5>
                <a href="<?= site_url('admin/course/' . $course['id'] . '/modules/' . $module['id'] . '/lessons') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-list"></i> Manage Lessons
                </a>
            </div>
            
            <?php
            // This would typically be populated from the controller
            $lessons = isset($lessons) ? $lessons : [];
            ?>
            
            <?php if (empty($lessons)): ?>
                <div class="alert alert-info">
                    No lessons found in this module. Click "Manage Lessons" to add some.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Title</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lessons as $lesson): ?>
                            <tr>
                                <td><?= $lesson['order_index'] + 1 ?></td>
                                <td><?= $lesson['title'] ?></td>
                                <td><?= gmdate('H:i:s', $lesson['video_duration']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

