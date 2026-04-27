<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Profil Pengguna</h1>
        <a href="<?= site_url('user/profile/edit') ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center">
            <i class="bi bi-pencil-square mr-2"></i> Edit Profil
        </a>
    </div>

    <?php if (session('message')): ?>
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
            <?= session('message') ?>
        </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row">
        <div class="w-full md:w-1/3 flex justify-center mb-6 md:mb-0">
            <div class="w-48 h-48 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?= base_url('avatar/' . $user['profile_picture']) ?>" alt="Profile" class="w-full h-full object-cover">
                <?php else: ?>
                    <i class="bi bi-person-fill text-7xl text-gray-400"></i>
                <?php endif; ?>
            </div>
        </div>

        <div class="w-full md:w-2/3">
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <h2 class="text-lg font-medium text-gray-700">Informasi Pribadi</h2>
                    <div class="mt-2 border-t border-gray-200 pt-4">
                        <dl class="divide-y divide-gray-200">
                            <div class="grid grid-cols-3 gap-4 py-3">
                                <dt class="text-sm font-medium text-gray-500">Username</dt>
                                <dd class="text-sm text-gray-900 col-span-2"><?= esc($user['username'] ?? '(Belum diisi)') ?></dd>
                            </div>

                            <div class="grid grid-cols-3 gap-4 py-3">
                                <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                                <dd class="text-sm text-gray-900 col-span-2"><?= esc($user['full_name'] ?? '(Belum diisi)') ?></dd>
                            </div>

                            <div class="grid grid-cols-3 gap-4 py-3">
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900 col-span-2"><?= esc($user['email'] ?? '(Belum diisi)') ?></dd>
                            </div>

                            <div class="grid grid-cols-3 gap-4 py-3">
                                <dt class="text-sm font-medium text-gray-500">Bergabung Sejak</dt>
                                <dd class="text-sm text-gray-900 col-span-2">
                                    <?= date('d F Y', strtotime($user['created_at'])) ?>
                                </dd>
                            </div>

                            <div class="grid grid-cols-3 gap-4 py-3">
                                <dt class="text-sm font-medium text-gray-500">Terakhir Login</dt>
                                <dd class="text-sm text-gray-900 col-span-2">
                                    <?= !empty($user['last_login']) ? date('d F Y H:i', strtotime($user['last_login'])) : '(Tidak Ada Data)' ?>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-medium text-gray-700">Bio</h2>
                    <div class="mt-2 border-t border-gray-200 pt-4">
                        <p class="text-gray-700">
                            <?= !empty($user['bio']) ? nl2br(esc($user['bio'])) : '(Bio belum diisi)' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>