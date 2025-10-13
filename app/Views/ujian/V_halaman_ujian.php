<?= $this->extend('Main_siswa') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/css/nav-ujian.css">
<!-- Toast -->
<div id="toastContainer" class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1080;">
    <div id="toastJawaban" class="toast bg-info text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            Jawaban tersimpan!
        </div>
    </div>
</div>
<div class="container my-3">
    <?php
    // dd(json_encode($soal));
    if (isset($soal)): ?>
        <div class="row">
            <!-- Kolom Navigasi -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm card-primary">
                    <div class="card-body">
                        <center>
                            <h6 class="fw-bold"><?= session()->get('nama') ?></h6>
                        </center>
                        <div class="progress my-2" style="height:22px;">
                            <div class="progress-bar bg-success" id="progressBar" style="width:0%">0%</div>
                        </div>
                        <p class="small text-center">
                            <span id="progressCount">0 / <?= count($soal) ?> Soal</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kolom Soal -->
            <div class="col-12 col-md-8 mb-3">
                <div class="card shadow-sm  card-primary">
                    <div class="card-body">
                        <input type="hidden" value="<?= $id_detail ?>" name="id-detail" id="inp-id-detail">
                        <input type="hidden" value="<?= $id_siswa ?>" name="id-siswa" id="inp-id-siswa">
                        <?php if (empty($soal)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> Soal tidak ditemukan atau belum tersedia.
                            </div>
                        <?php else: ?>
                            <?php foreach ($soal as $i => $item): ?>
                                <?= view('ujian/_soal_item', array('item' => $item, 'i' => $i)) ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-between">
                    <button class="btn btn-secondary" id="prevBtn">Sebelumnya</button>
                    <button class="btn btn-primary" id="nextBtn">Selanjutnya</button>
                    <button class="btn btn-success d-none" id="btnSelesai">
                        <i class="fas fa-check-circle"></i> Selesai Ujian
                    </button>
                </div>
            </div>
        </div>
    <?php else: ?>
        <h3 class="form-control">Data Kosong</h3>
    <?php endif; ?>
</div>
<!-- Floating Button -->
<button id="btnToggleSoal" class="btn btn-primary floating-btn"
    style="position:fixed; bottom:20px; left:50%; transform:translateX(-50%);width:60px; height:60px; border-radius:50%; z-index:2000; font-size:24px; display:flex; justify-content:center; align-items:center;">
    ☰
</button>

<!-- Panel Nomor Soal -->
<div id="panelSoal">
    <div id="soalNavContainer" class="panel">
        <?php foreach ($soal as $i => $s):
            $status = $statusSoal[$s['id_soal']] ?? 'none';
            $warna = $status === 'jawab' ? 'btn-success' : ($status === 'ragu' ? 'btn-warning' : 'btn-outline-secondary');
            ?>
            <button class="btn <?= $warna ?> soal-nav px-3" data-target="<?= $i ?>" data-idsoal="<?= $s['id_soal'] ?>">
                <?= $i + 1 ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>
<?php
if (isset($soal)): ?>
    <!-- Variabel JS -->
    <script>
        //masih ada bug, dimana tidak bisa checked jawaban langsung dengan memberi attribut "checked" pada input radio. dan untuk saat ini menggunakan javascript
        // Konversi jawaban siswa ke format JS
        const idSiswa = "<?= esc($id_siswa) ?>";
        const idDetail = "<?= esc($id_detail) ?>";
        const simpanUrl = "<?= base_url('/' . bin2hex('ujian-simpan-jawaban')) ?>";
        const finishUrl = "<?= base_url('/' . bin2hex('ujian-selesai')) ?>";
        const statusUrl = "<?= base_url('/' . bin2hex('ujian-inactive')) ?>";
        const logoutUrl = "<?= base_url('/') ?>";
    </script>
<?php endif; ?>
<!-- JS Floating Button & Panel -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnToggle = document.getElementById('btnToggleSoal');
        const panel = document.getElementById('panelSoal');

        // Toggle panel
        btnToggle.addEventListener('click', () => {
            panel.classList.toggle('show');

            // Ganti icon
            if (panel.classList.contains('show')) {
                btnToggle.innerHTML = '&times;'; // x
            } else {
                btnToggle.innerHTML = '&#9776;'; // hamburger
            }
        });

        // Klik tombol nomor soal
        document.querySelectorAll('#soalNavContainer .soal-nav').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetIndex = +btn.dataset.target;
                if (typeof showSoal === "function") showSoal(targetIndex);
                panel.classList.remove('show');
                btnToggle.innerHTML = '&#9776;'; // kembalikan hamburger
            });
        });
    });
    (function () {
        // Tambahkan history baru setiap kali halaman dimuat
        history.pushState(null, "", location.href);

        window.addEventListener('popstate', function (event) {
            history.pushState(null, "", location.href); // dorong kembali state
            alert("Anda tidak dapat kembali selama ujian berlangsung!");
        });

        // Optional: cegah Alt+← dan Backspace
        document.addEventListener('keydown', function (e) {
            if ((e.key === 'Backspace' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) ||
                (e.altKey && e.key === 'ArrowLeft')) {
                e.preventDefault();
            }
        });
    })();
</script>
<script>
    window.jawabanSiswa = <?= json_encode($jawaban_siswa) ?>;
</script>
<script src="<?= base_url('assets/js/ujian.js') ?>"></script>
<?= $this->endSection() ?>