@extends('layouts.admin')
@section('title', 'Dashboard')

@section('heading')
  <h1 class="text-3xl text-brand"><span class="font-semibold">Dashboard</span> <span class="font-light">Pendapatan</span></h1>
@endsection

@php
  $kartu = 'border border-brand-light/60 rounded-xl p-4 sm:p-5 relative bg-white min-w-0';
  $lingkar = 'absolute top-4 right-4 w-7 h-7 rounded-full bg-[#E3E9F2] text-brand-light flex items-center justify-center';
  $ikonFilter = '<svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h18l-7 8v6l-4-2v-4z"/></svg>';
  $btnFilter = 'flex items-center gap-2 bg-[#DCE3EC] text-brand text-[11px] px-4 py-1.5 rounded-lg hover:opacity-90';
  $panelFilter = 'panel-filter hidden absolute right-0 mt-2 z-20 w-72 max-w-[calc(100vw-3rem)] bg-white border border-brand-light/60 rounded-xl shadow-lg p-4 sm:p-5';
@endphp

@section('content')
{{-- Kartu ringkasan --}}
<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
  <div class="{{ $kartu }}">
    <span class="{{ $lingkar }}"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2 10h10l2-7H6"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></svg></span>
    <p class="text-xs text-brand pr-8">Pesanan Diproses</p>
    <p id="k-proses" class="mt-2 text-3xl font-bold text-brand">-</p>
    <p id="k-periode" class="text-[10px] text-brand-light text-right"></p>
  </div>

  <div class="{{ $kartu }}">
    <span class="{{ $lingkar }}"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2"/></svg></span>
    <p class="text-xs text-brand pr-8">Pelanggan Aktif</p>
    <p id="k-pelanggan" class="mt-2 text-3xl font-bold text-brand">-</p>
  </div>

  <div class="{{ $kartu }}">
    <span class="{{ $lingkar }}"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2"/></svg></span>
    <p class="text-xs text-brand pr-8">Total Pendapatan</p>
    <p id="k-total" class="mt-2 text-xl sm:text-2xl font-bold text-brand break-words">-</p>
  </div>

  <div class="{{ $kartu }}">
    <span class="{{ $lingkar }}"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2"/></svg></span>
    <p id="k-label-bulan" class="text-xs text-brand pr-8">Pendapatan / Bulan</p>
    <p id="k-bulanan" class="mt-2 text-xl sm:text-2xl font-bold text-brand break-words">-</p>
  </div>
</div>

<div class="mt-5 grid xl:grid-cols-[minmax(0,1fr)_22rem] gap-4 items-start">
  <div class="space-y-4 min-w-0">
    {{-- Grafik Penjualan --}}
    <div class="border border-brand-light/60 rounded-xl p-4 sm:p-5 bg-white min-w-0">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <h2 class="font-semibold text-brand">Grafik Penjualan</h2>
          <p id="sub-penjualan" class="text-[10px] text-brand-light uppercase"></p>
        </div>
        <div class="relative">
          <button id="btn-filter-penjualan" type="button" class="{{ $btnFilter }}">{!! $ikonFilter !!} Filter data</button>
          <div id="panel-penjualan" class="{{ $panelFilter }}"></div>
        </div>
      </div>
      <div class="relative h-56 sm:h-64 xl:h-72 mt-4"><canvas id="grafik"></canvas></div>
    </div>

    {{-- Grafik Pertumbuhan Pelanggan --}}
    <div class="border border-brand-light/60 rounded-xl p-4 sm:p-5 bg-white min-w-0">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <h2 class="font-semibold text-brand">Grafik Pertumbuhan Pelanggan</h2>
          <p id="sub-pelanggan" class="text-[10px] text-brand-light uppercase"></p>
        </div>
        <div class="relative">
          <button id="btn-filter-pelanggan" type="button" class="{{ $btnFilter }}">{!! $ikonFilter !!} Filter data</button>
          <div id="panel-pelanggan" class="{{ $panelFilter }}"></div>
        </div>
      </div>
      <div class="relative h-56 sm:h-64 xl:h-72 mt-4"><canvas id="grafik-pelanggan"></canvas></div>
    </div>
  </div>

  {{-- Data pesanan diproses --}}
  <div class="border border-brand-light/60 rounded-xl p-4 sm:p-5 bg-white min-w-0">
    <div class="flex flex-wrap items-start justify-between gap-3">
      <div>
        <h2 class="font-semibold text-brand">Data Pesanan</h2>
        <p class="text-[10px] text-brand-light">DIPROSES</p>
      </div>
      <a href="/admin/pesanan" class="bg-brand text-white text-[11px] px-3 py-1.5 rounded-lg hover:bg-brand-dark">Detail data</a>
    </div>

    <div class="mt-4 rounded-t-lg overflow-x-auto">
      <table class="w-full min-w-[260px] text-[11px] sm:text-xs xl:text-[11px] text-brand">
        <thead class="bg-[#4F8DB8] text-white">
          <tr>
            <th class="py-2 px-2 font-medium w-8">No.</th>
            <th class="py-2 px-2 font-medium">Kode Pesanan</th>
            <th class="py-2 px-2 font-medium">Nama Pelanggan</th>
          </tr>
        </thead>
        <tbody id="tbody" class="text-center"></tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const $ = (id) => document.getElementById(id);

