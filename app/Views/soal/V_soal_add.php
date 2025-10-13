<?= $this->extend('Main') ?>
<?= $this->section('content') ?>

<!-- Toast -->
<div id="toastContainer" class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 1080;">
    <div id="toastJawaban" class="toast bg-info text-white" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            Gambar tersimpan!
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-body">
                <form action="<?= base_url('/' . bin2hex('soal') . '/' . bin2hex('add')) ?>" method="post"
                    enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sel-judul">Judul</label>
                            <select class="form-control" name="id-judul" id="sel-judul" required>
                                <?php foreach ($judul as $row): ?>
                                    <option value="<?= $row['id_judul'] ?>">
                                        <?= $row['judul'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="sel-mapel">Mapel</label>
                            <select class="form-control" name="id-mapel" id="sel-mapel" required>
                                <?php foreach ($mapel as $row): ?>
                                    <option value="<?= $row['id_mapel'] ?>">
                                        <?= $row['mapel'] ?>
                                    </option>
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
        const toastEl = document.getElementById('toastJawaban');
        toastContainer.appendChild(toastEl);

        const toast = new bootstrap.Toast(toastEl, { delay: 2000 });
        const allJenisSoal = [
            { val: "pilihan_ganda", text: "Pilihan Ganda" },
            { val: "isian", text: "Isian Singkat" },
            { val: "uraian", text: "Uraian" }
        ];

        let editors = []; // array untuk menyimpan semua instance CKEditor

        // Cegah form submit saat dialog insert image dibuka
        $(document).on('submit', '.note-image-dialog form', function (e) {
            e.preventDefault();
        });
        // Tambah field baru
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
            $(this).removeClass("btn-success btn-add").addClass("btn-danger btn-remove").text("-");
            refreshOptions();
        });

        // Hapus field
        $(document).on("click", ".btn-remove", function () {
            $(this).closest(".input-row").remove();
            refreshOptions();
        });

        function refreshOptions() {
            let selected = $(".jenis-soal").map(function () { return $(this).val(); }).get();
            $(".jenis-soal").each(function () {
                let currentVal = $(this).val();
                let $select = $(this);
                $select.empty();
                $select.append('<option value="" disabled>Pilih Jenis Soal</option>');
                allJenisSoal.forEach(opt => {
                    if (!selected.includes(opt.val) || opt.val === currentVal) {
                        $select.append(`<option value="${opt.val}" ${opt.val === currentVal ? "selected" : ""}>${opt.text}</option>`);
                    }
                });
                if (!currentVal) { $select.prop("selectedIndex", 0); }
            });
        }
        refreshOptions();

        const lastImagesMap = {}; // menyimpan lastImages per editor
        // Generate soal
        $("#btn-generate").on("click", function () {
            $("#generatedFields").empty();
            editors.forEach(e => e.destroy()); // hapus editor lama
            editors = [];

            let soalCounter = 1;
            $(".input-row").each(function (rowIndex) {
                let jenis = $(this).find(".jenis-soal").val();
                let jumlah = parseInt($(this).find(".jumlah-soal").val()) || 0;
                if (!jenis || jumlah <= 0) return;

                for (let i = 1; i <= jumlah; i++) {
                    let field = `<div class="mb-4 p-3 border rounded">
                    <label><strong>Soal ${soalCounter} (${jenis.replace("_", " ")})</strong></label>
                    <textarea class="form-control soal-editor mb-2" name="pertanyaan[${rowIndex}][${i}]" data-id="${rowIndex}_${i}" placeholder="Tulis pertanyaan di sini"></textarea>
                    <span id="image-upload-status-${rowIndex}_${i}" class="upload-status badge badge-success" style="display:none; color:white; margin-bottom:5px;"></span>`;

                    if (jenis === "pilihan_ganda") {
                        field += `
                        <div class="input-group mb-1"><span class="input-group-text text-uppercase"><strong>A.</strong></span><input type="text" class="form-control" name="jawaban[${rowIndex}][${i}][a]" placeholder="Pilihan A"></div>
                        <div class="input-group mb-1"><span class="input-group-text text-uppercase"><strong>B.</strong></span><input type="text" class="form-control" name="jawaban[${rowIndex}][${i}][b]" placeholder="Pilihan B"></div>
                        <div class="input-group mb-1"><span class="input-group-text text-uppercase"><strong>C.</strong></span><input type="text" class="form-control" name="jawaban[${rowIndex}][${i}][c]" placeholder="Pilihan C"></div>
                        <div class="input-group mb-1"><span class="input-group-text text-uppercase"><strong>D.</strong></span><input type="text" class="form-control" name="jawaban[${rowIndex}][${i}][d]" placeholder="Pilihan D"></div>
                        <label class="mt-2"><strong>Kunci Jawaban</strong></label>
                        <select class="form-control col-1" name="kunci[${rowIndex}][${i}]">
                            <option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option>
                        </select>`;
                    } else if (jenis === "isian") {
                        field += `<label class="mt-2">Kunci Jawaban</label><input type="text" class="form-control" name="kunci[${rowIndex}][${i}]" placeholder="Kunci jawaban">`;
                    } else if (jenis === "uraian") {
                        field += `<label class="mt-2">Panduan/Kunci Jawaban</label><textarea class="form-control" name="kunci[${rowIndex}][${i}]" placeholder="Tuliskan kunci jawaban uraian"></textarea>`;
                    }

                    field += `</div>`;
                    $("#generatedFields").append(field);
                    soalCounter++;
                }
            });


            $('.soal-editor').each(function () {
                const editor = $(this);
                const editorId = editor.data('id');
                lastImagesMap[editorId] = [];

                editor.summernote({
                    height: 300,
                    dialogsInBody: true,
                    placeholder: 'Tulis soal atau teks di sini...',
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['insert', ['picture', 'link', 'table', 'hr']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['view', ['fullscreen', 'codeview']]
                    ],
                    callbacks: {
                        onImageUpload: function (files) {
                            const data = new FormData();
                            data.append('upload', files[0]);
                            console.log(editorId);
                            const status = $('#image-upload-status-' + editorId);
                            status.text('Uploading image...').show();

                            $.ajax({
                                url: '<?= base_url("/" . bin2hex("data-soal") . "/" . bin2hex("upload")) ?>',
                                method: 'POST',
                                data: data,
                                cache: false,
                                contentType: false,
                                processData: false,
                                xhr: function () {
                                    const xhr = new window.XMLHttpRequest();
                                    xhr.upload.addEventListener("progress", function (evt) {
                                        if (evt.lengthComputable) {
                                            const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                            status.text('Uploading image... ' + percentComplete + '%');
                                        }
                                    }, false);
                                    return xhr;
                                },
                                success: function (response) {
                                    editor.summernote('insertImage', response.url);
                                    lastImagesMap[editorId].push(response.url);
                                    toast.show();
                                    setTimeout(() => status.hide(), 3000);
                                },
                                error: function () {
                                    status.text('Upload gagal!').css('color', 'red');
                                    setTimeout(() => status.hide(), 3000);
                                }
                            });
                        },
                        onChange: function (contents) {
                            const currentImages = [];
                            $(contents).find('img').each(function () {
                                currentImages.push($(this).attr('src'));
                            });

                            const deleted = lastImagesMap[editorId].filter(src => !currentImages.includes(src));
                            deleted.forEach(src => {
                                $.post('<?= base_url("/" . bin2hex("data-soal") . "/" . bin2hex("hapus")) ?>', { url: src });
                            });

                            lastImagesMap[editorId] = currentImages;
                        }
                    }
                });
            });


            // Simpan draft
            $("#btn-save-draft").on("click", function () {
                let formData = $("form").serializeArray();
                $.post("<?= base_url(bin2hex('data-draft') . '/' . bin2hex('save')) ?>", formData, function (res) {
                    if (res.status == "ok") { alert("Draft tersimpan!"); window.location.replace("<?= base_url('/' . bin2hex('data-soal')) ?>"); }
                    else { alert("GALAT: " + res.msg); console.log(res); }
                });
            });

            // Simpan final
            $(document).on("click", ".btn-primary", function () {
                if (validateForm()) {
                    let formData = $("form").serializeArray();
                    $.post("<?= base_url(bin2hex('data-soal') . '/' . bin2hex('save')) ?>", formData, function (res) {
                        if (res.status == "ok") { alert("Tersimpan!"); window.location.replace("<?= base_url('/' . bin2hex('data-soal')) ?>"); }
                        else { alert("GALAT: " + res.msg); console.log(res); }
                    }, "json");
                }
            });

            // Validasi tetap seperti sebelumnya
            function validateForm() {
                let valid = true;
                // validasi sederhana, bisa dikembangkan
                $("textarea[name^='pertanyaan']").each(function () {
                    if (!$(this).val().trim()) { valid = false; $(this).addClass("is-invalid"); }
                });
                return valid;
            }

        });
    });
</script>

<?= $this->endSection() ?>