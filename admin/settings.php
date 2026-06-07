<?php
include "auth.php";
include "../db.php";
$adminPage = 'settings';
$saved = false;

if (isset($_POST['save'])) {
    foreach ($_POST['settings'] ?? [] as $key => $val) {
        $stmt = $conn->prepare("UPDATE settings SET setting_value=? WHERE setting_key=?");
        $stmt->bind_param("ss", $val, $key);
        $stmt->execute();
    }
    $saved = true;
}

$rows = $conn->query("SELECT * FROM settings ORDER BY id ASC");
$current = [];
while ($r = $rows->fetch_assoc()) $current[$r['setting_key']] = $r['setting_value'];
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>Setting Website</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css"></head><body>
<?php include "sidebar.php"; ?>
<div class="content">
    <div class="header"><div><h1>Setting Website</h1><p>Konfigurasi nomor WhatsApp, kontak, dan teks website</p></div></div>
    <?php if ($saved): ?><div class="alert alert-success"><i class="fa-solid fa-check-circle me-2"></i>Setting berhasil disimpan!</div><?php endif; ?>
    <div class="form-box" style="max-width:none;">
        <form method="POST">
            <h2><i class="fa-solid fa-globe me-2"></i>Identitas Website</h2>
            <div class="form-group"><label>Nama Situs</label><input type="text" name="settings[site_name]" value="<?= htmlspecialchars($current['site_name'] ?? '') ?>"></div>
            <div class="form-group"><label>Hero Title (judul utama)</label><input type="text" name="settings[hero_title]" value="<?= htmlspecialchars($current['hero_title'] ?? '') ?>"></div>
            <div class="form-group"><label>Hero Subtitle</label><input type="text" name="settings[hero_subtitle]" value="<?= htmlspecialchars($current['hero_subtitle'] ?? '') ?>"></div>
            <div class="form-group"><label>Tentang Kami (Profil)</label><textarea name="settings[about_text]" rows="4"><?= htmlspecialchars($current['about_text'] ?? '') ?></textarea></div>

            <h2 style="margin-top:30px;"><i class="fa-solid fa-address-book me-2"></i>Kontak & WhatsApp</h2>
            <div class="form-row">
                <div class="form-group"><label>Nomor WhatsApp (format 62...)</label><input type="text" name="settings[whatsapp_number]" placeholder="6281234567890" value="<?= htmlspecialchars($current['whatsapp_number'] ?? '') ?>"></div>
                <div class="form-group"><label>Telepon</label><input type="text" name="settings[telepon]" value="<?= htmlspecialchars($current['telepon'] ?? '') ?>"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Email</label><input type="email" name="settings[email]" value="<?= htmlspecialchars($current['email'] ?? '') ?>"></div>
                <div class="form-group"><label>Link Instagram</label><input type="url" name="settings[instagram_url]" value="<?= htmlspecialchars($current['instagram_url'] ?? '') ?>"></div>
            </div>
            <div class="form-group"><label>Alamat</label><textarea name="settings[alamat]" rows="2"><?= htmlspecialchars($current['alamat'] ?? '') ?></textarea></div>

            <div class="form-actions">
                <button name="save" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Setting</button>
            </div>
        </form>
    </div>
</div>
</body></html>
