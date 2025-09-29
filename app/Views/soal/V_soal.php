<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h4>Data Soal</h4>
                <div class="card-header-action" role="group" aria-label="Basic example" id="group-btn">
                    <button type="button" class="btn btn-primary" id="btn-add">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="card-body" id="tbl-data">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-soal">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    No.
                                </th>
                                <th class="text-center">Aksi</th>
                                <th class="text-center">Nama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            if (isset($soal) && count($soal) > 0) {
                                foreach ($soal as $row) {
                                    ?>
                                    <tr>
                                        <td class="text-center" style="width: 7%">
                                            <?= $i++ . "."; ?>
                                        </td>
                                        <td class="text-center" style="width: 13%">
                                            <button class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit Soal"
                                                onclick="update_soal('<?= $row['id_soal'] ?>','<?= $row['mapel'] ?>')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Hapus Soal"
                                                onclick="hapus('<?= $row['id_soal'] ?>','<?= $row['mapel'] ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <?= $row['mapel'] ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-body" id="f-add" style="display:none;">
                <form action="<?= base_url('/' . bin2hex('soal') . '/' . bin2hex('add')) ?>" method="post"
                    onsubmit="return confirm('Simpan Data?')">
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
                                <select class="form-control jenis-soal" name="jenis_soal[]">
                                    <option value="" disabled selected>Pilih Jenis Soal</option>
                                    <option value="pilihan_ganda">Pilihan Ganda</option>
                                    <option value="isian">Isian Singkat</option>
                                    <option value="uraian">Uraian</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control jumlah-soal" name="jumlah_soal[]"
                                    placeholder="Jumlah Soal" min="1">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control bobot" name="bobot[]" placeholder="Bobot"
                                    min="1">
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
                        <button class="btn btn-primary" type="submit">Selesai</button>
                        <button class="btn btn-secondary" type="button" id="btn-cancel">Kembali</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {

        $('#table-soal').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            language: {
                emptyTable: "Tidak ada data tersedia"
            }
        });

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

                                <label class="mt-2">Kunci Jawaban</label>
                                <select class="form-control" name="kunci[${rowIndex}][${i}]">
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
                    window.location.replace("<?= base_url('/' . bin2hex('data-draft')) ?>");
                } else {
                    alert("GALAT : " + res.msg);
                    console.log(res);
                }
            });
        });
    });


    $('#btn-add').click(function () {
        $('#tbl-data').hide('slow');
        $('#group-btn').hide('slow');
        $('#f-add').show('slow');
    });
    $('#btn-cancel').click(function () {
        $('#f-add').hide('slow');
        $('#group-btn').show('slow');
        $('#tbl-data').show('slow');
    });

    function hapus(id, mapel) {
        $('#d-id').val(id);
        $('#d-mapel').val(mapel);
        $('#delete-modal').appendTo('body').modal('show');
    }
</script>
<?= $this->endSection() ?>