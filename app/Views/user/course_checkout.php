<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<?php $hasPendingTransaction = !empty($pendingTransaction); ?>
<?php $purchaseStatusLabel = $hasPendingTransaction ? 'Checkout aktif tersedia' : 'Siap dibuat'; ?>
<div class="container mx-auto px-4 py-8">
    <nav class="mb-4 text-sm" aria-label="Breadcrumb">
        <ol class="list-none p-0 inline-flex space-x-2">
            <li class="flex items-center">
                <a href="<?= site_url('user/courses') ?>" class="text-muted-foreground hover:text-primary">Kursus</a>
            </li>
            <li class="flex items-center">
                <span class="text-muted-foreground mx-2">/</span>
                <a href="<?= site_url('user/view-course/' . $course['id']) ?>" class="text-muted-foreground hover:text-primary"><?= esc($course['title']) ?></a>
            </li>
            <li class="flex items-center">
                <span class="text-muted-foreground mx-2">/</span>
                <span class="text-foreground">Checkout</span>
            </li>
        </ol>
    </nav>

    <?php if (session()->getFlashdata('error')): ?>
        <?= view('components/alert', [
            'type' => 'error',
            'title' => 'Checkout gagal',
            'message' => esc(session()->getFlashdata('error')),
        ]) ?>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <?= view('components/alert', [
            'type' => 'success',
            'title' => 'Informasi',
            'message' => esc(session()->getFlashdata('success')),
        ]) ?>
    <?php endif; ?>

    <?php if ($hasPendingTransaction): ?>
        <?= view('components/alert', [
            'type' => 'info',
            'title' => 'Transaksi pending ditemukan',
            'message' => 'Kami akan memakai ulang checkout yang masih aktif untuk kursus ini' . (!empty($pendingTransaction['expires_at']) ? ' sampai ' . date('d M Y H:i', strtotime($pendingTransaction['expires_at'])) : '') . '.',
        ]) ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-background border rounded-lg p-6">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">Premium</span>
                    <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-medium"><?= htmlspecialchars(strtoupper((string) ($course['price_currency'] ?? 'IDR')), ENT_QUOTES, 'UTF-8') ?> <?= number_format((int) ($course['price_amount'] ?? 0)) ?></span>
                </div>

                <h1 class="text-3xl font-bold text-foreground mb-2">Checkout Kursus</h1>
                <p class="text-muted-foreground mb-6">Tinjau detail pembelianmu sebelum melanjutkan ke halaman pembayaran Xendit.</p>

                <div class="rounded-lg border bg-muted/40 p-5 space-y-4">
                    <div>
                        <p class="text-sm text-muted-foreground mb-1">Kursus</p>
                        <p class="text-lg font-semibold text-foreground"><?= esc($course['title']) ?></p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-muted-foreground mb-1">Harga</p>
                            <p class="text-lg font-semibold text-foreground"><?= htmlspecialchars(strtoupper((string) ($course['price_currency'] ?? 'IDR')), ENT_QUOTES, 'UTF-8') ?> <?= number_format((int) ($course['price_amount'] ?? 0)) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground mb-1">Jenis Pembayaran</p>
                            <p class="text-lg font-semibold text-foreground">Sekali bayar</p>
                        </div>
                    </div>
                </div>

                <form action="<?= site_url('user/view-course/' . $course['id'] . '/checkout') ?>" method="post" class="mt-6">
                    <?= csrf_field() ?>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-primary text-primary-foreground rounded-md font-medium hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                            <i class="fas fa-credit-card mr-2"></i><?= $hasPendingTransaction ? 'Lanjutkan Pembayaran' : 'Bayar sekarang' ?>
                        </button>
                        <a href="<?= site_url('user/view-course/' . $course['id']) ?>" class="w-full sm:w-auto px-6 py-3 border border-muted-foreground text-muted-foreground rounded-md font-medium text-center hover:bg-muted hover:text-foreground">
                            Kembali ke Detail Kursus
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="sticky top-8">
                <div class="bg-background border rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-foreground mb-4">Ringkasan</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-muted-foreground">Level</span>
                            <span class="font-medium text-foreground"><?= htmlspecialchars(ucfirst((string) ($course['level'] ?? 'general')), ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-muted-foreground">Status Pembelian</span>
                            <span class="font-medium text-foreground"><?= htmlspecialchars($purchaseStatusLabel, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-muted-foreground">Total dibayar</span>
                            <span class="font-semibold text-foreground"><?= htmlspecialchars(strtoupper((string) ($course['price_currency'] ?? 'IDR')), ENT_QUOTES, 'UTF-8') ?> <?= number_format((int) ($course['price_amount'] ?? 0)) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
