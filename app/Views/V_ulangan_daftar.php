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
        document.addEventListener("DOMContentLoaded", function () {
            update_log();//saat pertama kali halaman dibuka
        });

        const idDetail = <?= $ujian[0]['id_ujian_detail'] ?>;
        function get_data() {
            $.ajax({
                url: "<?= base_url('/' . bin2hex('get-ujian')) ?>",
                method: 'POST',
                data: { id_detail: idDetail },
                success: function (data) {
                    renderTable(data);
                },
                error: function (xhr, status, error) {
                    console.error('Gagal mengambil data:', error);
                }
            });
        }

        let last_update = 0;

        function update_log() {
            $.ajax({
                url: "<?= base_url('/' . bin2hex('cek-last-update')) ?>",
                method: 'GET',
                success: function (res) {
                    let obj = JSON.parse(res);
                    if (obj) {
                        if (last_update != obj.updated_at) {
                            get_data();
                            last_update = obj.updated_at;
                        }
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        }
        document.addEventListener("DOMContentLoaded", function () {
            setInterval(update_log, 5000);//cek setiap 5 detik
        });

        function renderTable(data) {
            if (!data || data.length === 0) return;

            // Ambil jawaban pertama untuk hitung total soal
            const firstJawaban = JSON.parse(data[0].jawaban);
            const totalSoal = Object.keys(firstJawaban).length;

            // Buat header nomor soal
            let headerSoal = '';
            for (let i = 1; i <= totalSoal; i++) {
                headerSoal += `<th class="text-center">${i}</th>`;
            }
            $('#headerSoal').html(headerSoal);
            $('#judulSoal').attr('colspan', totalSoal);

            // Isi body tabel
            let rows = '';
            data.forEach((siswa, index) => {
                const jawaban = JSON.parse(siswa.jawaban);
                let row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>${siswa.nama_siswa}</td>
                    <td>${siswa.status}</td>
                    <td>${formatUnixTime(siswa.log)}</td>
            `;

                for (let i = 1; i <= totalSoal; i++) {
                    const isi = jawaban[i]?.isi ?? '-';
                    const ragu = jawaban[i]?.ragu == 1;
                    const bg = ragu ? ' style="background-color:yellow"' : '';
                    row += `<td${bg}>${isi}</td>`;
                }

                row += '</tr>';
                rows += row;
            });

            $('#bodyHasil').html(rows);
        }

        function formatUnixTime(unix) {
            // Cek dulu apakah ada nilai dan bisa diubah jadi angka
            if (!unix || isNaN(unix)) return "";

            const date = new Date(parseInt(unix) * 1000);
            if (isNaN(date.getTime())) return "";

            return date.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
    </script>
<?php else: ?>
    <div class="alert alert-warning mt-xl-5">
        <center>
            <h1>Data Tidak ditemukan</h1>
        </center>
    </div>
<?php endif ?>
<?= $this->endSection() ?>