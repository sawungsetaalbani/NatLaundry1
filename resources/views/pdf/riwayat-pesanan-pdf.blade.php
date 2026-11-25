@php
  $namaBulan = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
  $totalSemua = $data->sum('total_harga');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Riwayat Pesanan - NAT Laundry</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e3a5f; }
    h1 { font-size: 18px; margin: 0; color: #2F5B8C; }
    .sub { margin: 2px 0 14px; color: #64748b; font-size: 10px; }
    .info { width: 100%; background: #E3ECF6; border-radius: 6px; padding: 10px 12px; margin-bottom: 14px; }
    .info td { border: none; padding: 2px 6px; vertical-align: top; }
    .label { color: #64748b; width: 70px; }
    table.data { width: 100%; border-collapse: collapse; }
    table.data th, table.data td { border: 1px solid #94a3b8; padding: 6px 8px; text-align: left; vertical-align: top; }
    table.data th { background: #2F5B8C; color: #fff; font-size: 10px; }
    .kanan { text-align: right; }
    .tengah { text-align: center; }
    .total td { font-weight: bold; background: #E3ECF6; }
    .kosong { text-align: center; color: #64748b; padding: 16px; }
    .footer { margin-top: 18px; font-size: 9px; color: #64748b; text-align: center; }
  </style>
</head>
<body>
  <h1>NAT Laundry</h1>
  <p class="sub">Riwayat Pesanan &mdash; {{ $namaBulan[(int) $bulan] ?? $bulan }} {{ $tahun }}</p>

  <table class="info">
    <tr>
      <td class="label">Nama</td><td>: {{ $pelanggan->nama_pengguna ?? '-' }}</td>
    </tr>
    <tr>
      {{-- Nama kolom di database: no_telpon --}}
      <td class="label">Telepon</td><td>: {{ $pelanggan->no_telpon ?? '-' }}</td>
    </tr>
    <tr>
      <td class="label">Alamat</td><td>: {{ $pelanggan->alamat ?? '-' }}</td>
    </tr>
  </table>

  <table class="data">
    <thead>
      <tr>
        <th class="tengah" style="width:24px">No</th>
        <th>Kode</th>
        <th>Tanggal</th>
        <th>Layanan</th>
        <th>Pembayaran</th>
        <th class="kanan">Total Harga</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($data as $i => $p)
        <tr>
          <td class="tengah">{{ $i + 1 }}</td>
          <td>{{ $p->kode_pesanan }}</td>
          <td>{{ \Carbon\Carbon::parse($p->tgl_pesanan)->format('d/m/Y H.i') }}</td>
          <td>
            @foreach ($p->detailPesanan as $d)
              {{ $d->layanan->nama_layanan ?? '-' }} ({{ $d->kuantitas }} {{ $d->layanan->satuan ?? '' }})@if (!$loop->last)<br>@endif
            @endforeach
          </td>
          <td>{{ strtoupper($p->status_pembayaran) }}</td>
          <td class="kanan">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="6" class="kosong">Tidak ada riwayat pesanan pada periode ini.</td></tr>
      @endforelse
    </tbody>
    @if ($data->count())
      <tfoot>
        <tr class="total">
          <td colspan="5" class="kanan">Total Keseluruhan</td>
          <td class="kanan">Rp {{ number_format($totalSemua, 0, ',', '.') }}</td>
        </tr>
      </tfoot>
    @endif
  </table>

  <p class="footer">Dicetak pada {{ now()->format('d/m/Y H.i') }} &middot; NAT Laundry</p>
</body>
</html>