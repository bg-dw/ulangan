<?= $this->extend('Main') ?>
<?= $this->section('content') ?>

<div class="container my-4">
    <div class="row">
        <!-- Kolom soal -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php foreach ($soal as $i => $s): ?>
                        <div class="soal-item d-none" id="soal-<?= $i ?>">
                            <h5>Soal <?= $i + 1 ?></h5>
                            <p><?= esc($s['pertanyaan']) ?></p>

                            <!-- opsi jawaban -->
                            <?php foreach (json_decode($s['opsi'], true) as $opsiKey => $opsiVal): ?>
                                <div class="form-check mb-2">
                                    <input type="radio" class="form-check-input jawaban-input" data-index="<?= $i ?>"
                                        data-idsoal="<?= $s['id_soal'] ?>" name="jawaban[<?= $s['id_soal'] ?>]"
                                        value="<?= $opsiKey ?>">
                                    <label class="form-check-label"><?= $opsiVal ?></label>
                                </div>
                            <?php endforeach; ?>

                            <!-- tanda ragu-ragu -->
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input ragu-input" data-index="<?= $i ?>"
                                    data-idsoal="<?= $s['id_soal'] ?>">
                                <label class="form-check-label text-warning">Tandai Ragu-ragu</label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-between">
                <button class="btn btn-secondary" id="prevBtn">Sebelumnya</button>
                <button class="btn btn-primary" id="nextBtn">Selanjutnya</button>
            </div>
        </div>

        <!-- Kolom navigasi soal -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Nomor Soal</h6>

                    <!-- progress bar -->
                    <div class="mb-3">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" id="progressBar" role="progressbar" style="width: 0%">
                                0%
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap">
                        <?php foreach ($soal as $i => $s): ?>
                            <button class="btn btn-outline-secondary m-1 soal-nav" id="nav-<?= $i ?>"
                                data-target="<?= $i ?>">
                                <?= $i + 1 ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <button class="btn btn-success w-100 mt-3">Selesai Ujian</button>
        </div>
    </div>
</div>

<script>
    let currentSoal = 0;
    const totalSoal = <?= count($soal) ?>;
    const idSiswa = <?= $id_siswa ?>;
    const idUjian = <?= $id_ujian ?>;

    function showSoal(index) {
        $(".soal-item").addClass("d-none");
        $("#soal-" + index).removeClass("d-none");
    }

    function updateNavColor(index, status) {
        let btn = $("#nav-" + index);
        btn.removeClass("btn-outline-secondary btn-success btn-warning");

        if (status === "belum") {
            btn.addClass("btn-outline-secondary"); // abu-abu
        } else if (status === "jawab") {
            btn.addClass("btn-success"); // hijau
        } else if (status === "ragu") {
            btn.addClass("btn-warning"); // kuning
        }
    }

    $(document).ready(function () {
        showSoal(currentSoal);

        $("#nextBtn").click(function () {
            if (currentSoal < totalSoal - 1) {
                currentSoal++;
                showSoal(currentSoal);
            }
        });

        $("#prevBtn").click(function () {
            if (currentSoal > 0) {
                currentSoal--;
                showSoal(currentSoal);
            }
        });

        $(".soal-nav").click(function () {
            currentSoal = $(this).data("target");
            showSoal(currentSoal);
        });

        function updateProgress() {
            let total = totalSoal;
            let answered = $(".soal-nav.btn-success, .soal-nav.btn-warning").length;
            // hijau = sudah jawab, kuning = ragu-ragu tapi dihitung sudah terisi
            let percent = Math.round((answered / total) * 100);

            $("#progressBar")
                .css("width", percent + "%")
                .text(percent + "%");
        }

        // panggil setiap update jawaban
        $(".jawaban-input").on("change", function () {
            let idSoal = $(this).data("idsoal");
            let jawaban = $(this).val();
            let index = $(this).data("index");

            $.ajax({
                url: "<?= base_url(bin2hex('save-jawaban')) ?>",
                method: "POST",
                data: {
                    id_siswa: idSiswa,
                    id_ujian: idUjian,
                    id_soal: idSoal,
                    jawaban: jawaban,
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                dataType: "json",
                success: function (res) {
                    if (res.success) {
                        updateNavColor(index, "jawab");
                        updateProgress(); // ⬅️ update progress bar
                    }
                }
            });
        });

        $(".ragu-input").on("change", function () {
            let index = $(this).data("index");
            if ($(this).is(":checked")) {
                updateNavColor(index, "ragu");
            } else {
                let radioChecked = $("#soal-" + index + " .jawaban-input:checked").length > 0;
                updateNavColor(index, radioChecked ? "jawab" : "belum");
            }
            updateProgress(); // ⬅️ update progress bar
        });


        // inisialisasi semua nomor soal -> abu-abu
        for (let i = 0; i < totalSoal; i++) {
            updateNavColor(i, "belum");
        }
    });
</script>

<?= $this->endSection() ?>