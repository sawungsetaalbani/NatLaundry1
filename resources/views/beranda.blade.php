@extends('layouts.app')
@section('title', 'Beranda - NAT Laundry')

@php
  $svg = fn($d, $cls) => '<svg class="'.$cls.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$d.'</svg>';
  $ikon = fn($d) => $svg($d, 'w-8 h-8 text-brand');
@endphp

@section('content')

{{-- HERO --}}
<section class="max-w-6xl mx-auto px-6 pt-10 grid md:grid-cols-2 gap-10 items-center">
  <div>
    <h1 class="text-3xl md:text-4xl text-brand leading-tight">
      <span class="font-semibold">Cucian Lebih Higienis,</span>
      <span class="font-light">Rapi Sempurna Tanpa Khawatir.</span>
    </h1>
    <p class="mt-5 text-xs text-brand max-w-md leading-relaxed">
      Pencucian khusus tanpa dicampur dengan setrika uap yang aman untuk segala jenis pakaian.
      Pakaian awet, rapi, dan wangi sepanjang hari.
    </p>
    <div class="mt-7 flex items-center gap-5">
      <a href="/layanan" class="bg-brand text-white text-xs px-6 py-2.5 rounded-lg hover:bg-brand-dark">Lihat Layanan</a>
      <a href="#kunjungi-kami" class="flex items-center gap-2 text-xs text-brand">
        <span class="w-6 h-6 rounded-full bg-brand-soft flex items-center justify-center">&rsaquo;</span> Datang sekarang!
      </a>
    </div>
  </div>
  {{-- taruh gambar mesin cuci di public/images/hero.png --}}
  <img src="{{ asset('images/hero.png') }}" alt="NAT Laundry" class="w-full max-w-md mx-auto" onerror="this.style.display='none'">
</section>

{{-- KEUNGGULAN --}}
<section class="max-w-6xl mx-auto px-6 mt-8">
  <div class="grid sm:grid-cols-3 gap-4">
    @foreach ([
      ['1 Mesin', '1 Pelanggan', '<rect x="4" y="3" width="16" height="18" rx="3"/><circle cx="12" cy="13" r="4"/>'],
      ['Request Cuci', 'Manual', '<path d="M9 11V5a2 2 0 0 1 4 0v6"/><path d="M13 9a2 2 0 0 1 4 0v5a6 6 0 0 1-6 6h-1a6 6 0 0 1-5-3l-2-4a2 2 0 0 1 3-2l2 2"/>'],
      ['Menggunakan', 'Setrika Uap', '<path d="M3 17h18v2H3z"/><path d="M3 17c0-5 4-9 9-9h3a4 4 0 0 1 4 4v5"/>'],
    ] as [$a, $b, $d])
      <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm flex items-center gap-3">
        <span class="w-8 h-8 rounded-md bg-brand-soft flex items-center justify-center">
          {!! $svg($d, 'w-5 h-5 text-brand') !!}
        </span>
        <p class="text-[11px] font-medium text-brand leading-tight">{{ $a }}<br>{{ $b }}</p>
      </div>
    @endforeach
  </div>
</section>

{{-- REDEEM KODE PESANAN --}}
<section class="max-w-6xl mx-auto px-6 mt-16 text-center">
  <h2 class="text-xl font-semibold text-brand">Redeem Kode Pesanan</h2>
  <p class="mt-1 text-xs text-brand">Masukkan kode dan pantau status cucian laundry anda dengan mudah!</p>

  <form id="form-redeem" class="mt-6 max-w-xl mx-auto bg-brand rounded-2xl p-5 flex items-center gap-3 shadow-md">
    <input id="kode-pesanan" type="text" placeholder="Input kode pesanan disini" autocomplete="off"
      class="flex-1 bg-white text-slate-700 text-xs rounded-lg px-4 py-2.5 outline-none placeholder:text-slate-400">
    <button type="submit" class="flex items-center gap-2 bg-brand-soft text-brand text-xs font-medium px-5 py-2.5 rounded-lg hover:opacity-90">
      {!! $svg('<rect x="3" y="8" width="18" height="8" rx="2"/><path d="M12 8v8"/>', 'w-4 h-4') !!}
      Redeem
    </button>
  </form>
