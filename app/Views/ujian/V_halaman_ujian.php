<?= $this->extend('Main_siswa') ?>
<?= $this->section('content') ?>
<!-- <link rel="stylesheet" href="<?= base_url() ?>/assets/css/nav-ujian.css"> -->
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
    // dd(json_encode($jawaban_siswa));
    if (isset($soal)): ?>
        <div class="row">

            <!-- Kolom Navigasi -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <center>
                            <h6 class="fw-bold"><?= session()->get('nama') ?></h6>
                        </center>
                        <div class="progress my-2" style="height:22px;">
                            <div class="progress-bar bg-success" id="progressBar" style="width:0%">0%</div>
                        </div>
                        <p class="small text-center mb-3">
                            <span id="progressCount">0 / <?= count($soal) ?> Soal</span>
                        </p>
                        <div id="soalNavContainer" class="d-flex flex-wrap justify-content-start">
                            <?php foreach ($soal as $i => $s): ?>
                                <?php
                                $status = $statusSoal[$s['id_soal']] ?? 'none';
                                $warna = $status === 'jawab' ? 'btn-success' :
                                    ($status === 'ragu' ? 'btn-warning' : 'btn-outline-secondary');
                                ?>
                                <button class="btn <?= $warna ?> m-1 soal-nav px-3" id="nav-<?= $i ?>" data-target="<?= $i ?>"
                                    data-idsoal="<?= $s['id_soal'] ?>">
                                    <?= $i + 1 ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Kolom Soal -->
            <div class="col-12 col-md-8 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
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

<?php
if (isset($soal)): ?>
    <!-- Variabel JS -->
    <script>
        //masih ada bug, dimana tidak bisa checked jawaban langsung dengan memberi attribut "checked" pada input radio. dan untuk saat ini menggunakan javascript
        // Konversi jawaban siswa ke format JS
        const idSiswa = "<?= esc($id_siswa) ?>";
        const idDetail = "<?= esc($id_detail) ?>";
        const baseUrl = "<?= base_url('/' . bin2hex('ujian-simpan-jawaban')) ?>";
        const finishUrl = "<?= base_url('ulangan/selesai') ?>";


    </script>
<?php endif; ?>
<script>
    window.jawabanSiswa = <?= json_encode($jawaban_siswa) ?>;
</script>
<script src="<?= base_url('assets/js/ujian.js') ?>"></script>
<?= $this->endSection() ?>