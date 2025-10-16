<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Hasil Ujian</title>
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/app.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/style.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/components.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/custom.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/shadow__btn.css">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url() ?>/public/assets/img/favicon.ico">
</head>
<style>
    @media print {

        /* sembunyikan elemen tertentu saat print */
        .hide-on-print-share {
            display: none !important;
        }
    }

    .card {
        padding: 10px;
        border-radius: 8px;
    }
</style>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="card card-info" id="cardHasil">
                    <div class="card-header">
                        <h4>Hasil Ujian</h4>
                        <div>
                            <button class="btn btn-success hide-on-print-share" onclick="window.print()">Print</button>
                            <button class="btn btn-info hide-on-print-share" id="btnDownload">Download</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <center>
                            <select name="id_detail" class="form-control mb-3 no-print hide-on-print-share"
                                id="sel-id-detail">
                                <?php foreach ($ujian as $row): ?>
                                    <option value="<?= $row['id_ujian_detail'] ?>"
                                        <?= ($ujian_terpilih == $row['id_ujian_detail']) ? 'selected' : null; ?>>
                                        <?= $row['judul'] . " - " . $row['mapel'] . " [ " . date('d F Y', strtotime($row['tgl'])) . " ]" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </center>
                        <table class="mb-2">
                            <tr>
                                <td style="width:80px;">Nama</td>
                                <td>:
                                    <b><?= $nama ?></b>
                                </td>
                            </tr>
                            <tr>
                                <td>Mapel</td>
                                <td>:
                                    <b><?= $mapel ?></b>
                                </td>
                            </tr>
                        </table>
                        <input type="hidden" value="<?= $id_siswa ?>" id="id-siswa">
                        <input type="hidden" value="<?= $nama ?>" id="inp-nama">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Soal</th>
                                        <th class="text-center">Kunci Jawaban</th>
                                        <th class="text-center">Jawaban</th>
                                        <th class="text-center">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelHasil">
                                    <!-- Isi tabel akan di-render dengan JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- General JS Scripts -->
    <script src="<?= base_url() ?>/public/assets/js/jquery-3.7.0.js"></script>
    <!-- JS Libraies -->
    <script src="<?= base_url() ?>/public/assets/js/app.min.js"></script>
    <!-- tamplate JS File -->
    <script src="<?= base_url() ?>/public/assets/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="<?= base_url() ?>/public/assets/js/custom.js"></script>

    <script src="<?= base_url('assets/js/html2canvas.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jspdf.umd.min.js') ?>"></script>
    <script>
        const id_siswa = document.getElementById('id-siswa');
        const nama_siswa = document.getElementById('inp-nama');

        // data JSON kamu (contoh dari PHP)
        const dataPHP = <?= json_encode($nilai); ?>; // misal data dikirim dari PHP
        const nilai = JSON.parse(dataPHP.nilai);
        const jawaban = JSON.parse(dataPHP.jawaban);
        const data = JSON.parse(dataPHP.data);

        // fungsi untuk render tabel
        function renderTable() {
            const tbody = document.getElementById("tabelHasil");
            tbody.innerHTML = ""; // kosongkan dulu

            let nomor = 1;
            let indexPG = 0, indexIsian = 0, indexUraian = 0;

            data.jenis_soal.forEach((jenis, idxJenis) => {
                const jumlah = parseInt(data.jumlah_soal[idxJenis]);
                for (let i = 0; i < jumlah; i++) {
                    const soalText = data.pertanyaan[idxJenis][i];
                    const kunci = data.kunci[idxJenis][i];
                    const jawab = jawaban[nomor]?.isi ?? "-";
                    const skor = nilai[nomor]?.nilai ?? 0;

                    const row = `
            <tr>
              <td>${nomor}.</td>
              <td>${soalText}</td>
              <td class="text-center">${kunci.toUpperCase()}</td>
              <td class="text-center">${jawab}</td>
              <td class="text-center">${skor}</td>
            </tr>`;
                    tbody.insertAdjacentHTML("beforeend", row);
                    nomor++;
                }
            });
        }

        // jalankan setelah DOM siap
        document.addEventListener("DOMContentLoaded", function () {
            renderTable();
        });

        $('#sel-id-detail').on('change', function () {
            const id = $(this).val();
            const id_s = id_siswa.value;
            const nama = nama_siswa.value;
            if (id) {
                window.location.href = "<?= base_url('/' . bin2hex('rapor-tampil-pilihan')) ?>/" + id_s + "/" + nama + "/" + id;
            }
        });
        document.getElementById("btnDownload").addEventListener("click", async function () {
            const cardHasil = document.getElementById("cardHasil");

            // sembunyikan elemen yang tidak ingin dicetak
            const hideElements = cardHasil.querySelectorAll(".hide-on-print-share");
            hideElements.forEach(el => el.style.display = "none");

            // capture cardHasil
            const canvas = await html2canvas(cardHasil, {
                scale: 2,
                scrollY: -window.scrollY,
                useCORS: true,
                backgroundColor: "#ffffff"
            });

            hideElements.forEach(el => el.style.display = ""); // kembalikan elemen

            const imgData = canvas.toDataURL("image/png");

            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF("p", "mm", "a4");
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            // hitung ukuran agar muat halaman
            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pageWidth - 20; // margin 10mm kiri/kanan
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

            pdf.addImage(imgData, "PNG", 10, 10, pdfWidth, pdfHeight);
            pdf.save("hasil_ujian.pdf");
        });

    </script>
</body>

</html>