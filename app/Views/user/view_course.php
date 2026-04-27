<?php
// Helper function untuk mengkonversi menit ke format jam dan menit
function convertMinutesToHoursMinutes($totalMinutes) {
    if ($totalMinutes <= 0) return 'N/A';
    $jam = floor($totalMinutes / 60);
    $menit = $totalMinutes % 60;
    
    $result = [];
    if ($jam > 0) {
        $result[] = $jam . " jam";
    }
    if ($menit > 0) {
        $result[] = $menit . " menit";
    }
    return empty($result) ? '0 menit' : implode(" ", $result);
}
?>

<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <?php if (session()->getFlashdata('error')): ?>
        <?= view('components/alert', [
            'type' => 'error',
            'title' => 'Informasi',
            'message' => esc(session()->getFlashdata('error')),
        ]) ?>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <?= view('components/alert', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => esc(session()->getFlashdata('success')),
        ]) ?>
    <?php endif; ?>

    <!-- Breadcrumbs -->
    <nav class="mb-4 text-sm" aria-label="Breadcrumb">
        <ol class="list-none p-0 inline-flex space-x-2">
            <li class="flex items-center">
                <a href="<?= site_url('user/courses') ?>" class="text-muted-foreground hover:text-primary">Kursus</a>
            </li>
            <li class="flex items-center">
                <span class="text-muted-foreground mx-2">/</span>
                <span class="text-foreground"><?= esc($course['title']) ?></span>
            </li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Kiri: Detail Kursus & Modul -->
        <div class="lg:col-span-2">
            <div class="mb-6">
                <h1 class="text-3xl md:text-4xl font-bold text-foreground mb-2"><?= esc($course['title']) ?></h1>
                <p class="text-lg text-muted-foreground mb-4"><?= esc($course['description'] ?? 'Tidak ada deskripsi detail untuk kursus ini.') ?></p>
                <div class="flex flex-wrap gap-2 items-center text-sm mb-4">
                    <span class="px-3 py-1 bg-primary/10 text-primary rounded-full font-medium"><?= htmlspecialchars(ucfirst((string) ($course['level'] ?? 'general')), ENT_QUOTES, 'UTF-8') ?></span>
                    <?php if (!empty($purchaseState['is_premium'])): ?>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full font-medium">
                            Premium · <?= htmlspecialchars((string) ($purchaseState['price_currency'] ?? 'IDR'), ENT_QUOTES, 'UTF-8') ?> <?= number_format((int) ($purchaseState['price_amount'] ?? 0)) ?>
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-medium">Gratis</span>
                    <?php endif; ?>
                    <?php if (isset($course['duration']) && $course['duration'] > 0): ?>
                        <span class="flex items-center text-muted-foreground">
                            <i class="far fa-clock mr-1.5"></i><?= convertMinutesToHoursMinutes($course['duration']) ?>
                        </span>
                    <?php endif; ?>
                    <span class="flex items-center text-muted-foreground">
                        <i class="fas fa-layer-group mr-1.5"></i><?= count($modules) ?> Modul
                    </span>
                     <?php 
                        $totalLessons = 0;
                        foreach ($modules as $module) {
                            $totalLessons += count($module['lessons'] ?? []);
                        }
                    ?>
                    <span class="flex items-center text-muted-foreground">
                        <i class="fas fa-book-open mr-1.5"></i><?= $totalLessons ?> Materi
                    </span>
                </div>
            </div>

            <div class="bg-background border rounded-lg">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-xl font-semibold text-foreground">Roadmap Kursus</h3>
                </div>
                <div class="p-2 sm:p-4">
                    <?php if (empty($modules)): ?>
                        <div class="p-4 text-center text-muted-foreground">
                            Roadmap kursus belum tersedia.
                        </div>
                    <?php else: ?>
                        <div class="space-y-1" id="moduleAccordion">
                            <?php foreach ($modules as $index => $module): ?>
                                <div class="border rounded-md overflow-hidden">
                                    <button type="button" class="w-full flex items-center justify-between px-4 py-3 text-left bg-muted/50 hover:bg-muted/80 focus:outline-none accordion-button" data-accordion-target="#collapse<?= $module['id'] ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $module['id'] ?>">
                                        <span class="font-semibold text-foreground"><span class="mr-2 text-primary"><?= $index + 1 ?>.</span> <?= esc($module['title']) ?></span>
                                        <i class="fas fa-chevron-down transition-transform duration-200 accordion-icon <?= $index === 0 ? 'rotate-180' : '' ?>"></i>
                                    </button>
                                    <div id="collapse<?= $module['id'] ?>" class="accordion-content <?= $index !== 0 ? 'hidden' : '' ?>" aria-labelledby="heading<?= $module['id'] ?>">
                                        <div class="p-4 border-t">
                                            <?php if (!empty($module['description'])): ?>
                                                <p class="text-sm text-muted-foreground mb-4"><?= esc($module['description']) ?></p>
                                            <?php endif; ?>
                                            
                                            <?php if (empty($module['lessons'])): ?>
                                                <p class="text-sm text-muted-foreground">Modul ini belum memiliki materi pembelajaran.</p>
                                            <?php else: ?>
                                                <ul class="space-y-2">
                                                    <?php foreach ($module['lessons'] as $lessonIndex => $lesson): ?>
                                                        <li class="flex items-start p-3 rounded-md hover:bg-muted/50">
                                                            <span class="mr-3 mt-1 flex-shrink-0 h-5 w-5 bg-primary/10 text-primary text-xs rounded-full flex items-center justify-center font-semibold">
                                                                <?= $lessonIndex + 1 ?>
                                                            </span>
                                                            <div class="flex-grow">
                                                                <h5 class="font-medium text-foreground text-sm"><?= esc($lesson['title']) ?></h5>
                                                                <?php if (!empty($lesson['description'])): ?>
                                                                    <p class="text-xs text-muted-foreground mt-0.5"><?= esc($lesson['description']) ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php if (isset($lesson['video_duration']) && $lesson['video_duration'] > 0): ?>
                                                                <div class="text-xs text-muted-foreground ml-4 flex-shrink-0 whitespace-nowrap pt-1">
                                                                    <i class="far fa-clock mr-1"></i> <?= convertMinutesToHoursMinutes($lesson['video_duration']) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Informasi Tambahan & Aksi -->
        <div class="lg:col-span-1">
            <div class="sticky top-8 space-y-6">
                 <!-- Course Image/Placeholder -->
                <div class="aspect-w-16 aspect-h-9 mb-6 rounded-lg overflow-hidden border">
                     <img src="<?= base_url(!empty($course['thumbnail']) ? $course['thumbnail'] : '/home/placeholder.svg') ?>" alt="<?= esc($course['title']) ?>" class="w-full h-full object-cover">
                </div>

                <div class="bg-background border rounded-lg p-6">
                     <h3 class="text-xl font-semibold text-foreground mb-4">Tentang Kursus Ini</h3>
                    
                    <div class="space-y-3 text-sm">
                        <?php if (isset($averageRating)): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground font-medium">Rating:</span>
                            <div class="flex items-center">
                                <span class="text-yellow-400 mr-1.5">
                                    <?php for($i = 1; $i <= 5; $i++): ?><i class="fas fa-star <?= $i <= round($averageRating) ? '' : 'text-gray-300' ?>"></i><?php endfor; ?>
                                </span>
                                <span class="text-foreground font-semibold"><?= number_format($averageRating, 1) ?></span>
                                <a href="<?= site_url('course/' . $course['id'] . '/reviews') ?>" class="text-muted-foreground hover:text-primary ml-1">(<?= $totalRatings ?> ulasan)</a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground font-medium">Penulis:</span> 
                            <span class="text-foreground font-semibold"><?= esc($course['author'] ?? 'Tim KodeNusantara') ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground font-medium">Terakhir Update:</span> 
                            <span class="text-foreground font-semibold"><?= date('d M Y', strtotime($course['updated_at'] ?? $course['created_at'])) ?></span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <?php if (isset($isEnrolled) && $isEnrolled): ?>
                             <a href="<?= site_url('course/' . $course['id']) ?>" class="w-full py-3 bg-primary text-primary-foreground rounded-md inline-flex items-center justify-center font-medium hover:bg-primary/90 mb-3">
                                <i class="fa-solid fa-play mr-2"></i> Lanjutkan Belajar 
                                <?php if($progress > 0 && $progress < 100) echo "(" . floor($progress) . "%)"; ?>
                            </a>
                            <div class="grid grid-cols-2 gap-2">
                                <a href="<?= site_url('course/' . $course['id'] . '/reviews/create') ?>" class="w-full py-2 border border-muted-foreground text-muted-foreground hover:bg-muted hover:text-foreground rounded-md inline-flex items-center justify-center text-sm font-medium">
                                    <i class="far fa-star mr-1.5"></i> Beri Ulasan
                                </a>
                                <?php if (isset($progress) && floor($progress) == 100): ?>
                                    <!-- <a href="</?= site_url('course/' . $course['id'] . '/certificate') ?>" class="w-full py-2 border border-green-500 text-green-600 hover:bg-green-50 rounded-md inline-flex items-center justify-center text-sm font-medium">
                                        <i class="fas fa-award mr-1.5"></i> Sertifikat
                                    </a> -->
                                <?php endif; ?>
                            </div>
                        <?php elseif (!empty($purchaseState['is_premium'])): ?>
                            <?php if (!empty($purchaseState['can_checkout'])): ?>
                                <a href="<?= esc($course['checkout_url']) ?>" class="w-full py-3 bg-primary text-primary-foreground rounded-md inline-flex items-center justify-center font-medium hover:bg-primary/90 mb-3">
                                    <i class="fas fa-credit-card mr-2"></i> Checkout Kursus Ini
                                </a>
                            <?php else: ?>
                                <div class="w-full py-3 px-4 border border-yellow-200 bg-yellow-50 text-yellow-800 rounded-md text-sm font-medium text-center">
                                    <?= esc($purchaseState['checkout_blocked_message'] ?? 'Kursus premium ini belum tersedia untuk checkout.') ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= esc($course['enroll_url']) ?>" class="w-full py-3 bg-primary text-primary-foreground rounded-md inline-flex items-center justify-center font-medium hover:bg-primary/90">
                                <i class="fas fa-plus-circle mr-2"></i> Daftar Kursus Ini (Gratis)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const accordionButtons = document.querySelectorAll('.accordion-button');

    accordionButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-accordion-target');
            const targetContent = document.querySelector(targetId);
            const icon = button.querySelector('.accordion-icon');

            // Tutup semua accordion lain jika perlu (opsional)
            // accordionButtons.forEach(otherButton => {
            //     if (otherButton !== button) {
            //         const otherTargetId = otherButton.getAttribute('data-accordion-target');
            //         const otherTargetContent = document.querySelector(otherTargetId);
            //         const otherIcon = otherButton.querySelector('.accordion-icon');
            //         if (otherTargetContent && !otherTargetContent.classList.contains('hidden')) {
            //             otherTargetContent.classList.add('hidden');
            //             otherButton.setAttribute('aria-expanded', 'false');
            //             if(otherIcon) otherIcon.classList.remove('rotate-180');
            //         }
            //     }
            // });

            if (targetContent) {
                const isExpanded = button.getAttribute('aria-expanded') === 'true';
                targetContent.classList.toggle('hidden', isExpanded);
                button.setAttribute('aria-expanded', !isExpanded);
                if(icon) icon.classList.toggle('rotate-180', !isExpanded);
            }
        });
    });
});
</script>
<?= $this->endSection() ?> 
