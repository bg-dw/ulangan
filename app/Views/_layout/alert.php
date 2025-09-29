<?php if (session()->has('success')): ?>
    <script>
        iziToast.success({
            title: 'Success!',
            message: '<?= session()->getFlashdata('success') ?>',
            position: 'topCenter'
        });
    </script>
    <?php
endif; ?>
<?php if (session()->has('warning')):
    ?>
    <script>
        iziToast.error({
            title: 'error!',
            message: '<?= session()->getFlashdata('warning') ?>',
            position: 'topCenter'
        });
    </script>
    <?php
endif; ?>

<?php if (session()->has('error')):
    ?>
    <script>swal('Error', "<?= session()->getFlashdata('error') ?>", 'error');</script>
    <?php
endif; ?>

<script>
    function notif(msg, title) {
        if (title == "suc") {
            iziToast.success({
                title: 'Success!',
                message: msg,
                position: 'topCenter'
            });
        } else {
            iziToast.error({
                title: 'error!',
                message: msg,
                position: 'topCenter'
            });
        }
    }
</script>