const BULAN_PENUH = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const BULAN_PENDEK = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agust','Sep','Okt','Nov','Des'];
const WARNA = ['#F2A0A0','#F5D9A0','#B5E0A0','#A0E8F0','#9EC5F0','#E3B5B5','#D8B8A0','#8E8E8E','#F2E8A0','#DDE3EE','#DDE3EE','#DDE3EE'];
const WARNA_PELANGGAN = ['#B5E0A0','#E3B5B5','#F5D9A0','#F2A0A0','#9EC5F0','#F5E0A0','#8E8E8E','#D8B8A0','#A0E8F0','#DDE3EE','#DDE3EE','#DDE3EE'];

const rpt = (n) => 'Rp. ' + Number(n || 0).toLocaleString('id-ID');
// Sama seperti halaman Data Pesanan: kode tersimpan, atau turunan dari id bila kosong
const kodeDari = (x) => x.kode_pesanan || kodePesanan(x.id_pesanan);
const tg = (s) => (s ? String(s).slice(0, 10).split('-').reverse().join('/') : '-');

const sekarang = new Date();
const tahunIni = sekarang.getFullYear();
const bulanIni = sekarang.getMonth() + 1;

function singkat(v) {
  if (v >= 1e6) return (v / 1e6).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' Jt';
  if (v >= 1e3) return (v / 1e3).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + 'K';
  return v;
}

/* ================== KOMPONEN FILTER (popup "Filtering Data Grafik") ================== */
const TAHUN_OPSI = Array.from({ length: 5 }, (_, i) => tahunIni - i); // 2026 ... 2022
const opsiTahun = (sel) => TAHUN_OPSI.map(t => `<option value="${t}" ${t == sel ? 'selected' : ''}>${t}</option>`).join('');
const opsiBulan = (sel) => BULAN_PENUH.map((b, i) => `<option value="${i + 1}" ${i + 1 == sel ? 'selected' : ''}>${b}</option>`).join('');

const CHEVRON = '<svg class="w-3 h-3 absolute right-3 top-1/2 -translate-y-1/2 text-brand pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>';
const SEL = 'w-full appearance-none border border-brand-light/60 rounded-lg pl-3 pr-8 py-1.5 text-xs text-brand bg-white focus:outline-none focus:border-brand';
const selBox = (key, isi) => `<span class="relative block flex-1"><select data-k="${key}" class="${SEL}">${isi}</select>${CHEVRON}</span>`;

const JUDUL = '<h3 class="text-center text-xs font-semibold text-brand">Filtering Data Grafik</h3>';
const LABEL = (t) => `<p class="text-[11px] text-brand">${t}</p>`;
const FOOTER = `
  <div class="mt-5 flex justify-end gap-2">
    <button type="button" data-aksi="reset" class="flex items-center gap-1.5 bg-[#DCE3EC] text-brand-light text-[10px] px-3 py-1.5 rounded-md hover:opacity-90">Reset Filter <span>&times;</span></button>
    <button type="button" data-aksi="terapkan" class="bg-brand text-white text-[10px] px-4 py-1.5 rounded-md hover:bg-brand-dark">Terapkan</button>
  </div>`;

function tutupSemuaFilter() {
  document.querySelectorAll('.panel-filter').forEach(p => p.classList.add('hidden'));
}
document.addEventListener('click', tutupSemuaFilter);