</section>

<hr class="max-w-6xl mx-auto mt-14 border-t border-dashed border-slate-300">

{{-- LAYANAN KAMI (data dari API) --}}
<section class="max-w-6xl mx-auto px-6 mt-10">
  <div class="flex items-start justify-between">
    <div class="flex items-start gap-3">
      {!! $ikon('<rect x="4" y="3" width="16" height="18" rx="3"/><circle cx="12" cy="13" r="4"/>') !!}
      <div>
        <h2 class="text-xl font-semibold text-brand">Layanan Kami</h2>
        <p class="text-xs text-brand">Banyak pilihan, sesuaikan dengan kebutuhan anda!</p>
      </div>
    </div>
    <a href="/layanan" class="text-xs bg-brand text-white px-4 py-1.5 rounded-lg hover:bg-brand-dark">Detail Layanan &rsaquo;</a>
  </div>
  <div id="layanan-grid" class="mt-8 grid md:grid-cols-3 gap-8">
    <p class="text-sm text-slate-400">Memuat layanan...</p>
  </div>
</section>

{{-- ALUR CARA KERJA --}}
<section class="max-w-6xl mx-auto px-6 mt-20">
  <div class="flex items-start gap-3">
    {!! $ikon('<rect x="3" y="3" width="13" height="13" rx="2"/><path d="M8 21h11a2 2 0 0 0 2-2V8"/>') !!}
    <div>
      <h2 class="text-xl font-semibold text-brand">Alur Cara Kerja</h2>
      <p class="text-xs text-brand">Pengecekan mudah dan lebih aman!</p>
    </div>
  </div>
  <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach ([
      ['Fresh Laundry Counter Exchange.png', 'Datang & Serahkan Pakaian', 'Bawa pakaian kotor Anda langsung ke gerai kami. Tim kami siap menerima dan mencatat pesanan Anda.'],
      ['RegistrasidanLogin.png', 'Registrasi & Login', 'Lakukan registrasi agar pesanan Anda tercatat. Cukup login dengan akun yang sudah dibuat.'],
      ['PantauViaWeb.png', 'Pantau via Web', 'Cek status pesanan Anda kapan saja melalui website, dari proses hingga selesai.'],
      ['AmbilPakaian.png', 'Ambil Pakaian Rapi & Wangi', 'Setelah status "Selesai", ambil pakaian Anda di gerai dalam keadaan rapi dan wangi.'],
    ] as $i => [$img, $judul, $isi])
      <div class="relative">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden h-full">
          <div class="h-28 bg-brand-soft">
            <img src="{{ asset('images/'.$img) }}" alt="{{ $judul }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
          </div>
          <div class="p-4">
            <p class="text-[11px] font-semibold text-brand">{{ $judul }}</p>
            <p class="mt-2 text-[10px] text-slate-500 leading-relaxed">{{ $isi }}</p>
          </div>
        </div>
        @if ($i < 3)
          <span class="hidden lg:block absolute -right-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">&rarr;</span>
        @endif
      </div>
    @endforeach
  </div>
</section>

