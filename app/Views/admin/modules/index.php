<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Course Modules</h1>
        <a href="<?= site_url('admin/course/' . $course['id'] . '/modules/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Module
        </a>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course') ?>">Courses</a></li>
        <li class="breadcrumb-item active">Modules</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-folder me-1"></i>
                    Modules for: <?= $course['title'] ?>
                </div>
                <div>
                    <span class="badge bg-<?= $course['status'] === 'published' ? 'success' : ($course['status'] === 'private' ? 'warning' : 'secondary') ?>">
                        <?= ucfirst($course['status']) ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($modules)): ?>
                <div class="alert alert-info">
                    No modules found for this course. Click "Add New Module" to create one.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">Order</th>
                                <th width="40%">Title</th>
                                <th width="30%">Description</th>
                                <th width="10%">Lessons</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="modules-list">
                            <?php foreach ($modules as $module): ?>
                            <tr data-module-id="<?= $module['id'] ?>">
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $module['order_index'] + 1 ?></span>
                                    <div class="mt-1">
                                        <i class="fas fa-arrows-alt handle" style="cursor: move;"></i>
                                    </div>
                                </td>
                                <td><?= $module['title'] ?></td>
                                <td><?= substr($module['description'] ?? '', 0, 100) ?><?= strlen($module['description'] ?? '') > 100 ? '...' : '' ?></td>
                                <td class="text-center">
                                    <a href="<?= site_url('admin/course/' . $course['id'] . '/modules/' . $module['id'] . '/lessons') ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-list"></i> Lessons
                                    </a>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/course/' . $course['id'] . '/modules/' . $module['id'] . '/edit') ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?= site_url('admin/course/' . $course['id'] . '/modules/' . $module['id'] . '/delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this module?')">
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
        const modulesList = document.getElementById('modules-list');
        if (modulesList) {
            new Sortable(modulesList, {
                handle: '.handle',
                animation: 150,
                onEnd: function() {
                    // Get the new order
                    const modules = [];
                    document.querySelectorAll('#modules-list tr').forEach(function(row) {
                        modules.push(row.dataset.moduleId);
                    });
                    
                    // Save the new order
                    fetch('<?= site_url('admin/api/courses/' . $course['id'] . '/modules/reorder') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                        },
                        body: JSON.stringify({ modules: modules })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the order numbers
                            document.querySelectorAll('#modules-list tr').forEach(function(row, index) {
                                row.querySelector('.badge').textContent = index + 1;
                            });
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                Module order updated successfully
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.table-responsive'));
                            setTimeout(() => alertDiv.remove(), 3000);
                        } else {
                            // Show error message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                Failed to save module order: ${data.message}
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
                            An error occurred while saving the module order
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

