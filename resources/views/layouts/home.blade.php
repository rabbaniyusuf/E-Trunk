<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-TRANK</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">E-TRANK</div>
            <ul class="nav-links">
                <li><a href="#">Beranda</a></li>
                @auth
                    @role('petugas_pusat')
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @endrole
                    @role('petugas_kebersihan')
                        <li><a href="{{ route('petugas.dashboard') }}">Dashboard</a></li>
                    @endrole
                    @role('masyarakat')
                        <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                    @endrole
                @endauth
                {{-- <li><a href="{{ asset('aboutus.html') }}">Tentang Kami</a></li> --}}
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>E-TRANK</h1>
            <p>
                Selamat datang di E-TRANK (Electronic Trash Bank). E-TRANK adalah suatu portal bagi warga RT03/RW01
                Kelurahan Arjosari untuk mengakses sistem Pengelolaan Bank Sampah.
            </p>
            <a href="{{ route('login') }}" class="btn-primary">Mulai Sekarang</a>
        </div>
    </section>

    <!-- Bank Sampah Section -->
    <section class="bank-sampah" onclick="window.location.href='#';">
        <div class="bank-sampah-inner">
            <h2>Bank Sampah</h2>
            <p>
                Bank Sampah adalah tempat untuk menabung sampah yang telah dipilah. Masyarakat dapat menyetor sampah dan
                memperoleh poin atau insentif sebagai bentuk kontribusi terhadap lingkungan.
            </p>
        </div>
    </section>

    <!-- Monitoring Volume Sampah -->
    <section class="Monitoring" onclick="window.location.href='#';">
        <div class="monitoring-volume-sampah">
            <h3>Monitoring Volume Sampah</h3>
            <p>
                Monitoring volume sampah adalah proses pemantauan secara otomatis terhadap tingkat kepenuhan tempat
                sampah dengan menggunakan sensor. Monitoring ini dirancang untuk mendeteksi seberapa penuh tempat sampah
                mulai dari masih kosong, setengah penuh, hingga sudah harus segera dikosongkan.
            </p>
        </div>
    </section>



</body>

</html>
