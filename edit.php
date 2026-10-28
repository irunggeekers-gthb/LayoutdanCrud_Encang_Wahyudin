<?php
/**
 * ==============================================================================
 * File: edit.php
 * Fungsi: Halaman Formulir untuk Mengubah (UPDATE) Data Mahasiswa Berdasarkan ID
 *         Dilengkapi Preview & Ganti Foto Profil serta Dokumen / Berkas (Maks. 4 MB)
 * Modul: Pemrograman Web 1 - CRUD PHP PDO & Templating Layout
 * ==============================================================================
 */

// 1. Menyisipkan file koneksi database PDO
require "koneksi.php";

// 2. Menangkap parameter ID mahasiswa yang dikirim melalui URL GET (misal: edit.php?id=3)
$id = $_GET['id'] ?? '';

// 3. Menyiapkan query SQL SELECT dengan Prepared Statement untuk mengambil 1 baris data mahasiswa
$sql = "SELECT * FROM mahasiswa WHERE id = :id";
$stmt = $pdo->prepare($sql);

// Mengeksekusi query dengan menyuntikkan ID secara aman
$stmt->execute(['id' => $id]);

// Mengambil 1 baris data mahasiswa ke variabel array $mahasiswa
$mahasiswa = $stmt->fetch();

// 4. Validasi Keamanan: Jika ID tidak ditemukan di database, tampilkan peringatan dan kembali ke index.php
if (!$mahasiswa) {
    echo "<script>
            alert('Data mahasiswa tidak ditemukan!');
            window.location='index.php';
          </script>";
    exit;
}

// 5. Memanggil komponen layout modular bagian atas (Header) dan samping (Sidebar)
require_once 'layouts/header.php';
require_once 'layouts/sidebar.php';
?>

