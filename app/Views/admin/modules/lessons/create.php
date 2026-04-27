<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Add New Lesson</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course') ?>">Courses</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url("admin/course/{$course['id']}/modules") ?>"><?= $course['title'] ?></a></li>
        <li class="breadcrumb-item"><a href="<?= site_url("admin/course/{$course['id']}/modules/{$module['id']}/lessons") ?>"><?= $module['title'] ?> - Lessons</a></li>
        <li class="breadcrumb-item active">Add New</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Lesson Details
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
            
            <form action="<?= site_url("admin/course/{$course['id']}/modules/{$module['id']}/lessons") ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="10"><?= old('content') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="video_url" class="form-label">Video URL <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" id="video_url" name="video_url" value="<?= old('video_url') ?>" required>
                            <div class="form-text">Enter the URL of the video (YouTube, Vimeo, etc.)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="video_duration" class="form-label">Video Duration (minute) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="video_duration" name="video_duration" value="<?= old('video_duration') ?>" required>
                            <div class="form-text">Enter the duration of the video in seconds</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="<?= site_url("admin/course/{$course['id']}/modules/{$module['id']}/lessons") ?>" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize any required JavaScript here
</script>
<?= $this->endSection() ?>

