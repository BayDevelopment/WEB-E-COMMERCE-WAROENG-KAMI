 <div id="layoutSidenav_nav">
     <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
         <div class="sb-sidenav-menu">
             <div class="nav">
                 <div class="sb-sidenav-menu-heading">Core</div>
                 <a class="nav-link mb-2 <?= ($nav_link === "Dashboard") ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                     <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                     Dashboard
                 </a>
                 <!-- <div class="sb-sidenav-menu-heading">Interface</div>
                 <a class="nav-link collapsed mb-2" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                     <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                     Layouts
                     <div class="sb-sidenav-collapse-arrow text-dark dark:text-gray-300"><i class="fas fa-angle-down"></i></div>
                 </a>
                 <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                     <nav class="sb-sidenav-menu-nested nav">
                         <a class="nav-link mb-2" href="layout-static.html">Carousel</a>
                         <a class="nav-link mb-2" href="layout-sidenav-light.html">Tentang Kami</a>
                     </nav>
                 </div> -->
                 <div class="sb-sidenav-menu-heading">Master</div>
                 <a class="nav-link mb-2 <?= in_array($nav_link, ['produk', 'pelanggan', 'pemesanan', 'karyawan']) ? '' : 'collapsed' ?>"
                     href="#"
                     data-bs-toggle="collapse"
                     data-bs-target="#collapsePages"
                     aria-expanded="<?= in_array($nav_link, ['produk', 'pelanggan', 'pemesanan', 'karyawan']) ? 'true' : 'false' ?>"
                     aria-controls="collapsePages">
                     <div class="sb-nav-link-icon"><i class="fas fa-database"></i></div>
                     Master
                     <div class="sb-sidenav-collapse-arrow text-dark dark:text-gray-300">
                         <i class="fas fa-angle-down"></i>
                     </div>
                 </a>

                 <div class="collapse <?= in_array($nav_link, ['produk', 'pelanggan', 'pemesanan', 'karyawan']) ? 'show' : '' ?>"
                     id="collapsePages"
                     aria-labelledby="headingTwo"
                     data-bs-parent="#sidenavAccordion">

                     <nav class="sb-sidenav-menu-nested nav mb-2">
                         <a class="nav-link <?= ($nav_link === 'produk') ? 'active' : '' ?>"
                             href="<?= base_url('admin/produk') ?>">
                             <i class="fas fa-box-open"></i>
                             <span>Produk</span>
                         </a>
                     </nav>

                     <nav class="sb-sidenav-menu-nested nav mb-2">
                         <a class="nav-link <?= ($nav_link === 'pelanggan') ? 'active' : '' ?>"
                             href="<?= base_url('admin/pelanggan') ?>">
                             <i class="fas fa-users me-2"></i>
                             <span>Pelanggan</span>
                         </a>
                     </nav>

                     <nav class="sb-sidenav-menu-nested nav mb-2">
                         <a class="nav-link <?= ($nav_link === 'pemesanan') ? 'active' : '' ?>"
                             href="<?= base_url('admin/pemesanan') ?>">
                             <i class="fas fa-file-invoice-dollar me-2"></i>
                             <span>Pemesanan</span>
                         </a>
                     </nav>
                 </div>
                 <div class="sb-sidenav-menu-heading">Lainnya</div>
                 <a class="nav-link mb-3 <?= ($nav_link === 'laporan') ? 'active' : '' ?>" href="<?= base_url('admin/laporan') ?>">
                     <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                     Laporan
                 </a>
             </div>
         </div>
         <div class="sb-sidenav-footer">
             <div class="small">Logged in as:</div>
             <span><?= session()->get('admin_name') ?></span>
         </div>
     </nav>
 </div>