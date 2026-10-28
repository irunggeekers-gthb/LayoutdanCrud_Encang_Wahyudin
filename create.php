<?php
/**
 * ==============================================================================
 * File: create.php
 * Fungsi: Halaman Formulir untuk Menambahkan (CREATE) Data Mahasiswa Baru
 *         Dilengkapi Upload Foto Profil & Dokumen / Berkas (Maks. 4 MB)
 * Modul: Pemrograman Web 1 - CRUD PHP PDO & Templating Layout
 * ==============================================================================
 */

// 1. Menyisipkan file koneksi database PDO
require "koneksi.php";

// 2. Memanggil komponen layout modular bagian atas (Header) dan samping (Sidebar)
require_once 'layouts/header.php';
require_once 'layouts/sidebar.php';
?>

<!-- 3. KONTEN UTAMA FORMULIR TAMBAH (FRONTEND ADMINLTE) -->
<div class="content-wrapper">
    <!-- Header Konten (Judul Halaman) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-1">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark" style="font-size: 1.15rem; font-weight: 600;">
                        <i class="fas fa-user-plus text-primary mr-2"></i>Tambah Data Mahasiswa
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Isi Formulir Utama -->
    <section class="content">
        <div class="container-fluid">
            <!-- Komponen Card Bootstrap / AdminLTE beraksen biru (card-primary) -->
            <div class="card card-primary">
                <div class="card-header py-2">
                    <h3 class="card-title font-weight-bold" style="font-size: 0.9rem;">Formulir Input Mahasiswa Baru</h3>
                </div>

                <!-- Formulir HTML mengirim data ke 'proses.php' menggunakan metode HTTP POST dan multipart/form-data untuk file upload -->
                <form action="proses.php" method="POST" enctype="multipart/form-data">
                    <!-- Input Tersembunyi: Memberitahu proses.php bahwa aksinya adalah 'create' (tambah data) -->
                    <input type="hidden" name="action" value="create">

                    <div class="card-body">
                        <div class="row">
                            <!-- Kolom Kiri: Data Teks Mahasiswa -->
                            <div class="col-md-6">
                                <!-- Input Kolom NIM -->
                                <div class="form-group">
                                    <label for="nim">Nomor Induk Mahasiswa (NIM) <span class="text-danger">*</span></label>
                                    <input type="text" name="nim" id="nim" class="form-control form-control-sm" placeholder="Masukkan NIM (contoh: 230101001)" required autofocus>
                                </div>

                                <!-- Input Kolom Nama Lengkap -->
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap Mahasiswa <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" id="nama" class="form-control form-control-sm" placeholder="Masukkan Nama Lengkap" required>
                                </div>

                                <!-- Input Kolom Program Studi -->
                                <div class="form-group">
                                    <label for="prodi">Program Studi / Jurusan <span class="text-danger">*</span></label>
                                    <input type="text" name="prodi" id="prodi" class="form-control form-control-sm" placeholder="Contoh: Teknik Informatika / Sistem Informasi" required>
                                </div>

                                <!-- Input Kolom Tahun Angkatan -->
                                <div class="form-group">
                                    <label for="angkatan">Tahun Angkatan <span class="text-danger">*</span></label>
                                    <input type="number" name="angkatan" id="angkatan" class="form-control form-control-sm" placeholder="Contoh: 2024" required>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Upload Foto & Berkas -->
                            <div class="col-md-6">
                                <!-- Upload Foto Mahasiswa -->
                                <div class="form-group">
                                    <label for="foto">
                                        <i class="fas fa-image mr-1 text-primary"></i>Foto Mahasiswa
                                        <small class="text-muted">(Opsional, Maks 4 MB)</small>
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" name="foto" id="foto" class="form-control-file border rounded p-1" accept=".jpg,.jpeg,.png,.webp">
                                    </div>
                                    <small class="form-text text-muted">Format: JPG, JPEG, PNG, WEBP (Maksimal 4 MB).</small>
                                </div>

                                <!-- Upload File Berkas / Dokumen -->
                                <div class="form-group">
                                    <label for="berkas">
                                        <i class="fas fa-file-alt mr-1 text-info"></i>Upload Berkas / Dokumen
                                        <small class="text-muted">(Opsional, Maks 4 MB)</small>
                                    </label>
                                    <div class="custom-file">
                                        <input type="file" name="berkas" id="berkas" class="form-control-file border rounded p-1" accept=".pdf,.doc,.docx,.zip,.xls,.xlsx">
                                    </div>
                                    <small class="form-text text-muted">Format: PDF, DOCX, ZIP, XLS (Maksimal 4 MB).</small>
                                </div>

                                <div class="alert alert-light border text-muted p-2 mt-3" style="font-size: 0.8rem;">
                                    <i class="fas fa-info-circle text-info mr-1"></i>
                                    <strong>Ketentuan Berkas:</strong> File yang diunggah akan otomatis divalidasi batas ukuran maksimal 4 MB dan disimpan di direktori <code>uploads/</code> secara terisolasi.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi: Simpan (Submit Form) dan Batal (Kembali ke Index) -->
                    <div class="card-footer py-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Data Mahasiswa
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
// 4. Memanggil komponen layout penutup bawah (Footer & Scripts)
require_once 'layouts/footer.php';
?>