function pasangFilter({ btn, panel, awal, bentuk, terapkan }) {
  let state = awal();
  let draft = { ...state };
  const render = () => { panel.innerHTML = bentuk(draft); };

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const mauBuka = panel.classList.contains('hidden');
    tutupSemuaFilter();
    if (mauBuka) {
      draft = { ...state };
      render();
      panel.classList.remove('hidden');
    }
  });

  panel.addEventListener('click', (e) => {
    e.stopPropagation(); // klik di dalam popup jangan menutupnya
    const m = e.target.closest('[data-mode]');
    if (m) { draft.mode = m.dataset.mode; render(); return; }

    const a = e.target.closest('[data-aksi]');
    if (!a) return;
    if (a.dataset.aksi === 'reset') {
      state = awal();
      draft = { ...state };
      render();
      terapkan(state);
    } else if (a.dataset.aksi === 'terapkan') {
      state = { ...draft };
      if (state.from > state.to) [state.from, state.to] = [state.to, state.from];
      panel.classList.add('hidden');
      terapkan(state);
    }
  });

  panel.addEventListener('change', (e) => {
    const k = e.target.dataset.k;
    if (k) draft[k] = Number(e.target.value);
  });

  terapkan(state);
}

/* ================== GRAFIK ================== */
function buatGrafik(canvasId, lama, labels, nilai, palet, labelTooltip) {
  if (lama) lama.destroy();
  return new Chart($(canvasId), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        data: nilai,
        backgroundColor: nilai.map((_, i) => palet[i % palet.length]),
        borderRadius: 8,
        borderSkipped: false,
        barPercentage: 0.7,
        categoryPercentage: 0.85,
        maxBarThickness: 36,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: (c) => labelTooltip(c.parsed.y) } },
      },
      scales: {
        x: { grid: { display: false }, border: { display: false }, ticks: { color: '#2F5B8C', font: { size: window.innerWidth < 640 ? 9 : 10 }, maxRotation: 0, autoSkip: true, maxTicksLimit: window.innerWidth < 640 ? 7 : 12 } },
        y: {
          beginAtZero: true,
          grid: { display: false },
          border: { display: false },
          ticks: { color: '#2F5B8C', font: { size: window.innerWidth < 640 ? 9 : 10 }, callback: singkat, precision: 0, maxTicksLimit: 6 },
        },
      },
    },
  });
}

/* ---- Grafik Penjualan: TAHUNAN / BULANAN / MINGGUAN ---- */
let chartPenjualan = null;

const bentukPenjualan = (d) => {
  const tombol = (m, t) =>
    `<button type="button" data-mode="${m}" class="flex-1 text-[10px] py-1.5 rounded-md ${d.mode === m ? 'bg-brand text-white' : 'bg-[#DCE3EC] text-brand'}">${t}</button>`;

  let isi = '';
  if (d.mode === 'tahunan') {
    isi = `${LABEL('Rentang Tahun')}
      <div class="mt-2 flex items-center gap-2">
        ${selBox('from', opsiTahun(d.from))}
        <span class="text-[10px] text-brand">s.d</span>
        ${selBox('to', opsiTahun(d.to))}
      </div>`;
  } else if (d.mode === 'mingguan') {
    isi = `${LABEL('Sesuaikan bulan dan tahun!')}
      <div class="mt-2 flex items-center gap-2">
        ${selBox('month', opsiBulan(d.month))}
        ${selBox('year', opsiTahun(d.year))}
      </div>`;
  } else {
    isi = `${LABEL('Pilih tahun pendapatan')}
      <div class="mt-2 flex">${selBox('year', opsiTahun(d.year))}</div>`;
  }

  return `${JUDUL}
    <p class="mt-4 text-[11px] text-brand">Pilih Jenis Filtering</p>
    <div class="mt-2 flex gap-2">${tombol('tahunan', 'TAHUNAN')}${tombol('bulanan', 'BULANAN')}${tombol('mingguan', 'MINGGUAN')}</div>
    <hr class="my-4 border-t border-dashed border-slate-300">
    ${isi}
    ${FOOTER}`;
};

