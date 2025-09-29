<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h4>Edit Draft:</h4>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/' . bin2hex('data-draft') . '/' . bin2hex('update')) ?>" method="post">
                    <input type="hidden" name="id_draft" value="<?= esc($draft['id_draft']) ?>" required>
                    <input type="hidden" name="id-mapel" value="<?= esc($draft['data']['id-mapel']) ?>" required>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Judul</label>
                            <input type="text" class="form-control" name="judul" value="<?= esc($draft['judul']) ?>"
                                placeholder="Ulangan Harian" readonly required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Mapel</label>
                            <input type="text" class="form-control" value="<?= $draft['mapel'] ?>" disabled>
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

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">Update Draft</button>
                        <a href="<?= base_url('/' . bin2hex('data-draft')) ?>" class="btn btn-secondary">Kembali</a>
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

    /** escape helpers */
    function escapeHtml(s) {
        if (s == null) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function escapeAttr(s) {
        if (s == null) return '';
        return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    /** builder untuk nested opsi/kunci menjadi array-of-arrays */
    function buildNestedGroups(raw) {
        if (!raw) return [];
        if (Array.isArray(raw)) {
            return raw.map(item => toArrayMaybe(item));
        }
        if (typeof raw === 'object') {
            const keys = Object.keys(raw).sort((a, b) => Number(a) - Number(b));
            return keys.map(k => toArrayMaybe(raw[k]));
        }
        return [];
    }

    /** MAIN: generate fields di #generatedFields */
    function generateSoalFieldFromDraft(data) {
        const container = document.getElementById('generatedFields');
        container.innerHTML = '';

        if (!data) return;

        const jenisArr = toArrayMaybe(data.jenis_soal || data.jenis || []);
        const jumlahArr = toArrayMaybe(data.jumlah_soal || data.jumlah || []);
        const pertGroups = buildNestedGroups(data.pertanyaan || []);
        const opsiGroups = buildNestedGroups(data.opsi || data.option || data.pilihan || []);
        const kunciGroups = buildNestedGroups(data.kunci || []);

        let soalCounter = 1; // counter global

        jenisArr.forEach((jenis, groupIndex) => {
            const savedQuestions = pertGroups[groupIndex] || [];
            let count = savedQuestions.length;
            if (count === 0) {
                const j = parseInt(jumlahArr[groupIndex]);
                count = isNaN(j) ? 0 : j;
            }
            if (count <= 0) return;

            for (let si = 0; si < count; si++) {
                const qText = savedQuestions[si] ?? savedQuestions[si + 1] ?? '';
                const wrapper = document.createElement('div');
                wrapper.className = 'mb-3 p-3 border rounded';

                wrapper.innerHTML = `
                <label class="fw-bold"><strong>Soal ${soalCounter} (${String(jenis).replace("_", " ")})</strong></label>
                <textarea class="form-control mb-2" name="pertanyaan[${groupIndex}][]">${escapeHtml(qText)}</textarea>
            `;

                if (String(jenis) === 'pilihan_ganda') {
                    const opsiForGroup = opsiGroups[groupIndex] || [];
                    const opsiForSoal = opsiForGroup[si] || opsiForGroup[si + 1] || [];

                    for (let oi = 0; oi < 4; oi++) {
                        const letter = String.fromCharCode(97 + oi);
                        const val = opsiForSoal[oi] ?? '';
                        wrapper.innerHTML += `
                        <div class="input-group mb-1">
                            <span class="input-group-text text-uppercase">${letter}.</span>
                            <input type="text" class="form-control"
                                name="opsi[${groupIndex}][${si}][]"
                                placeholder="Pilihan ${letter.toUpperCase()}"
                                value="${escapeAttr(val)}">
                        </div>
                    `;
                    }

                    const kForGroup = kunciGroups[groupIndex] || [];
                    const savedKunci = kForGroup[si] ?? kForGroup[si + 1] ?? '';
                    wrapper.innerHTML += `
                    <label class="mt-2">Kunci Jawaban</label>
                                <select class="form-control mb-1" name="kunci[${groupIndex}][]">
                        ${['a', 'b', 'c', 'd'].map(opt => `
                            <option value="${opt}" ${savedKunci === opt ? 'selected' : ''}>
                                ${opt.toUpperCase()}
                            </option>
                        `).join('')}
                    </select>
                `;
                } else if (String(jenis) === 'isian' || String(jenis) === 'uraian') {
                    const kForGroup = kunciGroups[groupIndex] || [];
                    const savedKunci = kForGroup[si] ?? kForGroup[si + 1] ?? '';
                    if (String(jenis) === 'uraian') {
                        wrapper.innerHTML += `
                        <label class="mt-2">Panduan / Kunci Jawaban</label>
                        <textarea class="form-control" name="kunci[${groupIndex}][]"
                          placeholder="Kunci jawaban">${escapeHtml(savedKunci)}</textarea>
                    `;
                    } else {
                        wrapper.innerHTML += `
                        <label class="mt-2">Kunci Jawaban</label>
                        <input type="text" class="form-control" name="kunci[${groupIndex}][]"
                          placeholder="Kunci jawaban" value="${escapeAttr(savedKunci)}">
                    `;
                    }
                }

                container.appendChild(wrapper);
                soalCounter++; // increment global
            }
        });
    }

    /** run on DOM ready */
    document.addEventListener('DOMContentLoaded', function () {
        console.log('draftData', draftData);
        generateSoalFieldFromDraft(draftData);
    });

</script>
<?= $this->endSection() ?>