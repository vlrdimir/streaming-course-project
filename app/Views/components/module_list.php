<div class="w-80 bg-dark border-l border-gray-700 overflow-y-auto scrollbar-thin scrollbar-dark">
    <div class="p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Daftar Modul</h2>
            <button class="bg-primary text-white rounded-full p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
        
        <div class="h-1 bg-gradient-to-r from-primary to-blue-400 rounded-full mb-2"></div>
        <div class="text-sm text-gray-400 mb-4">100% Selesai</div>
        
        <?php foreach ($modules as $module): ?>
            <div class="mb-2">
                <div class="flex items-center justify-between p-2 bg-gray-800 rounded-md">
                    <div class="flex items-center">
                        <?php if ($module['completed']): ?>
                            <div class="w-5 h-5 rounded-full bg-primary flex items-center justify-center mr-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        <?php else: ?>
                            <div class="w-5 h-5 rounded-full border border-gray-600 mr-2"></div>
                        <?php endif; ?>
                        <span><?= esc($module['title']) ?></span>
                    </div>
                    <?php if (isset($module['submodules'])): ?>
                        <button class="text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform <?= $module['expanded'] ? 'rotate-180' : '' ?>" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($module['submodules']) && $module['expanded']): ?>
                    <div class="ml-4 mt-2 space-y-2">
                        <?php foreach ($module['submodules'] as $submodule): ?>
                            <div class="flex items-center p-2 hover:bg-gray-800 rounded-md">
                                <?php if ($submodule['completed']): ?>
                                    <div class="w-5 h-5 rounded-full bg-primary flex items-center justify-center mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                <?php else: ?>
                                    <div class="w-5 h-5 rounded-full border border-gray-600 mr-2"></div>
                                <?php endif; ?>
                                <div>
                                    <a href="<?= isset($submodule['slug']) ? base_url('courses/module/' . $submodule['slug']) : '#' ?>" class="block">
                                        <?= esc($submodule['title']) ?>
                                    </a>
                                    <?php if (isset($submodule['gratis']) && $submodule['gratis']): ?>
                                        <span class="text-xs text-gray-500">(Gratis)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ambil semua tombol expand/collapse pada modul
    const moduleButtons = document.querySelectorAll('.mb-2 .bg-gray-800 button');
    
    moduleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Dapatkan parent element (div.mb-2)
            const moduleItem = this.closest('.mb-2');
            
            // Dapatkan submodule container (div yang memiliki class ml-4 mt-2 space-y-2)
            const submoduleContainer = moduleItem.querySelector('.ml-4.mt-2.space-y-2');
            
            // Toggle icon rotasi
            const icon = this.querySelector('svg');
            icon.classList.toggle('rotate-180');
            
            // Jika submodule container sudah ada, toggle display-nya
            if (submoduleContainer) {
                if (submoduleContainer.style.display === 'none') {
                    submoduleContainer.style.display = 'block';
                } else {
                    submoduleContainer.style.display = 'none';
                }
            } else {
                // Jika belum ada container (karena PHP belum me-render), kita bisa
                // mengirim AJAX request untuk mendapatkan submodules
                // Contoh pseudo-code:
                /*
                const moduleId = this.closest('.mb-2').dataset.moduleId;
                
                fetch(`/api/modules/${moduleId}/submodules`)
                    .then(response => response.json())
                    .then(data => {
                        // Create submodule container and populate it
                        const container = document.createElement('div');
                        container.className = 'ml-4 mt-2 space-y-2';
                        
                        // Populate with submodules...
                        
                        moduleItem.appendChild(container);
                    });
                */
            }
        });
    });
    
    // Inisialisasi - Sembunyikan semua submodule list yang tidak expanded secara default
    document.querySelectorAll('.mb-2').forEach(moduleItem => {
        const button = moduleItem.querySelector('.bg-gray-800 button');
        const icon = button?.querySelector('svg');
        const submoduleContainer = moduleItem.querySelector('.ml-4.mt-2.space-y-2');
        
        // Check if the icon doesn't have rotate-180 class (not expanded)
        if (icon && !icon.classList.contains('rotate-180') && submoduleContainer) {
            submoduleContainer.style.display = 'none';
        }
    });
});
</script>