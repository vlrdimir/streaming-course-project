<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Categories</h1>
        <a href="<?= site_url('admin/categories/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Category
        </a>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Categories</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All Categories
        </div>
        <div class="card-body">
            <?php if (empty($categories)): ?>
                <div class="alert alert-info">
                    No categories found. Click "Add New Category" to create one.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="categoriesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                            
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['id'] ?></td>
                                <td><?= $category['name'] ?></td>
                                <td><?= $category['slug'] ?></td>
                             
                                <td><?= substr($category['description'] ?? '', 0, 100) ?><?= strlen($category['description'] ?? '') > 100 ? '...' : '' ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/categories/' . $category['id'] . '/edit') ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?= site_url('admin/categories/' . $category['id'] . '/delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category?')">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const table = new simpleDatatables.DataTable("#categoriesTable");
    });
</script>
<?= $this->endSection() ?>

