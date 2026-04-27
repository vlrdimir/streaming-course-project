<?php
// Helper function to convert minutes to hours and minutes format
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
    <!-- Breadcrumbs -->
    <nav class="mb-6 text-sm" aria-label="Breadcrumb">
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
                <a href="<?= site_url('course/' . $course['id'] . '/reviews') ?>" class="text-muted-foreground hover:text-primary">Ulasan</a>
            </li>
            <li class="flex items-center">
                <span class="text-muted-foreground mx-2">/</span>
                <span class="text-foreground">Edit Ulasan</span>
            </li>
        </ol>
    </nav>

    <h1 class="text-3xl font-bold text-foreground mb-6">Edit Ulasan untuk: <?= esc($course['title']) ?></h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Kiri: Form Ulasan -->
        <div class="lg:col-span-2">
            <div class="bg-background border rounded-lg p-6">
                <?php if (session()->has('errors')): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md mb-4" role="alert">
                        <p class="font-bold">Oops! Ada kesalahan:</p>
                        <ul class="list-disc list-inside text-sm">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="<?= site_url('course/' . $course['id'] . '/reviews/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-foreground mb-2">Rating Anda</label>
                        <div class="flex items-center space-x-1 rating-stars">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input class="sr-only rating-input" type="radio" name="rating" id="rating-<?= $i ?>" value="<?= $i ?>" required <?= (old('rating', $review['rating']) == $i) ? 'checked' : '' ?>>
                                <label for="rating-<?= $i ?>" class="rating-star-label cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition-colors duration-150" title="<?= $i ?> Bintang">
                                    <i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating_value" id="rating_value_input" value="<?= old('rating', $review['rating']) ?>">
                    </div>
                    
                    <div class="mb-6">
                        <label for="review" class="block text-sm font-medium text-foreground mb-2">Ulasan Anda</label>
                        <textarea class="w-full px-3 py-2 border border-muted rounded-md focus:outline-none focus:ring-2 focus:ring-primary min-h-[120px]" id="review" name="review" placeholder="Bagikan pengalaman Anda mengikuti kursus ini... (minimal 10 karakter)" required><?= old('review', $review['review']) ?></textarea>
                        <p class="text-xs text-muted-foreground mt-1">Minimum 10 karakter, maksimum 1000 karakter.</p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button type="submit" class="px-6 py-2.5 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 text-sm font-medium">
                            Perbarui Ulasan
                        </button>
                        <a href="<?= site_url('course/' . $course['id'] . '/reviews') ?>" class="px-6 py-2.5 border border-muted-foreground text-muted-foreground hover:bg-muted rounded-md text-sm font-medium">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Kolom Kanan: Info Kursus & Tips -->
        <div class="lg:col-span-1">
            <div class="sticky top-8 space-y-6">
                <div class="bg-background border rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-3">Tentang Kursus Ini</h3>
                     <img src="<?= base_url(!empty($course['thumbnail']) ? $course['thumbnail'] : '/home/placeholder.svg') ?>" alt="<?= esc($course['title']) ?>" class="w-full h-auto object-cover rounded-md mb-3">
                    <h4 class="font-medium text-foreground mb-1"><?= esc($course['title']) ?></h4>
                    
                    <?php if (!empty($course['short_description'])): ?>
                        <p class="text-xs text-muted-foreground mb-2 line-clamp-3"><?= esc($course['short_description']) ?></p>
                    <?php endif; ?>
                    
                    <div class="flex flex-wrap gap-2 text-xs mb-3">
                        <span class="px-2 py-0.5 bg-primary/10 text-primary rounded-full font-medium"><?= ucfirst(esc($course['level'])) ?></span>
                        <?php if (isset($course['duration']) && $course['duration'] > 0): ?>
                            <span class="flex items-center text-muted-foreground">
                                <i class="far fa-clock mr-1"></i><?= convertMinutesToHoursMinutes($course['duration']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bg-background border rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-foreground mb-3"><i class="fas fa-lightbulb mr-2 text-yellow-400"></i>Tips Menulis Ulasan</h3>
                    <ul class="space-y-1.5 text-xs text-muted-foreground list-disc list-inside">
                        <li>Perbarui ulasan Anda jika ada pemikiran atau pengalaman baru.</li>
                        <li>Fokus pada perubahan atau perbaikan yang ingin Anda sampaikan.</li>
                        <li>Tetaplah jujur dan konstruktif dalam memberikan masukan.</li>
                         <li>Pastikan ulasan Anda relevan dengan konten kursus.</li>
                        <li>Hindari penggunaan bahasa yang tidak pantas atau informasi pribadi.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingStarsContainer = document.querySelector('.rating-stars');
    const ratingInputs = ratingStarsContainer.querySelectorAll('.rating-input');
    const ratingLabels = ratingStarsContainer.querySelectorAll('.rating-star-label');
    const ratingValueInput = document.getElementById('rating_value_input');

    function initializeStars() {
        const currentRating = parseInt(ratingValueInput.value) || 0;
        ratingLabels.forEach((label, index) => {
            const starValue = 5 - index; 
            const icon = label.querySelector('i');
            if (starValue <= currentRating) {
                icon.classList.remove('text-gray-300');
                icon.classList.add('text-yellow-400');
            } else {
                icon.classList.remove('text-yellow-400');
                icon.classList.add('text-gray-300');
            }
        });
    }

    ratingLabels.forEach((label, index) => {
        const radioInput = ratingInputs[index];
        const starValue = parseInt(radioInput.value);

        label.addEventListener('click', function() {
            radioInput.checked = true;
            ratingValueInput.value = starValue;
            updateStars(starValue);
        });

        label.addEventListener('mouseenter', function() {
            highlightStars(starValue);
        });

        label.addEventListener('mouseleave', function() {
            const checkedValue = ratingValueInput.value ? parseInt(ratingValueInput.value) : 0;
            updateStars(checkedValue);
        });
    });

    function updateStars(currentRating) {
        ratingLabels.forEach((lbl, idx) => {
            const val = 5 - idx; 
            const icon = lbl.querySelector('i');
            if (val <= currentRating) {
                icon.classList.remove('text-gray-300');
                icon.classList.add('text-yellow-400');
            } else {
                icon.classList.remove('text-yellow-400');
                icon.classList.add('text-gray-300');
            }
        });
    }

    function highlightStars(hoverRating) {
        ratingLabels.forEach((lbl, idx) => {
            const val = 5 - idx; 
            const icon = lbl.querySelector('i');
            if (val <= hoverRating) {
                icon.classList.remove('text-gray-300');
                icon.classList.add('text-yellow-400');
            } else {
                icon.classList.remove('text-yellow-400');
                icon.classList.add('text-gray-300');
            }
        });
    }
    
    initializeStars();
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 