<!DOCTYPE html>
<html lang="en">

<?= $this->include('_layout_siswa/Header') ?>

<body>
    <div class="loader"></div>
    <div id="app">
        <section class="section">
            <?php $this->renderSection('content'); ?>
        </section>
    </div>

    <?= $this->include('_layout_siswa/Footer') ?>
    <?= $this->include('_layout/alert') ?>