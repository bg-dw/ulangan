function get_data() {
    $.ajax({
        url: dataUrl,
        method: 'POST',
        data: { id_detail: idDetail },
        success: function (data) {
            renderTable(data);
        },
        error: function (xhr, status, error) {
            console.error('Gagal mengambil data:', error);
        }
    });
}

function initNilaiKeServer(data) {
    const dataKirim = data.map(siswa => {
        let jawaban = {};
        try { jawaban = JSON.parse(siswa.jawaban); } catch (e) {}

        const nilaiAwal = {};
        Object.keys(jawaban).forEach(no => {
            const jenis = jawaban[no]?.jenis ?? 'pilihan_ganda';
            const nilai = 0; // awalnya 0, nanti bisa pakai auto-score jika mau
            nilaiAwal[no] = { jenis, nilai };
        });

        return {
            id_hasil: siswa.id_hasil,
            nilai: nilaiAwal
        };
    });

    $.ajax({
        url: initUrl,
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ data: dataKirim }),
        success: function (res){
            // console.log('Init nilai:', res)
        } ,
        error: (err) => console.error('Init error:', err)
    });
}


function renderTable(data) {
    if (!data || data.length === 0) return;

    let firstJawaban = {};
    try { firstJawaban = JSON.parse(data[0].jawaban); } catch (e) { return; }

    const totalSoal = Object.keys(firstJawaban).length;

    // Header nomor soal
    let headerSoal = '';
    for (let i = 1; i <= totalSoal; i++) {
        headerSoal += `<th class="text-center">${i}</th>`;
    }
    headerSoal += `<th class="text-center">Total</th>`;
    $('#headerSoal').html(headerSoal);
    $('#judulSoal').attr('colspan', totalSoal + 1);

    let rows = '';

    data.forEach((siswa, index) => {
        let row = `
            <tr data-index="${index}" data-id-hasil="${siswa.id_hasil}">
                <td>${index + 1}</td>
                <td>${siswa.nama_siswa}</td>
                <td>${siswa.status}</td>
                <td>${formatUnixTime(siswa.log)}</td>
            `;

        let jawaban = {};
        try { jawaban = JSON.parse(siswa.jawaban); } catch (e) { jawaban = {}; }

        // Counter agar posisi kunci per jenis benar
        let counterPG = 0;
        let counterIsian = 0;
        let counterUraian = 0;

        let totalSkor = 0;

        for (let i = 1; i <= totalSoal; i++) {
        const isi = (jawaban[i]?.isi ?? '-').trim();
        const ragu = jawaban[i]?.ragu == 1;
        const jenis = (jawaban[i]?.jenis ?? 'pilihan_ganda').toLowerCase();

        let indexBobot = jenis_soal.indexOf(jenis);
        if (indexBobot === -1) indexBobot = 0;

        const bobotJenis = bobot[indexBobot]
            || bobot[jenis.replace(/[^a-z]/g, '')]
            || 1;

        let grupIndex = 0;
        let idxDalamGrup = 0;
        if (jenis.includes('isian')) {
            grupIndex = 1;
            idxDalamGrup = counterIsian++;
        } else if (jenis.includes('uraian')) {
            grupIndex = 2;
            idxDalamGrup = counterUraian++;
        } else {
            grupIndex = 0;
            idxDalamGrup = counterPG++;
        }

        const kunciJawaban = (kunci[grupIndex]?.[idxDalamGrup] ?? '-').trim();

        // ✅ Nilai otomatis (berdasarkan jawaban benar/salah)
        let nilai = 0;
        if (isi === '-' || isi === '') {
            // kosong
        } else if (kunciJawaban === '-' || kunciJawaban === '') {
            // tidak ada kunci
        } else if (isi.toLowerCase() === kunciJawaban.toLowerCase()) {
            nilai = bobotJenis;
        }

        // ✅ Ambil nilai dari DB jika sudah ada
        const nilaiTersimpan = (nilai_tersimpan[siswa.id_hasil] ?? {});
        const nilaiAwal = (nilaiTersimpan[i]?.nilai ?? nilai); // <-- DIDEFINISIKAN DI SINI

        const warna = isi === '-' ? 'background-color:#eee' :
                    isi.toLowerCase() === kunciJawaban.toLowerCase() ? 'background-color:#b2f7b2' :
                    'background-color:#f7b2b2';

        const borderRagu = ragu ? 'border:2px solid orange;' : '';
        const inputWidth = jenis === 'uraian' ? '70px' : jenis === 'isian' ? '60px' : '50px';

        const tampilKunci = `
            <div style="
                margin-top:3px;
                font-size:12px;
                background-color:#007bff;
                color:white;
                padding:1px 4px;
                border-radius:3px;
                display:inline-block;">
                ${kunciJawaban || '-'}
            </div>
        `;

        row += `
            <td style="${warna};${borderRagu}; text-align:center; vertical-align:middle;">
                <div style="display:flex; flex-direction:column; align-items:center; gap:3px;">
                    <div style="font-size:13px;">${isi || '-'}</div>
                    ${tampilKunci}
                    <input type="number" step="1" min="0" 
                        max="${bobot[indexBobot]}" 
                        value="${nilaiAwal}" 
                        class="form-control form-control-sm text-center skor-input" 
                        data-jenis="${jenis}" 
                        style="width:${inputWidth}; padding:2px 4px; font-size:13px;">
                </div>
            </td>
        `;

        totalSkor += parseFloat(nilaiAwal);
    }


        // Kolom total skor (menampilkan (total/nilai_max)*100)
        const persentase = ((totalSkor / nilai_max) * 100).toFixed(1);
        row += `
            <td class="text-center fw-bold">
                <div style="display:flex; flex-direction:column; align-items:center; gap:2px;">
                    <div class="formula-text" style="font-size:13px; color:#555;">
                        (${totalSkor.toFixed(1)} / ${nilai_max}) × 100
                    </div>

                    <input type="number" 
                        step="0.1" 
                        min="0" 
                        max="100"
                        value="${persentase}"
                        class="form-control form-control-sm text-center total-skor"
                        style="width:80px; font-weight:bold; background-color:#f8f9fa;"
                        readonly>
                </div>
            </td>
            </tr>`;
        rows += row;
        initNilaiKeServer(data);
    });

    $('#bodyHasil').html(rows);


    // Hentikan handler lama dan pasang satu handler yang bersih
    $(document).on('input', '.skor-input', function () {
        const row = $(this).closest('tr');
        const id_hasil = row.data('id-hasil');
        const inputs = row.find('.skor-input');

        // Kumpulkan semua nilai per soal
        const nilai = {};
        let total = 0;
        inputs.each(function (i) {
            const jenis = $(this).data('jenis');
            const val = parseFloat($(this).val()) || 0;
            nilai[i + 1] = { jenis: jenis, nilai: val };
            total += val;
        });

        // Hitung persentase
        const persentase = ((total / nilai_max) * 100).toFixed(1);

        // 🔥 Update tampilan langsung di tabel
        const totalInput = row.find('.total-skor');
        if (totalInput.length) {
            totalInput.val(persentase);
        } else {
            // fallback kalau struktur kolom berubah
            row.find('.total-skor input').val(persentase);
        }

        // Kirim data ke server
        $.ajax({
            url: updateUrl,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id_hasil: id_hasil,
                nilai: nilai,
                total_skor: total,
                persentase: persentase
            }),
            success: function (res) {
                if (res.status === 'ok') {
                    showToast(`Nilai ID ${id_hasil} berhasil disimpan`, true);
                } else {
                    showToast(res.message || 'Gagal menyimpan nilai', false);
                }
            },
            error: function () {
                showToast('Terjadi kesalahan server saat menyimpan nilai', false);
            }
        });
    });

}

function formatUnixTime(unix) {
    // Cek dulu apakah ada nilai dan bisa diubah jadi angka
    if (!unix || isNaN(unix)) return "";

    const date = new Date(parseInt(unix) * 1000);
    if (isNaN(date.getTime())) return "";

    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function showToast(message, success = true) {
    const toastEl = document.getElementById('toastStatus');
    const toastBody = toastEl.querySelector('.toast-body');

    // ubah warna background
    toastEl.classList.remove('bg-success', 'bg-danger');
    toastEl.classList.add(success ? 'bg-success' : 'bg-danger');

    toastEl.classList.remove('bg-success', 'bg-danger');
    toastEl.classList.add(success ? 'bg-success' : 'bg-danger');
    toastBody.textContent = message;

    const toast = new bootstrap.Toast(toastEl, {
        delay: 2000 // 🕐 tampil 5 detik
    });
    toast.show();
}


document.addEventListener("DOMContentLoaded", function () {
    get_data();
});