 <footer>
     <nav class="app-bottom-nav" role="navigation" aria-label="Bottom navigation">
         <ul class="nav w-100">
             <!-- PESANAN -->
             <!-- PESANAN -->
             <li class="nav-item flex-fill">
                 <a class="nav-link <?= ($nav_link === 'pesanan') ? 'active' : '' ?>"
                     href="<?= base_url('pelanggan/produk'); ?>"
                     aria-label="Pesanan">
                     <i class="bi bi-bag"></i>
                     <span class="label">Pesanan</span>
                     <span class="active-dot" aria-hidden="true"></span>
                 </a>
             </li>

             <!-- TENTANG KAMI -->
             <li class="nav-item flex-fill">
                 <a class="nav-link <?= ($nav_link === 'tentang') ? 'active' : '' ?>"
                     href="<?= base_url('tentang-kami'); ?>"
                     aria-label="Tentang Kami">
                     <i class="bi bi-info-circle"></i>
                     <span class="label">Tentang Kami</span>
                     <span class="active-dot" aria-hidden="true"></span>
                 </a>
             </li>

         </ul>
     </nav>
 </footer>