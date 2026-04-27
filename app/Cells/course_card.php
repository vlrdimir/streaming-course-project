<div class="overflow-hidden hover:shadow-lg transition-shadow rounded-md border bg-background">
    <div class="relative">
        <a href="<?= $userLayout ? site_url('user/view-course/' . $course['course_id']) : site_url('course/' . $course['course_id']) ?>">
            <img
                src="<?= base_url(!empty($course['thumbnail']) ? $course['thumbnail'] : '/home/placeholder.svg') ?>"
                alt="<?= esc($course['title']) ?>"
                class="w-full h-48 object-cover"
            >
        </a>
        <?php if ($userLayout && $showProgress): ?>
            <?= $this->getStatusBadge() ?>
        <?php else: ?>
            <div class="absolute top-2 left-2 px-2 py-1 text-xs bg-primary text-primary-foreground rounded-md">
                <?= ucfirst(esc($course['level'] ?? 'General')) ?>
            </div>
             <div class="absolute top-2 right-2 px-2 py-1 text-xs bg-green-500 text-white rounded-md">
                Gratis
            </div>
        <?php endif; ?>
    </div>
    <div class="p-4">
        <h3 class="text-lg font-bold line-clamp-2 mb-2">
            <a href="<?= $userLayout ? site_url('user/view-course/' . $course['course_id']) : site_url('course/' . $course['course_id']) ?>" class="hover:text-primary">
                <?= esc($course['title']) ?>
            </a>
        </h3>
        
        <?php if (!$userLayout): ?>
        <p class="text-sm text-muted-foreground line-clamp-3 mb-4">
            <?= esc($course['short_description'] ?? ($course['description'] ?? 'Tidak ada deskripsi singkat.')) ?>
        </p>
        <?php endif; ?>

        <?php if ($showProgress): ?>
            <div class="w-full bg-muted rounded-full h-2.5 mb-1">
                <div class="bg-primary h-2.5 rounded-full" style="width: <?= $progressPercentage ?>%"></div>
            </div>
            <p class="text-xs text-muted-foreground mb-3"><?= number_format($progressPercentage, 0) ?>% selesai</p>
        <?php endif; ?>

        <?php if ($enrolledAt): ?>
            <p class="text-xs text-muted-foreground mb-1">Terdaftar: <?= date('d M Y', strtotime($enrolledAt)) ?></p>
        <?php endif; ?>
        <?php if ($completedAt): ?>
            <p class="text-xs text-muted-foreground mb-1">Selesai: <?= date('d M Y', strtotime($completedAt)) ?></p>
        <?php endif; ?>

        <div class="mt-4 flex flex-col sm:flex-row gap-2">
            <?php if ($userLayout): ?>
                <?php if ($progressPercentage == 100): ?>
                    <a href="<?= site_url('course/' . $course['course_id']) ?>" class="flex-1 text-center px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                        <i class="fa-solid fa-check mr-1"></i> Lihat Materi
                    </a>
                    <a href="<?= site_url('certificate/' . $course['course_id']) ?>" class="flex-1 text-center px-4 py-2 border border-primary text-primary rounded-md text-sm font-medium hover:bg-primary/10">
                        <i class="fa-solid fa-award mr-1"></i> Sertifikat
                    </a>
                <?php else: ?>
                    <a href="<?= site_url('course/' . $course['course_id']) ?>" class="flex-1 text-center px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90">
                        <i class="fa-solid fa-play mr-1"></i> 
                        <?= $progressPercentage > 0 ? 'Lanjutkan Belajar' : 'Mulai Belajar' ?>
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?= site_url('user/view-course/' . $course['id']) ?>" class="flex-1 text-center px-4 py-2 border border-muted-foreground rounded-md text-sm hover:bg-muted">
                    Lihat Detail
                </a>
                <?php if (in_array($course['id'], $enrolledCourseIds ?? [])): ?>
                    <?php if (isset($courseProgress[$course['id']]) && $courseProgress[$course['id']] == 100): ?>
                        <a href="<?= site_url('course/' . $course['id']) ?>" class="flex-1 text-center px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                            <i class="fa-solid fa-check mr-1"></i> Materi Selesai
                        </a>
                    <?php else: ?>
                        <a href="<?= site_url('course/' . $course['id']) ?>" class="flex-1 text-center px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90">
                           <i class="fa-solid fa-play mr-1"></i> Lanjutkan Belajar
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= site_url('course/' . $course['id'] . '/enroll') ?>" class="flex-1 text-center px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90">
                        Daftar Sekarang
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div> 