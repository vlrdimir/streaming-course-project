<div class="flex h-[calc(100vh-112px)]">
    <!-- Main Content Area - Scrollable -->
    <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-dark p-6">
       <div class="px-24">
            <?= var_dump($currentModule) ?>
       </div>
    </div>
    
    <!-- Sidebar - Module List - Scrollable -->
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
            
           
        </div>
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