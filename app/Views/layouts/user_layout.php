<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Management System</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap Icons (jika masih digunakan di beberapa tempat) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        /* Gaya kustom tambahan bisa diletakkan di sini jika diperlukan, 
           atau lebih baik lagi, di file CSS terpisah yang di-import.
           Untuk saat ini, kita akan mengandalkan kelas utilitas Tailwind. */
        body {
            @apply bg-gray-100 flex flex-col min-h-screen;
        }

        /* Contoh kustomisasi untuk sidebar jika diperlukan, namun idealnya gunakan kelas Tailwind */
        .sidebar .nav-link.active {
            @apply text-blue-600 bg-blue-100 font-semibold;
        }

        .sidebar .nav-link:hover {
            @apply bg-gray-200;
        }
    </style>
    <script>
        // Konfigurasi default Tailwind jika diperlukan
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#0D6EFD', // Biru Bootstrap sebagai contoh
                            foreground: '#FFFFFF',
                            50: '#EBF5FF',
                            100: '#D6EBFF',
                            200: '#ADDAFF',
                            300: '#84CAFF',
                            400: '#5BBFFF',
                            500: '#32B4FF',
                            600: '#09A9FF',
                            700: '#0094E6',
                            800: '#007ABF',
                            900: '#005C8F',
                        },
                        background: '#FFFFFF', // Warna latar utama
                        foreground: '#212529', // Warna teks utama
                        muted: {
                            DEFAULT: '#F8F9FA', // Warna latar yang diredam
                            foreground: '#6C757D' // Warna teks yang diredam
                        },
                        // Tambahkan warna lain sesuai kebutuhan
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'], // Ganti dengan font pilihan Anda
                    },
                }
            }
        }
    </script>
</head>

<body class="font-sans">
    <!-- Navbar -->
    <nav class="bg-primary text-primary-foreground shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a class="text-xl font-bold" href="<?= site_url('/') ?>">Nusantara Kode</a>

                <div class="flex items-center">
                    <div class="relative group">
                        <button class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-primary focus:ring-white" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <span class="sr-only">Open user menu</span>
                            <i class="bi bi-person-circle text-2xl mr-2"></i>
                            <span class="hidden md:inline"><?= session()->get('username') ?></span>

                        </button>
                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-background ring-1 ring-black ring-opacity-5 focus:outline-none hidden group-hover:block" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <a href="<?= site_url('user/profile') ?>" class="block px-4 py-2 text-sm text-foreground hover:bg-muted" role="menuitem" tabindex="-1" id="user-menu-item-0"><i class="bi bi-person mr-2"></i> Profile</a>
                            <a href="<?= site_url('user/settings') ?>" class="block px-4 py-2 text-sm text-foreground hover:bg-muted" role="menuitem" tabindex="-1" id="user-menu-item-1"><i class="bi bi-gear mr-2"></i> Settings</a>
                            <hr class="my-1 border-muted">
                            <a href="<?= site_url('logout') ?>" class="block px-4 py-2 text-sm text-foreground hover:bg-muted" role="menuitem" tabindex="-1" id="user-menu-item-2"><i class="bi bi-box-arrow-right mr-2"></i> Logout</a>
                        </div>
                    </div>
                    <button class="md:hidden ml-3 p-2 rounded-md text-primary-foreground hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" id="mobile-menu-button">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu, show/hide based on menu state. -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="<?= site_url('/') ?>" class="block px-3 py-2 rounded-md text-base font-medium text-primary-foreground hover:bg-primary-700">Home</a>
                <a href="<?= site_url('user/courses') ?>" class="block px-3 py-2 rounded-md text-base font-medium text-primary-foreground hover:bg-primary-700">Courses</a>
                <a href="<?= site_url('user/dashboard') ?>" class="block px-3 py-2 rounded-md text-base font-medium text-primary-foreground hover:bg-primary-700">My Dashboard</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 flex-grow">
        <div class="flex flex-col md:flex-row">
            <!-- Sidebar -->
            <aside class="w-full md:w-64 bg-background shadow-sm md:rounded-lg p-4 mt-4 md:mt-8 md:mr-6 hidden md:block">
                <nav class="space-y-1">
                    <a class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md text-foreground hover:bg-gray-100 <?= uri_string() == 'user/dashboard' ? 'active' : '' ?>" href="<?= site_url('user/dashboard') ?>">
                        <i class="bi bi-speedometer2 mr-3 flex-shrink-0 h-5 w-5"></i> Dashboard
                    </a>
                    <a class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md text-foreground hover:bg-gray-100 <?= uri_string() == 'user/courses' ? 'active' : '' ?>" href="<?= site_url('user/courses') ?>">
                        <i class="bi bi-book mr-3 flex-shrink-0 h-5 w-5"></i> Browse Courses
                    </a>
                    <a class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md text-foreground hover:bg-gray-100 <?= uri_string() == 'user/courses/enrolled' ? 'active' : '' ?>" href="<?= site_url('user/courses/enrolled') ?>">
                        <i class="bi bi-journal-check mr-3 flex-shrink-0 h-5 w-5"></i> My Courses
                    </a>
                    <a class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md text-foreground hover:bg-gray-100 <?= uri_string() == 'user/profile' ? 'active' : '' ?>" href="<?= site_url('user/profile') ?>">
                        <i class="bi bi-person mr-3 flex-shrink-0 h-5 w-5"></i> Profile
                    </a>
                    <a class="nav-link group flex items-center px-3 py-2 text-sm font-medium rounded-md text-foreground hover:bg-gray-100 <?= uri_string() == 'user/settings' ? 'active' : '' ?>" href="<?= site_url('user/settings') ?>">
                        <i class="bi bi-gear mr-3 flex-shrink-0 h-5 w-5"></i> Settings
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 py-6 md:py-8">
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-background border-t border-gray-200 mt-auto fixed bottom-0 w-full">
        <div class="container mx-auto py-6 px-4 text-center text-muted-foreground text-sm">
            <p>&copy; <?= date('Y') ?> Nusantara Kode. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                const icon = mobileMenuButton.querySelector('i');
                if (mobileMenu.classList.contains('hidden')) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                } else {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                }
            });
        }

        // User dropdown toggle (jika diperlukan JavaScript custom, namun group-hover Tailwind biasanya cukup)
        // const userMenuButton = document.getElementById('user-menu-button');
        // const userMenu = userMenuButton.nextElementSibling; // Asumsi menu adalah sibling berikutnya
        // if (userMenuButton && userMenu) {
        //     userMenuButton.addEventListener('click', (event) => {
        //         userMenu.classList.toggle('hidden');
        //         userMenuButton.setAttribute('aria-expanded', !userMenu.classList.contains('hidden'));
        //     });
        //     // Klik di luar untuk menutup
        //     document.addEventListener('click', (event) => {
        //         if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
        //             userMenu.classList.add('hidden');
        //             userMenuButton.setAttribute('aria-expanded', 'false');
        //         }
        //     });
        // }
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>