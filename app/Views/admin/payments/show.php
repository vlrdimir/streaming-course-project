<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Payment Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/payments') ?>">Payments</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>

    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-receipt me-1"></i>
                    Payment Summary
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge <?= esc($statusMeta['class']) ?>"><?= esc($statusMeta['label']) ?></span>
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">Payment Information</h5>
                        <?php foreach ($paymentFacts as $fact): ?>
                            <p class="mb-1"><strong><?= esc($fact['label']) ?>:</strong> <?= esc($fact['value']) ?></p>
                        <?php endforeach; ?>
                    </div>

                    <div class="mb-4">
                        <h5 class="border-bottom pb-2">User Information</h5>
                        <?php foreach ($userFacts as $fact): ?>
                            <p class="mb-1"><strong><?= esc($fact['label']) ?>:</strong> <?= esc($fact['value']) ?></p>
                        <?php endforeach; ?>
                    </div>

                    <div>
                        <h5 class="border-bottom pb-2">Course Context</h5>
                        <?php foreach ($courseFacts as $fact): ?>
                            <p class="mb-1">
                                <strong><?= esc($fact['label']) ?>:</strong>
                                <?php if (!empty($fact['is_link'])): ?>
                                    <a href="<?= esc($fact['value']) ?>"><?= esc($fact['value']) ?></a>
                                <?php else: ?>
                                    <?= esc($fact['value']) ?>
                                <?php endif; ?>
                            </p>
                        <?php endforeach; ?>
                        <?php if (empty($courseFacts)): ?>
                            <p class="mb-0 text-muted">No course or enrollment context is attached yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-clock me-1"></i>
                    Transaction Timeline
                </div>
                <div class="card-body">
                    <?php if (empty($timelineFacts)): ?>
                        <div class="alert alert-info mb-0">
                            No timestamps are available for this transaction yet.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($timelineFacts as $fact): ?>
                                        <tr>
                                            <td><?= esc($fact['label']) ?></td>
                                            <td><?= esc($fact['value']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-link me-1"></i>
                    Redirect and Provider Links
                </div>
                <div class="card-body">
                    <?php if (empty($linkFacts)): ?>
                        <div class="alert alert-info mb-0">
                            No provider or redirect links are stored for this transaction.
                        </div>
                    <?php else: ?>
                        <?php foreach ($linkFacts as $fact): ?>
                            <p class="mb-2">
                                <strong><?= esc($fact['label']) ?>:</strong>
                                <a href="<?= esc($fact['value']) ?>" target="_blank" rel="noopener noreferrer"><?= esc($fact['value']) ?></a>
                            </p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>
