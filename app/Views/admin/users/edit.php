<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit User</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/users') ?>">Users</a></li>
        <li class="breadcrumb-item active">Edit User</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit me-1"></i>
            User Details
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

            <?php if (!$canManagePrivilegedRoles): ?>
                <div class="alert alert-info">
                    Anda hanya dapat menetapkan role User.
                </div>
            <?php endif; ?>
            
            <form action="<?= site_url('admin/users/' . $user['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= old('username', $user['username']) ?>" required>
                            <div class="form-text">Username must be unique and may contain letters, numbers, spaces, and hyphens.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Leave blank to keep current password. New password must be at least 8 characters long.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= old('full_name', $user['full_name']) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <?php foreach ($assignableRoles as $role): ?>
                                    <option value="<?= esc($role) ?>" <?= old('role', $user['role']) === $role ? 'selected' : '' ?>>
                                        <?= esc(ucwords(str_replace('_', ' ', $role))) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($canManagePrivilegedRoles): ?>
                                <div class="form-text">Super admins can elevate or reduce privileged roles here.</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="5"><?= old('bio', $user['bio']) ?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

