<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Profil</h1>
        <p class="text-gray-600 mt-2">Perbarui informasi profil dan foto diri Anda.</p>
    </div>

    <?php if (session('errors')): ?>
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
            <h4 class="font-bold">Ada kesalahan dengan input Anda:</h4>
            <ul class="list-disc ml-8">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('user/profile/update') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kolom Kiri - Foto Profil -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Foto Profil</h3>
                    <div class="flex flex-col items-center space-y-4">
                        <div class="w-48 h-48 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                            <?php if (!empty($user['profile_picture'])): ?>
                                <img src="<?= base_url('avatar/' . $user['profile_picture']) ?>" alt="Profile" class="w-full h-full object-cover" id="profile-preview">
                            <?php else: ?>
                                <i class="bi bi-person-fill text-7xl text-gray-400" id="profile-icon"></i>
                                <img src="" alt="Profile Preview" class="w-full h-full object-cover hidden" id="profile-preview">
                            <?php endif; ?>
                        </div>

                        <div class="flex w-full justify-center">
                            <label class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md cursor-pointer">
                                <i class="bi bi-camera mr-2"></i> Pilih Foto
                                <input type="file" name="profile_picture" id="profile-upload" class="hidden" accept="image/jpeg,image/png,image/jpg">
                            </label>
                        </div>
                        <p class="text-sm text-gray-500">Format yang didukung: JPG, JPEG, atau PNG. Ukuran maksimal 1MB.</p>
                    </div>
                </div>

                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea id="bio" name="bio" rows="6" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= old('bio', $user['bio'] ?? '') ?></textarea>
                    <p class="text-sm text-gray-500 mt-1">Tuliskan sedikit tentang diri Anda.</p>
                </div>
            </div>

            <!-- Kolom Kanan - Informasi Akun -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Informasi Akun</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username*</label>
                            <input disabled type="text" id="username" name="username" value="<?= old('username', $user['username'] ?? '') ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100 cursor-not-allowed">
                            <input type="hidden" name="username" value="<?= $user['username'] ?? '' ?>">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email*</label>
                            <input disabled type="email" id="email" name="email" value="<?= old('email', $user['email'] ?? '') ?>" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100 cursor-not-allowed">
                            <input type="hidden" name="email" value="<?= $user['email'] ?? '' ?>">
                        </div>

                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" id="full_name" name="full_name" value="<?= old('full_name', $user['full_name'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="pt-4">
                            <p class="text-sm text-gray-500">Bergabung sejak: <?= date('d F Y', strtotime($user['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6">
            <a href="<?= site_url('user/profile') ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?= $this->section('scripts') ?>
<script>
    // Preview foto profil yang diunggah
    document.getElementById('profile-upload').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const preview = document.getElementById('profile-preview');
                const icon = document.getElementById('profile-icon');

                if (icon) {
                    icon.classList.add('hidden');
                }

                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }

            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>