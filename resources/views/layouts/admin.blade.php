<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin') - NAT Laundry</title>

  <script>
    // Hanya admin yang boleh membuka halaman ini
    try {
      const u = JSON.parse(localStorage.getItem('pengguna'));
      if (!localStorage.getItem('token') || !u || u.role !== 'admin') location.replace('/login');
    } catch (e) { location.replace('/login'); }
  </script>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
        fontFamily: { sans: ['Poppins', 'sans-serif'] },
        colors: { brand: { DEFAULT: '#2F5B8C', dark: '#274C78', light: '#4F94CD', soft: '#E3ECF6' } },
      } },
    };
  </script>
  <style>
    /* Judul halaman (text-3xl) dikecilkan di layar HP supaya tidak terlalu besar */
    @media (max-width: 767px) {
      main h1.text-3xl { font-size: 1.5rem !important; line-height: 2rem !important; }
    }
  </style>
</head>
<body class="font-sans bg-white text-slate-700">
  <div class="md:flex min-h-screen">

    {{-- Latar gelap di belakang menu (hanya HP, saat menu terbuka) --}}
    <div id="overlay" class="hidden fixed inset-0 bg-black/40 z-40 md:hidden"></div>

    {{-- SIDEBAR: HP = menu geser dari kiri, laptop = tetap di samping --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 z-50 w-56 -translate-x-full transition-transform duration-200 overflow-y-auto bg-brand text-white
                  md:sticky md:top-0 md:h-screen md:self-start md:w-44 md:shrink-0 md:translate-x-0">
      <div class="px-4 pt-7 pb-4 text-center relative">
        <button id="tutup-menu" type="button" aria-label="Tutup menu"
                class="md:hidden absolute right-3 top-3 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
        <p class="text-sm font-semibold">NAT <em class="font-medium">Laundry</em></p>
        <div class="mt-4 border-t border-dashed border-white/30"></div>
      </div>

      @php
        $ikon = [
          'dashboard' => '<path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/>',
          'pesanan'   => '<path d="M3 4h2l2 10h10l2-7H6"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/>',
          'pelanggan' => '<circle cx="9" cy="8" r="3"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2"/><path d="M17 14c2.5 0 4 1.5 4 4"/>',
          'layanan'   => '<path d="M6 7h12l1 13H5zM9 7a3 3 0 0 1 6 0"/>',
          'jenis'     => '<rect x="5" y="4" width="14" height="16" rx="2"/><path d="M9 10h6"/>',
          'laporan'   => '<rect x="6" y="5" width="12" height="16" rx="2"/><path d="M9 5V3h6v2"/>',
        ];
        $menu = [
          ['/admin', 'Dashboard', 'dashboard', request()->is('admin')],
          ['/admin/pesanan', 'Data Pesanan', 'pesanan', request()->is('admin/pesanan', 'admin/pesanan/*')],
          ['/admin/pengguna', 'Data Pelanggan', 'pelanggan', request()->is('admin/pengguna', 'admin/pengguna/*')],
          ['/admin/layanan', 'Data Layanan', 'layanan', request()->is('admin/layanan', 'admin/layanan/*')],
          ['/admin/jenis-layanan', 'Jenis Layanan', 'jenis', request()->is('admin/jenis-layanan')],
          ['/admin/laporan', 'Laporan Transaksi', 'laporan', request()->is('admin/laporan')],
        ];
      @endphp

      <nav class="flex flex-col gap-1 px-3 pb-6 text-xs">
        @foreach ($menu as [$url, $nama, $ik, $aktif])
          <a href="{{ $url }}" class="flex items-center gap-2 px-3 py-2.5 md:py-2 rounded-lg {{ $aktif ? 'font-semibold bg-white/10' : 'hover:bg-white/10' }}">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $ikon[$ik] !!}</svg>
            {{ $nama }}
          </a>
        @endforeach
      </nav>
    </aside>

    <main class="flex-1 min-w-0 p-4 sm:p-6 md:p-8">
      <header class="flex items-start justify-between gap-3 mb-6">
        <div class="flex items-start gap-3 min-w-0">
          {{-- Tombol hamburger (hanya HP) --}}
          <button id="buka-menu" type="button" aria-label="Buka menu"
                  class="md:hidden shrink-0 w-9 h-9 mt-0.5 flex items-center justify-center rounded-lg bg-brand-soft text-brand">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
          </button>
          <div class="min-w-0">@yield('heading')</div>
        </div>

        {{-- PROFIL + DROPDOWN --}}
        <div class="relative shrink-0">
          <button id="user-btn" type="button"
                  class="flex items-center gap-3 bg-white border border-slate-200 rounded-xl pl-2 pr-3 py-1.5 text-left hover:bg-slate-50">
            {{-- Avatar bulat --}}
            <span class="w-9 h-9 shrink-0 rounded-full bg-brand-soft text-brand flex items-center justify-center">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7z"/></svg>
            </span>
            <span class="hidden sm:block">
              <span id="u-nama" class="block text-sm font-semibold text-brand-dark leading-tight max-w-[8rem] truncate">-</span>
              <span class="block text-xs text-slate-500 leading-tight">Admin Laundry</span>
            </span>
            <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>

          <div id="user-menu" class="hidden absolute right-0 mt-2 w-60 bg-white border border-slate-200 rounded-xl shadow-lg z-30 p-4">
            <p id="u-nama-menu" class="text-sm font-semibold text-brand-dark truncate">-</p>
            <p class="text-xs text-slate-500">Admin Laundry</p>

            <a href="/admin/profil" class="block mt-4 text-sm text-slate-700 hover:text-brand">Profil saya</a>

            <button id="btn-logout" type="button"
                    class="mt-3 w-full bg-brand hover:bg-brand-dark text-white text-sm font-semibold rounded-lg py-2.5">
              Logout
            </button>
          </div>
        </div>
      </header>

      @yield('content')
    </main>
  </div>

  {{-- POP UP KONFIRMASI LOGOUT --}}
  <div id="modal-logout" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black/40 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6" role="dialog" aria-modal="true" aria-labelledby="judul-logout">
      <div class="flex items-center gap-3 text-brand-dark">
        <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M12 8v5"/><path d="M12 16.5v.01"/>
        </svg>
        <h2 id="judul-logout" class="text-xl font-bold">Logout</h2>
      </div>

      <p class="mt-5 text-sm text-brand">Apakah anda yakin ingin mengakhiri sesi ini?</p>

      <div class="mt-8 flex justify-end gap-3">
        <button id="batal-logout" type="button"
                class="px-6 py-2 rounded-lg bg-brand-soft text-brand-light text-sm font-medium hover:bg-slate-200">Batal</button>
        <button id="ya-logout" type="button"
                class="px-6 py-2 rounded-lg bg-brand text-white text-sm font-medium hover:bg-brand-dark">Logout</button>
      </div>
    </div>
  </div>

  <script src="{{ asset('js/api.js') }}"></script>
  <script src="{{ asset('js/admin.js') }}"></script>
  <script>
    // Isi nama di tombol profil & dropdown
    const namaUser = Auth.getUser()?.nama_pengguna || '-';
    document.getElementById('u-nama').textContent = namaUser;
    document.getElementById('u-nama-menu').textContent = namaUser;

    // Dropdown profil
    const userMenu = document.getElementById('user-menu');
    document.getElementById('user-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      userMenu.classList.toggle('hidden');
    });
    userMenu.addEventListener('click', (e) => e.stopPropagation());
    document.addEventListener('click', () => userMenu.classList.add('hidden'));

    // Pop up konfirmasi logout
    const modalLogout = document.getElementById('modal-logout');
    function bukaModalLogout(buka) {
      modalLogout.classList.toggle('hidden', !buka);
      document.body.classList.toggle('overflow-hidden', buka);
    }
    document.getElementById('btn-logout').addEventListener('click', () => {
      userMenu.classList.add('hidden');
      bukaModalLogout(true);
    });
    document.getElementById('batal-logout').addEventListener('click', () => bukaModalLogout(false));
    document.getElementById('ya-logout').addEventListener('click', () => logout());
    modalLogout.addEventListener('click', (e) => { if (e.target === modalLogout) bukaModalLogout(false); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') bukaModalLogout(false); });

    // Menu geser (HP)
    (function () {
      const side = document.getElementById('sidebar');
      const tabir = document.getElementById('overlay');
      function menu(buka) {
        side.classList.toggle('-translate-x-full', !buka);
        tabir.classList.toggle('hidden', !buka);
        document.body.classList.toggle('overflow-hidden', buka);
      }
      document.getElementById('buka-menu').addEventListener('click', () => menu(true));
      document.getElementById('tutup-menu').addEventListener('click', () => menu(false));
      tabir.addEventListener('click', () => menu(false));
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') menu(false); });
      // Kembali ke tampilan laptop: pastikan kondisi bersih
      window.matchMedia('(min-width: 768px)').addEventListener('change', (e) => { if (e.matches) menu(false); });
    })();
  </script>
  @stack('scripts')
</body>
</html>