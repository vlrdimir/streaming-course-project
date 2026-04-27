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

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground">Jelajahi Kursus</h1>
        <p class="text-muted-foreground">Temukan kursus baru untuk meningkatkan keahlianmu.</p>
    </div>

    <!-- Filter Section -->
    <div class="mb-8">
        <div class="bg-background p-6 rounded-lg border">
            <form action="<?= site_url('user/courses') ?>" method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="category" class="block text-sm font-medium text-foreground mb-1">Kategori</label>
                    <select name="category" id="category" class="w-full px-3 py-2 border border-muted rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= isset($selectedCategory) && $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                                <?= esc($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="level" class="block text-sm font-medium text-foreground mb-1">Level</label>
                    <select name="level" id="level" class="w-full px-3 py-2 border border-muted rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Semua Level</option>
                        <option value="beginner" <?= isset($selectedLevel) && $selectedLevel == 'beginner' ? 'selected' : '' ?>>Beginner</option>
                        <option value="intermediate" <?= isset($selectedLevel) && $selectedLevel == 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                        <option value="advanced" <?= isset($selectedLevel) && $selectedLevel == 'advanced' ? 'selected' : '' ?>>Advanced</option>
                    </select>
                </div>
                <button type="submit" class="w-full md:w-auto px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                    Filter Kursus
                </button>
            </form>
        </div>
    </div>

    <!-- All Courses -->
    <div>
        <h2 class="text-2xl font-bold text-foreground mb-6">
            <?php if (!empty($selectedCategory) || !empty($selectedLevel)): ?>
                Hasil Filter Kursus
                <?php if (!empty($selectedLevel)): ?>
                    <span class="text-primary">[Level: <?= ucfirst($selectedLevel) ?>]</span>
                <?php endif; ?>
                <?php if (!empty($selectedCategory)): ?>
                    <?php 
                    $categoryName = '';
                    foreach ($categories as $cat) {
                        if ($cat['id'] == $selectedCategory) {
                            $categoryName = $cat['name'];
                            break;
                        }
                    }
                    ?>
                    <span class="text-primary">[Kategori: <?= esc($categoryName) ?>]</span>
                <?php endif; ?>
            <?php else: ?>
                Semua Kursus
            <?php endif; ?>
        </h2>

        <?php if (empty($courses)): ?>
            <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-md" role="alert">
                <p class="font-bold">Informasi</p>
                <p class="text-sm">Tidak ada kursus yang sesuai dengan kriteria pencarianmu.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($courses as $course): ?>
                    <div class="overflow-hidden hover:shadow-lg transition-shadow rounded-md border">
                        <div class="relative">
                            <img
                                src="<?= base_url(!empty($course['thumbnail']) ? $course['thumbnail'] : '/home/placeholder.svg') ?>"
                                alt="<?= esc($course['title']) ?>"
                                class="w-full h-48 object-cover"
                            >
                            <div class="absolute top-2 left-2 px-2 py-1 text-xs bg-primary text-primary-foreground rounded-md">
                                <?= ucfirst(esc($course['level'])) ?>
                            </div>
                            <?php if (!empty($course['purchase_state']['is_premium'])): ?>
                                <div class="absolute top-2 right-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-md">
                                    Premium
                                </div>
                            <?php else: ?>
                                <div class="absolute top-2 right-2 px-2 py-1 text-xs bg-green-500 text-white rounded-md">
                                    Gratis
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-4 flex flex-col h-[200px]">
                            <h3 class="text-lg font-bold line-clamp-2 mb-2"><?= esc($course['title']) ?></h3>
                            <p class="text-sm font-medium <?= !empty($course['purchase_state']['is_premium']) ? 'text-yellow-700' : 'text-green-600' ?> mb-2">
                                <?php if (!empty($course['purchase_state']['is_premium'])): ?>
                                    <?= htmlspecialchars((string) ($course['purchase_state']['price_currency'] ?? 'IDR'), ENT_QUOTES, 'UTF-8') ?> <?= number_format((int) ($course['purchase_state']['price_amount'] ?? 0)) ?>
                                <?php else: ?>
                                    Gratis
                                <?php endif; ?>
                            </p>
                            <p class="text-sm text-muted-foreground line-clamp-3 mb-4 flex-1">
                                <?= esc($course['short_description'] ?? 'Tidak ada deskripsi singkat.') ?>
                            </p>
                            <div class="flex flex-col sm:flex-row gap-2 mt-auto">
                                <a href="<?= site_url('user/view-course/' . $course['id']) ?>" class="flex-1 text-center px-4 py-2 border border-muted-foreground rounded-md text-sm hover:bg-muted">
                                    Lihat Detail
                                </a>
                                <?php if (in_array($course['id'], $enrolledCourseIds)): ?>
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
                                    <?php if (!empty($course['purchase_state']['is_premium'])): ?>
                                        <?php if (!empty($course['purchase_state']['can_checkout'])): ?>
                                            <a href="<?= esc($course['checkout_url']) ?>" class="flex-1 text-center px-4 py-2 bg-primary text-primary-foreground rounded-md text-sm hover:bg-primary/90">
                                                Checkout
                                            </a>
                                        <?php else: ?>
                                            <span class="flex-1 text-center px-4 py-2 border border-muted text-muted-foreground rounded-md text-sm bg-muted/50 cursor-not-allowed">
                                                Pembelian Tidak Tersedia
                                            </span>
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
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
