<?php
/**
 * ==============================================================================
 * File: index.php
 * Fungsi: Halaman Utama untuk Membaca (READ) Data Mahasiswa, Pencarian (SEARCH),
 *         Foto Mahasiswa di Setiap Baris, Upload Berkas, serta Pagination Dinamis
 * Modul: Pemrograman Web 1 - CRUD PHP PDO & Templating Layout
 * ==============================================================================
 */

// 1. Menyisipkan file koneksi database PDO
require "koneksi.php";

// 2. Memanggil komponen layout modular bagian atas (Header) dan samping (Sidebar)
require_once 'layouts/header.php';
require_once 'layouts/sidebar.php';

// 3. Menangkap kata kunci pencarian dari parameter URL 'q'
$keyword = trim($_GET['q'] ?? '');

// ==============================================================================
// 4. Konfigurasi dan Logika Pagination (Paginasi Data)
// ==============================================================================
$limit = 5; // Jumlah data yang ditampilkan per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}

// 4a. Menghitung Total Data (Sesuai Filter Pencarian)
if (!empty($keyword)) {
    $countSql = "SELECT COUNT(*) FROM mahasiswa 
                 WHERE nim LIKE :kw1 OR nama LIKE :kw2 OR prodi LIKE :kw3 OR angkatan LIKE :kw4";
    $countStmt = $pdo->prepare($countSql);
    $param = "%{$keyword}%";
    $countStmt->execute([
        'kw1' => $param,
        'kw2' => $param,
        'kw3' => $param,
        'kw4' => $param
    ]);
} else {
    $countSql = "SELECT COUNT(*) FROM mahasiswa";
    $countStmt = $pdo->query($countSql);
}
$totalData = (int)$countStmt->fetchColumn();

// Menghitung total halaman
$totalPages = $totalData > 0 ? (int)ceil($totalData / $limit) : 1;
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $limit;

