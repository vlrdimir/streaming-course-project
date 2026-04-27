<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<?php $transactionStatus = $transaction['status'] ?? 'pending'; ?>
<div class="container mx-auto px-4 py-8">
    <nav class="mb-4 text-sm" aria-label="Breadcrumb">
        <ol class="list-none p-0 inline-flex space-x-2">
            <li class="flex items-center">
                <a href="<?= site_url('user/courses') ?>" class="text-muted-foreground hover:text-primary">Kursus</a>
            </li>
            <li class="flex items-center">
                <span class="text-muted-foreground mx-2">/</span>
                <span class="text-foreground">Status Pembayaran</span>
            </li>
        </ol>
    </nav>

    <div class="max-w-3xl mx-auto space-y-6">
        <?= view('components/alert', [
            'type' => $statusMeta['variant'],
            'title' => $statusMeta['title'],
            'message' => $statusMeta['message'],
        ]) ?>

        <div class="bg-background border rounded-lg p-6 space-y-6">
            <div class="flex flex-wrap items-center gap-3">
                <span class="px-3 py-1 rounded-full text-sm font-medium <?= $statusMeta['variant'] === 'success' ? 'bg-green-100 text-green-800' : ($statusMeta['variant'] === 'error' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') ?>">
                    <?= esc($statusMeta['badge']) ?>
                </span>
                <span class="px-3 py-1 bg-muted text-foreground rounded-full text-sm font-medium">
                    DB status: <?= esc(strtoupper((string) $transactionStatus)) ?>
                </span>
            </div>

            <?php if ($transaction): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-muted-foreground mb-1">Kursus</p>
                        <p class="text-lg font-semibold text-foreground"><?= esc($transaction['course_title'] ?? ('Kursus #' . $transaction['course_id'])) ?></p>
                    </div>
                    <div>
                        <p class="text-muted-foreground mb-1">Referensi</p>
                        <p class="font-medium text-foreground break-all"><?= esc($transaction['reference_code']) ?></p>
                    </div>
                    <div>
                        <p class="text-muted-foreground mb-1">Total</p>
                        <p class="font-medium text-foreground"><?= esc(strtoupper((string) ($transaction['currency'] ?? 'IDR'))) ?> <?= number_format((int) ($transaction['amount'] ?? 0)) ?></p>
                    </div>
                    <div>
                        <p class="text-muted-foreground mb-1">Diperbarui</p>
                        <p class="font-medium text-foreground"><?= esc(date('d M Y H:i', strtotime($transaction['updated_at'] ?? $transaction['created_at']))) ?></p>
                    </div>
                    <div>
                        <p class="text-muted-foreground mb-1">Webhook terakhir</p>
                        <p class="font-medium text-foreground">
                            <?= !empty($transaction['last_webhook_at']) ? esc(date('d M Y H:i', strtotime($transaction['last_webhook_at']))) : 'Belum ada webhook masuk' ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-muted-foreground mb-1">Akses kursus</p>
                        <p class="font-medium text-foreground"><?= !empty($transaction['granted_enrollment_id']) ? 'Sudah diberikan' : 'Belum diberikan' ?></p>
                    </div>
                </div>

                <?php if (!empty($transaction['failure_message']) && $transactionStatus !== 'paid'): ?>
                    <div class="rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <?= esc($transaction['failure_message']) ?>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <?php if ($transactionStatus === 'paid'): ?>
                        <a href="<?= site_url('course/' . $transaction['course_id']) ?>" class="w-full sm:w-auto px-6 py-3 bg-primary text-primary-foreground rounded-md font-medium text-center hover:bg-primary/90">
                            Buka Kursus
                        </a>
                    <?php else: ?>
                        <a href="<?= site_url('user/view-course/' . $transaction['course_id']) ?>" class="w-full sm:w-auto px-6 py-3 bg-primary text-primary-foreground rounded-md font-medium text-center hover:bg-primary/90">
                            Kembali ke Detail Kursus
                        </a>
                    <?php endif; ?>
                    <a href="<?= site_url('user/courses') ?>" class="w-full sm:w-auto px-6 py-3 border border-muted-foreground text-muted-foreground rounded-md font-medium text-center hover:bg-muted hover:text-foreground">
                        Lihat Kursus Lain
                    </a>
                </div>
            <?php else: ?>
                <div class="rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                    Transaksi tidak ditemukan untuk akun ini. Jika baru kembali dari Xendit, buka lagi checkout dari halaman kursus agar sistem bisa menampilkan status yang benar dari database.
                </div>
                <div class="flex gap-3">
                    <a href="<?= site_url('user/courses') ?>" class="px-6 py-3 bg-primary text-primary-foreground rounded-md font-medium text-center hover:bg-primary/90">
                        Kembali ke Daftar Kursus
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
