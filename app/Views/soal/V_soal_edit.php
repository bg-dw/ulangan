<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<?php
// dd($draft['data']); 
?>
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
            <div class="card-header">
                <h4>Edit Draft</h4>
            </div>
            <div class="card-body">
                <form id="draftForm" action="<?= base_url('/' . bin2hex('data-draft') . '/' . bin2hex('update')) ?>"
                    method="post">
                    <input type="hidden" name="id-soal" value="<?= esc($draft['id_soal']) ?>" required>
                    <input type="hidden" name="status" id="formStatus" value="draft">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sel-judul">Judul</label>
                            <select class="form-control" name="id-judul" id="sel-judul" required>
                                <?php foreach ($judul as $row): ?>
                                    <option value="<?= $row['id_judul'] ?>" <?php if ($draft['id_judul'] == $row['id_judul']) {
                                          echo "selected";
                                      } ?>>
                                        <?= $row['judul'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="sel-mapel">Mapel</label>
                            <select class="form-control" name="id-mapel" id="sel-mapel" required>
                                <?php foreach ($mapel as $row): ?>
                                    <option value="<?= $row['id_mapel'] ?>" <?php if ($draft['id_mapel'] == $row['id_mapel']) {
                                          echo "selected";
                                      } ?>>
                                        <?= $row['mapel'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- konfigurasi soal -->
                    <div id="dynamicForm">
                        <?php if (!empty($draft['data']['jenis_soal'])): ?>
                            <?php foreach ($draft['data']['jenis_soal'] as $i => $jenis): ?>
                                <div class="row g-2 mb-2 input-row">
                                    <div class="col-md-4">
                                        <input type="hidden" name="jenis_soal[]" value="<?= $jenis ?>" readonly required>
                                        <select class="form-control jenis-soal" disabled>
                                            <option value="pilihan_ganda" <?= $jenis == 'pilihan_ganda' ? 'selected' : '' ?>>
                                                Pilihan Ganda
                                            </option>
                                            <option value="isian" <?= $jenis == 'isian' ? 'selected' : '' ?>>Isian</option>
                                            <option value="uraian" <?= $jenis == 'uraian' ? 'selected' : '' ?>>Uraian</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="jumlah_soal[]" class="form-control jumlah-soal"
                                            value="<?= esc($draft['data']['jumlah_soal'][$i] ?? '') ?>"
                                            placeholder="Jumlah Soal" readonly>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="bobot[]" class="form-control bobot"
                                            value="<?= esc($draft['data']['bobot'][$i] ?? '') ?>" placeholder="Bobot" readonly>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- field soal hasil generate -->
                    <div class="mt-4" id="generatedFields"></div>

                    <div class="text-right mt-3">
                        <?php if ($draft['status'] != "final"): ?>
                            <button type="submit" class="btn btn-warning" id="btnUpdate">Update Draft</button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-primary" id="btnFinal">Simpan Final</button>
                        <a href="<?= base_url('/' . bin2hex('data-soal')) ?>" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const draftData = <?= json_encode($draft['data']) ?> || null;
        const lastImagesMap = {}; // global untuk semua editor

        /** helper untuk normalisasi object menjadi array */
        function normalizeIndexedObject(obj) {
            if (Array.isArray(obj)) return obj;
            if (obj && typeof obj === 'object') {
                const keys = Object.keys(obj).sort((a, b) => Number(a) - Number(b));
                return keys.map(k => obj[k]);
            }
            return obj ? [obj] : [];
        }

        /** helper: pastikan nilai menjadi array */
        function toArrayMaybe(x) {
            if (x == null) return [];
            if (Array.isArray(x)) return x;
            if (typeof x === 'object') {
                const keys = Object.keys(x);
                const numericKeys = keys.every(k => String(Number(k)) === String(k));
                if (numericKeys) {
                    return keys.sort((a, b) => Number(a) - Number(b)).map(k => x[k]);
                }
                return Object.values(x);
            }
            return [x];
        }

        /** MAIN: generate fields di #generatedFields */
        /** escape helpers */
        function escapeHtml(s) {
            if (s == null) return '';
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }
        function escapeAttr(s) {
            if (s == null) return '';
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        /** Generate soal dinamis */
        function generateSoalFieldFromDraft(data) {
            const container = document.getElementById('generatedFields');
            container.innerHTML = '';
            if (!data) return;
            const jenisArr = normalizeIndexedObject(data.jenis_soal) || [];
            const jumlahArr = normalizeIndexedObject(data.jumlah_soal) || [];
            const pertGroups = normalizeIndexedObject(data.pertanyaan) || [];
            const opsiGroups = normalizeIndexedObject(data.opsi) || [];
            const kunciGroups = normalizeIndexedObject(data.kunci) || [];

            let soalCounter = 1;

            jenisArr.forEach((jenis, groupIndex) => {
                const savedQuestions = normalizeIndexedObject(pertGroups[groupIndex]) || [];
                const opsiForGroup = normalizeIndexedObject(opsiGroups[groupIndex]) || [];
                const kForGroup = normalizeIndexedObject(kunciGroups[groupIndex]) || [];
                let count = savedQuestions.length || parseInt(jumlahArr[groupIndex]) || 0;
                if (count <= 0) return;

                for (let si = 0; si < count; si++) {
                    const qText = savedQuestions[si] || '';
                    const editorId = `${soalCounter}_${si}`;

                    const wrapper = document.createElement('div');
                    wrapper.className = 'mb-3 p-3 border rounded';

                    wrapper.innerHTML = `
                    <label class="fw-bold">Soal ${soalCounter} (${jenis.replace("_", " ")})</label>
                    <textarea class="form-control soal-editor mb-2" name="pertanyaan[${groupIndex}][]" data-id="${editorId}" placeholder="Tulis pertanyaan">${escapeHtml(qText)}</textarea>
                    <span id="image-upload-status-${editorId}" class="upload-status badge bg-success text-white" style="display:none;"></span>
                `;
                    if (jenis === 'pilihan_ganda') {
                        const opsiForSoal = normalizeIndexedObject(opsiForGroup[si]) || [];
                        ['a', 'b', 'c', 'd'].forEach(letter => {
                            const val = opsiForSoal[letter] || opsiForSoal[letter === 'a' ? 0 : letter === 'b' ? 1 : letter === 'c' ? 2 : 3] || '';
                            wrapper.innerHTML += `
                        <div class="input-group mb-1">
                            <span class="input-group-text text-uppercase">${letter}.</span>
                            <input type="text" class="form-control"
                                name="opsi[${groupIndex}][${si}][]"
                                placeholder="Pilihan ${letter.toUpperCase()}"
                                value="${escapeAttr(val)}">
                        </div>
                    `;
                        });

                        const savedKunci = kForGroup[si] || '';
                        wrapper.innerHTML += `
                    <label class="mt-2">Kunci Jawaban</label>
                    <select class="form-control mb-1" name="kunci[${groupIndex}][]">
                        ${['a', 'b', 'c', 'd'].map(opt => `
                            <option value="${opt}" ${savedKunci === opt ? 'selected' : ''}>${opt.toUpperCase()}</option>
                        `).join('')}
                    </select>
                `;
                    } else if (jenis === 'isian' || jenis === 'uraian') {
                        const savedKunci = kForGroup[si] || '';
                        if (jenis === 'uraian') {
                            wrapper.innerHTML += `
                        <label class="mt-2">Panduan / Kunci Jawaban</label>
                        <textarea class="form-control" name="kunci[${groupIndex}][]">${escapeHtml(savedKunci)}</textarea>
                    `;
                        } else {
                            wrapper.innerHTML += `
                        <label class="mt-2">Kunci Jawaban</label>
                        <input type="text" class="form-control" name="kunci[${groupIndex}][]" value="${escapeAttr(savedKunci)}">
                    `;
                        }
                    }
                    container.appendChild(wrapper);

                    lastImagesMap[editorId] = [];
                    soalCounter++;
                }
            });

            // Inisialisasi Summernote untuk semua textarea
            $('.soal-editor').each(function () {
                const editor = $(this);
                const editorId = editor.data('id');
                const status = $('#image-upload-status-' + editorId);
                status.hide();
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

                            status.text('Uploading image...').show();

                            $.ajax({
                                url: '<?= base_url('/' . bin2hex('data-soal') . '/' . bin2hex('upload')) ?>',
                                method: 'POST',
                                data: data,
                                cache: false,
                                contentType: false,
                                processData: false,
                                xhr: function () {
                                    const xhr = new window.XMLHttpRequest();
                                    xhr.upload.addEventListener("progress", function (evt) {
                                        if (evt.lengthComputable) {
                                            const percent = Math.round((evt.loaded / evt.total) * 100);
                                            status.text('Uploading image... ' + percent + '%');
                                        }
                                    });
                                    return xhr;
                                },
                                success: function (response) {
                                    console.log(response);
                                    editor.summernote('insertImage', response.url);
                                    lastImagesMap[editorId].push(response.url);

                                    // Show toast
                                    if (typeof bootstrap !== 'undefined') {
                                        const toastEl = document.getElementById('toastJawaban');
                                        new bootstrap.Toast(toastEl, { delay: 3000 }).show();
                                    }
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
                                $.post('<?= base_url('/' . bin2hex('data-soal') . '/' . bin2hex('hapus')) ?>', { url: src });
                            });

                            lastImagesMap[editorId] = currentImages;
                        }
                    }
                });
            });
        }

        /** Jalankan generate */
        generateSoalFieldFromDraft(draftData);

        /** Form control */
        const form = document.getElementById("draftForm");
        const statusInput = document.getElementById("formStatus");

        function validateForm() {
            let isValid = true;
            const fields = form.querySelectorAll("input[name], textarea[name], select[name]");
            fields.forEach(el => {
                if (el.type === "hidden" || el.disabled) return;
                if (!el.value.trim()) {
                    el.classList.add("is-invalid");
                    isValid = false;
                } else {
                    el.classList.remove("is-invalid");
                }
            });
            return isValid;
        }

        const btnFinal = document.getElementById("btnFinal");
        const btnUpdate = document.getElementById("btnUpdate");

        if (btnFinal) btnFinal.addEventListener("click", function () {
            if (!validateForm()) {
                alert("Masih ada field kosong, lengkapi semua isian.");
                return;
            }
            statusInput.value = "final";
            form.action = "<?= base_url('/' . bin2hex('data-draft') . '/' . bin2hex('final')) ?>";
            form.submit();
        });

        if (btnUpdate) btnUpdate.addEventListener("click", function () {
            statusInput.value = "draft";
        });
    });

</script>
<?= $this->endSection() ?>