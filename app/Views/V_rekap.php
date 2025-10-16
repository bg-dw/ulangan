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
        background: #fff;
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
        background-color: #6D94C5;
        color: white;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .table-container tr:nth-child(even) {
        background-color: #fafafa;
    }

    .table-container tr:hover {
        background-color: #f1f1f1;
    }

    table td {
        font-weight: bold;
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
<?php
if (isset($daftar)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Detail Ujian</h4>
                </div>
                <?php if (isset($daftar)): ?>
                    <div class="card-body">
                        <center>
                            <h6><?= $judul . " - " . $mapel . " [ " . date('l, j F Y', strtotime($tgl)) . " ]" ?>
                            </h6>
                        </center>
                        <div class="row mb-3">
                            <div class="col-md-6 position-relative">
                                <button id="btnPilihUjian" class="btn btn-primary w-100 col-md-2">
                                    <i class="fas fa-list"></i> Pilih Ujian
                                </button>

                                <div id="floatingSelect"
                                    class="border rounded shadow-sm bg-white position-absolute w-100 d-none"
                                    style="z-index: 1050; max-height: 250px; overflow-y: auto;">
                                    <?php foreach ($daftar as $u): ?>
                                        <div class="dropdown-item p-2 border-bottom pilih-ujian-item"
                                            data-id="<?= $u['id_ujian_detail'] ?>" style="cursor:pointer;">
                                            <strong><?= esc($u['judul']) ?></strong> - <?= esc($u['mapel']) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="table-container" class="table-scroll-wrapper"
                            style="overflow: auto; max-height: 70vh; border: 1px solid;" id="tableContainer">
                            <table id="tabelHasil" border="1" width="100%" cellspacing="0" cellpadding="4">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center" style="width: 5%;">No.</th>
                                        <th rowspan="2" class="text-center" style="width: 20%;">Nama</th>
                                        <th rowspan="2" class="text-center" style="width: 10%;">Status</th>
                                        <th rowspan="2" class="text-center" style="width: 10%;">Log</th>
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
        const idDetail = <?= $id_ujian_detail ?>;

        const dataUrl = "<?= base_url('/' . bin2hex('get-ujian')) ?>";
        const initUrl = "<?= base_url('/' . bin2hex('rekap-init')) ?>";
        const updateUrl = "<?= base_url('/' . bin2hex('rekap-update-nilai')) ?>";

        let sudahInitNilai = false;

        $(document).ready(function () {
            // tampilkan default ujian saat halaman pertama kali dibuka
            get_data($("#selectUjian").val());
        });

        $(document).ready(function () {
            const dropdown = $("#floatingSelect");

            // Toggle tampil/sembunyi daftar ujian
            $("#btnPilihUjian").on("click", function (e) {
                e.stopPropagation();
                dropdown.toggleClass("d-none");
            });

            // Klik di luar → sembunyikan dropdown
            $(document).on("click", function () {
                dropdown.addClass("d-none");
            });

            // Klik item ujian
            $(".pilih-ujian-item").on("click", function (e) {
                e.stopPropagation();
                const id = $(this).data("id");
                dropdown.addClass("d-none");

                // Redirect ke hasil ujian yang dipilih
                window.location.replace("<?= base_url('/' . bin2hex('rekap-get-hasil')) ?>/" + id);
            });

            // Inisialisasi default data
            get_data(<?= $id_ujian_detail ?>);
        });


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