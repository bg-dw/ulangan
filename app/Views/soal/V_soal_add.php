<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-body">
                <form action="<?= base_url('/' . bin2hex('soal') . '/' . bin2hex('add')) ?>" method="post">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="">Judul</label>
                            <input type="text" class="form-control" name="judul" placeholder="Ulangan Harian" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="sel-mapel">Mapel</label>
                            <select class="form-control" name="id-mapel" id="sel-mapel" required>
                                <?php foreach ($mapel as $row): ?>
                                    <option value="<?= $row['id_mapel'] ?>"><?= $row['mapel'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div id="dynamicForm">
                        <div class="row g-2 mb-2 input-row">
                            <div class="col-md-4">
                                <select class="form-control jenis-soal" name="jenis_soal[]" required>
                                    <option value="" disabled selected>Pilih Jenis Soal</option>
                                    <option value="pilihan_ganda">Pilihan Ganda</option>
                                    <option value="isian">Isian Singkat</option>
                                    <option value="uraian">Uraian</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control jumlah-soal" name="jumlah_soal[]"
                                    placeholder="Jumlah Soal" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control bobot" name="bobot[]" placeholder="Bobot"
                                    min="1" required>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-success btn-add">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Generate -->
                    <div class="mt-3">
                        <button type="button" class="btn btn-info" id="btn-generate">Generate
                            Soal</button>
                    </div>

                    <!-- Tempat hasil generate -->
                    <div class="mt-3" id="generatedFields"></div>
                    <div class="text-right mt-5">
                        <button class="btn btn-warning" type="button" id="btn-save-draft">Selesaikan
                            Nanti</button>
                        <button class="btn btn-primary" type="button">Simpan Final</button>
                        <button class="btn btn-secondary" type="button"
                            onclick="location.href='<?= base_url(bin2hex('data-soal')) ?>'">Kembali</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        // semua jenis soal (bisa ambil dari PHP juga)
        const allJenisSoal = [
            { val: "pilihan_ganda", text: "Pilihan Ganda" },
            { val: "isian", text: "Isian Singkat" },
            { val: "uraian", text: "Uraian" }
        ];

        // tambah field baru
        $(document).on("click", ".btn-add", function () {
            let row = `
                    <div class="row g-2 mb-2 input-row">
                        <div class="col-md-4">
                            <select class="form-control jenis-soal" name="jenis_soal[]">
                                <option value="" disabled selected>Pilih Jenis Soal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control jumlah-soal" name="jumlah_soal[]" placeholder="Jumlah Soal" min="1">
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control bobot" name="bobot[]" placeholder="Bobot" min="1">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-success btn-add">+</button>
                        </div>
                    </div>`;

            $("#dynamicForm").append(row);

            $(this).removeClass("btn-success btn-add")
                .addClass("btn-danger btn-remove")
                .text("-");

            refreshOptions();
            hitungTotal();
        });

        // hapus field
        $(document).on("click", ".btn-remove", function () {
            $(this).closest(".input-row").remove();
            refreshOptions();
            hitungTotal();
        });

        // kalau option berubah
        $(document).on("change", ".jenis-soal", function () {
            refreshOptions();
        });

        // ketika jumlah soal atau bobot berubah
        $(document).on("input", ".jumlah-soal, .bobot", function () {
            hitungTotal();
        });

        // fungsi hitung total poin
        function hitungTotal() {
            let total = 0;

            $(".input-row").each(function () {
                let jumlah = parseInt($(this).find(".jumlah-soal").val()) || 0;
                let bobot = parseInt($(this).find(".bobot").val()) || 0;
                total += jumlah * bobot;
            });
        }

        // 🔑 refresh semua select supaya hanya menampilkan option yang belum dipakai
        function refreshOptions() {
            // ambil semua value yang sudah dipilih
            let selected = $(".jenis-soal").map(function () {
                return $(this).val();
            }).get();

            $(".jenis-soal").each(function () {
                let currentVal = $(this).val();
                let $select = $(this);

                // kosongkan & rebuild
                $select.empty();
                $select.append('<option value="" disabled>Pilih Jenis Soal</option>');

                allJenisSoal.forEach(opt => {
                    // tampilkan hanya kalau belum dipilih atau itu value saat ini
                    if (!selected.includes(opt.val) || opt.val === currentVal) {
                        $select.append(
                            `<option value="${opt.val}" ${opt.val === currentVal ? "selected" : ""}>${opt.text}</option>`
                        );
                    }
                });

                // kalau belum ada pilihan, tetap selected default
                if (!currentVal) {
                    $select.prop("selectedIndex", 0);
                }
            });
        }

        // panggil pertama kali
        refreshOptions();
        hitungTotal();
        $(document).on("click", "#btn-generate", function () {
            $("#generatedFields").empty(); // reset dulu (biar tidak double)

            let soalCounter = 1; // counter global untuk semua soal

            $(".input-row").each(function (rowIndex) {
                let jenis = $(this).find(".jenis-soal").val();
                let jumlah = parseInt($(this).find(".jumlah-soal").val()) || 0;

                if (!jenis || jumlah <= 0) return; // skip kalau kosong

                for (let i = 1; i <= jumlah; i++) {
                    let field = `<div class="mb-4 p-3 border rounded">
                <label><strong>Soal ${soalCounter} (${jenis.replace("_", " ")})</strong></label>
                <textarea class="form-control mb-2" 
                    name="pertanyaan[${rowIndex}][${i}]" 
                    placeholder="Tulis pertanyaan di sini"></textarea>
            `;

                    if (jenis === "pilihan_ganda") {
                        field += `
                                <div class="input-group mb-1">
                                    <span class="input-group-text text-uppercase">A.</span>
                                    <input type="text" class="form-control"
                                        name="jawaban[${rowIndex}][${i}][a]"
                                        placeholder="Pilihan A">
                                </div>
                                <div class="input-group mb-1">
                                    <span class="input-group-text text-uppercase">B.</span>
                                    <input type="text" class="form-control"
                                        name="jawaban[${rowIndex}][${i}][b]"
                                        placeholder="Pilihan B">
                                </div>
                                <div class="input-group mb-1">
                                    <span class="input-group-text text-uppercase">C.</span>
                                    <input type="text" class="form-control"
                                        name="jawaban[${rowIndex}][${i}][c]"
                                        placeholder="Pilihan C">
                                </div>
                                <div class="input-group mb-1">
                                    <span class="input-group-text text-uppercase">D.</span>
                                    <input type="text" class="form-control"
                                        name="jawaban[${rowIndex}][${i}][d]"
                                        placeholder="Pilihan D">
                                </div>

                                <label class="mt-2"><strong>Kunci Jawaban</strong></label>
                                <select class="form-control col-1" name="kunci[${rowIndex}][${i}]">
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="c">C</option>
                                    <option value="d">D</option>
                                </select>
                            `;
                    } else if (jenis === "isian") {
                        field += `
                    <label class="mt-2">Kunci Jawaban</label>
                    <input type="text" class="form-control" name="kunci[${rowIndex}][${i}]" placeholder="Kunci jawaban">
                `;
                    } else if (jenis === "uraian") {
                        field += `
                    <label class="mt-2">Panduan/Kunci Jawaban</label>
                    <textarea class="form-control" name="kunci[${rowIndex}][${i}]" placeholder="Tuliskan kunci jawaban uraian"></textarea>
                `;
                    }

                    field += `</div>`;
                    $("#generatedFields").append(field);

                    soalCounter++; // naikkan nomor soal global
                }
            });

            // ubah tombol jadi Regenerate setelah sekali klik
            $("#btn-generate").text("Regenerate Soal")
                .removeClass("btn-info")
                .addClass("btn-warning");
        });


        // Simpan ke Server
        $("#btn-save-draft").on("click", function () {
            let formData = $("form").serializeArray();
            // simpan ke server
            $.post("<?= base_url(bin2hex('data-draft') . '/' . bin2hex('save')) ?>", formData, function (res) {
                if (res.status == "ok") {
                    alert("Draft tersimpan di server!");
                    window.location.replace("<?= base_url('/' . bin2hex('data-soal')) ?>");
                } else {
                    alert("GALAT : " + res.msg);
                    console.log(res);
                }
            });
        });

        function clearErrors() {
            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").remove();
        }

        function validateForm() {
            clearErrors();
            let valid = true;

            // field inti
            const requiredFields = [
                { selector: "input[name='judul']", message: "Judul harus diisi" },
                { selector: "select[name='id-mapel']", message: "Mapel harus dipilih" },
                { selector: "select.jenis-soal", message: "Jenis soal harus dipilih" },
                { selector: "input.jumlah-soal", message: "Jumlah soal harus diisi" },
                { selector: "input.bobot", message: "Bobot harus diisi" }
            ];

            requiredFields.forEach(field => {
                $(field.selector).each(function () {
                    if (!$(this).val() || $(this).val().trim() === "") {
                        valid = false;
                        $(this).addClass("is-invalid");
                        if ($(this).next(".invalid-feedback").length === 0) {
                            $(this).after(`<div class="invalid-feedback">${field.message}</div>`);
                        }
                    }
                });
            });

            // field pertanyaan hasil generate
            $("textarea[name^='pertanyaan']").each(function () {
                if (!$(this).val().trim()) {
                    valid = false;
                    $(this).addClass("is-invalid");
                    if ($(this).next(".invalid-feedback").length === 0) {
                        $(this).after(`<div class="invalid-feedback">Pertanyaan wajib diisi</div>`);
                    }
                }
            });

            // field jawaban (pilihan ganda)
            $("input[name^='jawaban']").each(function () {
                if (!$(this).val().trim()) {
                    valid = false;
                    $(this).addClass("is-invalid");
                    if ($(this).next(".invalid-feedback").length === 0) {
                        $(this).after(`<div class="invalid-feedback">Jawaban wajib diisi</div>`);
                    }
                }
            });

            // field kunci jawaban
            $("[name^='kunci']").each(function () {
                if (!$(this).val().trim()) {
                    valid = false;
                    $(this).addClass("is-invalid");
                    if ($(this).next(".invalid-feedback").length === 0) {
                        $(this).after(`<div class="invalid-feedback">Kunci jawaban wajib diisi</div>`);
                    }
                }
            });

            return valid;
        }

        // klik tombol simpan
        $(document).on("click", ".btn-primary", function () {
            if (validateForm()) {
                let formData = $("form").serializeArray();

                $.post("<?= base_url(bin2hex('data-soal') . '/' . bin2hex('save')) ?>", formData, function (res) {
                    if (res.status == "ok") {
                        alert("Draft tersimpan di server!");
                        window.location.replace("<?= base_url('/' . bin2hex('data-soal')) ?>");
                    } else {// hapus error lama
                        $(".is-invalid").removeClass("is-invalid");
                        $(".invalid-feedback").remove();

                        // tampilkan error inline
                        for (let field in res.msg) {
                            let pesan = res.msg[field];
                            // cari input berdasarkan name^ (prefix)
                            $(`[name^='${field.replace(/\.\*\.\*/g, "")}']`).each(function () {
                                $(this).addClass("is-invalid");
                                if ($(this).next(".invalid-feedback").length === 0) {
                                    $(this).after(`<div class="invalid-feedback">${pesan}</div>`);
                                }
                            });
                        }
                        console.log(res);
                    }
                }, "json"); // pastikan response dibaca sebagai JSON
            }
        });

        // hapus error begitu user isi field
        $(document).on("input change", "input, textarea, select", function () {
            if ($(this).val().trim()) {
                $(this).removeClass("is-invalid");
                $(this).next(".invalid-feedback").remove();
            }
        });
    });
</script>
<?= $this->endSection() ?>