// 4b. Query Mengambil Data dengan LIMIT & OFFSET
if (!empty($keyword)) {
    $sql = "SELECT * FROM mahasiswa 
            WHERE nim LIKE :kw1 OR nama LIKE :kw2 OR prodi LIKE :kw3 OR angkatan LIKE :kw4 
            ORDER BY id DESC 
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $param = "%{$keyword}%";
    $stmt->bindValue(':kw1', $param, PDO::PARAM_STR);
    $stmt->bindValue(':kw2', $param, PDO::PARAM_STR);
    $stmt->bindValue(':kw3', $param, PDO::PARAM_STR);
    $stmt->bindValue(':kw4', $param, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $sql = "SELECT * FROM mahasiswa ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
}

// 5. Mengambil data mahasiswa untuk halaman aktif
$dataMahasiswa = $stmt->fetchAll();
?>

<!-- 6. KONTEN UTAMA HALAMAN (FRONTEND ADMINLTE) -->
<div class="content-wrapper">
    <!-- Header Konten (Judul Halaman) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark" style="font-size: 1.15rem; font-weight: 600;">
                        <i class="fas fa-users text-primary mr-2"></i>Data Mahasiswa
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Isi Konten Utama -->
    <section class="content">
        <div class="container-fluid">
            <!-- Komponen Card Bootstrap / AdminLTE -->
            <div class="card shadow-sm">
                <!-- Header Card: Tombol Tambah Data & Form Pencarian -->
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <!-- Tombol Tambah Data -->
                    <a href="create.php" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Data
                    </a>

                    <!-- Form Pencarian (Rata Kanan) -->
                    <form action="index.php" method="GET" class="ml-auto mb-0 d-flex align-items-center">
                        <div class="input-group input-group-sm" style="width: 260px;">
                            <input type="text" name="q" class="form-control" placeholder="Cari NIM, Nama, Prodi..." value="<?= htmlspecialchars($keyword); ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default" title="Cari Data">
                                    <i class="fas fa-search"></i>
                                </button>
                                <?php if (!empty($keyword)): ?>
                                    <a href="index.php" class="btn btn-default text-danger" title="Reset Pencarian">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Body Card: Tabel Responsif Data Mahasiswa -->
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 45px;" class="text-center align-middle">No</th>
                                <th style="width: 60px;" class="text-center align-middle">Foto</th>
                                <th style="width: 120px;" class="align-middle">NIM</th>
                                <th class="align-middle">Nama Mahasiswa</th>
                                <th class="align-middle">Program Studi</th>
                                <th style="width: 85px;" class="text-center align-middle">Angkatan</th>
                                <th style="width: 110px;" class="text-center align-middle">Berkas</th>
                                <th style="width: 135px;" class="text-center align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Pengecekan jika tidak ada data -->
                            <?php if (empty($dataMahasiswa)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <?php if (!empty($keyword)): ?>
                                            Tidak ditemukan data mahasiswa dengan kata kunci "<strong><?= htmlspecialchars($keyword); ?></strong>". 
                                            <a href="index.php" class="ml-1 font-weight-bold">Tampilkan Semua Data</a>
                                        <?php else: ?>
                                            Belum ada data mahasiswa dalam database.
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <!-- Looping merender data mahasiswa baris demi baris -->
                                <?php
                                $no = $offset + 1;
                                foreach ($dataMahasiswa as $mhs):
                                ?>
                                <tr>
                                    <!-- Kolom nomor urut dinamis sesuai halaman -->
                                    <td class="text-center align-middle"><?= $no++; ?></td>

                                    <!-- Kolom Foto Mahasiswa Setiap Baris -->
                                    <td class="text-center align-middle">
                                        <?php if (!empty($mhs['foto']) && file_exists(__DIR__ . '/uploads/' . $mhs['foto'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($mhs['foto']); ?>" alt="Foto" class="img-circle elevation-1" style="width: 36px; height: 36px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center border" style="width: 36px; height: 36px;">
                                                <i class="fas fa-user text-muted" style="font-size: 1rem;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Kolom NIM -->
                                    <td class="align-middle font-weight-500"><?= htmlspecialchars($mhs['nim']); ?></td>

                                    <!-- Kolom Nama Mahasiswa -->
                                    <td class="align-middle font-weight-bold"><?= htmlspecialchars($mhs['nama']); ?></td>

                                    <!-- Kolom Program Studi -->
                                    <td class="align-middle"><?= htmlspecialchars($mhs['prodi']); ?></td>

                                    <!-- Kolom Angkatan -->
                                    <td class="text-center align-middle"><?= htmlspecialchars($mhs['angkatan']); ?></td>

                                    <!-- Kolom Berkas / Dokumen Mahasiswa (Maks 4 MB) -->
                                    <td class="text-center align-middle">
                                        <?php if (!empty($mhs['berkas']) && file_exists(__DIR__ . '/uploads/' . $mhs['berkas'])): ?>
                                            <a href="uploads/<?= htmlspecialchars($mhs['berkas']); ?>" target="_blank" class="btn btn-xs btn-outline-info" title="Unduh / Lihat Berkas">
                                                <i class="fas fa-download mr-1"></i>Unduh
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted" style="font-size: 0.8rem;">-</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Kolom AKSI: Edit & Hapus -->
                                    <td class="text-center align-middle text-nowrap">
                                        <div class="d-inline-flex" style="gap: 4px;">
                                            <a href="edit.php?id=<?= $mhs['id']; ?>" class="btn btn-xs btn-warning text-white" title="Edit Data">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="delete.php?id=<?= $mhs['id']; ?>" onclick="return confirm('Yakin ingin menghapus data <?= htmlspecialchars($mhs['nama']); ?>?');" class="btn btn-xs btn-danger" title="Hapus Data">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Card: Ringkasan Informasi Data & Navigasi Pagination -->
                <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap py-2">
                    <div class="text-muted text-sm my-1">
                        <?php if ($totalData > 0): ?>
                            Menampilkan <strong><?= $offset + 1; ?></strong> - <strong><?= min($offset + count($dataMahasiswa), $totalData); ?></strong> dari <strong><?= $totalData; ?></strong> mahasiswa
                            <?php if (!empty($keyword)): ?>
                                (difilter dari pencarian "<em><?= htmlspecialchars($keyword); ?></em>")
                            <?php endif; ?>
                        <?php else: ?>
                            Total 0 data mahasiswa
                        <?php endif; ?>
                    </div>

                    <!-- Navigasi Komponen Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <ul class="pagination pagination-sm m-0 my-1">
                            <?php
                            $queryParam = !empty($keyword) ? '&q=' . urlencode($keyword) : '';
                            ?>
                            <!-- Tombol Previous -->
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?= $page - 1; ?><?= $queryParam; ?>" aria-label="Previous">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>

                            <!-- Nomor Halaman -->
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?= $i; ?><?= $queryParam; ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Tombol Next -->
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?= $page + 1; ?><?= $queryParam; ?>" aria-label="Next">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
// 7. Memanggil komponen layout penutup bawah (Footer & Scripts)
require_once 'layouts/footer.php';
?>
