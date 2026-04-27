<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Course Lessons</h1>
        <a href="<?= site_url('admin/course/' . $course['id'] . '/modules/' . $module['id'] . '/lessons/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Lesson
        </a>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course') ?>">Courses</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course/' . $course['id'] . '/modules') ?>">Modules</a></li>
        <li class="breadcrumb-item active">Lessons</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-book me-1"></i>
                    Lessons for: <?= $module['title'] ?>
                </div>
                <div>
                    <span class="badge bg-<?= $course['status'] === 'published' ? 'success' : ($course['status'] === 'private' ? 'warning' : 'secondary') ?>">
                        <?= ucfirst($course['status']) ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($lessons)): ?>
                <div class="alert alert-info">
                    No lessons found for this module. Click "Add New Lesson" to create one.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">Order</th>
                                <th width="35%">Title</th>
                                <th width="25%">Video URL</th>
                                <th width="15%">Duration</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="lessons-list">
                            <?php foreach ($lessons as $lesson): ?>
                            <tr data-lesson-id="<?= $lesson['id'] ?>">
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $lesson['order_index'] + 1 ?></span>
                                    <div class="mt-1">
                                        <i class="fas fa-arrows-alt handle" style="cursor: move;"></i>
                                    </div>
                                </td>
                                <td><?= $lesson['title'] ?></td>
                                <td><?= $lesson['video_url'] ?></td>
                                <td class="text-center"><?= $lesson['video_duration'] ?> min</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/course/' . $course['id'] . '/modules/' . $module['id'] . '/lessons/' . $lesson['id'] . '/edit') ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?= site_url('admin/course/' . $course['id'] . '/modules/' . $module['id'] . '/lessons/' . $lesson['id'] . '/delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this lesson?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize sortable
        const lessonsList = document.getElementById('lessons-list');
        if (lessonsList) {
            new Sortable(lessonsList, {
                handle: '.handle',
                animation: 150,
                onEnd: function() {
                    // Get the new order
                    const lessons = [];
                    document.querySelectorAll('#lessons-list tr').forEach(function(row) {
                        lessons.push(row.dataset.lessonId);
                    });
                    
                    // Save the new order
                    fetch('<?= site_url('admin/api/courses/' . $course['id'] . '/modules/' . $module['id'] . '/lessons/reorder') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                        },
                        body: JSON.stringify({ lessons: lessons })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the order numbers
                            document.querySelectorAll('#lessons-list tr').forEach(function(row, index) {
                                row.querySelector('.badge').textContent = index + 1;
                            });
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                Lesson order updated successfully
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.table-responsive'));
                            setTimeout(() => alertDiv.remove(), 3000);
                        } else {
                            // Show error message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                Failed to save lesson order: ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.table-responsive'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error message
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                        alertDiv.innerHTML = `
                            An error occurred while saving the lesson order
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.table-responsive'));
                    });
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>

