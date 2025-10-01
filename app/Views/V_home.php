<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h4>Data Peserta Didik</h4>
                <div class="card-header-action" role="group" aria-label="Basic example" id="group-btn">
                    <button type="button" class="btn btn-primary" id="btn-add">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="card-body" id="tbl-data">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-siswa">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    No.
                                </th>
                                <th class="text-center">Aksi</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            if (isset($siswa) && count($siswa) > 0) {
                                foreach ($siswa as $row) {
                                    ?>
                                    <tr>
                                        <td class="text-center" style="width: 7%">
                                            <?= $i++ . "."; ?>
                                        </td>
                                        <td class="text-center" style="width: 13%">
                                            <button class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit Siswa"
                                                onclick="update_siswa('<?= $row['id_siswa'] ?>','<?= $row['nama_siswa'] ?>','<?= $row['jk'] ?>')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Hapus Siswa"
                                                onclick="hapus('<?= $row['id_siswa'] ?>','<?= $row['nama_siswa'] ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <b><?= $row['nama_siswa'] ?></b>
                                        </td>
                                        <td class="text-center" style="width: 13%">
                                            <span class="badge status-badge <?= $row['status_login'] == 'enable' ? 'badge-success' : 'badge-danger' ?>" data-id="<?= $row['id_siswa'] ?>"
                                                data-status="<?= $row['status_login'] ?>">
                                                <?= ucfirst($row['status_login']) ?>
                                            </span>
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
                <form action="<?= base_url('/' . bin2hex('siswa') . '/' . bin2hex('add')) ?>" method="post"
                    onsubmit="return confirm('Simpan Data?')">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inp-nama">Nama Siswa</label>
                            <input type="text" name="nama" class="form-control" id="inp-nama" placeholder="Midas"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Jenis Kelamin</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="jk" type="radio" id="jk-l" value="L" checked>
                                <label class="form-check-label" for="jk-l">Laki - Laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="jk" type="radio" id="jk-p" value="P">
                                <label class="form-check-label" for="jk-p">Perempuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-primary" type="submit">Simpan</button>
                        <button class="btn btn-secondary" type="button" id="btn-cancel">Batal</button>
                    </div>
                </form>
            </div>
            <div class="card-body" id="f-update" style="display:none;">
                <form action="<?= base_url('/' . bin2hex('siswa') . '/' . bin2hex('update')) ?>" method="post"
                    onsubmit="return confirm('Simpan Data?')">
                    <div class="form-row">
                        <input type="hidden" name="id" id="u-inp-id" required>
                        <div class="form-group col-md-6">
                            <label for="u-inp-nama">Nama Siswa</label>
                            <input type="text" name="nama" class="form-control" id="u-inp-nama" placeholder="Midas"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Jenis Kelamin</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="jk" type="radio" id="u-jk-l" value="L">
                                <label class="form-check-label" for="u-jk-l">Laki - Laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" name="jk" type="radio" id="u-jk-p" value="P">
                                <label class="form-check-label" for="u-jk-p">Perempuan</label>
                            </div>
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
            <form action="<?= base_url('/' . bin2hex('siswa') . '/' . bin2hex('delete')) ?>" method="post"
                enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" class="form-control" required id="d-id">
                    <div class="form-group">
                        <label>Nama Siswa</label>
                        <input type="text" name="nama" class="form-control" disabled id="d-nama">
                    </div><br>
                    <center>
                        <h4>Seluruh data yang berkaitan dengan Siswa diatas akan terhapus. Hapus data?</h4>
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
        $('#table-siswa').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            language: {
                emptyTable: "Tidak ada data tersedia"
            }
        });

        $(document).on("click", ".status-badge", function () {
            let $this = $(this);
            let id = $this.data("id");
            let currentStatus = $this.data("status");
            let buttons = {
                cancel: {
                    text: "Batal",
                    visible: true,
                    className: "btn btn-secondary"
                }
            };

            // jika status sekarang "enable", hanya tampilkan disable
            if (currentStatus === "enable") {
                buttons.disable = {
                    text: "Disable",
                    value: "disable",
                    className: "btn btn-danger"
                };
            } else {
                // kalau status "disable", hanya tampilkan enable
                buttons.enable = {
                    text: "Enable",
                    value: "enable",
                    className: "btn btn-success"
                };
            }

            swal({
                title: "Ubah Status",
                text: "Pilih status baru untuk siswa ini",
                icon: "warning",
                buttons: buttons
            }).then((value) => {
                if (value) {
                    $.post("<?= base_url('/' . bin2hex('siswa') . '/' . bin2hex('update-status')) ?>", {
                        id: id,
                        status: value
                    }, function (res) {
                        if (res.success) {
                            $this.text(value.charAt(0).toUpperCase() + value.slice(1));
                            $this.data("status", value);
                            $this.removeClass("badge-success badge-danger");
                            $this.addClass(value === "enable" ? "badge-success" : "badge-danger");

                            swal("Berhasil", "Status berhasil diperbarui", "success");
                        } else {
                            swal("Gagal", res.message, "error");
                        }
                    }, "json");
                }
            });
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

    function update_siswa(id, nama, jk) {
        $('#tbl-data').hide('slow');
        $('#group-btn').hide('slow');
        $('#f-update').show('slow');
        $('#u-inp-id').val(id);
        $('#u-inp-nama').val(nama);
        if (jk == 'L') {
            $('#u-jk-l').prop('checked', true);
        } else {
            $('#u-jk-p').prop('checked', true);
        }
    }

    function cancel() {
        $('#f-update').hide('slow');
        $('#group-btn').show('slow');
        $('#tbl-data').show('slow');
    }

    function hapus(id, nama) {
        $('#d-id').val(id);
        $('#d-nama').val(nama);
        $('#delete-modal').appendTo('body').modal('show');
    }
</script>
<?= $this->endSection() ?>