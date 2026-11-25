@extends('layouts.admin')
@section('title', 'Tambah Pesanan')

@php
  $in = 'w-full bg-[#EDF0F5] text-[#2F5B8C] placeholder-[#4F8DB8] rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-light';
  $chev = '<svg class="w-3 h-3 absolute right-3 top-1/2 -translate-y-1/2 text-brand-light pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>';
@endphp

@section('heading')
  <h1 class="text-3xl text-brand"><span class="font-semibold">Tambah</span> <span class="font-light">Pesanan</span></h1>
@endsection

@section('content')
<a href="/admin/pesanan" title="Kembali"
   class="w-7 h-7 rounded-full border border-brand-light text-brand flex items-center justify-center hover:bg-brand-soft">
  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
</a>

<form id="form" onsubmit="return false" novalidate class="mt-4 max-w-4xl">
  <div class="border border-brand-light/50 rounded-2xl p-6 md:p-8">

    {{-- Kode redeem (otomatis) + status --}}
    <div class="flex flex-wrap items-center justify-between gap-3 text-[11px]">
      <div class="flex items-center gap-3">
        <span class="font-semibold text-slate-400">KODE PESANAN</span>
        <span id="kode" class="text-sm font-bold tracking-widest text-brand select-all">-</span>
        <button type="button" id="btn-salin" title="Salin kode"
          class="text-[10px] border border-brand-light text-brand px-3 py-1 rounded-md hover:bg-brand-soft">Salin</button>
      </div>
      <div>
        <span class="font-semibold text-slate-400">STATUS PESANAN</span>
        <span class="ml-3 font-bold text-[#E8913A]">DIPROSES</span>
      </div>
    </div>

    <div class="mt-4 grid md:grid-cols-2 gap-x-10 gap-y-5">
      <div>
        <label for="pelanggan" class="block text-xs font-semibold text-brand mb-2">Nama Pelanggan</label>
        <div class="relative">
          <select id="pelanggan" class="{{ $in }} appearance-none pr-9"><option value="" disabled selected>Pilih nama pelanggan</option></select>
          {!! $chev !!}
        </div>
      </div>

      <div>
        <label for="bayar" class="block text-xs font-semibold text-brand mb-2">Status Pembayaran</label>
        <div class="relative">
          <select id="bayar" class="{{ $in }} appearance-none pr-9">
            <option value="" disabled selected>Pilih status pembayaran</option>
            <option value="Belum Lunas">Belum Lunas</option>
            <option value="Lunas">Lunas</option>
          </select>
          {!! $chev !!}
        </div>
      </div>

      <div>
        <label for="layanan" class="block text-xs font-semibold text-brand mb-2">Layanan</label>
        <div class="relative">
          <select id="layanan" class="{{ $in }} appearance-none pr-9"><option value="" disabled selected>Pilih layanan laundry</option></select>
          {!! $chev !!}
        </div>
      </div>

      <div class="grid grid-cols-[1fr_8rem] gap-4">
        <div>
          <label for="berat" class="block text-xs font-semibold text-brand mb-2">Berat Laundry</label>
          <div class="flex items-center bg-[#EDF0F5] rounded-lg focus-within:ring-2 focus-within:ring-brand-light">
            <input id="berat" type="number" min="0" step="any" placeholder="Masukkan berat"
              class="w-full bg-transparent text-[#2F5B8C] placeholder-[#4F8DB8] px-4 py-2.5 text-xs focus:outline-none">
            <span id="satuan" class="px-3 text-xs font-semibold text-brand border-l border-slate-300">kg</span>
          </div>
        </div>
        <div>
          <p class="block text-xs font-semibold text-brand mb-2">Harga Satuan</p>
          <p id="harga-satuan" class="text-xs font-semibold text-brand pt-2.5">Rp. -</p>
        </div>
      </div>
    </div>

    {{-- Diskon loyalitas: hanya muncul jika pelanggan sudah mencapai kelipatan 10 pesanan --}}
    <div id="blok-diskon" class="hidden mt-5">
      <div class="flex flex-wrap items-center gap-4">
        <label class="flex items-start gap-3 cursor-pointer">
          <input id="pakai-diskon" type="checkbox" class="mt-1 w-4 h-4 rounded border-brand-light accent-[#2F5B8C]">
          <span>
            <span class="block text-xs font-semibold italic text-brand"><span id="info-pesanan">10 Pesanan Tercapai</span> &#127881;</span>
            <span class="block text-[11px] italic text-brand-light">Terapkan diskon pesanan laundry</span>
          </span>
        </label>
        <div class="flex items-center bg-[#EDF0F5] rounded-lg w-44 focus-within:ring-2 focus-within:ring-brand-light">
          <input id="persen" type="number" min="0" max="100" step="any" placeholder="Besar diskon" disabled
            class="w-full bg-transparent text-[#2F5B8C] placeholder-[#4F8DB8] px-4 py-2.5 text-xs focus:outline-none disabled:opacity-60">
          <span class="px-3 text-xs font-semibold text-brand">%</span>
        </div>
      </div>
    </div>

    <button type="button" id="btn-tambah" class="mt-5 bg-brand text-white text-xs px-5 py-2.5 rounded-lg hover:bg-brand-dark">
      Simpan Layanan
    </button>

    <h3 class="mt-8 text-xs font-semibold text-brand">LIST PESANAN LAUNDRY</h3>
    <div class="mt-3 rounded-t-xl overflow-x-auto">
      <table class="w-full min-w-[620px] text-xs text-brand">
        <thead class="bg-[#4F8DB8] text-white">
          <tr>
            <th class="py-2.5 px-3 font-medium w-10">No.</th>
            <th class="py-2.5 px-3 font-medium">Layanan</th>
            <th class="py-2.5 px-3 font-medium">Berat / Jumlah</th>
            <th class="py-2.5 px-3 font-medium">Harga</th>
            <th class="py-2.5 px-3 font-medium">Subtotal</th>
            <th class="py-2.5 px-3 font-medium w-14">Aksi</th>
          </tr>
        </thead>
        <tbody id="tbody" class="text-center"></tbody>
      </table>
    </div>

    <div class="mt-5 pt-4 border-t border-brand-light/30 flex items-end justify-between gap-4">
      <div>
        <p class="text-[11px] font-semibold text-brand">TOTAL BIAYA LAUNDRY</p>
        <p id="info-diskon" class="hidden text-[11px] text-brand-light"></p>
        <p id="total" class="text-sm font-bold text-brand">Rp. 0</p>
      </div>
      <button type="submit" id="btn" class="bg-brand text-white text-xs px-6 py-2.5 rounded-lg hover:bg-brand-dark disabled:opacity-60">Simpan Data</button>
    </div>
  </div>

  <p id="error" class="hidden mt-4 text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2" role="alert"></p>
