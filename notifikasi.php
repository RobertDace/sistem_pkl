<?php
include 'templates/header.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$user_id = $_SESSION['user_id'];

// Ambil semua notifikasi untuk user ini
$sql = "SELECT * FROM notifikasi WHERE user_id_tujuan = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Setelah halaman dibuka, tandai semua notifikasi user ini sebagai "sudah_dibaca"
$sql_update = "UPDATE notifikasi SET status = 'sudah_dibaca' WHERE user_id_tujuan = ?";
$stmt_update = mysqli_prepare($koneksi, $sql_update);
mysqli_stmt_bind_param($stmt_update, "i", $user_id);
mysqli_stmt_execute($stmt_update);
?>

<div class="row">
    <div class="col-md-3">
        <?php include 'templates/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header"><h4><i class="fas fa-bell"></i> Notifikasi</h4></div>
            <div class="card-body">
                <div class="list-group">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while($notif = mysqli_fetch_assoc($result)): ?>
                            <a href="<?php echo !empty($notif['link_terkait']) ? htmlspecialchars($notif['link_terkait']) : '#'; ?>" class="list-group-item list-group-item-action">
                                <p class="mb-1"><?php echo htmlspecialchars($notif['pesan']); ?></p>
                                <small class="text-muted"><?php echo date('d M Y, H:i', strtotime($notif['created_at'])); ?></small>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center p-3 text-muted">Tidak ada notifikasi.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>