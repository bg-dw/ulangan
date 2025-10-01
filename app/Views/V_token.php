<audio id="notifSound" src="<?= base_url('assets/sound/notif.mp3') ?>" preload="auto"></audio>

<form id="formToken">
    <input type="hidden" name="id" value="<?= $ujian['id_ujian'] ?>">
    <div class="form-group">
        <label>Masukkan Token Ujian</label>
        <input type="text" name="token" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Masuk</button>
</form>
<div id="tokenAlert"></div>
<div id="token-box" class="alert alert-info">Menunggu token ujian...</div>

<script>
    let countdownInterval;
    let lastToken = null;

    function loadToken() {
        $.ajax({
            url: "<?= base_url('/' . bin2hex('ujian') . '/' . bin2hex('get-token')) ?>/" + <?= $ujian['id_ujian'] ?>,
            type: "GET",
            success: function (res) {
                clearInterval(countdownInterval); // reset countdown

                if (res.success && res.token) {
                    // Kalau token baru muncul atau berubah -> bunyikan notifikasi
                    if (lastToken !== res.token) {
                        document.getElementById("notifSound").play();
                        alert("Token baru tersedia: " + res.token);
                        lastToken = res.token;
                    }

                    $("#token-box").removeClass('alert-info alert-danger')
                        .addClass('alert-success')
                        .html("Token aktif: <b>" + res.token + "</b><br><span id='countdown'></span>");

                    // Jalankan countdown
                    let expiredAt = new Date(res.expired_at).getTime();
                    countdownInterval = setInterval(function () {
                        let now = new Date().getTime();
                        let distance = expiredAt - now;

                        if (distance <= 0) {
                            clearInterval(countdownInterval);
                            $("#token-box").removeClass('alert-success')
                                .addClass('alert-info')
                                .html("Token sudah expired. Menunggu token baru...");
                            lastToken = null; // reset biar kalau ada token baru tetap notif
                        } else {
                            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            $("#countdown").text("Expired dalam: " + minutes + "m " + seconds + "s");
                        }
                    }, 1000);

                } else {
                    $("#token-box").removeClass('alert-success')
                        .addClass('alert-info')
                        .html("Menunggu token ujian...");
                    lastToken = null;
                }
            },
            error: function () {
                $("#token-box").removeClass('alert-success')
                    .addClass('alert-danger')
                    .html("Gagal memuat token.");
            }
        });
    }

    // cek otomatis setiap 5 detik
    setInterval(loadToken, 5000);

    // load pertama kali
    loadToken();

    // Submit form cek token
    $("#formToken").on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: "<?= base_url('/' . bin2hex('data-ulangan') . '/' . bin2hex('cek-token')) ?>",
            type: "POST",
            data: $(this).serialize(),
            success: function (res) {
                let data = JSON.parse(res);
                if (data.success) {
                    $("#tokenAlert").html('<div class="alert alert-success">' + data.message + '</div>');
                    setTimeout(() => {
                        window.location.href = "<?= base_url('/ujian/mulai/') ?>" + $("input[name=id]").val();
                    }, 1000);
                } else {
                    $("#tokenAlert").html('<div class="alert alert-danger">' + data.message + '</div>');
                }
            },
            error: function () {
                $("#tokenAlert").html('<div class="alert alert-danger">Terjadi kesalahan server.</div>');
            }
        });
    });
</script>