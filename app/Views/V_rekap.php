<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Rekap Data</h4>
            </div>
            <div class="card-body" id="tbl-data">
                <div class="table-responsive">
                    <table class="table table-striped" id="table-hasil">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    No.
                                </th>
                                <th class="text-center">Aksi</th>
                                <th class="text-center">Judul</th>
                                <th class="text-center">Mata Pelajaran</th>
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
                                        <td class="text-center" style="width: 13%">
                                            <button class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit ujian"
                                                onclick="update_ujian('<?= $row['id_ujian'] ?>','<?= $row['id_judul'] ?>','<?= $row['id_mapel'] ?>','<?= $row['tgl'] ?>')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <?= $row['judul'] ?>
                                        </td>
                                        <td><span class="badge badge-primary"><?= $row['mapel'] ?></span>
                                        </td>
                                        <td class="text-center"><span
                                                class="badge badge-secondary"><?= strtoupper($row['status']) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?= $row['tgl'] ?>
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
                <form action="<?= base_url('/' . bin2hex('hasil') . '/' . bin2hex('update')) ?>" method="post"
                    onsubmit="return confirm('Simpan Data?')">
                    <div class="form-row">
                        <input type="hidden" name="id" id="u-inp-id" required>
                        <div class="form-group col-md-6">
                            <label for="u-inp-nama">Nama hasil</label>
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
<script>
    $(document).ready(function () {
        $('#table-hasil').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            language: {
                emptyTable: "Tidak ada data tersedia"
            }
        });
    });

    function update_hasil(id, nama, jk) {
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