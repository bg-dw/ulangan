<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h4>Status Ujian</h4>
            </div>
            <div class="card-body" id="tbl-data">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-ujian">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    No.
                                </th>
                                <th class="text-center">Detail Ujian</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                                <th class="text-center">Token</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            if (isset($ujian) && count($ujian) > 0) {
                                foreach ($ujian as $row) {
                                    ?>
                                    <tr>
                                        <td class="text-center" style="width: 7%">
                                            <?= $i++ . "."; ?>
                                        </td>

                                        <td>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <span class="badge badge-primary">
                                                        <?= date("d F Y", strtotime($row['tgl'])) ?></span>
                                                </div>
                                                <div class="col-md-9">
                                                    <strong>
                                                        <?= $row['judul'] ?> - <?= $row['mapel'] ?>
                                                    </strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-primary"><?= strtoupper($row['status']) ?></span>
                                        </td>
                                        <td class="text-center aksi-col-<?= $row['id_ujian'] ?>" style="width: 13%">
                                            <?php if ($row['status'] == "dikerjakan"): ?>
                                                <button class="btn btn-info btn-rilis-token" data-id="<?= $row['id_ujian'] ?>">
                                                    Rilis Token
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="token-display-<?= $row['id_ujian'] ?> text-center">
                                                <?php if (!empty($row['token']) && !empty($row['expired_at'])): ?>
                                                    <h4><?= $row['token'] ?></h4>
                                                    <small class="text-muted d-block countdown"
                                                        data-expired="<?= $row['expired_at'] ?>" data-id="<?= $row['id_ujian'] ?>">
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#table-ujian').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            language: {
                emptyTable: "Tidak ada data tersedia"
            }
        });

        $(document).on("change", ".status-select", function () {
            let select = $(this);
            let idUjian = select.data("id");
            let statusBaru = select.val();
            let statusLama = select.find("option[selected]").val();

            if (confirm("Apakah Anda yakin ingin mengubah status menjadi " + statusBaru.toUpperCase() + " ?")) {
                $.ajax({
                    url: "<?= base_url('/' . bin2hex('data-ujian') . '/' . bin2hex('update-status')) ?>",
                    type: "POST",
                    data: { id: idUjian, status: statusBaru },
                    success: function (res) {
                        alert("Status berhasil diperbarui!");

                        // update option[selected]
                        select.find("option").removeAttr("selected");
                        select.find("option[value='" + statusBaru + "']").attr("selected", true);

                        // === manipulasi tombol edit/hapus ===
                        let aksiCol = $(".aksi-col-" + idUjian);

                        if (statusBaru === "dikerjakan" || statusBaru === "selesai") {
                            aksiCol.html('<span class="badge badge-info">Tidak bisa diubah</span>');
                        } else {
                            aksiCol.html(`
                        <button class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit ujian"
                            onclick="update_ujian('${idUjian}','dummy','dummy','2025-10-01')">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Hapus ujian"
                            onclick="hapus('${idUjian}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `);
                        }
                    },
                    error: function () {
                        alert("Gagal update status.");
                        select.val(statusLama);
                    }
                });
            } else {
                select.val(statusLama);
            }
        });

        $(document).on("click", ".btn-rilis-token", function () {
            let idUjian = $(this).data("id");

            $.ajax({
                url: "<?= base_url('/' . bin2hex('data-ulangan') . '/' . bin2hex('rilis-token')) ?>",
                type: "POST",
                data: { id: idUjian },
                dataType: "json",   // 👈 penting
                success: function (data) {
                    console.log(data);
                    if (data.success) {
                        $(".token-display-" + idUjian).html(
                            '<h4>' + data.token + '</h4>' +
                            '<small class="text-muted d-block countdown" data-expired="' + data.expired_at + '" data-id="' + idUjian + '"></small>'
                        );

                        startCountdown();
                        alert("Token berhasil dibuat, berlaku 5 menit!");
                    } else {
                        alert("Gagal membuat token.");
                    }
                },
                error: function () {
                    alert("Terjadi kesalahan server.");
                }
            });
        });



        function startCountdown() {
            $(".countdown").each(function () {
                let $this = $(this);
                let expiredAt = parseInt($this.data("expired")) * 1000; // ✅ timestamp detik → ms
                let idUjian = $this.data("id");

                let timer = setInterval(function () {
                    let now = new Date().getTime();
                    let distance = expiredAt - now;

                    if (distance <= 0) {
                        clearInterval(timer);
                        // Hapus di UI
                        $(".token-display-" + idUjian).html('');

                        // Hapus di DB
                        $.post("<?= base_url('/' . bin2hex('data-ulangan') . '/' . bin2hex('hapus-token')) ?>",
                            { id: idUjian },
                            function (res) {
                                console.log("Token expired & dihapus dari DB");
                            }, "json"
                        );
                    } else {
                        // Hitung menit & detik
                        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        $this.text("Expired dalam: " + minutes + "m " + seconds + "s");
                    }
                }, 1000);
            });
        }
        startCountdown();
    });
</script>
<?= $this->endSection() ?>