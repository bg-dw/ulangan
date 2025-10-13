<?php
// print_r($jawaban_siswa);
foreach ($soal as $index => $s):
    ?>
    <div class="soal-item <?= $index === 0 ? '' : 'd-none' ?>" data-index="<?= $index ?>"
        data-id_soal="<?= esc($s['id_soal']) ?>" data-jenis="<?= esc($s['jenis_soal']) ?>">

        <div class="mb-3">
            <button class="btn btn-info mb-3">
                <span class="fw-bold" style="font-size: 24px;"><b><?= $index + 1 ?></b></span>
            </button>
            <div class="jumbotron"><?= $s['pertanyaan'] ?></div>
        </div>
        <?php if ($s['jenis_soal'] === 'pilihan_ganda'): ?>
            <?php
            $hurufOpsi = ['A', 'B', 'C', 'D'];
            $i = 0;
            foreach (array_keys($s['opsi']) as $opsiText):
                $huruf = $hurufOpsi[$i] ?? chr(65 + $i);
                ?>
                <div class="form-check mb-1">
                    <label class="form-check-label">
                        <input type="radio" class="form-check-input jawaban-input" name="jawaban[<?= esc($s['id_soal']) ?>]"
                            value="<?= esc($huruf) ?>" data-idsoal="<?= esc($s['id_soal']) ?>" data-jenis="pilihan_ganda">
                        <?= $huruf ?>. <?= esc($opsiText) ?>
                    </label>
                </div>
                <?php $i++; endforeach; ?>

        <?php elseif ($s['jenis_soal'] === 'isian'): ?>
            <input type="text" class="form-control jawaban-input" data-idsoal="<?= esc($s['id_soal']) ?>" data-jenis="isian"
                placeholder="Ketik jawaban Anda di sini" value="<?= esc($s['jawaban_siswa'] ?? '') ?>">

        <?php elseif ($s['jenis_soal'] === 'uraian'): ?>
            <textarea class="form-control jawaban-input" data-idsoal="<?= esc($s['id_soal']) ?>" data-jenis="uraian" rows="4"
                placeholder="Tulis jawaban uraian Anda..."><?= esc($s['jawaban_siswa'] ?? '') ?></textarea>
        <?php endif; ?>

        <div class="mt-3">
            <div class="form-check">
                <input type="checkbox" class="form-check-input ragu-check" id="ragu_<?= esc($s['id_soal']) ?>"
                    data-idsoal="<?= esc($s['id_soal']) ?>">
                <label class="form-check-label" for="ragu_<?= esc($s['id_soal']) ?>">Ragu-ragu</label>
            </div>
        </div>

        <hr>
    </div>
<?php endforeach; ?>