{{-- KUNJUNGI KAMI --}}
<section id="kunjungi-kami" class="max-w-6xl mx-auto px-6 mt-20 mb-16">
  <div class="bg-brand rounded-3xl p-8 text-white grid md:grid-cols-2 gap-8">
    <div>
      <h2 class="text-xl font-semibold">Kunjungi Kami</h2>
      <p class="text-xs text-white/80">Informasi kontak dan alamat laundry</p>

      <div class="mt-6 bg-white text-slate-700 rounded-xl p-5 text-xs">
        <p class="font-semibold text-brand">Jam Operasional</p>
        <div class="mt-3 grid grid-cols-2 gap-y-1">
          <span class="font-medium">Senin - Sabtu</span><span>08.00 - 18.00</span>
          <span class="font-medium">Minggu</span><span class="text-red-600 font-medium">Tutup</span>
        </div>
      </div>

      <div class="mt-5 flex items-center gap-3">
        <span class="w-9 h-9 rounded-full bg-white text-brand flex items-center justify-center text-sm">&#9742;</span>
        <div class="text-xs">
          <p class="text-white/80">Kontak</p>
          <p class="font-semibold">081282918435</p>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <iframe class="w-full h-44 rounded-xl border-0" loading="lazy" title="Peta NAT Laundry"
        src="https://www.google.com/maps/place/NAT+laundryshop/@-7.9146412,112.6340265,17z/data=!3m1!4b1!4m6!3m5!1s0x2dd62baf44bd8fcd:0xa0e03f087d0ee5fe!8m2!3d-7.9146412!4d112.6340265!16s%2Fg%2F11t03qjs89?entry=ttu&g_ep=EgoyMDI2MTAyMC4wIKXMDSoASAFQAw%3D%3D"></iframe>
      <div class="bg-white text-slate-700 rounded-xl p-4 text-xs flex gap-3">
        <span class="text-brand">&#128205;</span>
        <div>
          <p class="font-semibold text-brand">Alamat</p>
          <p class="mt-1">Gunung Ring, Tanjungrejo, Kec. Bringsatan, Kabupaten Malang, Jawa Timur 65313, Indonesia</p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
// Redeem kode pesanan -> simpan kode (menu Pesanan jadi muncul) -> arahkan ke halaman pesanan
document.getElementById('form-redeem').addEventListener('submit', (e) => {
  e.preventDefault();
  const kode = document.getElementById('kode-pesanan').value.trim();
  if (!kode) { document.getElementById('kode-pesanan').focus(); return; }

  try { localStorage.setItem('kode_pesanan', kode); } catch (err) {}

  // tampilkan menu Pesanan langsung
  document.querySelectorAll('[data-menu-pesanan]').forEach(el => el.style.display = '');

  window.location.href = '/pesanan?kode=' + encodeURIComponent(kode);
});

// paket + durasi dari field API (sesuaikan kalau beda)
const paketOf = (l) => {
  const nama = l.paket?.nama_paket || l.nama_paket || (typeof l.paket === 'string' ? l.paket : '');
  const durasi = l.paket?.durasi || l.durasi || '';
  return [nama, durasi].filter(Boolean).join(' ');
};

(async () => {
  const grid = document.getElementById('layanan-grid');
  try {
    const res = await api('/jenis-layanan');
    const list = res.data || [];
    if (!list.length) {
      grid.innerHTML = '<p class="text-sm text-slate-400">Belum ada layanan.</p>';
      return;
    }
    grid.innerHTML = list.slice(0, 3).map(j => {
      const paket = [...new Set((j.layanan || []).map(paketOf).filter(Boolean))];
      // kalau belum ada data paket, tampilkan nama layanan sebagai gantinya
      const item = paket.length ? paket : (j.layanan || []).slice(0, 3).map(l => l.nama_layanan);
      return `
        <div class="drop-shadow-sm">
          <div class="inline-block bg-brand text-white text-xs font-semibold px-6 py-2.5 rounded-t-xl uppercase">${esc(j.nama_jenis)}</div>
          <div class="bg-[#D3DCE6] rounded-b-xl rounded-tr-xl px-6 py-5 min-h-[96px]">
            <ul class="space-y-1.5 text-xs text-brand">
              ${item.map(t => `<li class="flex gap-2"><span>&bull;</span><span>${esc(t)}</span></li>`).join('')
                || '<li class="text-slate-500">Belum ada layanan.</li>'}
            </ul>
          </div>
        </div>`;
    }).join('');
  } catch (e) {
    grid.innerHTML = `<p class="text-sm text-red-500">${esc(e.message)}</p>`;
  }
})();
</script>
@endpush