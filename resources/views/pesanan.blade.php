@extends('layouts.app')
@section('title', 'Pesanan - NAT Laundry')

@section('content')
<section class="max-w-6xl mx-auto px-6 py-10">
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-3xl text-brand">
        <span class="font-semibold">Pesanan</span>
        <span class="font-light" id="judul-kode"></span>
      </h1>
      <p id="tgl-sekarang" class="mt-1 text-xs text-slate-500"></p>
    </div>
    <div class="flex gap-3">
      <button id="btn-pdf" class="bg-brand-light text-white text-xs px-5 py-2 rounded-lg hover:opacity-90">Export PDF</button>
      <a id="btn-riwayat" href="/riwayat" class="bg-brand text-white text-xs px-5 py-2 rounded-lg hover:bg-brand-dark">Riwayat Pesanan</a>
    </div>
  </div>

  <div id="daftar" class="mt-8 grid md:grid-cols-2 gap-6">
    <p class="text-sm text-slate-400">Memuat pesanan...</p>
  </div>
</section>
@endsection

@push('scripts')
<script>
// ===== Tanpa login: pesanan dicari lewat kode (?kode=PSFKA09S) =====
const KODE = (new URLSearchParams(location.search).get('kode') || '').trim().toUpperCase();

// endpoint API publik (sesuaikan dengan route backend-mu)
const URL_PESANAN = (k) => `/pesanan/kode/${encodeURIComponent(k)}`;
const URL_PDF     = (k) => `/pesanan/kode/${encodeURIComponent(k)}/export-pdf`;

if (!KODE) location.replace('/');   // tidak ada kode -> balik ke beranda (form Redeem)

document.getElementById('judul-kode').textContent = '#' + KODE;
document.getElementById('btn-riwayat').href = '/riwayat?kode=' + encodeURIComponent(KODE);

const NAMA_BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const pad = (n) => String(n).padStart(2, '0');

// tanggal & jam hari ini
(() => {
  const d = new Date();
  document.getElementById('tgl-sekarang').textContent =
    `${d.getDate()} ${NAMA_BULAN[d.getMonth()]} ${d.getFullYear()}  ${pad(d.getHours())}:${pad(d.getMinutes())}`;
})();

// 2026-09-10 11:54:00 -> 10/09/2026 11.54
const tglF = (s) => {
  if (!s) return '-';
  const [t, j] = String(s).replace('T', ' ').split(' ');
  const tgl = t.slice(0, 10).split('-').reverse().join('/');
  return j ? `${tgl} ${j.slice(0, 5).replace(':', '.')}` : tgl;
};

const namaPelanggan = (x) => x.pelanggan?.nama || x.nama_pelanggan || '';

const labelBayar = (s) => {
  const t = String(s || '');
  const lunas = /lunas/i.test(t) && !/belum/i.test(t);
  return `<span class="text-[11px] font-semibold ${lunas ? 'text-green-300' : 'text-red-300'}">${esc(t.toUpperCase())}</span>`;
};

// teks kanan header: "CUCI KILOAN - REGULAR 3X24 JAM" (sesuaikan field API-mu)
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
    <div class="mt-1 border-t border-white/30"></div>

    <div class="mt-3 space-y-3">
      ${(x.detail_pesanan || []).map(baris).join('') || '<p class="text-xs text-white/70">Belum ada rincian layanan.</p>'}
    </div>

    <div class="mt-4 flex items-center justify-between text-[11px]">
      <span class="text-white/70">Total Harga</span>
      <span class="font-semibold">${rupiah(x.total_harga)}</span>
    </div>
  </div>`;

document.getElementById('btn-pdf').onclick = async () => {
  try { await downloadFile(URL_PDF(KODE), `pesanan-${KODE}.pdf`); }
  catch (e) { alert(e.message); }
};

(async () => {
  const box = document.getElementById('daftar');
  try {
    const res = await api(URL_PESANAN(KODE));
    // dukung respon berupa 1 objek atau array
    const data = Array.isArray(res.data) ? res.data : (res.data ? [res.data] : []);
    box.innerHTML = data.map(kartu).join('') ||
      '<p class="text-sm text-slate-400">Pesanan dengan kode ini tidak ditemukan.</p>';
  } catch (e) {
    box.innerHTML = e.status === 404
      ? '<p class="text-sm text-slate-400">Pesanan dengan kode ini tidak ditemukan.</p>'
      : `<p class="text-sm text-red-500">${esc(e.message)}</p>`;
  }
})();
</script>
@endpush