</form>
@endsection

@push('scripts')
<script>
const $ = (id) => document.getElementById(id);
let layananList = [];
let jenisMap = {};
let items = []; // { id_layanan, qty }

/* ===== Diskon loyalitas =====
   Diskon bisa dipakai bila jumlah pesanan pelanggan sebelumnya adalah kelipatan
   KELIPATAN_DISKON (10, 20, 30, ...), jadi pesanan ini adalah yang ke-11, 21, dst.
   Ubah angka ini bila aturannya berbeda. */
const KELIPATAN_DISKON = 10;
let jumlahPesanan = {}; // id_pengguna -> jumlah pesanan yang sudah ada

/* ===== Kode redeem otomatis (8 karakter, tanpa 0/O/1/I agar mudah dibaca) =====
   Catatan: variabelnya dinamai kodeBaru, BUKAN kodePesanan, karena kodePesanan
   sudah dipakai sebagai fungsi global (layout) di halaman lain. Memakai nama yang
   sama dengan `let` membuat seluruh script error dan kode tidak muncul. */
const HURUF_KODE = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
function buatKode() {
  const acak = new Uint32Array(8);
  crypto.getRandomValues(acak);
  return Array.from(acak, n => HURUF_KODE[n % HURUF_KODE.length]).join('');
}
let kodeBaru = buatKode();
$('kode').textContent = kodeBaru;

$('btn-salin').addEventListener('click', async () => {
  try {
    await navigator.clipboard.writeText(kodeBaru);
  } catch {
    const t = document.createElement('textarea');
    t.value = kodeBaru; document.body.appendChild(t); t.select();
    document.execCommand('copy'); t.remove();
  }
  $('btn-salin').textContent = 'Tersalin';
  setTimeout(() => { $('btn-salin').textContent = 'Salin'; }, 1500);
});

