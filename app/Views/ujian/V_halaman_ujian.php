<?= $this->extend('Main_siswa') ?>
<?= $this->section('content') ?>

<div class="container my-3">
    <?php if (isset($soal)): ?>
        <div class="row">
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
                </div>
            </div>

            <!-- Kolom Navigasi -->
            <div class="col-12 col-md-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold">Nomor Soal</h6>
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
                </div><button class="btn btn-success w-100 d-none" id="btnSelesai">
                    <i class="fas fa-check-circle"></i> Selesai Ujian
                </button>

            </div>
        </div>
    <?php else: ?>
        <h3 class="form-control">Data Kosong</h3>
    <?php endif; ?>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1050">
    <div id="toastJawaban" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive">
        <div class="d-flex">
            <div class="toast-body">Jawaban berhasil disimpan!</div>
        </div>
    </div>
</div>
<?php if (isset($soal)): ?>
    <!-- Variabel JS -->
    <script>
        //masih ada bug, dimana tidak bisa checked jawaban langsung dengan memberi attribut "checked" pada input radio. dan untuk saat ini menggunakan javascript
        // Konversi jawaban siswa ke format JS
        const idSiswa = "<?= esc($id_siswa) ?>";
        const idDetail = "<?= esc($id_detail) ?>";
        const baseUrl = "<?= base_url('/' . bin2hex('ujian-simpan-jawaban')) ?>";
        const finishUrl = "<?= base_url('ulangan/selesai') ?>";


    </script>
    <script>
        window.jawabanSiswa = <?= json_encode($jawaban_siswa[0]) ?>;
    </script>
<?php endif; ?>
<script src="<?= base_url('assets/js/ujian.js') ?>"></script>
<?= $this->endSection() ?>