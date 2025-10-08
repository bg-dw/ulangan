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

<!-- <audio id="notifSound" src="<?= base_url('assets/sound/notif.mp3') ?>" preload="auto"></audio> -->

<body class="bg-info">
    <div class="loader"></div>
    <div id="app">
        <section class="section">
            <div class="container">
                <div class="row" style="margin-top: 20%;">
                    <div class="col-md-12 col-xl-6 offset-xl-3">
                        <div class="text-center mb-4 text-white">
                            <h1>Ujian Online</h1>
                            <h6>Selamat Mengerjakan</h6><br>
                            <h5><?= session()->get('nama') ?></h5>
                        </div>
                        <div class="card card-primary">
                            <form id="formToken">
                                <div class="col-md-12">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Masukkan Token Ujian</label>
                                            <input type="text" name="token" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <button type="submit" class="btn btn-primary">Masuk</button>
                                    </div>
                                    <div id="tokenAlert"></div>
                                    <div id="token-box" class="alert alert-info text-center">Menunggu token
                                        ujian...
                                    </div>
                                </div>
                            </form>
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
        let countdownInterval;
        let lastExpiredAt = null;

        function startCountdown(expiredAt) {
            // stop timer lama
            clearInterval(countdownInterval);

            countdownInterval = setInterval(function () {
                let now = Date.now();
                let distance = expiredAt.getTime() - now;

                if (distance <= 0) {
                    clearInterval(countdownInterval);
                    $("#token-box")
                        .removeClass('alert-success')
                        .addClass('alert-info')
                        .html("Token sudah expired. Menunggu token baru...");
                    lastExpiredAt = null;
                } else {
                    let minutes = Math.floor((distance / 1000 / 60) % 60);
                    let seconds = Math.floor((distance / 1000) % 60);
                    $("#countdown").text("Expired dalam: " + minutes + "m " + seconds + "s");
                }
            }, 1000);
        }

        function loadToken() {
            $.ajax({
                url: "<?= base_url('/' . bin2hex('ujian-get-token')) ?>",
                type: "GET",
                dataType: "json",
                success: function (res) {
                    if (res.success && res.expired_at) {
                        let expiredAt = new Date(res.expired_at);

                        // kalau expired_at baru / berbeda → reset timer
                        if (!lastExpiredAt || expiredAt.getTime() !== lastExpiredAt.getTime()) {
                            lastExpiredAt = expiredAt;

                            $("#token-box")
                                .removeClass('alert-info alert-danger')
                                .addClass('alert-success')
                                .html("<span id='countdown'></span>");

                            startCountdown(expiredAt);
                        }

                    } else {
                        $("#token-box")
                            .removeClass('alert-success')
                            .addClass('alert-info')
                            .html("Menunggu token ujian...");
                        lastExpiredAt = null;
                        clearInterval(countdownInterval);
                    }
                },
                error: function (err) {
                    console.error("AJAX error:", err);
                    $("#token-box")
                        .removeClass('alert-success')
                        .addClass('alert-danger')
                        .html("Gagal memuat token.");
                }
            });
        }

        // cek otomatis tiap 5 detik
        setInterval(loadToken, 5000);
        loadToken();


        $("#formToken").on("submit", function (e) {
            e.preventDefault();

            let $btn = $(this).find("button[type=submit]");
            let oldHtml = $btn.html();

            // state loading
            $btn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Cek...');

            $.ajax({
                url: "<?= base_url('/' . bin2hex('ujian-cek-token')) ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (res) {
                    if (res.success) {
                        iziToast.success({
                            title: 'Sukses',
                            message: res.message,
                            position: 'topCenter',
                            timeout: 2000
                        });

                        setTimeout(() => {
                            // redirect ke halaman ujian
                            window.location.href = "<?= base_url('/' . bin2hex('ujian-start')) ?>";
                        }, 1500);

                    } else {
                        iziToast.error({
                            title: 'Token Salah',
                            message: res.message,
                            position: 'topCenter',
                            timeout: 3000
                        });
                    }
                },
                error: function () {
                    iziToast.error({
                        title: 'Error',
                        message: 'Terjadi kesalahan server.',
                        position: 'topCenter',
                        timeout: 3000
                    });
                },
                complete: function () {
                    // restore button
                    $btn.prop("disabled", false).html(oldHtml);
                }
            });
        });

    </script>
</body>

</html>