<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>RAPOR</title>
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/app.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/style.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/components.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/custom.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/shadow__btn.css">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url() ?>/public/assets/img/favicon.ico">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/bundles/izitoast/css/iziToast.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/bundles/bootstrap-daterangepicker/daterangepicker.css">
</head>

<body>
    <div class="loader"></div>
    <div id="app">
        <section class="section" style="overflow: hidden;">
            <div class="container">
                <div class="row flogin" style="margin-top: 50px;">
                    <div
                        class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Cek Data</h4>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="" class="needs-validation" novalidate="">
                                    <?= csrf_field(); ?>
                                    <div class="form-group">
                                        <label for="inp-nama">Nama Depan</label>
                                        <input type="text" class="form-control" name="user" tabindex="1"
                                            placeholder="Midas" id="inp-nama">
                                        <div class="invalid-feedback">
                                            Isi Nama Depan
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="d-block">
                                            <label for="tgl" class="control-label">Tanggal Lahir</label>
                                        </div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <select id="tgl" class="form-control" style="width: 80px;">
                                            </select>
                                            <select id="bln" class="form-control" style="width: 100px;">
                                            </select>
                                            <select id="thn" class="form-control" style="width: 100px;">
                                            </select>
                                        </div>

                                        <!-- input hidden untuk hasil gabungan -->
                                        <input type="hidden" id="tanggalGabung" name="tanggal">

                                        <div class="invalid-feedback">
                                            Isi Tgl Tahir
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-primary btn-lg btn-block" tabindex="4"
                                            id="btnKirimTanggal">
                                            Proses
                                        </button>
                                    </div>
                                </form>
                            </div>
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
    <!-- Page Specific JS File -->
    <script src="<?= base_url() ?>/public/assets/bundles/izitoast/js/iziToast.min.js"></script>
    <script src="<?= base_url() ?>/public/assets/bundles/bootstrap-daterangepicker/daterangepicker.js"></script>
    <!-- tamplate JS File -->
    <script src="<?= base_url() ?>/public/assets/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="<?= base_url() ?>/public/assets/js/custom.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tgl = document.getElementById('tgl');
            const bln = document.getElementById('bln');
            const thn = document.getElementById('thn');
            const gabung = document.getElementById('tanggalGabung');
            const nama = document.getElementById('inp-nama');

            // === 1️⃣ Isi dropdown tanggal ===
            for (let i = 1; i <= 31; i++) {
                const opt = document.createElement('option');
                opt.value = i.toString().padStart(2, '0');
                opt.textContent = i.toString().padStart(2, '0');
                tgl.appendChild(opt);
            }

            // === 2️⃣ Isi dropdown bulan ===
            const bulanList = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            bulanList.forEach((b, i) => {
                const opt = document.createElement('option');
                opt.value = (i + 1).toString().padStart(2, '0');
                opt.textContent = b;
                bln.appendChild(opt);
            });

            // === 3️⃣ Isi dropdown tahun ===
            const tahunSekarang = new Date().getFullYear();
            for (let y = tahunSekarang; y >= 1990; y--) {
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                thn.appendChild(opt);
            }

            // === 4️⃣ Update gabungan saat berubah ===
            function updateTanggal() {
                const dd = tgl.value;
                const mm = bln.value;
                const yyyy = thn.value;
                if (dd && mm && yyyy) {
                    gabung.value = `${yyyy}/${mm}/${dd}`;
                    console.log("Tanggal dipilih:", gabung.value);
                }
            }

            [tgl, bln, thn].forEach(sel => sel.addEventListener('change', updateTanggal));

            // 4️⃣ Tombol kirim data via AJAX
            $('#btnKirimTanggal').on('click', function () {
                const dd = tgl.value;
                const mm = bln.value;
                const yyyy = thn.value;
                const nm = nama.value;

                if (!dd || !mm || !yyyy) {
                    alert("Pilih tanggal, bulan, dan tahun terlebih dahulu!");
                    return;
                }

                const tanggal = `${dd}-${mm}-${yyyy}`;

                $.ajax({
                    url: "<?= base_url('/' . bin2hex('rapor-cek')) ?>",
                    method: "POST",
                    data: {
                        nama: nm,
                        tgl: tanggal
                    },
                    dataType: "json",
                    success: function (res) {
                        if (res.status) {
                            iziToast.success({ title: 'Sukses', message: res.message, position: 'topCenter', timeout: 2000 });
                            setTimeout(() => {
                                window.location.href = "<?= base_url('/' . bin2hex('rapor-tampil')) ?>/" + res.id_siswa + "/" + res.nama;
                            }, 1500);
                        } else {
                            iziToast.error({ title: 'Gagal', message: res.message, position: 'topCenter', timeout: 3000 });
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);

                        iziToast.error({ title: 'Kesalahan Ajax', message: res.message, position: 'topCenter', timeout: 3000 });
                    }
                });
            });
        });
    </script>


    <?= $this->include('_layout/alert') ?>
</body>

</html>