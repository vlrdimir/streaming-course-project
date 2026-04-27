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
        <h1 class="text-3xl font-bold text-foreground">Selamat Datang, <?= esc($user['full_name']) ?>!</h1>
        <p class="text-muted-foreground">Lacak progres belajarmu dan lanjutkan dari bagian terakhir yang kamu pelajari.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-background p-6 rounded-lg border">
            <h5 class="text-lg font-semibold text-foreground mb-1">Kursus Terdaftar</h5>
            <h2 class="text-4xl font-bold text-primary mb-2"><?= $totalEnrolled ?></h2>
            <p class="text-sm text-muted-foreground">Total kursus yang telah kamu ikuti.</p>
        </div>
        <div class="bg-background p-6 rounded-lg border">
            <h5 class="text-lg font-semibold text-foreground mb-1">Sedang Dipelajari</h5>
            <h2 class="text-4xl font-bold text-primary mb-2"><?= $inProgressCount ?></h2>
            <p class="text-sm text-muted-foreground">Kursus yang sedang kamu ikuti saat ini.</p>
        </div>
        <div class="bg-background p-6 rounded-lg border">
            <h5 class="text-lg font-semibold text-foreground mb-1">Selesai</h5>
            <h2 class="text-4xl font-bold text-primary mb-2"><?= $completedCount ?></h2>
            <p class="text-sm text-muted-foreground">Kursus yang telah kamu selesaikan.</p>
        </div>
    </div>

    <!-- Continue Learning Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-foreground">Lanjutkan Pembelajaran</h2>
            <a href="<?= site_url('user/courses/enrolled') ?>" class="px-4 py-2 border border-muted-foreground rounded-md text-sm hover:bg-muted">
                Lihat Semua Kursus
            </a>
        </div>

        <?php if (empty($recentCourses)): ?>
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-md" role="alert">
                <p class="font-bold">Informasi</p>
                <p class="text-sm">Anda belum memulai kursus apapun. <a href="<?= site_url('user/courses') ?>" class="font-medium hover:underline">Jelajahi kursus</a> untuk memulai.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($recentCourses as $course): ?>
                    <div class="overflow-hidden hover:shadow-lg transition-shadow rounded-md border">
                        <div class="relative">
                            <img
                                src="<?= base_url(!empty($course['thumbnail']) ? $course['thumbnail'] : '/home/placeholder.svg') ?>"
                                alt="<?= esc($course['title']) ?>"
                                class="w-full h-48 object-cover"
                            >
                            <!-- <div class="absolute top-2 left-2 px-2 py-1 text-xs bg-primary text-primary-foreground rounded-md">
                                <?= esc($course['category_name'] ?? 'General') ?> 
                            </div> -->
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-bold line-clamp-2 mb-2"><?= esc($course['title']) ?></h3>
                            
                            <div class="w-full bg-muted rounded-full h-2.5 mb-1">
                                <div class="bg-primary h-2.5 rounded-full" style="width: <?= $course['progress_percentage'] ?>%"></div>
                            </div>
                            <p class="text-xs text-muted-foreground mb-3"><?= number_format($course['progress_percentage'], 0) ?>% selesai</p>
                            
                            <a href="<?= site_url('course/' . $course['course_id']) ?>" class="w-full py-2 bg-primary text-primary-foreground rounded-md inline-flex items-center justify-center text-sm font-medium hover:bg-primary/90">
                                <i class="fa-solid fa-play mr-2"></i>
                                Lanjutkan Belajar
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Learning Activity -->
    <div>
        <h2 class="text-2xl font-bold text-foreground mb-6">Aktivitas Belajarmu</h2>
        <div class="bg-background border rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-muted">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Kursus</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Terdaftar Pada</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Progres</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-muted">
                        <?php if (empty($enrolledCourses)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground text-center">Belum ada kursus yang diikuti.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (array_slice($enrolledCourses, 0, 5) as $course): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground"><?= esc($course['title']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground"><?= isset($course['enrolled_at']) ? date('d M Y', strtotime($course['enrolled_at'])) : 'N/A' ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                                        <div class="w-full bg-muted rounded-full h-1.5">
                                            <div class="bg-primary h-1.5 rounded-full" style="width: <?= $course['progress_percentage'] ?>%"></div>
                                        </div>
                                        <span class="text-xs"><?= number_format($course['progress_percentage'], 0) ?>%</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="<?= site_url('course/' . $course['course_id']) ?>" class="text-primary hover:text-primary/80">
                                            <?php if (number_format($course['progress_percentage'], 0) == 100): ?>
                                                Lihat Materi
                                            <?php else: ?>
                                                Lanjutkan
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Jika ada script khusus untuk halaman ini, bisa ditambahkan di sini
</script>
<?= $this->endSection() ?>
