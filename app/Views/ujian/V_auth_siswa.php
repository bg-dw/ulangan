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
    <link rel="stylesheet" href="<?= base_url() ?>/public/assets/bundles/select2/dist/css/select2.min.css">
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
                                <form method="POST" action="<?= base_url('/' . bin2hex('auth')) ?>"
                                    class="needs-validation" novalidate=""><?= csrf_field(); ?>
                                    <input type="hidden" value="<?= $id_ujian ?>" name="id-ujian" id="inp-id" required>
                                    <input type="hidden" value="<?= $id_detail ?>" name="id-detail" id="inp-id-detail"
                                        required>
                                    <div class="form-group" id="list-siswa-wrapper">
                                        <label>Daftar Siswa</label>
                                        <div id="list-siswa" class="d-flex flex-wrap">
                                            <select class="form-control select2" name="id-siswa" id="sel-siswa"
                                                required>
                                                <option value="">==Pilih Siswa==</option>
                                                <?php foreach ($siswa as $row): ?>
                                                    <option value="<?= $row['id_siswa'] ?>"><?= $row['nama_siswa'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
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
    <script src="<?= base_url() ?>/public/assets/bundles/select2/dist/js/select2.full.min.js"></script>
    <!-- tamplate JS File -->
    <script src="<?= base_url() ?>/public/assets/js/scripts.js"></script>
    <!-- Custom JS File -->
    <script src="<?= base_url() ?>/public/assets/js/custom.js"></script>
    <?= $this->include('_layout/alert') ?>


    <script>
        $("#sel-siswa").on("change", function () {
            let idSiswa = $(this).val();
            let idUjian = $("#inp-id").val();
            let idDetail = $("#inp-id-detail").val();
            $.ajax({
                url: "<?= base_url('/' . bin2hex('ujian-cek')) ?>",
                method: "POST",
                data: {
                    id_siswa: idSiswa,
                    id_ujian: idUjian,
                    id_detail: idDetail,
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                dataType: "json",
                success: function (res) {
                    if (res.success) {
                        window.location.href = res.redirect_url;
                    } else {
                        iziToast.error({
                            title: "Error",
                            message: res.message,
                            position: "topCenter"
                        });
                    }
                },
                error: function (xhr) {
                    console.log("XHR:", xhr.responseText);
                    iziToast.error({
                        title: "Error",
                        message: "Server error, tidak bisa login.",
                        position: "topCenter"
                    });
                }
            });
        });
    </script>
</body>

</html>