const IKON_HAPUS = '<svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>';
const cari = (id) => layananList.find(l => String(l.id_layanan) === String(id));
const daftarDari = (r) => Array.isArray(r) ? r : (r && r.data) || [];

(async () => {
  try {
    const [l, p, j, ps] = await Promise.all([
      api('/layanan'),
      api('/admin/pengguna'),
      api('/jenis-layanan').catch(() => null),
      api('/admin/pesanan').catch(() => null), // untuk menghitung jumlah pesanan tiap pelanggan
    ]);
    layananList = daftarDari(l);
    daftarDari(j).forEach(x => { jenisMap[x.id_jenis] = x.nama_jenis; });
    daftarDari(ps).forEach(x => {
      const idp = x.id_pengguna ?? x.pengguna?.id_pengguna;
      if (idp != null) jumlahPesanan[idp] = (jumlahPesanan[idp] || 0) + 1;
    });

    $('pelanggan').innerHTML = '<option value="" disabled selected>Pilih nama pelanggan</option>' +
      daftarDari(p).filter(u => u.role === 'pelanggan')
       .map(u => `<option value="${u.id_pengguna}">${esc(u.nama_pengguna)}</option>`).join('');
    $('layanan').innerHTML = '<option value="" disabled selected>Pilih layanan laundry</option>' +
      layananList.map(x => `<option value="${x.id_layanan}">${esc(x.nama_layanan)} - ${esc(x.tipe)}</option>`).join('');
  } catch (e) { showError($('error'), e); }
})();

$('layanan').addEventListener('change', () => {
  const l = cari($('layanan').value);
  $('satuan').textContent = l ? l.satuan : 'kg';
  $('harga-satuan').textContent = l ? `${rp(l.harga)}/${l.satuan}` : 'Rp. -';
});

/* ===== Diskon: tampil / sembunyi sesuai pelanggan yang dipilih ===== */
$('pelanggan').addEventListener('change', () => {
  const n = jumlahPesanan[$('pelanggan').value] || 0;
  const layak = n > 0 && n % KELIPATAN_DISKON === 0;

  $('blok-diskon').classList.toggle('hidden', !layak);
  $('info-pesanan').textContent = n + ' Pesanan Tercapai';

  // ganti pelanggan = reset diskon
  $('pakai-diskon').checked = false;
  $('persen').value = '';
  $('persen').disabled = true;
  gambar();
});

$('pakai-diskon').addEventListener('change', () => {
  const on = $('pakai-diskon').checked;
  $('persen').disabled = !on;
  if (on) $('persen').focus(); else $('persen').value = '';
  gambar();
});

$('persen').addEventListener('input', () => {
  let v = parseFloat($('persen').value);
  if (v > 100) { v = 100; $('persen').value = '100'; }
  if (v < 0) { v = 0; $('persen').value = '0'; }
  gambar();
});

// Persen diskon yang berlaku saat ini (0 bila tidak dipakai)
function persenDiskon() {
  if (!$('pakai-diskon').checked) return 0;
  const v = parseFloat($('persen').value);
  return v > 0 ? Math.min(v, 100) : 0;
}

// Subtotal semua layanan, lalu total setelah diskon
function hitungTotal() {
  const sub = items.reduce((t, it) => {
    const l = cari(it.id_layanan) || {};
    return t + Math.round(Number(l.harga || 0) * it.qty * 100) / 100;
  }, 0);
  const p = persenDiskon();
  const akhir = Math.round(sub * (1 - p / 100));
  return { sub, p, akhir: p > 0 ? akhir : sub };
}

