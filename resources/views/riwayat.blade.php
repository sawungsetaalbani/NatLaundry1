@extends('layouts.app')
@section('title', 'Riwayat Pesanan - NAT Laundry')

@php $in = 'border border-slate-300 rounded-md px-3 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-light'; @endphp

@section('content')
<section class="max-w-6xl mx-auto px-6 py-10">
  <div class="flex items-center gap-3">
    <a id="btn-kembali" href="/pesanan" aria-label="Kembali" class="w-8 h-8 rounded-full border border-brand text-brand flex items-center justify-center hover:bg-brand-soft">&larr;</a>
    <h1 class="text-3xl text-brand"><span class="font-semibold">Riwayat</span> <span class="font-light">Pesanan</span></h1>
  </div>

  {{-- INFO PELANGGAN + EXPORT + CARI --}}
  <div class="mt-6 grid md:grid-cols-2 gap-6 items-start">
    <div class="bg-brand-soft rounded-xl px-6 py-5 grid grid-cols-2 gap-4 text-[11px] text-brand">
      <div class="flex items-start gap-2">
        <span class="mt-0.5">&#128101;</span>
        <div>
          <p id="info-nama" class="font-semibold">-</p>
          <p id="info-telp" class="mt-0.5">-</p>
        </div>
      </div>
      <div>
        <p class="font-semibold">Alamat</p>
        <p id="info-alamat" class="mt-0.5 leading-relaxed">-</p>
      </div>
    </div>

    <div class="space-y-4">
      <div class="flex justify-end">
        <button id="btn-pdf" class="bg-brand-light text-white text-xs px-6 py-2 rounded-lg hover:opacity-90">Export PDF</button>
      </div>
      <div class="relative">
        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-light text-xs">&#128269;</span>
        <input id="cari" type="search" placeholder="Cari data riwayat pesanan disini"
          class="w-full border border-brand-light rounded-full pl-10 pr-4 py-2 text-xs focus:outline-none">
      </div>
    </div>
  </div>

  <hr class="mt-6 border-t border-dashed border-slate-300">

  <div class="mt-5">
    <label class="block text-[11px] text-brand-light">Filter berdasarkan bulan</label>
    {{-- Dropdown bulan kustom (sesuai desain Figma) --}}
    <div id="dd-bulan" class="relative mt-1 w-44">
      <button type="button" id="dd-btn" aria-haspopup="listbox" aria-expanded="false"
        class="w-full flex items-center justify-between gap-2 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-brand shadow-sm hover:border-brand-light focus:outline-none focus:border-brand-light">
        <span id="dd-label">Pilih bulan</span>
        <svg id="dd-chev" class="w-3.5 h-3.5 text-brand transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
      </button>
      <ul id="dd-list" role="listbox"
        class="hidden absolute z-20 mt-2 w-full bg-white border border-slate-200 rounded-xl shadow-lg py-2 max-h-72 overflow-auto"></ul>
    </div>
  </div>

  <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs">
    <span id="total" class="font-semibold text-brand uppercase">Total data : 0 data</span>
    <div class="flex items-center gap-4 text-brand">
      <span id="info-halaman"></span>
      <label class="flex items-center gap-2">Item per halaman
        <select id="per-halaman" class="{{ $in }}">
          <option>5</option><option>10</option><option>20</option>
        </select>
      </label>
      <button id="prev" aria-label="Sebelumnya" class="w-7 h-7 rounded-full border border-brand disabled:opacity-40">&lsaquo;</button>
      <button id="next" aria-label="Berikutnya" class="w-7 h-7 rounded-full border border-brand disabled:opacity-40">&rsaquo;</button>
    </div>
  </div>

  <div id="daftar" class="mt-5 grid md:grid-cols-2 gap-6 items-start">
    <p class="text-sm text-slate-400">Memuat riwayat...</p>
  </div>
</section>
@endsection

@push('scripts')
<script>
// ===== Tanpa login: riwayat dicari lewat kode pesanan (?kode=PSFKA09S) =====
const KODE = (new URLSearchParams(location.search).get('kode') || '').trim().toUpperCase();

// endpoint API publik (sesuaikan dengan route backend-mu)
const URL_RIWAYAT = (k) => `/pesanan/kode/${encodeURIComponent(k)}/riwayat`;
const URL_PDF     = (k) => `/pesanan/kode/${encodeURIComponent(k)}/riwayat/export-pdf`;

