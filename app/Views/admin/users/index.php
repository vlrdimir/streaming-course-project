<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h1 class="mt-4">Users</h1>
        <div class="mt-4 text-md-end">
            <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New User
            </a>
            <div class="small text-muted mt-2">
                <?php if ($canManagePrivilegedRoles): ?>
                    You can create users and assign admin privileges.
                <?php else: ?>
                    You can create standard users. Admin and super admin changes remain restricted.
                <?php endif; ?>
            </div>
        </div>
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Users</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            All Users
        </div>
        <div class="card-body">
            <?php if (empty($users)): ?>
                <div class="alert alert-info">
                    No users found. Click "Add New User" to create one.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Full Name</th>
                                <th>Role</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <?php
                                $isPrivilegedUser = in_array($user['role'], ['admin', 'super_admin'], true);
                                $canManageThisUser = $canManagePrivilegedRoles || !$isPrivilegedUser;
                            ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td><?= $user['full_name'] ?? '-' ?></td>
                                <td>
                                    <?php if ($user['role'] === 'super_admin'): ?>
                                        <span class="badge bg-dark">Super Admin</span>
                                    <?php elseif ($user['role'] === 'admin'): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">User</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['last_login']): ?>
                                        <?= date('M d, Y H:i', strtotime($user['last_login'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Never</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($canManageThisUser): ?>
                                        <div class="btn-group" role="group">
                                            <a href="<?= site_url('admin/users/' . $user['id'] . '/edit') ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="<?= site_url('admin/users/' . $user['id'] . '/delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Super admin only</span>
                                        <div><small class="text-muted">Privileged account management is restricted.</small></div>
                                    <?php endif; ?>
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
        const table = new simpleDatatables.DataTable("#usersTable");
    });
</script>
<?= $this->endSection() ?>

