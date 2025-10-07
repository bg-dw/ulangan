document.addEventListener('DOMContentLoaded', () => {
    const soalItems = document.querySelectorAll('.soal-item');
    const progressBar = document.getElementById('progressBar');
    const progressCount = document.getElementById('progressCount');
    const toastEl = document.getElementById('toastJawaban');
    const toast = new bootstrap.Toast(toastEl, { delay: 1500 });

    let currentIndex = 0;
    const total = soalItems.length;

    // Fungsi tampilkan soal sesuai index
    const showSoal = (index) => {
        soalItems.forEach((el, i) => el.classList.toggle('d-none', i !== index));
        document.getElementById('prevBtn').disabled = index === 0;
        document.getElementById('nextBtn').disabled = index === total - 1;
        currentIndex = index;
    };

    // Update warna tombol nomor soal
    const updateWarnaNomor = (idSoal, status) => {
        const btn = document.querySelector(`.soal-nav[data-idsoal="${idSoal}"]`);
        if (!btn) return;
        btn.className = `btn m-1 soal-nav ${
            status === 'jawab' ? 'btn-success' :
            status === 'ragu' ? 'btn-warning' :
            'btn-outline-secondary'
        }`;
    };

    // Update progress bar dan count
    const updateProgressBar = () => {
        const semuaSoal = document.querySelectorAll('.soal-nav');
        const totalSoal = semuaSoal.length;
        const terjawab = Array.from(semuaSoal).filter(btn =>
            btn.classList.contains('btn-success') ||
            btn.classList.contains('btn-warning')
        ).length;

        const percent = Math.round((terjawab / totalSoal) * 100);
        progressBar.style.width = `${percent}%`;
        progressBar.textContent = `${percent}%`;
        progressCount.textContent = `${terjawab} / ${totalSoal} Soal`;

        return terjawab;
    };

    // Simpan jawaban ke server
    const simpanJawaban = async (idSoal, jawaban, jenis, ragu = 0) => {
        try {
            const form = new FormData();
            form.append('id_siswa', idSiswa);
            form.append('id_detail', idDetail);
            form.append('id_soal', idSoal);
            form.append('jawaban', jawaban);
            form.append('jenis_soal', jenis);
            form.append('ragu', ragu);

            const res = await fetch(baseUrl, { method: 'POST', body: form });
            const data = await res.json();

            if (data.success) {
                toast.show();
                updateWarnaNomor(idSoal, ragu ? 'ragu' : jawaban ? 'jawab' : 'none');

                const btnSelesai = document.getElementById('btnSelesai');
                const terjawab = updateProgressBar();

                // Tampilkan tombol selesai jika semua soal terjawab/ragu
                if (terjawab === total) btnSelesai.classList.remove('d-none');
                else btnSelesai.classList.add('d-none');
            }
        } catch (err) {
            console.error('Gagal simpan jawaban:', err);
        }
    };

    // Auto-save saat user pilih jawaban atau cek ragu
    document.body.addEventListener('change', (e) => {
        const el = e.target;

        if (el.classList.contains('jawaban-input')) {
            const idSoal = el.dataset.idsoal;
            const jenis = el.dataset.jenis;
            const jawaban = el.type === 'radio'
                ? document.querySelector(`input[name="jawaban[${idSoal}]"]:checked`)?.value || ''
                : el.value;
            simpanJawaban(idSoal, jawaban, jenis);
        }

        if (el.classList.contains('ragu-check')) {
            const idSoal = el.id.replace('ragu_', '');
            const soal = document.querySelector(`[data-id_soal="${idSoal}"]`);
            const jawaban = soal?.querySelector('.jawaban-input')?.value || '';
            const jenis = soal?.dataset.jenis || 'uraian';
            simpanJawaban(idSoal, jawaban, jenis, el.checked ? 1 : 0);
        }
    });

    // Navigasi Next/Prev
    document.getElementById('prevBtn').onclick = () => currentIndex > 0 && showSoal(currentIndex - 1);
    document.getElementById('nextBtn').onclick = () => currentIndex < total - 1 && showSoal(currentIndex + 1);

    // Klik tombol nomor soal
    document.querySelectorAll('.soal-nav').forEach(btn => {
        btn.onclick = () => showSoal(currentIndex = +btn.dataset.target);
    });

    // Tombol selesai ujian
    document.getElementById('btnSelesai').onclick = () => {
        if (confirm('Apakah Anda yakin ingin mengakhiri ujian ini?')) {
            window.location.href = finishUrl;
        }
    };

    // Tampilkan soal pertama
    showSoal(0);
    updateProgressBar();

    if (!window.jawabanSiswa) return;

    for (const nomorSoal in window.jawabanSiswa) {
        const dataSoal = window.jawabanSiswa[nomorSoal];

        // tandai radio button pilihan ganda
        if (dataSoal.jenis === 'pilihan_ganda') {
            const radio = document.querySelector(
                `input[name="jawaban[${nomorSoal}]"][value="${dataSoal.isi}"]`
            );
            if (radio) radio.checked = true;
        }

         // tandai checkbox ragu-ragu
        const checkboxRagu = document.querySelector(
            `.ragu-check[data-idsoal="${nomorSoal}"]`
        );
        if (checkboxRagu) {
            checkboxRagu.checked = dataSoal.ragu && dataSoal.ragu == 1;
        }

        // update warna tombol soal
        let status;
        if (dataSoal.isi && dataSoal.isi !== "") {
            status = dataSoal.ragu && dataSoal.ragu == 1 ? 'ragu' : 'jawab';
        } else {
            status = 'kosong';
        }

        if (typeof updateWarnaNomor === 'function') {
            updateWarnaNomor(nomorSoal, status);
        }
    }
});
