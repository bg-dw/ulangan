<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<?php
// dd($draft['data']); 
?>
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
    /** Data draft dari server */
    const draftData = <?= json_encode($draft['data']) ?> || null;

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

    /** Normalisasi supaya object { "1":"...", "2":"..." } jadi array ["...","..."] */
    function normalizeIndexedObject(obj) {
        if (Array.isArray(obj)) return obj;
        if (obj && typeof obj === 'object') {
            const keys = Object.keys(obj).sort((a, b) => Number(a) - Number(b));
            return keys.map(k => obj[k]);
        }
        return obj ? [obj] : [];
    }


    /** MAIN: generate fields di #generatedFields */
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
                const wrapper = document.createElement('div');
                wrapper.className = 'mb-3 p-3 border rounded';

                wrapper.innerHTML = `
                <label class="fw-bold">Soal ${soalCounter} (${jenis.replace("_", " ")})</label>
                <textarea class="form-control mb-2" name="pertanyaan[${groupIndex}][]">${escapeHtml(qText)}</textarea>
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
                soalCounter++;
            }
        });
    }

    /** run on DOM ready */
    document.addEventListener('DOMContentLoaded', function () {
        console.log('draftData', draftData);
        generateSoalFieldFromDraft(draftData);

        const form = document.getElementById("draftForm");
        const statusInput = document.getElementById("formStatus");

        // validasi semua input dan textarea
        function validateForm() {
            let isValid = true;
            const fields = form.querySelectorAll("input[name], textarea[name], select[name]");
            fields.forEach(el => {
                // skip hidden
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

        // tombol simpan final
        const btnFinal = document.getElementById("btnFinal");
        const btnUpdate = document.getElementById("btnUpdate");

        if (btnFinal) {
            btnFinal.addEventListener("click", function () {
                if (!validateForm()) {
                    alert("Masih ada field yang kosong. Harap lengkapi semua isian sebelum finalisasi.");
                    return;
                }
                form.action = "<?= base_url('/' . bin2hex('data-draft') . '/' . bin2hex('final')) ?>";
                statusInput.value = "final";
                form.submit();
            });
        }

        // tombol update draft → biarkan default
        if (btnUpdate) {
            btnUpdate.addEventListener("click", function () {
                statusInput.value = "draft";
            });
        }
    });

</script>
<?= $this->endSection() ?>