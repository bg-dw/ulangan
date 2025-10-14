function get_data(idDetail) {
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

function initNilaiDariTabel() {
    const dataKirim = [];

    $("tr[data-id-hasil]").each(function () {
        const id_hasil = $(this).data("id-hasil");
        const nilaiAwal = {};
        let totalSkor = 0;

        $(this).find(".input-nilai").each(function () {
            const no = $(this).data("no");
            const nilai = parseFloat($(this).val()) || 0;
            const jenis = $(this).data("jenis") ?? "pilihan_ganda";
            nilaiAwal[no] = { jenis, nilai };
            totalSkor += nilai;
        });

        const persentase = ((totalSkor / nilai_max) * 100).toFixed(1);

        dataKirim.push({
            id_hasil: id_hasil,
            nilai: nilaiAwal,
            total_skor: totalSkor,
            persentase: persentase
        });
    });

    $.ajax({
        url: initUrl,
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ data: dataKirim }),
        success: function (res) {
            console.log("Init nilai (dari tabel)");
        },
        error: (err) => console.error("Init error:", err)
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

    // --- BUILD QUESTION MAP ONCE ---
    // counters per jenis untuk menentukan posisi kunci di masing-masing jenis
    const counters = {};
    // normalize jenis_soal to lower-case trimmed array for robust matching
    const jenisNormalized = (jenis_soal || []).map(j => String(j).toLowerCase().trim());

    // initialize counters for all known jenis
    jenisNormalized.forEach(j => counters[j] = 0);

    // questionMap indexed by question number (1..totalSoal)
    const questionMap = {}; // questionMap[i] = { jenis, indexBobot, posisiKunci }
    for (let i = 1; i <= totalSoal; i++) {
        const jenisRaw = (firstJawaban[i]?.jenis ?? 'pilihan_ganda');
        const jenis = String(jenisRaw).toLowerCase().trim();
        let indexBobot = jenisNormalized.indexOf(jenis);
        if (indexBobot === -1) indexBobot = 0;
        // ensure counter exists
        if (typeof counters[jenis] === 'undefined') counters[jenis] = 0;
        const posisiKunci = counters[jenis];
        counters[jenis]++; // advance for next question of same jenis
        questionMap[i] = { jenis, indexBobot, posisiKunci };
    }
    // --- END QUESTION MAP ---

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

        let totalSkor = 0;

        for (let i = 1; i <= totalSoal; i++) {
            const isi = (jawaban[i]?.isi ?? '-').trim();
            const ragu = jawaban[i]?.ragu == 1;

            // use precomputed questionMap
            const q = questionMap[i] || { jenis: 'pilihan_ganda', indexBobot: 0, posisiKunci: 0 };
            const jenis = q.jenis;
            const indexBobot = q.indexBobot;
            const posisiKunci = q.posisiKunci;

            const bobotJenis = (bobot && bobot[indexBobot]) ? bobot[indexBobot] : 1;
            const kunciJawaban = (kunci && kunci[indexBobot] && typeof kunci[indexBobot][posisiKunci] !== 'undefined')
                ? String(kunci[indexBobot][posisiKunci]).trim()
                : '-';

            let nilai = 0;
            if (isi && kunciJawaban !== '-' && isi.toLowerCase() === kunciJawaban.toLowerCase()) {
                nilai = bobotJenis;
            }

            const nilaiTersimpan = (nilai_tersimpan[siswa.id_hasil] ?? {});
            // nilai_tersimpan likely keyed by question number, so keep using i
            const nilaiAwal = (nilaiTersimpan[i]?.nilai ?? nilai);
            totalSkor += parseFloat(nilaiAwal) || 0;

            const warna = isi === '-' ? 'background-color:#eee' :
                (kunciJawaban !== '-' && isi.toLowerCase() === kunciJawaban.toLowerCase()) ? 'background-color:#b2f7b2' :
                'background-color:#f7b2b2';

            const tampilKunci = `
                <div style="margin-top:3px;font-size:12px;background-color:#007bff;color:white;padding:1px 4px;border-radius:3px;display:inline-block;">
                    ${jenis === 'pilihan_ganda' ? (kunciJawaban || '-').toUpperCase() : (kunciJawaban || '-')}
                </div>
            `;

            row += `
                <td style="${warna}; text-align:center; vertical-align:middle;">
                    <div style="display:flex; flex-direction:column; align-items:center; gap:3px;">
                        <div style="font-size:13px;">${isi || '-'}</div>
                        ${tampilKunci}
                        <input type="number" step="1" min="0" 
                            max="${bobotJenis}" 
                            value="${nilaiAwal}" 
                            class="form-control form-control-sm text-center skor-input input-nilai" 
                            data-no="${i}" 
                            data-jenis="${jenis}" 
                            style="width:60px; padding:2px 4px; font-size:13px;">
                    </div>
                </td>
            `;
        }

        const persentase = ((totalSkor / nilai_max) * 100).toFixed(1);
        row += `
            <td class="text-center fw-bold">
                <div style="display:flex; flex-direction:column; align-items:center; gap:2px;">
                    <div class="formula-text" style="font-size:13px; color:#555;">
                        (${totalSkor.toFixed(1)} / ${nilai_max}) × 100
                    </div>
                    <input type="number" step="0.1" min="0" max="100"
                        value="${persentase}"
                        class="form-control form-control-sm text-center total-skor"
                        style="width:80px; font-weight:bold; background-color:#f8f9fa;" readonly>
                </div>
            </td>
        </tr>`;
        rows += row;
    });

    $('#bodyHasil').html(rows);

    // 🔥 Setelah tabel selesai dirender, inisialisasi nilai aktual dari tabel (bukan 0)
    if (!sudahInitNilai) {
        initNilaiDariTabel();
        sudahInitNilai = true;
    }
}


// Event listener perubahan nilai input
$(document).off('input', '.skor-input').on('input', '.skor-input', function () {
    const row = $(this).closest('tr');
    const id_hasil = row.data('id-hasil');
    const inputs = row.find('.skor-input');

    const nilai = {};
    let total = 0;
    inputs.each(function (i) {
        const jenis = $(this).data('jenis');
        const val = parseFloat($(this).val()) || 0;
        nilai[i + 1] = { jenis: jenis, nilai: val };
        total += val;
    });

    const persentase = ((total / nilai_max) * 100).toFixed(1);
    row.find('.total-skor').val(persentase);
    row.find('.formula-text').text(`(${total.toFixed(1)} / ${nilai_max}) × 100`);

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
    get_data(idDetail);
});

