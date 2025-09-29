<div class="form-inline mr-auto d-print-none">
    <ul class="navbar-nav mr-3">
        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg
                                    collapse-btn"> <i data-feather="align-justify"></i></a></li>
        <li><a href="#" class="nav-link nav-link-lg fullscreen-btn" id="btn-full">
                <i data-feather="maximize"></i>
            </a>
        </li>
    </ul>
</div>
<ul class="navbar-nav navbar-right d-print-none">
    <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
            <img alt="image" src="<?= base_url() ?>/public/assets/img/user.png" class="user-img-radious-style"> <span
                class="d-sm-none d-lg-inline-block"></span></a>
        <div class="dropdown-menu dropdown-menu-right pullDown">
            <div class="dropdown-title">
                <?= session()->get('nama') ?>
            </div>
            <a href="#" class="dropdown-item has-icon"> <i class="far fa-user"></i> Ganti Username
            </a>
            <a href="#" class="dropdown-item has-icon"> <i class="fas fa-lock"></i>
                Ganti Password
            </a>
            <div class="dropdown-divider"></div>
            <a href="<?= base_url(route_to('/' . bin2hex('logout'))) ?>" class="dropdown-item has-icon text-danger"> <i
                    class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </li>
</ul>