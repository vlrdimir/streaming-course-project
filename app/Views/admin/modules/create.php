<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Add New Module</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course') ?>">Courses</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course/' . $course['id'] . '/modules') ?>">Modules</a></li>
        <li class="breadcrumb-item active">Add New</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-folder-plus me-1"></i>
                    Add Module to: <?= $course['title'] ?>
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
            
            <form action="<?= site_url('admin/course/' . $course['id'] . '/modules') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Module Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= old('description') ?></textarea>
                    <div class="form-text">Provide a brief description of what this module covers.</div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="<?= site_url('admin/course/' . $course['id'] . '/modules') ?>" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Module</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            Module Information
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h5 class="alert-heading">About Modules</h5>
                <p>Modules are used to organize your course content into logical sections. Each module can contain multiple lessons.</p>
                <hr>
                <p class="mb-0">After creating a module, you can add lessons to it from the modules page.</p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>Module Structure Best Practices:</h5>
                    <ul>
                        <li>Keep module titles clear and concise</li>
                        <li>Group related content together in the same module</li>
                        <li>Maintain a logical progression of topics</li>
                        <li>Consider breaking large topics into multiple modules</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Example Module Structure:</h5>
                    <ol>
                        <li>Introduction to the Course</li>
                        <li>Getting Started with [Topic]</li>
                        <li>Core Concepts</li>
                        <li>Advanced Techniques</li>
                        <li>Practical Applications</li>
                        <li>Final Project</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

