<div class="flex h-[calc(100vh-112px)]">
    <!-- Main Content Area - Scrollable -->
    <div class="flex-1 container mx-auto overflow-y-auto scrollbar-thin scrollbar-dark p-6">
        <div class="mb-5 fr-view academy-tutorial-content js-content-prettify content--prettify-dark">
            <h1 dir="ltr">Pengertian React</h1><p dir="ltr"><a href="#" class="zoomable-image-anchor" data-toggle="modal" data-target="#image-zoom-modal" title="React Official Logo"><img alt="React Official Logo" title="React Official Logo" src="https://assets.cdn.dicoding.com/original/academy/dos:0ed5e398a698a15be165f8d888a3110720220412103004.jpeg" class="fr-fic fr-dii"></a></p><p dir="ltr">React diciptakan oleh Meta (dahulu Facebook) dan mulai dipublikasi pada NPM repository sejak tahun 2012. Seperti yang disinggung sebelumnya, React adalah JavaScript library yang digunakan untuk membangun User Interface (antarmuka pengguna). Hal ini ditegaskan oleh tim pengembang React pada website resminya di <a href="https://react.dev/" target="_blank" rel="noreferrer noopener">react.dev</a>.&nbsp;</p><p dir="ltr">Tak jarang React dibandingkan dengan JavaScript Framework seperti Vue atau Angular karena keduanya memiliki fungsi yang sama yakni membuat antarmuka pengguna web jadi lebih mudah. Namun, sebenarnya hal itu tak sebanding mengingat React hanyalah library. Library biasanya tak lebih besar daripada framework, baik dari segi ukuran maupun tingkat abstraksi yang dapat dilakukan dari standar yang ada. React tidak memaksakan penggunaan library pendukung tertentu dalam mengembangkan aplikasi web. <em>Your code, your way</em>. React hanya library yang ukurannya kecil [<a href="https://www.npmjs.com/package/react">1</a>], tetapi dapat membangun antarmuka pengguna melalui JavaScript dengan pengalaman yang lebih baik. Hal itulah mengapa kami menyukai React.</p><p dir="ltr">Dengan React, kita dapat terhindar dari banyak kesulitan yang biasa terjadi ketika menggunakan standar W3C dalam membangun antarmuka pengguna. Dilansir dari website resminya, React memanfaatkan konsep komponen, deklaratif, dan <em>unidirectional data flow</em> (aliran data searah). Berikut penjelasan singkatnya.</p><ul><li dir="ltr"><p dir="ltr"><strong>Komponen</strong><br>React memanfaatkan komponen dalam membangun antarmuka. Setiap komponen terenkapsulasi dan dapat saling dikomposisikan satu sama lain. Karena adanya komponen, antarmuka yang dibangun menggunakan React sangat reusable. Anda tidak perlu menuliskan kode yang sama berulang kali untuk menggunakan antarmuka yang serupa.<br><br></p></li><li dir="ltr"><p dir="ltr"><strong>Deklaratif</strong><br>Dengan konsep deklaratif, pembuatan antarmuka pengguna dapat lebih cepat. Pasalnya, kita cukup fokus terhadap apa yang ingin dicapai. Tak ada kode imperatif lagi ketika menggunakan React. Bahkan, Anda bisa menuliskan “layaknya” sintaksis HTML di dalam kode JavaScript. Hal yang mustahil dilakukan oleh JavaScript standar saat ini. Karena itu, Anda bisa mengucapkan selamat tinggal pada fungsi DOM manipulation, seperti appendChild, getElementById, addEventListener, dan sebagainya.<br><br></p></li><li dir="ltr"><p dir="ltr"><strong>Aliran Data Searah<br></strong>Komponen React dapat menampung sekumpulan data. React secara reaktif akan memperbarui dan me-render komponen jika data di dalamnya berubah. Karena sifat reaktifnya tersebut, kami rasa inilah alasan mengapa dinamakan React. Komponen React dapat dikomposisikan dan aliran data pada komponen dilakukan secara searah dari <em>parent ke child</em>. Hal itu membuat perubahan data pada React lebih terukur dan masuk akal.</p></li></ul><blockquote><p dir="ltr"><strong>Catatan:&nbsp;</strong>Jangan khawatir bila Anda belum memahami betul penjelasan di atas. Kami akan membahas konsep dasar React lebih detail pada modul selanjutnya.</p></blockquote><p dir="ltr">Oke, saat ini Anda sudah tahu sekilas tentang React. Di modul selanjutnya, kami akan menjelaskan mengapa React adalah pilihan yang tepat untuk Anda pelajari saat ini sebagai library dalam membangun antarmuka pengguna.</p><blockquote><p><strong>Catatan:</strong> Tak afdal rasanya bila memulai belajar React tanpa mengetahui alasan mengapa React sendiri dibangun. Kami sarankan Anda untuk menyimak penjelasan Pete Hunt sebagai tim inti React pada artikel berjudul “<a href="https://reactjs.org/blog/2013/06/05/why-react.html" target="_blank" rel="noreferrer noopener">Why did we build React?</a>”.</p></blockquote>
        </div>
    </div>
    
    <!-- Sidebar - Module List - Scrollable -->
    <?= view_cell('\App\Libraries\ModuleList::render') ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah modal sudah ada di halaman
    let modalExists = document.getElementById('image-zoom-modal');
    
  console.log(modalExists);
  
    // Tangani klik pada gambar yang bisa di-zoom
    const zoomableImages = document.querySelectorAll('.zoomable-image-anchor');
    
    zoomableImages.forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const img = anchor.querySelector('img');
            const modalImg = document.getElementById('zoom-image');
            const modalTitle = document.querySelector('#imageZoomModalLabel');
            
            // Set gambar dan judul pada modal
            if (img && modalImg) {
                modalImg.src = img.src;
                if (modalTitle) {
                    modalTitle.textContent = img.title || img.alt;
                }
                
                // Tampilkan modal menggunakan Bootstrap
                $('#image-zoom-modal').modal('show');
            }
        });
    });
});
</script>