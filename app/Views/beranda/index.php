<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KodeNusantara - Platform Kursus Programming Gratis</title>
  <!-- Tailwind CSS dari CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Konfigurasi Tailwind sesuai dengan tema yang ada di React -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              DEFAULT: '#3b82f6',
              foreground: '#ffffff',
            },
            secondary: {
              DEFAULT: '#6b7280',
              foreground: '#ffffff',
            },
            muted: {
              DEFAULT: '#f3f4f6',
              foreground: '#6b7280',
            },
            background: {
              DEFAULT: '#ffffff',
            },
            foreground: {
              DEFAULT: '#1f2937',
            },
          }
        }
      }
    }
  </script>
  <!-- Font Awesome untuk menggantikan Lucide Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen bg-background">
  <!-- Header -->
  <header class="border-b">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <i class="fa-solid fa-code text-2xl text-primary"></i>
        <span class="text-2xl font-bold">KodeNusantara</span>
      </div>
      <!-- <nav class="hidden md:flex items-center space-x-6">
        <a href="#courses" class="text-muted-foreground hover:text-foreground">Kursus</a>
        <a href="#about" class="text-muted-foreground hover:text-foreground">Tentang</a>
        <a href="#instructors" class="text-muted-foreground hover:text-foreground">Instruktur</a>
        <button class="px-4 py-2 bg-primary text-primary-foreground rounded-md">Daftar Gratis</button>
      </nav> -->
    </div>
  </header>

  <!-- Hero Section -->
  <section class="py-20 bg-gradient-to-br from-primary/10 via-background to-secondary/10">
    <div class="container mx-auto px-4">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
          <div class="w-fit px-3 py-1 bg-secondary/20 rounded-full text-sm">
            🇮🇩 Karya Anak Bangsa
          </div>
          <h1 class="text-4xl md:text-6xl font-bold leading-tight">
            Belajar Programming <span class="text-primary">Gratis</span> Bersama Expert Indonesia
          </h1>
          <p class="text-xl text-muted-foreground">
            Platform kursus video programming terlengkap buatan Indonesia.
            Semua kursus 100% gratis, berkualitas tinggi, dan dipandu oleh
            praktisi berpengalaman dari industri teknologi Indonesia.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <button class="text-lg px-8 py-3 bg-primary text-primary-foreground rounded-md">
              <i class="fa-solid fa-play mr-2"></i>
              <a href="/login">Mulai Belajar Sekarang</a>
            </button>
            <button class="text-lg px-8 py-3 border border-muted-foreground rounded-md">
              <a href="#courses">Lihat Semua Kursus</a>
            </button>
          </div>
          <div class="flex items-center space-x-8 pt-4">
            <div class="text-center">
              <div class="text-2xl font-bold">50+</div>
              <div class="text-sm text-muted-foreground">Kursus Gratis</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold">10K+</div>
              <div class="text-sm text-muted-foreground">Siswa Aktif</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold">4.8</div>
              <div class="text-sm text-muted-foreground">Rating Rata-rata</div>
            </div>
          </div>
        </div>
        <div class="relative">
          <img
            src="<?= base_url('/home/images/hero-programming.png') ?>"
            alt="Indonesian programmers learning together"
            class="rounded-2xl shadow-2xl w-full h-auto">
          <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-2xl"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Categories -->
  <section class="py-16">
    <div class="container mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold mb-4">Kategori Kursus</h2>
        <p class="text-muted-foreground">
          Pilih jalur pembelajaran sesuai minat dan tujuan karir Anda
        </p>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        $categories = [
          ['name' => 'Web Development', 'icon' => 'fa-globe', 'count' => 45],
          ['name' => 'Mobile Development', 'icon' => 'fa-mobile-screen', 'count' => 28],
          ['name' => 'Data Science', 'icon' => 'fa-database', 'count' => 32],
          ['name' => 'DevOps', 'icon' => 'fa-code', 'count' => 19],
        ];

        foreach ($categories as $category) :
        ?>
          <div class="hover:shadow-lg transition-shadow cursor-pointer rounded-md border p-6">
            <div class="p-6 text-center">
              <i class="fa-solid <?= $category['icon'] ?> text-primary text-4xl mb-4"></i>
              <h3 class="font-semibold mb-2"><?= $category['name'] ?></h3>
              <p class="text-sm text-muted-foreground">
                <?= $category['count'] ?> kursus tersedia
              </p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Featured Courses -->
  <section id="courses" class="py-16 bg-muted/30">
    <div class="container mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold mb-4">Kursus Populer</h2>
        <p class="text-muted-foreground">
          Kursus terpopuler yang dipilih ribuan developer Indonesia
        </p>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php
        if (isset($featuredCourses) && is_array($featuredCourses)) {
          foreach ($featuredCourses as $course) :
            // Konversi level dari bahasa Inggris ke Indonesia
            $levelText = "Pemula";
            if ($course['level'] == 'intermediate') {
              $levelText = "Menengah";
            } elseif ($course['level'] == 'advanced') {
              $levelText = "Lanjutan";
            }

            // Konversi durasi dari menit ke format jam
            $durationHours = floor($course['duration'] / 60);
            $durationText = $durationHours . " jam";
        ?>
            <div class="overflow-hidden hover:shadow-lg transition-shadow rounded-md border">
              <div class="relative">
                <img
                  src="<?= base_url($course['thumbnail']) ?>"
                  alt="<?= $course['title'] ?>"
                  class="w-full h-48 object-cover">
                <div class="absolute top-4 right-4 px-2 py-1 text-xs bg-secondary text-secondary-foreground rounded-md">
                  <?= $levelText ?>
                </div>
              </div>
              <div class="p-4">
                <h3 class="text-lg font-bold line-clamp-2"><?= $course['title'] ?></h3>
                <p class="text-sm text-muted-foreground line-clamp-3 mb-4">
                  <?= $course['short_description'] ?>
                </p>
              </div>
              <div class="px-4 pb-4">
                <div class="flex items-center justify-between text-sm text-muted-foreground mb-4">
                  <div class="flex items-center">
                    <i class="fa-regular fa-clock mr-1"></i>
                    <?= $durationText ?>
                  </div>
                  <div class="flex items-center">
                    <i class="fa-solid fa-calendar-alt mr-1"></i>
                    <?= date('d M Y', strtotime($course['published_at'])) ?>
                  </div>
                </div>

                <a href="<?= base_url('/user/view-course/' . $course['id']) ?>" class="block w-full py-2 bg-primary text-primary-foreground rounded-md text-center">
                  <i class="fa-solid fa-play mr-2"></i>
                  Mulai Belajar Gratis
                </a>
              </div>
            </div>
        <?php
          endforeach;
        } else {
          echo "<div class='col-span-3 text-center py-8'>Tidak ada kursus unggulan saat ini.</div>";
        }
        ?>
      </div>
      <div class="text-center mt-12">
        <button class="px-6 py-3 border border-muted-foreground rounded-md">
          <a href="/user/courses">Lihat Semua Kursus</a>
        </button>
      </div>
    </div>
  </section>

  <!-- Why Choose Us -->
  <section class="py-16">
    <div class="container mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold mb-4">
          Mengapa Pilih KodeNusantara?
        </h2>
        <p class="text-muted-foreground">
          Platform pembelajaran programming terbaik untuk developer
          Indonesia
        </p>
      </div>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="text-center">
          <div class="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl">🆓</span>
          </div>
          <h3 class="text-xl font-semibold mb-2">100% Gratis</h3>
          <p class="text-muted-foreground">
            Semua kursus tersedia gratis tanpa biaya tersembunyi. Komitmen
            kami untuk mencerdaskan bangsa.
          </p>
        </div>
        <div class="text-center">
          <div class="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl">🇮🇩</span>
          </div>
          <h3 class="text-xl font-semibold mb-2">Karya Anak Bangsa</h3>
          <p class="text-muted-foreground">
            Dibuat oleh dan untuk developer Indonesia dengan pemahaman
            mendalam tentang kebutuhan lokal.
          </p>
        </div>
        <div class="text-center">
          <div class="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl">⭐</span>
          </div>
          <h3 class="text-xl font-semibold mb-2">Kualitas Terjamin</h3>
          <p class="text-muted-foreground">
            Materi disusun oleh praktisi berpengalaman dari perusahaan
            teknologi terkemuka Indonesia.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Newsletter -->
  <section class="py-16 bg-primary text-primary-foreground">
    <div class="container mx-auto px-4 text-center">
      <h2 class="text-3xl font-bold mb-4">
        Jangan Lewatkan Update Terbaru
      </h2>
      <p class="text-primary-foreground/80 mb-8 max-w-2xl mx-auto">
        Dapatkan notifikasi kursus baru, tips programming, dan insight
        industri teknologi Indonesia langsung di inbox Anda.
      </p>
      <div class="max-w-md mx-auto flex gap-4">
        <input
          type="email"
          placeholder="Masukkan email Anda"
          class="px-4 py-2 rounded-md bg-background text-foreground flex-grow">
        <button class="px-4 py-2 bg-secondary text-secondary-foreground rounded-md">
          Berlangganan
        </button>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-muted py-12">
    <div class="container mx-auto px-4">
      <div class="grid md:grid-cols-4 gap-8">
        <div>
          <div class="flex items-center space-x-2 mb-4">
            <i class="fa-solid fa-code h-6 w-6 text-primary"></i>
            <span class="text-xl font-bold">KodeNusantara</span>
          </div>
          <p class="text-muted-foreground">
            Platform kursus programming gratis terbaik untuk developer
            Indonesia.
          </p>
        </div>
        <div>
          <h3 class="font-semibold mb-4">Kursus</h3>
          <ul class="space-y-2 text-muted-foreground">
            <li>
              <a href="#" class="hover:text-foreground">
                Web Development
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-foreground">
                Mobile Development
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-foreground">
                Data Science
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-foreground">
                DevOps
              </a>
            </li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold mb-4">Perusahaan</h3>
          <ul class="space-y-2 text-muted-foreground">
            <li>
              <a href="#" class="hover:text-foreground">
                Tentang Kami
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-foreground">
                Instruktur
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-foreground">
                Karir
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-foreground">
                Kontak
              </a>
            </li>
          </ul>
        </div>
        <div>
          <h3 class="font-semibold mb-4">Dukungan</h3>
          <ul class="space-y-2 text-muted-foreground">
            <li>
              <a href="#" class="hover:text-foreground">
                FAQ
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-foreground">
                Bantuan
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-foreground">
                Komunitas
              </a>
            </li>
            <li>
              <a href="#" class="hover:text-foreground">
                Blog
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="border-t mt-8 pt-8 text-center text-muted-foreground">
        <p>&copy; 2024 KodeNusantara. Dibuat dengan ❤️ untuk Indonesia.</p>
      </div>
    </div>
  </footer>
</body>

</html>