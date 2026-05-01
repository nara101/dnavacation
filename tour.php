<?php
include "db.php";
$data = $conn->query("SELECT * FROM tour ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Paket Tour</title>

  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: #f8f9fb;
    }

    .tour-section {
      padding: 60px 80px;
      text-align: center;
    }

    .tour-section h2 {
      font-size: 32px;
      margin-bottom: 10px;
    }

    .tour-section p {
      color: #666;
      margin-bottom: 40px;
    }

    .tour-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }

    .tour-card {
      background: white;
      border-radius: 22px;
      overflow: hidden;
      box-shadow: 0 15px 35px rgba(0, 0, 0, .1);
      text-decoration: none;
      color: black;
      transition: .3s;
    }

    .tour-card:hover {
      transform: translateY(-8px);
    }

    .tour-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
    }

    .tour-info {
      padding: 20px;
      text-align: left;
    }

    .tour-info h3 {
      margin: 0 0 10px;
      font-size: 18px;
    }

    .meta {
      color: #555;
      font-size: 14px;
    }
  </style>
</head>

<body>

  <section class="tour-section">
    <h2>PAKET TOUR TERBAIK</h2>
    <p>Temukan Pengalaman Liburan Terbaik Bersama Kami</p>

    <div class="tour-grid">
      <?php while ($t = $data->fetch_assoc()) : ?>
        <a href="tour-detail.php?id=<?= $t['id']; ?>" class="tour-card">
          <img src="uploads/<?= $t['gambar']; ?>" alt="<?= htmlspecialchars($t['judul']); ?>">

          <div class="tour-info">
            <h3><?= htmlspecialchars($t['judul']); ?></h3>
            <div class="meta">
              ⏱ <?= $t['durasi']; ?> | 📍 <?= $t['lokasi']; ?>
            </div>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
  </section>

</body>

</html>