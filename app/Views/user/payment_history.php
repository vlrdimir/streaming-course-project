<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <?php if (session()->getFlashdata('error')): ?>
        <?= view('components/alert', [
            'type' => 'error',
            'title' => 'Status Pembayaran',
            'message' => esc(session()->getFlashdata('error')),
        ]) ?>
    <?php elseif (session()->getFlashdata('success')): ?>
        <?= view('components/alert', [
            'type' => 'success',
            'title' => 'Status Pembayaran',
            'message' => esc(session()->getFlashdata('success')),
        ]) ?>
    <?php elseif (session()->getFlashdata('message')): ?>
        <?= view('components/alert', [
            'type' => 'info',
            'title' => 'Status Pembayaran',
            'message' => esc(session()->getFlashdata('message')),
        ]) ?>
    <?php endif; ?>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground">Riwayat Pembayaran</h1>
        <p class="text-muted-foreground mt-2">Lihat semua invoice, status pembayaran, dan akses cepat untuk melanjutkan checkout kursus premium.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-background p-6 rounded-lg border">
            <h5 class="text-lg font-semibold text-foreground mb-1">Total Transaksi</h5>
            <h2 class="text-4xl font-bold text-primary mb-2"><?= $totalTransactionCount ?></h2>
            <p class="text-sm text-muted-foreground">Semua histori pembayaran yang tercatat di akun kamu.</p>
        </div>
        <div class="bg-background p-6 rounded-lg border">
            <h5 class="text-lg font-semibold text-foreground mb-1">Sudah Dibayar</h5>
            <h2 class="text-4xl font-bold text-primary mb-2"><?= $paidTransactionCount ?></h2>
            <p class="text-sm text-muted-foreground">Transaksi yang sudah berhasil dan mengaktifkan akses kursus.</p>
        </div>
        <div class="bg-background p-6 rounded-lg border">
            <h5 class="text-lg font-semibold text-foreground mb-1">Menunggu Bayar</h5>
            <h2 class="text-4xl font-bold text-primary mb-2"><?= $pendingTransactionCount ?></h2>
            <p class="text-sm text-muted-foreground">Invoice yang masih aktif dan bisa kamu lanjutkan pembayarannya.</p>
        </div>
    </div>

    <div class="bg-background border rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b bg-muted/50">
            <h2 class="text-xl font-bold text-foreground">Daftar Invoice & Pembayaran</h2>
            <p class="text-sm text-muted-foreground mt-1">Urutan terbaru ditampilkan paling atas.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-muted">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Kursus</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Referensi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Timeline</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Invoice / Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-muted">
                    <?php if (empty($paymentTransactions)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-sm text-muted-foreground text-center">
                                Belum ada riwayat pembayaran. Saat kamu checkout kursus premium, invoice dan statusnya akan tampil di halaman ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($paymentTransactions as $transaction): ?>
                            <?php
                                $invoiceUrl = trim((string) ($transaction['xendit_invoice_url'] ?? $transaction['checkout_url'] ?? ''));
                                $detailCourseUrl = site_url('user/view-course/' . $transaction['course_id']);
                            ?>
                            <tr>
                                <td class="px-6 py-4 text-sm text-foreground">
                                    <div class="font-medium"><?= esc($transaction['course_title'] ?? ('Kursus #' . $transaction['course_id'])) ?></div>
                                    <div class="text-xs text-muted-foreground mt-1">ID transaksi #<?= esc($transaction['id']) ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-muted-foreground">
                                    <div class="max-w-[220px] break-all"><?= esc($transaction['reference_code'] ?? '-') ?></div>
                                    <?php if (!empty($transaction['xendit_invoice_id'])): ?>
                                        <div class="text-xs mt-1">Invoice: <?= esc($transaction['xendit_invoice_id']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">
                                    <?= esc(strtoupper((string) ($transaction['currency'] ?? 'IDR'))) ?> <?= number_format((int) ($transaction['amount'] ?? 0)) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold <?= esc($transaction['status_meta']['class']) ?>">
                                        <?= esc($transaction['status_meta']['label']) ?>
                                    </span>
                                    <?php if (!empty($transaction['failure_message']) && ($transaction['status'] ?? '') !== 'paid'): ?>
                                        <div class="text-xs text-red-700 mt-2 max-w-[220px]">
                                            <?= esc($transaction['failure_message']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                                    <div class="font-medium text-foreground"><?= esc($transaction['timeline_label']) ?></div>
                                    <div class="text-xs mt-1"><?= esc($transaction['timeline_value']) ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-col items-start gap-2">
                                        <?php if ($invoiceUrl !== ''): ?>
                                            <a href="<?= esc($invoiceUrl) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-3 py-2 bg-primary text-primary-foreground rounded-md text-xs font-medium hover:bg-primary/90">
                                                <i class="fa-solid fa-receipt mr-2"></i>
                                                <?= ($transaction['status'] ?? null) === 'pending' ? 'Lanjut Bayar' : 'Lihat Invoice' ?>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= $detailCourseUrl ?>" class="inline-flex items-center px-3 py-2 border border-muted-foreground text-muted-foreground rounded-md text-xs font-medium hover:bg-muted hover:text-foreground">
                                            <i class="fa-solid fa-book-open mr-2"></i>
                                            Lihat Kursus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
