<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h4>Daftar Soal Ujian</h4>
                <div class="card-header-action" role="group" aria-label="Basic example" id="group-btn">
                    <button type="button" class="btn btn-primary" id="btn-add">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="card-body" id="tbl-data">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-ujian">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    No.
                                </th>
                                <th class="text-center">Aksi</th>
                                <th class="text-center">Detail Ujian</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            if (isset($ujian) && count($ujian) > 0) {
                                foreach ($ujian as $row) {
                                    ?>
                                    <tr>
                                        <td class="text-center" style="width: 7%">
                                            <?= $i++ . "."; ?>
                                        </td>
                                        <td class="text-center aksi-col-<?= $row['id_ujian_detail'] ?>" style="width: 13%">
                                            <button class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit ujian"
                                                onclick="update_ujian('<?= $row['id_ujian_detail'] ?>','<?= $row['id_ujian'] ?>')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Hapus ujian"
                                                onclick="hapus('<?= $row['id_ujian_detail'] ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <span class="badge badge-primary">
                                                        <?= date("d F Y", strtotime($row['tgl'])) ?></span>
                                                </div>
                                                <div class="col-md-9">
                                                    <strong>
                                                        <?= $row['judul'] ?> - <?= $row['mapel'] ?>
                                                    </strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <select class="form-control form-control-sm status-select"
                                                data-id="<?= $row['id_ujian_detail'] ?>">
                                                <option value="final" <?= $row['status'] == 'final' ? 'selected' : '' ?>>Final
                                                </option>
                                                <option value="dikerjakan" <?= $row['status'] == 'dikerjakan' ? 'selected' : '' ?>>
                                                    Dikerjakan</option>
                                                <option value="selesai" <?= $row['status'] == 'selesai' ? 'selected' : '' ?>>
                                                    Selesai</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <?= date('d F Y', strtotime($row['tgl'])) ?>
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
                <form action="<?= base_url('/' . bin2hex('soal-ujian') . '/' . bin2hex('add')) ?>" method="post"
                    onsubmit="return confirm('Simpan Data?')">
                    <div class="form-row">
                        <div class="form-group col-md-9">
                            <label for="i-sel-ujian">Ujian</label>
                            <select class="form-control" name="id-ujian" id="i-sel-ujian" required>
                                <?php foreach ($soal as $row): ?>
                                    <option value="<?= $row['id_ujian'] ?>">
                                        <?= $row['judul'] . " - " . $row['mapel'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <button class="btn btn-secondary" type="button" id="btn-cancel">Batal</button>
                    </div>
                </form>
            </div>
            <div class="card-body" id="f-update" style="display:none;">
                <form action="<?= base_url('/' . bin2hex('soal-ujian') . '/' . bin2hex('update')) ?>" method="post"
                    onsubmit="return confirm('Simpan Data?')">
                    <input type="hidden" name="id" id="u-inp-id" required>
                    <div class="form-row">
                        <div class="form-group col-md-9">
                            <label for="u-sel-ujian">Ujian</label>
                            <select class="form-control" name="id-ujian" id="u-sel-ujian" required>
                                <?php foreach ($soal as $row): ?>
                                    <option value="<?= $row['id_ujian'] ?>">
                                        <?= $row['judul'] . " - " . $row['mapel'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <button class="btn btn-secondary" type="button" onclick="cancel()">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete-modal" tabindex="-2" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('/' . bin2hex('soal-ujian') . '/' . bin2hex('delete')) ?>" method="post"
                enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" class="form-control" required id="d-id">
                    <center>
                        <h4>Hapus data terpilih?</h4>
                    </center>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="submit" class="btn btn-primary">Ya</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#table-ujian').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            language: {
                emptyTable: "Tidak ada data tersedia"
            }
        });

        $(document).on("change", ".status-select", function () {
            let select = $(this);
            let idUjian = select.data("id");
            let statusBaru = select.val();
            let statusLama = select.find("option[selected]").val();

            if (confirm("Apakah Anda yakin ingin mengubah status menjadi " + statusBaru.toUpperCase() + " ?")) {
                $.ajax({
                    url: "<?= base_url('/' . bin2hex('soal-ujian') . '/' . bin2hex('update-status')) ?>",
                    type: "POST",
                    data: { id: idUjian, status: statusBaru },
                    success: function (res) {
                        alert("Status berhasil diperbarui!");

                        // update option[selected]
                        select.find("option").removeAttr("selected");
                        select.find("option[value='" + statusBaru + "']").attr("selected", true);

                        // === manipulasi tombol edit/hapus ===
                        let aksiCol = $(".aksi-col-" + idUjian);

                        if (statusBaru === "dikerjakan" || statusBaru === "selesai") {
                            aksiCol.html('<span class="badge badge-info">Tidak bisa diubah</span>');
                        } else {
                            aksiCol.html(`
                        <button class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit ujian"
                            onclick="update_ujian('${idUjian}','dummy','dummy','2025-10-01')">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Hapus ujian"
                            onclick="hapus('${idUjian}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    `);
                        }
                    },
                    error: function () {
                        alert("Gagal update status.");
                        select.val(statusLama);
                    }
                });
            } else {
                select.val(statusLama);
            }
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

    function update_ujian(id_detail, id_ujian) {
        $('#tbl-data').hide('slow');
        $('#group-btn').hide('slow');
        $('#f-update').show('slow');
        $('#u-inp-id').val(id_detail);
        $('#u-sel-ujian').val(id_ujian).change();
    }

    function cancel() {
        $('#f-update').hide('slow');
        $('#group-btn').show('slow');
        $('#tbl-data').show('slow');
    }

    function hapus(id) {
        $('#d-id').val(id);
        $('#delete-modal').appendTo('body').modal('show');
    }
</script>
<?= $this->endSection() ?>