<?= $this->extend('layouts/user_layout') ?>

<?= $this->section('content') ?>
<div class="bg-white shadow-md rounded-lg p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Akun</h1>
        <p class="text-gray-600 mt-2">Kelola pengaturan akun dan preferensi Anda.</p>
    </div>
    
    <?php if (session('message')): ?>
    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
        <?= session('message') ?>
    </div>
    <?php endif; ?>
    
    <?php if (session('error')): ?>
    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
        <?= session('error') ?>
    </div>
    <?php endif; ?>
    
    <?php if (session('warning')): ?>
    <div class="mb-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
        <?= session('warning') ?>
    </div>
    <?php endif; ?>
    
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
    
    <!-- Bagian Tab -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex space-x-8" aria-label="Tabs">
            <button class="border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 whitespace-nowrap focus:outline-none" id="password-tab" onclick="showTab('password')">
                Ganti Password
            </button>
            <!-- Tambahan tab lain bisa ditambahkan di sini -->
        </nav>
    </div>
    
    <!-- Konten Tab -->
    <div id="password-content" class="tab-content">
        <div class="max-w-lg">
            <form action="<?= site_url('user/settings') ?>" method="post" id="password-form">
                <?= csrf_field() ?>
                
                <div class="space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini*</label>
                        <input type="password" id="current_password" name="current_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru*</label>
                        <input type="password" id="new_password" name="new_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-sm text-gray-500 mt-1">Password minimal 8 karakter.</p>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru*</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center">
                            <i class="bi bi-check2 mr-2"></i> Simpan Password Baru
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    // Fungsi untuk menampilkan tab
    function showTab(tabId) {
        // Sembunyikan semua konten tab
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.style.display = 'none';
        });
        
        // Tampilkan konten tab yang dipilih
        document.getElementById(tabId + '-content').style.display = 'block';
        
        // Perbarui status aktif pada tab
        const tabs = document.querySelectorAll('nav button');
        tabs.forEach(tab => {
            tab.classList.remove('border-blue-500', 'text-blue-600');
            tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });
        
        document.getElementById(tabId + '-tab').classList.add('border-blue-500', 'text-blue-600');
        document.getElementById(tabId + '-tab').classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    }

    // Validasi form password
    document.getElementById('password-form').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Password baru dan konfirmasi password tidak cocok.');
            return false;
        }
        
        if (newPassword.length < 8) {
            e.preventDefault();
            alert('Password baru minimal harus 8 karakter.');
            return false;
        }
        
        return true;
    });
    
    // Tampilkan tab password saat halaman dimuat (default)
    showTab('password');
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?> 