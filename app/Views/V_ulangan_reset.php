<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h4>Reset Login</h4>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            if (isset($siswa)) {
                                foreach ($siswa as $row) {
                                    ?>
                                    <tr>
                                        <td class="text-center" style="width: 7%">
                                            <?= $i++ . "."; ?>
                                        </td>
                                        <td class="text-center" style="width: 13%">
                                            <button class="btn btn-sm btn-warning" data-toggle="tooltip" title="Reset Login"
                                                onclick="update_siswa('<?= $row['id_hasil'] ?>','<?= $row['nama_siswa'] ?>')">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <?= $row['nama_siswa'] ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-body" id="f-update" style="display:none;">
                <form action="<?= base_url('/' . bin2hex('reset-act')) ?>" method="post"
                    onsubmit="return confirm('Reset Login?')">
                    <div class="form-row">
                        <input type="hidden" name="id" id="u-inp-id" required>
                        <div class="form-group col-md-6">
                            <label for="u-inp-nama">Nama Siswa</label>
                            <input type="text" name="nama" class="form-control" id="u-inp-nama" placeholder="Midas"
                                required>
                        </div>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-primary" type="submit">Reset</button>
                        <button class="btn btn-secondary" type="button" onclick="cancel()">Batal</button>
                    </div>
                </form>
            </div>
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
    });
    $('#btn-cancel').click(function () {
        $('#f-add').hide('slow');
        $('#group-btn').show('slow');
        $('#tbl-data').show('slow');
    });

    function update_siswa(id, nama) {
        $('#tbl-data').hide('slow');
        $('#group-btn').hide('slow');
        $('#f-update').show('slow');
        $('#u-inp-id').val(id);
        $('#u-inp-nama').val(nama);
    }

    function cancel() {
        $('#f-update').hide('slow');
        $('#group-btn').show('slow');
        $('#tbl-data').show('slow');
    }
</script>
<?= $this->endSection() ?>