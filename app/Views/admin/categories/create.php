<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Add New Category</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/categories') ?>">Categories</a></li>
        <li class="breadcrumb-item active">Add New</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Category Details
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
            
            <form action="<?= site_url('admin/categories') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="slug" name="slug" value="<?= old('slug') ?>" required>
                    <div class="form-text">The slug is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</div>
                </div>
                

                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= old('description') ?></textarea>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="<?= site_url('admin/categories') ?>" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate slug from name
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        nameInput.addEventListener('keyup', function() {
            slugInput.value = nameInput.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        });
    });
</script>
<?= $this->endSection() ?>