function gambar() {
  $('tbody').innerHTML = items.map((it, i) => {
    const l = cari(it.id_layanan) || {};
    const sub = Math.round(Number(l.harga || 0) * it.qty * 100) / 100;
    const jenis = l.jenis_layanan?.nama_jenis || jenisMap[l.id_jenis] || '';
    return `
    <tr class="border-b border-slate-100">
      <td class="py-3 px-3">${i + 1}.</td>
      <td class="py-3 px-3 text-left">
        <div>${esc(jenis)} (${esc(l.tipe || '')})</div>
        <div>${esc(l.nama_layanan || '-')}</div>
      </td>
      <td class="py-3 px-3">${angka(it.qty)} ${esc(l.satuan || '')}</td>
      <td class="py-3 px-3 whitespace-nowrap">${rp(l.harga)}/${esc(l.satuan || '')}</td>
      <td class="py-3 px-3 whitespace-nowrap">${rp(sub)}</td>
      <td class="py-3 px-3">
        <button type="button" onclick="hapusItem(${i})" title="Hapus" class="w-6 h-6 rounded-full bg-brand text-white inline-flex items-center justify-center hover:bg-brand-dark">${IKON_HAPUS}</button>
      </td>
    </tr>`;
  }).join('') || rowsKosong(6, 'Belum ada layanan yang ditambahkan.');

  const t = hitungTotal();
  $('total').textContent = rp(t.akhir);
  const info = $('info-diskon');
  if (t.p > 0 && t.sub > 0) {
    info.textContent = `Diskon ${angka(t.p)}% (-${rp(t.sub - t.akhir)})`;
    info.classList.remove('hidden');
  } else {
    info.classList.add('hidden');
  }
}

function tambahItem() {
  hideError($('error'));
  const id = $('layanan').value;
  const qty = parseFloat($('berat').value);
  if (!id) return showError($('error'), new Error('Pilih layanan terlebih dahulu.'));
  if (!(qty > 0)) return showError($('error'), new Error('Berat / jumlah harus lebih dari nol.'));

  const ada = items.find(it => String(it.id_layanan) === String(id));
  if (ada) ada.qty = Math.round((ada.qty + qty) * 100) / 100;
  else items.push({ id_layanan: id, qty });

  $('layanan').selectedIndex = 0;
  $('berat').value = '';
  $('satuan').textContent = 'kg';
  $('harga-satuan').textContent = 'Rp. -';
  gambar();
}

function hapusItem(i) { items.splice(i, 1); gambar(); }

$('btn-tambah').addEventListener('click', tambahItem);
$('berat').addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); tambahItem(); } });

$('form').addEventListener('submit', async (e) => {
  e.preventDefault();
  hideError($('error'));

  const idPengguna = $('pelanggan').value;
  const bayar = $('bayar').value;
  if (!idPengguna || !bayar) return showError($('error'), new Error('Pilih nama pelanggan dan status pembayaran.'));
  if (!items.length) return showError($('error'), new Error('Tambahkan minimal satu layanan.'));
  if ($('pakai-diskon').checked && !(persenDiskon() > 0)) {
    return showError($('error'), new Error('Isi besar diskon (1 - 100%) atau hilangkan centang diskon.'));
  }

  const { p, akhir } = hitungTotal();

  $('btn').disabled = true;
  let idPesanan = null;
  try {
    const res = await api('/admin/pesanan', { method: 'POST', body: {
      kode_pesanan: kodeBaru,
      id_pengguna: idPengguna,
      tgl_pesanan: hariIni(),
      status_pesanan: 'Diproses',
      status_pembayaran: bayar,
      total_harga: 0,
    } });
    idPesanan = res.data.id_pesanan;

    for (const it of items) {
      await api('/admin/detail-pesanan', { method: 'POST', body: {
        id_pesanan: idPesanan, id_layanan: it.id_layanan, kuantitas: it.qty,
      } });
    }

    // API menghitung total dari rincian layanan (tanpa diskon).
    // Bila diskon dipakai, total pesanan dikoreksi setelah semua rincian tersimpan.
    if (p > 0) {
      await api('/admin/pesanan/' + idPesanan, { method: 'PUT', body: { total_harga: akhir } });
    }

    location.href = '/admin/pesanan';
  } catch (ex) {
    if (idPesanan) { try { await api('/admin/pesanan/' + idPesanan, { method: 'DELETE' }); } catch {} }
    // kode bentrok dengan pesanan lain -> buat kode baru, admin tinggal klik simpan lagi
    if (ex.status === 422 && ex.errors?.kode_pesanan) {
      kodeBaru = buatKode();
      $('kode').textContent = kodeBaru;
    }
    showError($('error'), ex);
    $('btn').disabled = false;
  }
});

gambar();
</script>
@endpush