if (!KODE) location.replace('/');
document.getElementById('btn-kembali').href = '/pesanan?kode=' + encodeURIComponent(KODE);

const $ = (id) => document.getElementById(id);
const NAMA_BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

const tglF = (s) => {
  if (!s) return '-';
  const [t, j] = String(s).replace('T', ' ').split(' ');
  const tgl = t.slice(0, 10).split('-').reverse().join('/');
  return j ? `${tgl} ${j.slice(0, 5).replace(':', '.')}` : tgl;
};

let pelanggan = {};
const namaPelanggan = (x) => x.pengguna?.nama_pengguna || pelanggan.nama_pengguna || '';

const labelBayar = (s) => {
  const t = String(s || '');
  const lunas = /lunas/i.test(t) && !/belum/i.test(t);
  return `<span class="text-[11px] font-semibold ${lunas ? 'text-green-300' : 'text-red-300'}">${esc(t.toUpperCase())}</span>`;
};

const labelJenis = (x) => {
  const d = (x.detail_pesanan || [])[0]?.layanan;
  const jenis = d?.jenis_layanan?.nama_jenis || d?.jenis?.nama_jenis || '';
  const paket = x.paket || x.paket_layanan?.nama_paket || '';
  return [jenis, paket].filter(Boolean).join(' - ').toUpperCase();
};

const baris = (d) => {
  const satuan = d.layanan?.satuan || '';
  const kg = /kg/i.test(satuan);
  return `
    <div class="grid grid-cols-[70px_1fr] gap-y-1 text-[11px]">
      <span class="text-white/70">Layanan</span>
      <span class="font-semibold">${esc(d.layanan?.nama_layanan || '-')}</span>
      <span class="text-white/70">${kg ? 'Berat' : 'Jumlah'}</span>
      <span class="font-semibold">${esc(d.kuantitas)} ${esc(satuan)}</span>
    </div>`;
};

const kartu = (x) => `
  <div class="bg-brand text-white rounded-xl px-5 py-4 shadow">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="text-sm font-semibold">${esc(namaPelanggan(x))}</p>
        <p class="text-[11px] text-white/80">Pemesanan : ${tglF(x.tgl_pesanan || x.created_at)}</p>
      </div>
      <span class="text-[10px] bg-white text-brand font-medium px-3 py-0.5 rounded-full">${esc(String(x.status_pesanan).toUpperCase())}</span>
    </div>
    <div class="mt-2 flex items-center justify-between">
      ${labelBayar(x.status_pembayaran)}
      <span class="text-[11px] font-semibold">${esc(labelJenis(x))}</span>
    </div>
    <div class="mt-1 border-t border-dashed border-white/40"></div>
    <div class="mt-3 space-y-3">
      ${(x.detail_pesanan || []).map(baris).join('') || '<p class="text-xs text-white/70">Tidak ada rincian layanan.</p>'}
    </div>
    <div class="mt-4 flex items-center justify-between text-[11px]">
      <span class="text-white/70">Total Harga</span>
      <span class="font-semibold">${rupiah(x.total_harga)}</span>
    </div>
  </div>`;

let semua = [];
let halaman = 1;
let bulanDipilih = 0; // 0 = semua bulan, 1-12 = Januari-Desember

/* ===== Dropdown bulan kustom ===== */
function labelBulan() {
  $('dd-label').textContent = bulanDipilih ? NAMA_BULAN[bulanDipilih - 1] : 'Pilih bulan';
}
function renderBulan() {
  $('dd-list').innerHTML = NAMA_BULAN.map((nama, i) => {
    const aktif = bulanDipilih === i + 1;
    return `<li role="option" aria-selected="${aktif}">
      <button type="button" data-bulan="${i + 1}"
        class="w-full text-left px-4 py-1.5 text-xs text-brand hover:bg-brand-soft ${aktif ? 'font-semibold bg-brand-soft' : ''}">${nama}</button>
    </li>`;
  }).join('');
}
function bukaBulan(buka) {
  $('dd-list').classList.toggle('hidden', !buka);
  $('dd-btn').setAttribute('aria-expanded', String(buka));
  $('dd-chev').style.transform = buka ? 'rotate(180deg)' : '';
}
$('dd-btn').addEventListener('click', () => bukaBulan($('dd-list').classList.contains('hidden')));
$('dd-list').addEventListener('click', (e) => {
  const b = e.target.closest('button[data-bulan]');
  if (!b) return;
  const n = parseInt(b.dataset.bulan, 10);
  bulanDipilih = (bulanDipilih === n) ? 0 : n; // klik lagi = hapus filter
  labelBulan(); renderBulan(); bukaBulan(false);
  halaman = 1; tampil();
});
document.addEventListener('click', (e) => { if (!$('dd-bulan').contains(e.target)) bukaBulan(false); });
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') bukaBulan(false); });
renderBulan();

