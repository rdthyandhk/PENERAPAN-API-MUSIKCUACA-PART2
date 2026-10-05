<?php
require_once __DIR__ . '/config/app.php';

$profile = $pdo->query("SELECT * FROM profile LIMIT 1")->fetch();
$recent = $pdo->query("SELECT * FROM search_history ORDER BY created_at DESC LIMIT 6")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($profile['name'] ?? 'DTZY') ?> - Profile & API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>" rel="stylesheet">
    <link href="assets/css/redesign.css?v=<?= filemtime(__DIR__ . '/assets/css/redesign.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#home">DTZY<span>.</span></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#profile">Profil</a></li>
                <li class="nav-item"><a class="nav-link" href="#music">Music API</a></li>
                <li class="nav-item"><a class="nav-link" href="#weather">Cuaca API</a></li>
                <li class="nav-item"><a class="nav-link" href="#favorites">Favorit</a></li>
            </ul>
        </div>
    </div>
</nav>

<section id="home" class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge rounded-pill text-bg-primary px-3 py-2 mb-3">PHP + MySQL + REST API</span>
                <h1>Halo, saya <span><?= htmlspecialchars($profile['name'] ?? 'Radithya Andhika') ?></span>.</h1>
                <p class="lead"><?= htmlspecialchars($profile['bio'] ?? 'Pelajar yang tertarik dengan pemrograman dan pengembangan website.') ?></p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="#music" class="btn btn-primary btn-lg"><i class="bi bi-music-note-beamed"></i> Coba Music API</a>
                    <a href="#weather" class="btn btn-outline-light btn-lg"><i class="bi bi-cloud-sun"></i> Cek Cuaca</a>
                </div>
            </div>
            <div class="col-lg-5 text-center">
                <div class="profile-orbit">
                    <div class="avatar"><?= strtoupper(substr($profile['name'] ?? 'RA', 0, 2)) ?></div>
                    <div class="orbit-dot dot-1"><i class="bi bi-code-slash"></i></div>
                    <div class="orbit-dot dot-2"><i class="bi bi-database"></i></div>
                    <div class="orbit-dot dot-3"><i class="bi bi-cloud"></i></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="profile" class="section">
    <div class="container">
        <div class="section-title"><span>ABOUT ME</span><h2>Profil Saya</h2></div>
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card dark-card h-100">
                    <div class="card-body p-4 p-lg-5">
                        <h4><i class="bi bi-person-circle text-primary"></i> <?= htmlspecialchars($profile['name']) ?></h4>
                        <p class="text-secondary"><?= nl2br(htmlspecialchars($profile['bio'])) ?></p>
                        <div class="row mt-4 g-3">
                            <div class="col-sm-6"><div class="info-box"><small>Keahlian</small><strong><?= htmlspecialchars($profile['skills']) ?></strong></div></div>
                            <div class="col-sm-6"><div class="info-box"><small>Hobi</small><strong><?= htmlspecialchars($profile['hobby']) ?></strong></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card dark-card h-100">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Teknologi</h5>
                        <div class="tech-list">
                            <span>PHP</span><span>MySQL</span><span>Bootstrap</span><span>JavaScript</span><span>REST API</span><span>HTML/CSS</span>
                        </div>
                        <hr class="border-secondary my-4">
                        <p class="small text-secondary mb-0">Website ini menggunakan database untuk menyimpan profil dan riwayat pencarian API.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="music" class="section section-alt">
    <div class="container">
        <div class="section-title"><span>REST API</span><h2>Music Explorer</h2><p>Cari lagu melalui iTunes Search API tanpa perlu API key.</p></div>
        <div class="search-panel">
            <div class="input-group input-group-lg">
                <input id="musicQuery" class="form-control" placeholder="Contoh: Bruno Mars, NIKI, Coldplay...">
                <button id="musicBtn" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
            </div>
        </div>
        <div id="musicStatus" class="text-center text-secondary my-4"></div>
        <div id="musicResults" class="row g-4"></div>
    </div>
</section>

<section id="favorites" class="section section-alt">
    <div class="container">
        <div class="section-title">
            <span>MY COLLECTION</span>
            <h2>Musik Favorit</h2>
            <p>Lagu yang kamu favoritkan tersimpan di database MySQL.</p>
        </div>
        <div id="favoriteStatus" class="text-center text-secondary my-3"></div>
        <div id="favoriteResults" class="row g-4"></div>
    </div>
</section>

<section id="weather" class="section">
    <div class="container">
        <div class="section-title"><span>REST API</span><h2>Weather Dashboard</h2><p>Data cuaca menggunakan Open-Meteo dan koordinat kota secara otomatis.</p></div>
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="card dark-card h-100">
                    <div class="card-body p-4">
                        <label class="form-label">Nama kota</label>
                        <div class="input-group">
                            <input id="cityInput" class="form-control form-control-lg">
                            <button id="weatherBtn" class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </div>
                        <div class="quick-cities mt-3">
                            <button data-city="Jakarta">Jakarta</button>
                            <button data-city="Surabaya">Surabaya</button>
                            <button data-city="Semarang">Semarang</button>
                            <button data-city="Yogyakarta">Yogyakarta</button>
                            <button data-city="Bandung">Bandung</button>
                            <button data-city="Karanganyar">Karanganyar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div id="weatherResult" class="weather-card h-100">
                    <div class="text-center text-secondary">Masukkan kota untuk melihat cuaca.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-title"><span>DATABASE</span><h2>Riwayat Pencarian</h2></div>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead><tr><th>Jenis</th><th>Query</th><th>Waktu</th></tr></thead>
                <tbody>
                <?php if (!$recent): ?>
                    <tr><td colspan="3" class="text-center text-secondary">Belum ada riwayat.</td></tr>
                <?php else: foreach ($recent as $row): ?>
                    <tr>
                        <td><span class="badge <?= $row['type'] === 'music' ? 'text-bg-primary' : 'text-bg-info' ?>"><?= htmlspecialchars($row['type']) ?></span></td>
                        <td><?= htmlspecialchars($row['query_text']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<footer><div class="container text-center">© <?= date('Y') ?> <?= htmlspecialchars($profile['name'] ?? 'DTZY') ?> · Built with PHP & MySQL</div></footer>

<script src="assets/js/app.js?v=<?= filemtime(__DIR__ . '/assets/js/app.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
