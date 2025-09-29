<?php
$uri = current_url(true);
$segments = $uri->getSegments(); ?>
<aside id="sidebar-wrapper" class="d-print-none">
    <div class="sidebar-brand">
        <a href="<?= base_url('/' . bin2hex('home')) ?>">
            <img alt="image" src="<?= base_url() ?>/public/assets/img/logo.png" class="header-logo"
                style="margin-top: -5px;" />
            <span class="logo-name" style="margin-top: 20px;">OWL</span>
        </a>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
        <li class="dropdown <?php if ($segments[0] === bin2hex('home')) {
            echo "active";
        } ?>">
            <a href="<?= base_url('/' . bin2hex('home')) ?>" class="nav-link"><i
                    data-feather="monitor"></i><span>Dashboard</span></a>
        </li>
        <li class="dropdown <?php if ($segments[0] === bin2hex('daftar-login') || $segments[0] === bin2hex('reset-login') || $segments[0] === bin2hex('status-tes')) {
            echo "active";
        } ?>">
            <a href="#" class="menu-toggle nav-link has-dropdown"><i
                    data-feather="file-text"></i><span>Ulangan</span></a>
            <ul class="dropdown-menu">
                <li class="<?php if ($segments[0] === bin2hex('daftar-login')) {
                    echo "active";
                } ?>"><a class="nav-link" href="<?= base_url('/' . bin2hex('daftar-login')) ?>">Daftar Login</a></li>
                <li class="<?php if ($segments[0] === bin2hex('daftar-login')) {
                    echo "active";
                } ?>"><a class="nav-link" href="<?= base_url('/' . bin2hex('reset-login')) ?>">Reset Login</a></li>
                <li class="<?php if ($segments[0] === bin2hex('admin') && $segments[1] === bin2hex('status-tes')) {
                    echo "active";
                } ?>"><a class="nav-link" href="<?= base_url('/' . bin2hex('status-tes')) ?>">Status tes</a></li>
            </ul>
        </li>
        <li class="menu-header">Data Master</li>
        <li class="dropdown <?php if ($segments[0] === bin2hex('data-mapel')) {
            echo "active";
        } ?>">
            <a href="<?= base_url('/' . bin2hex('data-mapel')) ?>" class="nav-link">
                <i data-feather="book"></i><span>Data Mapel</span>
            </a>
        </li>
        <li class="dropdown <?php if ($segments[0] === bin2hex('data-soal') || $segments[0] === bin2hex('data-draft')) {
            echo "active";
        } ?>">
            <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="book-open"></i><span>Data
                    Soal</span></a>
            <ul class="dropdown-menu">
                <li class="<?php if ($segments[0] === bin2hex('data-soal')) {
                    echo "active";
                } ?>"><a class="nav-link" href="<?= base_url('/' . bin2hex('data-soal')) ?>">Publish</a></li>
                <li class="<?php if ($segments[0] === bin2hex('data-draft')) {
                    echo "active";
                } ?>"><a class="nav-link" href="<?= base_url('/' . bin2hex('data-draft')) ?>">Draft</a></li>
            </ul>
        </li>
        <li class="dropdown <?php if ($segments[0] === bin2hex('hasil-ulangan')) {
            echo "active";
        } ?>">
            <a href="<?= base_url('/' . bin2hex('hasil-ulangan')) ?>" class="nav-link">
                <i data-feather="check-circle"></i><span>Hasil Ulangan</span>
            </a>
        </li>
    </ul>
</aside>