async function muatGrafikPenjualan(s) {
  let url, sub;
  if (s.mode === 'tahunan') {
    url = `/admin/dashboard/grafik-penjualan?mode=tahunan&from=${s.from}&to=${s.to}`;
    sub = `TAHUN - ${s.from} S.D ${s.to}`;
  } else if (s.mode === 'mingguan') {
    url = `/admin/dashboard/grafik-penjualan?mode=mingguan&year=${s.year}&month=${s.month}`;
    sub = `MINGGU - ${BULAN_PENUH[s.month - 1]} ${s.year}`;
  } else {
    url = `/admin/dashboard/grafik-penjualan?mode=bulanan&year=${s.year}`;
    sub = `BULAN - ${s.year}`;
  }
  $('sub-penjualan').textContent = sub;
  try {
    const g = await api(url);
    const labels = g.data.map((x, i) => x.label ?? BULAN_PENDEK[i]);
    chartPenjualan = buatGrafik('grafik', chartPenjualan, labels, g.data.map(x => x.total), WARNA, rpt);
  } catch (e) { alert(e.message); }
}

pasangFilter({
  btn: $('btn-filter-penjualan'),
  panel: $('panel-penjualan'),
  awal: () => ({ mode: 'bulanan', from: tahunIni, to: tahunIni, year: tahunIni, month: bulanIni }),
  bentuk: bentukPenjualan,
  terapkan: muatGrafikPenjualan,
});

/* ---- Grafik Pertumbuhan Pelanggan: pilih tahun ---- */
let chartPelanggan = null;

const bentukPelanggan = (d) => `${JUDUL}
  <div class="mt-4">${LABEL('Rentang Tahun')}
    <div class="mt-2 w-32 flex">${selBox('year', opsiTahun(d.year))}</div>
  </div>
  ${FOOTER}`;

async function muatGrafikPelanggan(s) {
  $('sub-pelanggan').textContent = `BERDASARKAN BULAN - ${s.year}`;
  try {
    const g = await api(`/admin/dashboard/grafik-pelanggan?year=${s.year}`);
    chartPelanggan = buatGrafik('grafik-pelanggan', chartPelanggan, BULAN_PENDEK, g.data.map(x => x.total),
      WARNA_PELANGGAN, (v) => v + ' pelanggan');
  } catch (e) { alert(e.message); }
}

pasangFilter({
  btn: $('btn-filter-pelanggan'),
  panel: $('panel-pelanggan'),
  awal: () => ({ year: tahunIni }),
  bentuk: bentukPelanggan,
  terapkan: muatGrafikPelanggan,
});

/* ================== RINGKASAN & TABEL ================== */
async function muatRingkasan() {
  try {
    const d = await api(`/admin/dashboard?year=${tahunIni}&month=${bulanIni}`);
    $('k-proses').textContent = d.pesanan_diproses;
    $('k-periode').textContent = BULAN_PENUH[bulanIni - 1] + ' ' + tahunIni;
    $('k-pelanggan').textContent = d.pelanggan_aktif;
    $('k-total').textContent = rpt(d.total_pendapatan);
    $('k-label-bulan').textContent = 'Pendapatan / ' + BULAN_PENUH[bulanIni - 1];
    $('k-bulanan').textContent = rpt(d.pendapatan_bulanan.total);
  } catch (e) { alert(e.message); }
}

async function muatPesanan() {
  try {
    // Sumber data sama dengan halaman Data Pesanan; "diproses" = belum selesai / belum dibatalkan
    const res = await api('/admin/pesanan');
    const diproses = (res.data || [])
      .filter(x => {
        const st = String(x.status_pesanan || '').toLowerCase();
        return st !== 'selesai' && !st.includes('batal');
      })
      .sort((a, b) => b.id_pesanan - a.id_pesanan) // terbaru di atas
      .slice(0, 3);
    $('tbody').innerHTML = diproses.map((x, i) => `
      <tr class="border-b border-slate-100">
        <td class="py-3 px-2">${i + 1}.</td>
        <td class="py-3 px-2 whitespace-nowrap">${esc(kodeDari(x))}</td>
        <td class="py-3 px-2 font-semibold break-words">${esc(x.pengguna?.nama_pengguna || '-')}</td>
      </tr>`).join('') || rowsKosong(3, 'Tidak ada pesanan diproses.');
  } catch (e) { $('tbody').innerHTML = rowsKosong(3, esc(e.message)); }
}

muatRingkasan();
muatPesanan();
</script>
@endpush