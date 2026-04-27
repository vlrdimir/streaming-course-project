<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Payments</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Payments</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-credit-card me-1"></i>
            All Payment Transactions
        </div>
        <div class="card-body">
            <?php if (empty($transactions)): ?>
                <div class="alert alert-info">
                    No payment transactions found.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="paymentsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Course</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Reference</th>
                                <th>Timeline</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?= $transaction['id'] ?></td>
                                    <td>
                                        <div><?= esc($transaction['full_name'] ?: $transaction['username'] ?: 'Unknown user') ?></div>
                                        <small class="text-muted"><?= esc($transaction['email'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <div><?= esc($transaction['course_title'] ?? 'Unknown course') ?></div>
                                        <small class="text-muted">Course ID: <?= esc($transaction['course_id']) ?></small>
                                    </td>
                                    <td>
                                        <div><?= esc(strtoupper((string) ($transaction['currency'] ?? 'IDR'))) ?> <?= number_format((int) ($transaction['amount'] ?? 0)) ?></div>
                                        <small class="text-muted"><?= esc($transaction['provider'] ?? 'xendit') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge <?= esc($transaction['status_meta']['class']) ?>"><?= esc($transaction['status_meta']['label']) ?></span>
                                        <?php if (!empty($transaction['xendit_status'])): ?>
                                            <div><small class="text-muted">Provider: <?= esc($transaction['xendit_status']) ?></small></div>
                                        <?php endif; ?>
                                        <?php if (!empty($transaction['failure_code'])): ?>
                                            <div><small class="text-danger">Code: <?= esc($transaction['failure_code']) ?></small></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div><?= esc($transaction['reference_code']) ?></div>
                                        <?php if (!empty($transaction['xendit_external_id'])): ?>
                                            <small class="text-muted d-block">External: <?= esc($transaction['xendit_external_id']) ?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($transaction['xendit_invoice_id'])): ?>
                                            <small class="text-muted d-block">Invoice: <?= esc($transaction['xendit_invoice_id']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div><small><strong>Created:</strong> <?= !empty($transaction['created_at']) ? date('M d, Y H:i', strtotime($transaction['created_at'])) : '-' ?></small></div>
                                        <div><small><strong>Expires:</strong> <?= !empty($transaction['expires_at']) ? date('M d, Y H:i', strtotime($transaction['expires_at'])) : '-' ?></small></div>
                                        <div><small><strong>Paid:</strong> <?= !empty($transaction['paid_at']) ? date('M d, Y H:i', strtotime($transaction['paid_at'])) : '-' ?></small></div>
                                        <div><small><strong>Webhook:</strong> <?= !empty($transaction['last_webhook_at']) ? date('M d, Y H:i', strtotime($transaction['last_webhook_at'])) : '-' ?></small></div>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('admin/payments/' . $transaction['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View Details
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
        const table = new simpleDatatables.DataTable("#paymentsTable");
    });
</script>
<?= $this->endSection() ?>
