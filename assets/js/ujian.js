// ============================
// Global functions
// ============================
function updateWarnaNomor(idSoal, status) {
    const btn = document.querySelector(`.soal-nav[data-idsoal="${idSoal}"]`);
    if (!btn) return;
    btn.className = `btn m-1 soal-nav px-3 ${
        status === 'jawab' ? 'btn-success' :
        status === 'ragu' ? 'btn-warning' :
        'btn-outline-secondary'
    }`;
}

// Tandai jawaban siswa & warna nomor soal
function rewrite_jawaban() {
    if (!window.jawabanSiswa) return;

    for (const idDetail in window.jawabanSiswa) {
        const soalPerDetail = window.jawabanSiswa[idDetail];

        for (const idSoal in soalPerDetail) {
            const dataSoal = soalPerDetail[idSoal];
            if (!dataSoal) continue;

            const jenis = dataSoal.jenis || 'uraian';
            const isi = dataSoal.isi || '';
            const ragu = parseInt(dataSoal.ragu || 0);

            // Pilihan ganda
            if (jenis === 'pilihan_ganda') {
                const radio = document.querySelector(
                    `input[name="jawaban[${idSoal}]"][value="${isi}"]`
                );
                if (radio) radio.checked = true;
            }

            // Isian / uraian
            if (jenis === 'isian' || jenis === 'uraian') {
                const input = document.querySelector(`.soal-item[data-id_soal="${idSoal}"] .jawaban-input`);
                if (input) input.value = isi;
            }

            // Checkbox ragu
            const checkboxRagu = document.querySelector(`.ragu-check[data-idsoal="${idSoal}"]`);
            if (checkboxRagu) checkboxRagu.checked = ragu === 1;

            // Update warna tombol
            let status;
            if (isi !== '') {
                status = ragu === 1 ? 'ragu' : 'jawab';
            } else {
                status = 'kosong';
            }

            updateWarnaNomor(idSoal, status);
            updateProgressBar();
        }
    }
}

// ============================
// Update progress bar
// ============================

const progressBar = document.getElementById('progressBar');
const progressCount = document.getElementById('progressCount');
const semuaSoal = document.querySelectorAll('.soal-nav');
const totalSoal = semuaSoal.length;
function updateProgressBar() {
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

// ============================
// DOMContentLoaded
// ============================
document.addEventListener('DOMContentLoaded', () => {
    const soalItems = document.querySelectorAll('.soal-item');
    const toastContainer = document.getElementById('toastContainer');
    const toastEl = document.getElementById('toastJawaban');
    toastContainer.appendChild(toastEl);

    const toast = new bootstrap.Toast(toastEl, { delay: 2000 });

    let currentIndex = 0;
    const total = soalItems.length;

    // ============================
    // Tampilkan soal sesuai index
    // ============================
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const btnSelesai = document.getElementById('btnSelesai');

    const showSoal = (index) => {
        soalItems.forEach((el, i) => el.classList.toggle('d-none', i !== index));
        currentIndex = index;
        const terjawab = updateProgressBar();
        prevBtn.disabled = index === 0;
        if (index === totalSoal - 1) {
            nextBtn.classList.add('d-none');      // sembunyikan Next
            if (terjawab===totalSoal) {
                btnSelesai.classList.remove('d-none'); // tampilkan tombol Selesai
            }
        } else {
            nextBtn.classList.remove('d-none');   // tampilkan Next
            btnSelesai.classList.add('d-none');   // sembunyikan Selesai
        }
    };

    // ============================
    // Simpan jawaban ke server
    // ============================
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
                updateWarnaNomor(idSoal, ragu ? 'ragu' : jawaban ? 'jawab' : 'kosong');
                const terjawab = updateProgressBar();
                btnSelesai.classList.toggle('d-none', terjawab !== totalSoal);
            }
        } catch (err) {
            console.error('Gagal simpan jawaban:', err);
        }
    };

    // ============================
    // Auto-save saat input berubah
    // ============================
    document.body.addEventListener('change', (e) => {
        const el = e.target;

        if (el.classList.contains('jawaban-input')) {
            const idSoal = el.dataset.idsoal;
            const jenis = el.dataset.jenis;
            const jawaban = el.type === 'radio'
                ? document.querySelector(`input[name="jawaban[${idSoal}]"]:checked`)?.value || ''
                : el.value;
            simpanJawaban(idSoal, jawaban, jenis, 
                document.querySelector(`#ragu_${idSoal}`)?.checked ? 1 : 0
            );
        }

        if (el.classList.contains('ragu-check')) {
            const idSoal = el.dataset.idsoal;
            const soal = document.querySelector(`[data-id_soal="${idSoal}"]`);
            const jawaban = soal?.querySelector('.jawaban-input')?.value || '';
            const jenis = soal?.dataset.jenis || 'uraian';
            simpanJawaban(idSoal, jawaban, jenis, el.checked ? 1 : 0);
        }
    });

    // ============================
    // Navigasi tombol
    // ============================
    prevBtn.onclick = () => currentIndex > 0 && showSoal(currentIndex - 1);
    nextBtn.onclick = () => currentIndex < total - 1 && showSoal(currentIndex + 1);
    var idDetail = $("#inp-id-detail").val();
    var idSiswa = $("#inp-id-siswa").val();
    btnSelesai.onclick = () => {
        swal({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menyimpan data ini?",
            icon: "warning",
            buttons: {
                cancel: "Batal",
                confirm: "Simpan"
            },
            dangerMode: true,
        })
        .then((willSave) => {
            if (willSave) {
                // Contoh proses simpan data dengan Ajax
                $.ajax({
                    url: finishUrl, // ganti dengan endpoint Anda
                    method: 'POST',
                    data: { id_detail:idDetail,id_siswa:idSiswa },
                    success: function(response) {
                        swal({
                            title: "Berhasil",
                            text: "Data berhasil disimpan!",
                            icon: "success",
                        }).then(() => {
                            window.location.href = logoutUrl; // Beranda ujian
                        });
                    },
                    error: function() {
                        swal("Gagal", "Data gagal disimpan!", "error");
                    }
                });
            } else {
                swal("Dibatalkan", "Data tidak disimpan", "info");
            }
        });
    };


    // ============================
    // Klik tombol nomor soal
    // ============================
    document.querySelectorAll('.soal-nav').forEach(btn => {
        btn.onclick = () => showSoal(currentIndex = +btn.dataset.target);
    });

    // ============================
    // Tampilkan soal pertama & warnai
    // ============================
    showSoal(0);
    updateProgressBar();
    rewrite_jawaban();
});