<!-- 6. KONTEN UTAMA FORMULIR EDIT (FRONTEND ADMINLTE) -->
<div class="content-wrapper">
    <!-- Header Konten (Judul Halaman) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark" style="font-size: 1.15rem; font-weight: 600;">
                        <i class="fas fa-edit text-warning mr-2"></i>Edit Data Mahasiswa
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Isi Formulir Utama -->
    <section class="content">
        <div class="container-fluid">
            <!-- Komponen Card Bootstrap / AdminLTE beraksen kuning (card-warning) -->
            <div class="card card-warning">
                <div class="card-header py-2">
                    <h3 class="card-title font-weight-bold" style="font-size: 0.9rem;">Formulir Perubahan Data Mahasiswa</h3>
                </div>

                <!-- Formulir HTML mengirim data ke 'proses.php' menggunakan metode HTTP POST dan multipart/form-data -->
                <form action="proses.php" method="POST" enctype="multipart/form-data">
                    <!-- Input Tersembunyi 1: Memberitahu proses.php bahwa aksinya adalah 'update' -->
                    <input type="hidden" name="action" value="update">
                    <!-- Input Tersembunyi 2: Menyimpan nilai ID mahasiswa yang sedang diubah nilainya -->
                    <input type="hidden" name="id" value="<?= htmlspecialchars($mahasiswa['id']); ?>">
                    <!-- Input Tersembunyi 3 & 4: Menyimpan nama file lama untuk perbandingan/penghapusan -->
                    <input type="hidden" name="old_foto" value="<?= htmlspecialchars($mahasiswa['foto'] ?? ''); ?>">
                    <input type="hidden" name="old_berkas" value="<?= htmlspecialchars($mahasiswa['berkas'] ?? ''); ?>">

                    <div class="card-body">
                        <div class="row">
                            <!-- Kolom Kiri: Atribut Teks Mahasiswa -->
                            <div class="col-md-6">
                                <!-- Input Kolom NIM (Pre-filled dengan nilai lama $mahasiswa['nim']) -->
                                <div class="form-group">
                                    <label for="nim">Nomor Induk Mahasiswa (NIM) <span class="text-danger">*</span></label>
                                    <input type="text" name="nim" id="nim" class="form-control form-control-sm" value="<?= htmlspecialchars($mahasiswa['nim']); ?>" required>
                                </div>

                                <!-- Input Kolom Nama Mahasiswa (Pre-filled dengan nilai lama $mahasiswa['nama']) -->
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap Mahasiswa <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" id="nama" class="form-control form-control-sm" value="<?= htmlspecialchars($mahasiswa['nama']); ?>" required>
                                </div>

                                <!-- Input Kolom Program Studi (Pre-filled dengan nilai lama $mahasiswa['prodi']) -->
                                <div class="form-group">
                                    <label for="prodi">Program Studi / Jurusan <span class="text-danger">*</span></label>
                                    <input type="text" name="prodi" id="prodi" class="form-control form-control-sm" value="<?= htmlspecialchars($mahasiswa['prodi']); ?>" required>
                                </div>

                                <!-- Input Kolom Tahun Angkatan (Pre-filled dengan nilai lama $mahasiswa['angkatan']) -->
                                <div class="form-group">
                                    <label for="angkatan">Tahun Angkatan <span class="text-danger">*</span></label>
                                    <input type="number" name="angkatan" id="angkatan" class="form-control form-control-sm" value="<?= htmlspecialchars($mahasiswa['angkatan']); ?>" required>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Preview & Ganti Foto & Berkas -->
                            <div class="col-md-6">
                                <!-- Preview dan Ganti Foto -->
                                <div class="form-group">
                                    <label for="foto">
                                        <i class="fas fa-image mr-1 text-primary"></i>Foto Mahasiswa
                                        <small class="text-muted">(Maks 4 MB)</small>
                                    </label>
                                    <div class="d-flex align-items-center mb-2">
                                        <?php if (!empty($mahasiswa['foto']) && file_exists(__DIR__ . '/uploads/' . $mahasiswa['foto'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($mahasiswa['foto']); ?>" alt="Foto Mahasiswa" class="img-thumbnail mr-3 shadow-sm" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px;">
                                            <div>
                                                <span class="badge badge-success mb-1">Foto Tersedia</span><br>
                                                <small class="text-muted text-break"><?= htmlspecialchars($mahasiswa['foto']); ?></small>
                                            </div>
                                        <?php else: ?>
                                            <div class="bg-light border rounded text-muted d-flex align-items-center justify-content-center mr-3" style="width: 70px; height: 70px;">
                                                <i class="fas fa-user-circle fa-3x text-secondary"></i>
                                            </div>
                                            <div>
                                                <span class="badge badge-secondary mb-1">Belum Ada Foto</span><br>
                                                <small class="text-muted">Gunakan form di bawah untuk mengunggah foto.</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" name="foto" id="foto" class="form-control-file border rounded p-1" accept=".jpg,.jpeg,.png,.webp">
                                    <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto. Format: JPG, PNG, WEBP (Maks 4 MB).</small>
                                </div>

                                <!-- Preview dan Ganti Berkas / Dokumen -->
                                <div class="form-group mt-3">
                                    <label for="berkas">
                                        <i class="fas fa-file-alt mr-1 text-info"></i>Berkas / Dokumen Mahasiswa
                                        <small class="text-muted">(Maks 4 MB)</small>
                                    </label>
                                    <div class="mb-2">
                                        <?php if (!empty($mahasiswa['berkas']) && file_exists(__DIR__ . '/uploads/' . $mahasiswa['berkas'])): ?>
                                            <div class="alert alert-light border py-2 px-3 mb-2 d-flex align-items-center justify-content-between">
                                                <div class="text-truncate mr-2">
                                                    <i class="fas fa-paperclip text-info mr-1"></i>
                                                    <small class="font-weight-bold"><?= htmlspecialchars($mahasiswa['berkas']); ?></small>
                                                </div>
                                                <a href="uploads/<?= htmlspecialchars($mahasiswa['berkas']); ?>" target="_blank" class="btn btn-xs btn-outline-info text-nowrap">
                                                    <i class="fas fa-eye mr-1"></i>Lihat Berkas
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge badge-secondary mb-1">Belum Ada Berkas</span>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" name="berkas" id="berkas" class="form-control-file border rounded p-1" accept=".pdf,.doc,.docx,.zip,.xls,.xlsx">
                                    <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengganti berkas. Format: PDF, DOCX, ZIP (Maks 4 MB).</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi: Update (Submit Perubahan) dan Batal (Kembali ke Index) -->
                    <div class="card-footer py-2">
                        <button type="submit" class="btn btn-sm btn-warning text-white">
                            <i class="fas fa-check mr-1"></i> Update Data Mahasiswa
                        </button>
                        <a href="index.php" class="btn btn-sm btn-secondary ml-2">
                            <i class="fas fa-arrow-left mr-1"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php
// 7. Memanggil komponen layout penutup bawah (Footer & Scripts)
require_once 'layouts/footer.php';
?>
