<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<?php
// dd($ujian);
?>
<style>
    .table-container {
        width: 100%;
        overflow-x: auto;
        /* Aktifkan scroll horizontal */
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .table-container table {
        border-collapse: collapse;
        width: 100%;
        min-width: 800px;
        /* Supaya tetap bisa di-scroll kalau kolom banyak */
    }

    .table-container th,
    .table-container td {
        white-space: nowrap;
        /* Supaya teks tidak turun ke baris baru */
        padding: 8px 12px;
    }

    .table-container th {
        background-color: #f4f4f4;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .table-container tr:nth-child(even) {
        background-color: #fafafa;
    }

    .table-container tr:hover {
        background-color: #f1f1f1;
    }
</style>
<div class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1055">
    <div id="toastStatus" class="toast align-items-center text-white bg-success border-0" role="alert"
        aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">Nilai berhasil disimpan</div>
        </div>
    </div>
</div>
<?php if (isset($ujian[0])): ?>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Detail Ujian</h4>
                </div>
                <?php if (isset($ujian[0])): ?>
                    <div class="card-body">
                        <center>
                            <h6><?= $ujian[0]['judul'] . " - " . $ujian[0]['mapel'] . " [ " . date('l, j F Y', strtotime($ujian[0]['tgl'])) . " ]" ?>
                            </h6>
                        </center>
                        <div class="table-container">
                            <table id="tabelHasil" border="1" width="100%" cellspacing="0" cellpadding="4">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center">No.</th>
                                        <th rowspan="2" class="text-center">Nama</th>
                                        <th rowspan="2" class="text-center">Status</th>
                                        <th rowspan="2" class="text-center">Log</th>
                                        <th id="judulSoal" colspan="0" class="text-center">Nomor Soal</th>
                                    </tr>
                                    <tr id="headerSoal"></tr>
                                </thead>
                                <tbody id="bodyHasil"></tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <center>
                        <h3>Data Tidak Ditemukan.</h3>
                    </center>
                <?php endif ?>
            </div>
        </div>
    </div>
    <script>
        const kunci = <?= json_encode($kunci) ?>;
        const bobot = <?= json_encode($bobot) ?>;
        const nilai_max = <?= json_encode($max) ?>;
        const jenis_soal = <?= json_encode($jenis) ?>;
        const nilai_tersimpan = <?= json_encode($nilai_tersimpan) ?>;

        const idDetail = <?= $ujian[0]['id_ujian_detail'] ?>;
        const dataUrl = "<?= base_url('/' . bin2hex('get-ujian')) ?>";
        const initUrl = "<?= base_url('/' . bin2hex('rekap-init')) ?>";
        const updateUrl = "<?= base_url('/' . bin2hex('rekap-update-nilai')) ?>";
    </script>
    <script src="<?= base_url('assets/js/rekap.js') ?>"></script>
<?php else: ?>
    <div class="alert alert-warning mt-xl-5">
        <center>
            <h1>Data Tidak ditemukan</h1>
        </center>
    </div>
<?php endif ?>

<?= $this->endSection() ?>