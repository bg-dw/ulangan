<?= $this->extend('Main') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card card-primary">
            <div class="card-header">
                <h4>Data Soal</h4>
                <div class="card-header-action" role="group" aria-label="Basic example" id="group-btn">
                    <button type="button" class="btn btn-primary"
                        onclick="location.href='<?= base_url(bin2hex('data-soal') . '/' . bin2hex('add')) ?>'">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="tbl-soal">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th class="text-center">Aksi</th>
                                <th class="text-center">Judul</th>
                                <th class="text-center">Mata Pelajaran</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Update Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($soals as $d): ?>
                                <tr>
                                    <td class="text-center" style="width: 7%"><?= $i . "."; ?></td>
                                    <td class="text-center" style="width: 13%">
                                        <?php if ($d['status'] != "dikerjakan"): ?>
                                            <button class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit Soal"
                                                onclick="location.href='<?= base_url(bin2hex('data-draft') . '/' . bin2hex('edit') . '/' . $d['id_soal']) ?>'">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" data-toggle="tooltip" title="Hapus Soal"
                                                onclick="hapus('<?= $d['id_soal'] ?>','<?= $d['judul'] ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($d['judul']) ?></td>
                                    <td class="text-center"><span class="badge badge-primary"><?= esc($d['mapel']) ?></span>
                                    </td>
                                    <td class="text-center"><span class="badge badge-<?php if ($d['status'] == "final") {
                                        echo "secondary";
                                    } elseif ($d['status'] == "dikerjakan") {
                                        echo "success";
                                    } else {
                                        echo "warning";
                                    } ?>"><?= strtoupper(esc($d['status'])) ?></span>
                                    </td>
                                    <td class="text-center"><?= esc($d['updated_at']) ?></td>
                                </tr>
                                <?php $i++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
            <form action="<?= base_url('/' . bin2hex('data-draft') . '/' . bin2hex('delete')) ?>" method="post"
                enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" class="form-control" required id="d-id">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" class="form-control" disabled id="d-judul">
                    </div><br>
                    <center>
                        <h4>Hapus draft?</h4>
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
    $(function () {
        $('#tbl-soal').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            language: {
                emptyTable: "Tidak ada data tersedia"
            }
        });
    });

    function hapus(id, judul) {
        $('#d-id').val(id);
        $('#d-judul').val(judul);
        $('#delete-modal').appendTo('body').modal('show');
    }
</script>
<?= $this->endSection() ?>