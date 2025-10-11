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
        const kunci = <?= json_encode($kunci) ?>;
        const bobot = <?= json_encode($bobot) ?>;
        const nilai_max = <?= json_encode($max) ?>;
        const jenis_soal = <?= json_encode($jenis) ?>;
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

        function renderTable(data) {
            if (!data || data.length === 0) return;

            let firstJawaban = {};
            try { firstJawaban = JSON.parse(data[0].jawaban); } catch (e) { return; }

            const totalSoal = Object.keys(firstJawaban).length;

            // Header nomor soal
            let headerSoal = '';
            for (let i = 1; i <= totalSoal; i++) {
                headerSoal += `<th class="text-center">${i}</th>`;
            }
            headerSoal += `<th class="text-center">Total</th>`;
            $('#headerSoal').html(headerSoal);
            $('#judulSoal').attr('colspan', totalSoal + 1);

            let rows = '';

            data.forEach((siswa, index) => {
                let row = `
        <tr data-index="${index}">
            <td>${index + 1}</td>
            <td>${siswa.nama_siswa}</td>
            <td>${siswa.status}</td>
            <td>${formatUnixTime(siswa.log)}</td>
        `;

                let jawaban = {};
                try { jawaban = JSON.parse(siswa.jawaban); } catch (e) { jawaban = {}; }

                // Counter agar posisi kunci per jenis benar
                let counterPG = 0;
                let counterIsian = 0;
                let counterUraian = 0;

                let totalSkor = 0;

                for (let i = 1; i <= totalSoal; i++) {
                    const isi = (jawaban[i]?.isi ?? '-').trim();
                    const ragu = jawaban[i]?.ragu == 1;
                    const jenis = (jawaban[i]?.jenis ?? 'pilihan_ganda').toLowerCase();
                    // Cari indeks jenis soal dalam array jenis_soal
                    let indexBobot = jenis_soal.indexOf(jenis);
                    if (indexBobot === -1) indexBobot = 0; // default ke PG bila tidak ditemukan

                    // Ambil bobot dengan pencocokan longgar
                    const bobotJenis = bobot[indexBobot]
                        || bobot[jenis.replace(/[^a-z]/g, '')]
                        || 1;


                    // Tentukan grup dan ambil kunci sesuai urutan per jenis
                    let grupIndex = 0;
                    let idxDalamGrup = 0;
                    if (jenis.includes('isian')) {
                        grupIndex = 1;
                        idxDalamGrup = counterIsian++;
                    } else if (jenis.includes('uraian')) {
                        grupIndex = 2;
                        idxDalamGrup = counterUraian++;
                    } else {
                        grupIndex = 0;
                        idxDalamGrup = counterPG++;
                    }

                    const kunciJawaban = (kunci[grupIndex]?.[idxDalamGrup] ?? '-').trim();

                    // Penilaian otomatis
                    let warna = '';
                    let nilai = 0;
                    if (isi === '-' || isi === '') {
                        warna = 'background-color:#eee';
                    } else if (kunciJawaban === '-' || kunciJawaban === '') {
                        warna = ''; // tidak otomatis dinilai
                    } else if (isi.toLowerCase() === kunciJawaban.toLowerCase()) {
                        warna = 'background-color:#b2f7b2';
                        nilai = bobotJenis;
                    } else {
                        warna = 'background-color:#f7b2b2';
                    }

                    const borderRagu = ragu ? 'border:2px solid orange;' : '';

                    // Ukuran input disesuaikan jenis soal
                    let inputWidth = '50px';
                    if (jenis === 'uraian') inputWidth = '70px';
                    else if (jenis === 'isian') inputWidth = '60px';

                    // Tampilkan kunci untuk semua jenis soal
                    const tampilKunci = `
                <div style="
                    margin-top:3px;
                    font-size:12px;
                    background-color:#007bff;
                    color:white;
                    padding:1px 4px;
                    border-radius:3px;
                    display:inline-block;
                ">
                    ${kunciJawaban || '-'}
                </div>
            `;

                    row += `
                <td style="${warna};${borderRagu}; text-align:center; vertical-align:middle;">
                    <div style="display:flex; flex-direction:column; align-items:center; gap:3px;">
                        <div style="font-size:13px;">${isi || '-'}</div>
                        ${tampilKunci}
                        <input type="number" step="1" min="0" 
                            max="${bobot[indexBobot]}" 
                            value="${nilai}" 
                            class="form-control form-control-sm text-center skor-input" 
                            data-jenis="${jenis}" 
                            style="width:${inputWidth}; padding:2px 4px; font-size:13px;">

                    </div>
                </td>
            `;
                    totalSkor += parseFloat(nilai);
                }

                // Kolom total skor (menampilkan (total/nilai_max)*100)
                const persentase = ((totalSkor / nilai_max) * 100).toFixed(1);
                row += `
                    <td class="text-center fw-bold">
                        <div style="display:flex; flex-direction:column; align-items:center; gap:2px;">
                            <div class="formula-text" style="font-size:13px; color:#555;">
                                (${totalSkor.toFixed(1)} / ${nilai_max}) × 100
                            </div>

                            <input type="number" 
                                step="0.1" 
                                min="0" 
                                max="100"
                                value="${persentase}"
                                class="form-control form-control-sm text-center total-skor"
                                style="width:80px; font-weight:bold; background-color:#f8f9fa;"
                                readonly>
                        </div>
                    </td>
                    </tr>`;
                rows += row;
            });

            $('#bodyHasil').html(rows);


            // Hentikan handler lama dan pasang satu handler yang bersih
            $('#bodyHasil').on('input', '.skor-input', function () {
                const tr = $(this).closest('tr');
                let total = 0;

                // Hitung ulang total skor dari semua input soal
                tr.find('.skor-input').each(function () {
                    total += parseFloat($(this).val()) || 0;
                });

                // Hitung persentase (total / nilai_max * 100)
                const persen = ((total / nilai_max) * 100).toFixed(1);

                // Update input total skor
                const tdTotal = tr.find('td').last(); // ambil kolom terakhir (total)
                tdTotal.find('.total-skor').val(persen); // update nilai input
                tdTotal.find('.formula-text').text(`(${total.toFixed(1)} / ${nilai_max}) × 100`); // update teks rumus di atas
            });


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

        document.addEventListener("DOMContentLoaded", function () {
            get_data();
        });
    </script>
<?php else: ?>
    <div class="alert alert-warning mt-xl-5">
        <center>
            <h1>Data Tidak ditemukan</h1>
        </center>
    </div>
<?php endif ?>
<?= $this->endSection() ?>