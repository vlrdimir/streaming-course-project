<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground">Kursus Saya</h1>
        <p class="text-muted-foreground">Kelola kursus yang kamu ikuti dan lacak progres belajarmu.</p>
    </div>

    <!-- Tabs for course status -->
    <div class="mb-8">
        <div class="border-b border-muted">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button data-tab-target="#all" class="tab-button active-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-primary text-primary">
                    Semua Kursus (<?= $totalEnrolled ?>)
                </button>
                <button data-tab-target="#in-progress" class="tab-button inactive-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-muted-foreground hover:text-foreground hover:border-gray-300">
                    Sedang Dipelajari (<?= count($inProgress) ?>)
                </button>
                <button data-tab-target="#completed" class="tab-button inactive-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-muted-foreground hover:text-foreground hover:border-gray-300">
                    Selesai (<?= count($completed) ?>)
                </button>
                <button data-tab-target="#not-started" class="tab-button inactive-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-muted-foreground hover:text-foreground hover:border-gray-300">
                    Belum Dimulai (<?= count($notStarted) ?>)
                </button>
            </nav>
        </div>

        <div class="pt-6">
            <!-- All Courses Tab -->
            <div id="all" class="tab-content">
                <?php if (empty($inProgress) && empty($completed) && empty($notStarted)): ?>
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-md" role="alert">
                        <p class="font-bold">Informasi</p>
                        <p class="text-sm">Kamu belum terdaftar di kursus manapun. <a href="<?= site_url('user/courses') ?>" class="font-medium hover:underline">Jelajahi kursus</a> untuk memulai.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach (array_merge($inProgress, $completed, $notStarted) as $course): ?>
                            <?= view_cell('App\Cells\CourseCardCell', [
                                'course' => $course,
                                'userLayout' => true,
                                'showProgress' => true,
                                'enrolledAt' => $course['enrolled_at'] ?? null,
                                'completedAt' => $course['completed_at'] ?? null,
                                'progressPercentage' => $course['progress_percentage'] ?? 0
                            ]) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- In Progress Tab -->
            <div id="in-progress" class="tab-content hidden">
                <?php if (empty($inProgress)): ?>
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-md" role="alert">
                        <p class="font-bold">Informasi</p>
                        <p class="text-sm">Kamu tidak memiliki kursus yang sedang dipelajari.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($inProgress as $course): ?>
                            <?= view_cell('App\Cells\CourseCardCell', [
                                'course' => $course,
                                'userLayout' => true,
                                'showProgress' => true,
                                'enrolledAt' => $course['enrolled_at'] ?? null,
                              
                                'progressPercentage' => $course['progress_percentage'] ?? 0
                            ]) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Completed Tab -->
            <div id="completed" class="tab-content hidden">
                <?php if (empty($completed)): ?>
                     <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-md" role="alert">
                        <p class="font-bold">Informasi</p>
                        <p class="text-sm">Kamu belum menyelesaikan kursus manapun.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($completed as $course): ?>
                             <?= view_cell('App\Cells\CourseCardCell', [
                                'course' => $course,
                                'userLayout' => true,
                                'showProgress' => true,
                                'enrolledAt' => $course['enrolled_at'] ?? null,
                                'completedAt' => $course['completed_at'] ?? null,
                                'progressPercentage' => $course['progress_percentage'] ?? 100
                            ]) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Not Started Tab -->
            <div id="not-started" class="tab-content hidden">
                <?php if (empty($notStarted)): ?>
                    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-md" role="alert">
                        <p class="font-bold">Informasi</p>
                        <p class="text-sm">Tidak ada kursus yang belum kamu mulai.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($notStarted as $course): ?>
                            <?= view_cell('App\Cells\CourseCardCell', [
                                'course' => $course,
                                'userLayout' => true,
                                'showProgress' => true,
                                'enrolledAt' => $course['enrolled_at'] ?? null,
                                'progressPercentage' => 0
                            ]) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = document.querySelector(tab.dataset.tabTarget);

            tabContents.forEach(tc => {
                tc.classList.add('hidden');
            });
            target.classList.remove('hidden');

            tabs.forEach(t => {
                t.classList.remove('active-tab', 'border-primary', 'text-primary');
                t.classList.add('inactive-tab', 'border-transparent', 'text-muted-foreground', 'hover:text-foreground', 'hover:border-gray-300');
            });
            tab.classList.add('active-tab', 'border-primary', 'text-primary');
            tab.classList.remove('inactive-tab', 'border-transparent', 'text-muted-foreground', 'hover:text-foreground', 'hover:border-gray-300');
        });
    });
});
</script>
<?= $this->endSection() ?>
