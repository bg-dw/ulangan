<meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<title>
    <?= isset($title) ? "OWL - " . $title : "OWL"; ?>
</title>
<!-- General CSS Files -->
<link rel="stylesheet" href="<?= site_url() ?>/public/assets/css/app.min.css">
<!-- Template CSS -->
<link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/style.css">
<link rel="stylesheet" href="<?= base_url() ?>/public/assets/css/components.css">
<!-- Custom style CSS -->
<link rel='shortcut icon' type='image/x-icon' href='<?= base_url() ?>/public/assets/img/favicon.ico' />
<link rel="stylesheet" href="<?= base_url() ?>/public/assets/bundles/datatables/datatables.min.css">
<link rel="stylesheet"
    href="<?= base_url() ?>/public/assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/public/assets/bundles/izitoast/css/iziToast.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/public/assets/bundles/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/public/assets/bundles/bootstrap-daterangepicker/daterangepicker.css">
<link rel="stylesheet" href="<?= base_url() ?>/public/assets/bundles/pretty-checkbox/pretty-checkbox.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/public/assets/bundles/bootstrap-daterangepicker/daterangepicker.css">
<script src="<?= base_url() ?>/public/assets/js/jquery-3.7.0.js"></script>
<style type="text/css" media="print">
    @page {
        size: landscape;
    }
</style>
<style>
    .card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(6.6px);
        -webkit-backdrop-filter: blur(6.6px);
    }

    .loader {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
        backdrop-filter: blur(6.6px);
        -webkit-backdrop-filter: blur(6.6px);
    }

    .spinner {
        position: relative;
        display: flex;
        top: 30%;
        left: 50%;
        width: 100%;
        max-width: 6rem;
    }

    .spinner:before,
    .spinner:after {
        content: "";
        position: absolute;
        border-radius: 50%;
        animation: pulsOut 1s ease-in-out infinite;
        filter: drop-shadow(0 0 1rem rgba(255, 255, 255, 0.75));
    }

    .spinner:before {
        width: 100%;
        padding-bottom: 100%;
        box-shadow: inset 0 0 0 1rem #427bf5;
        animation-name: pulsIn;
    }

    .spinner:after {
        width: calc(100% - 2rem);
        padding-bottom: calc(100% - 2rem);
        box-shadow: 0 0 0 0 #427bf5;
    }

    @keyframes pulsIn {
        0% {
            box-shadow: inset 0 0 0 1rem #427bf5;
            opacity: 1;
        }

        50%,
        100% {
            box-shadow: inset 0 0 0 0 #427bf5;
            opacity: 0;
        }
    }

    @keyframes pulsOut {

        0%,
        50% {
            box-shadow: 0 0 0 0 #427bf5;
            opacity: 0;
        }

        100% {
            box-shadow: 0 0 0 1rem #427bf5;
            opacity: 1;
        }
    }


    .loader-inframe {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        margin-left: 45%;
        width: 20px;
        height: 20px;
        -webkit-animation: spin 1s linear infinite;
        /* Safari */
        animation: spin 1s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>