function tampil() {
  const kata = $('cari').value.trim().toLowerCase();
  const perHal = parseInt($('per-halaman').value, 10);

  const hasil = semua.filter(x => {
    const tanggal = String(x.tgl_pesanan || x.created_at).slice(0, 10);
    if (bulanDipilih && parseInt(tanggal.slice(5, 7), 10) !== bulanDipilih) return false;
    if (kata) {
      const teks = ((x.kode_pesanan || '') + ' ' + namaPelanggan(x) + ' ' +
        (x.detail_pesanan || []).map(d => d.layanan?.nama_layanan || '').join(' ')).toLowerCase();
      if (!teks.includes(kata)) return false;
    }
    return true;
  });

  const totalHal = Math.max(1, Math.ceil(hasil.length / perHal));
  if (halaman > totalHal) halaman = totalHal;
  const potong = hasil.slice((halaman - 1) * perHal, halaman * perHal);

  $('total').textContent = `Total data : ${hasil.length} data`;
  $('daftar').innerHTML = potong.map(kartu).join('') ||
    '<p class="text-sm text-slate-400">Tidak ada riwayat pesanan.</p>';
  $('info-halaman').innerHTML = `Halaman <b>${halaman}</b> dari ${totalHal}`;
  $('prev').disabled = halaman <= 1;
  $('next').disabled = halaman >= totalHal;
}

$('btn-pdf').onclick = async () => {
  if (!bulanDipilih) { alert('Pilih bulan terlebih dahulu untuk Export PDF.'); return; }
  // Tahun: ambil tahun terbaru dari data yang punya bulan tersebut (cadangan: tahun ini)
  const tahunAda = semua
    .map(x => String(x.tgl_pesanan || x.created_at).slice(0, 10))
    .filter(t => parseInt(t.slice(5, 7), 10) === bulanDipilih)
    .map(t => parseInt(t.slice(0, 4), 10));
  const thn = tahunAda.length ? Math.max(...tahunAda) : new Date().getFullYear();
  const bln = String(bulanDipilih).padStart(2, '0');
  $('btn-pdf').disabled = true;
  try {
    await downloadFile(`${URL_PDF(KODE)}?bulan=${bulanDipilih}&tahun=${thn}`,
      `riwayat-transaksi-${thn}-${bln}.pdf`);
  } catch (e) { alert(e.message); }
  $('btn-pdf').disabled = false;
};

['cari', 'per-halaman'].forEach(id =>
  $(id).addEventListener(id === 'cari' ? 'input' : 'change', () => { halaman = 1; tampil(); }));
$('prev').onclick = () => { halaman--; tampil(); };
$('next').onclick = () => { halaman++; tampil(); };

(async () => {
  try {
    const res = await api(URL_RIWAYAT(KODE));
    pelanggan = res.pelanggan || {};
    semua = res.data || [];

    // Info pelanggan. Nama field di database: nama_pengguna, no_telpon, alamat.
    // Jika objek `pelanggan` tidak dikirim API, ambil dari data pengguna di pesanan pertama.
    const p0 = semua[0]?.pengguna || {};
    $('info-nama').textContent = pelanggan.nama_pengguna || p0.nama_pengguna || '-';
    $('info-telp').textContent = pelanggan.no_telpon || p0.no_telpon || '-';
    $('info-alamat').textContent = pelanggan.alamat || p0.alamat || '-';

    tampil();
  } catch (e) {
    $('daftar').innerHTML = e.status === 404
      ? '<p class="text-sm text-slate-400">Kode pesanan tidak ditemukan.</p>'
      : `<p class="text-sm text-red-500">${esc(e.message)}</p>`;
  }
})();
</script>
@endpush