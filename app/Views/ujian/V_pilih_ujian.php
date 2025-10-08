<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Ujian</title>
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/app.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/style.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/components.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/custom.css">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/shadow__btn.css">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url() ?>/public/assets/img/favicon.ico">
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/bundles/izitoast/css/iziToast.min.css">
</head>

<body class="bg-info">
    <div class="loader"></div>
    <div id="app">
        <section class="section">
            <div class="container">
                <div class="row" style="margin-top: 20%;">
                    <div class="col-md-12 col-xl-6 offset-xl-3">
                        <div class="text-center mb-4 text-white">
                            <h1>Ujian Online</h1>
                            <h6>Selamat Mengerjakan</h6>
                        </div>
                        <div class="card card-primary">
                            <div class="card-body">
                                <form method="POST" action="<?= base_url('/' . bin2hex('pilih-siswa')) ?>"
                                    class="needs-validation" novalidate="" id="form-ujian"><?= csrf_field(); ?>
                                    <input type="hidden" name="id-ujian" value="<?= $ujian[0]['id_ujian'] ?>">
                                    <div class="form-group">
                                        <label for="user">Daftar Ujian</label>
                                        <select class="form-control" name="id-ujian-detail" id="pilih-ujian" required>
                                            <option value="">== Pilih Ujian ==</option>
                                            <?php foreach ($ujian as $row): ?>
                                                <option value="<?= $row['id_ujian_detail'] ?>"><?= $row['judul'] ?> -
                                                    <?= $row['mapel'] ?>
                                                    [<?= date("d F Y", strtotime($row['tgl'])) ?>]
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
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
    <!-- tamplate JS File -->
    <script src="<?= base_url() ?>/public/assets/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="<?= base_url() ?>/public/assets/js/custom.js"></script>
    <?= $this->include('_layout/alert') ?>


    <script>
        $("#pilih-ujian").on("change", function () {
            $("#form-ujian").submit();
        }); 
    </script>
</body>

</html>