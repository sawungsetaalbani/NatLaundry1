<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'NAT Laundry')</title>

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
</head>
<body class="font-sans text-slate-700 bg-white min-h-screen flex flex-col overflow-x-hidden">

  @php
    $menuPublik = [
      ['/', 'Beranda', request()->is('/')],
      ['/layanan', 'Layanan', request()->is('layanan')],
      ['/pesanan', 'Pesanan', request()->is('pesanan')],
      
    ];
  @endphp

  {{-- NAVBAR (pill) --}}
  <header class="px-3 sm:px-6 pt-3 sm:pt-4 relative z-30">
    <nav class="max-w-6xl mx-auto h-12 px-4 sm:px-6 flex items-center justify-between gap-3 border border-slate-200 rounded-full shadow-sm bg-white">
      <a href="/" class="font-bold text-brand text-sm whitespace-nowrap">NAT Laundry</a>

      {{-- Laptop/tablet: menu biasa --}}
      <div class="hidden md:flex items-center gap-6">
        @foreach ($menuPublik as [$url, $nama, $aktif])
          <a href="{{ $url }}"
             @if ($url === '/pesanan') data-menu-pesanan style="display:none" @endif
             class="text-xs hover:text-brand-light {{ $aktif ? 'text-brand font-semibold' : '' }}">{{ $nama }}</a>
        @endforeach
      </div>

      {{-- HP: tombol garis tiga --}}
      <button id="buka-nav" type="button" aria-label="Buka menu" aria-expanded="false"
              class="md:hidden w-9 h-9 -mr-2 flex items-center justify-center rounded-full text-brand hover:bg-brand-soft">
        <svg id="ikon-buka" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        <svg id="ikon-tutup" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>

      {{-- dipertahankan tersembunyi supaya api.js tidak error kalau mencari elemen ini --}}
      <div id="nav-auth" class="hidden"></div>
    </nav>

    {{-- HP: menu turun di bawah navbar --}}
    <div id="menu-hp" class="hidden md:hidden absolute left-3 right-3 sm:left-6 sm:right-6 top-full mt-2">
      <div class="max-w-6xl mx-auto bg-white border border-slate-200 rounded-2xl shadow-lg overflow-hidden">
        @foreach ($menuPublik as [$url, $nama, $aktif])
          <a href="{{ $url }}"
             @if ($url === '/pesanan') data-menu-pesanan style="display:none" @endif
             class="block px-5 py-3 text-sm border-b border-slate-100 last:border-b-0 {{ $aktif ? 'text-brand font-semibold bg-brand-soft' : 'hover:bg-slate-50' }}">{{ $nama }}</a>
        @endforeach
      </div>
    </div>
  </header>

  <main class="flex-1 min-w-0">
    @yield('content')
  </main>

  {{-- FOOTER --}}
  <footer class="bg-brand text-white text-center text-xs px-4 py-6 mt-12 sm:mt-16 leading-relaxed">
    <p><span class="font-semibold">NAT Laundry</span> <span class="hidden sm:inline">&nbsp;|&nbsp;</span><br class="sm:hidden"> Pakaian Wangi ya NAT Laundry Solusinya</p>
    <p class="mt-1">Dikembangkan oleh Tim Capstone {{ date('Y') }}</p>
  </footer>

  <script src="/js/api.js"></script>
  <script>
    (function () {
      const tombol = document.getElementById('buka-nav');
      const panel = document.getElementById('menu-hp');
      function atur(buka) {
        panel.classList.toggle('hidden', !buka);
        document.getElementById('ikon-buka').classList.toggle('hidden', buka);
        document.getElementById('ikon-tutup').classList.toggle('hidden', !buka);
        tombol.setAttribute('aria-expanded', buka ? 'true' : 'false');
      }
      tombol.addEventListener('click', (e) => { e.stopPropagation(); atur(panel.classList.contains('hidden')); });
      document.addEventListener('click', (e) => { if (!panel.contains(e.target)) atur(false); });
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') atur(false); });
      window.matchMedia('(min-width: 768px)').addEventListener('change', (e) => { if (e.matches) atur(false); });

      // tampilkan menu Pesanan kalau sudah pernah redeem kode
      try {
        if (localStorage.getItem('kode_pesanan')) {
          document.querySelectorAll('[data-menu-pesanan]').forEach(el => el.style.display = '');
        }
      } catch (e) {}
    })();
  </script>
  @stack('scripts')
</body>
</html>