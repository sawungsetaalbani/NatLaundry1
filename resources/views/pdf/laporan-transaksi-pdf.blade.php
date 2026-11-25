@php
  $fmtTgl = fn ($t) => $t ? \Carbon\Carbon::parse($t)->format('d/m/Y') : null;
  $mulai = $fmtTgl($tanggalMulai);
  $akhir = $fmtTgl($tanggalAkhir);

  if ($mulai && $akhir)      $periode = $mulai . ' s/d ' . $akhir;
  elseif ($mulai)            $periode = 'Mulai ' . $mulai;
  elseif ($akhir)            $periode = 'Sampai ' . $akhir;
  else                       $periode = 'Semua tanggal';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Transaksi - NAT Laundry</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e3a5f; }
    h1 { font-size: 18px; margin: 0; color: #2F5B8C; }
    .sub { margin: 2px 0 12px; color: #64748b; font-size: 10px; }
    .info { width: 100%; background: #E3ECF6; margin-bottom: 12px; }
    .info td { padding: 6px 10px; border: none; }
    .label { color: #64748b; }
    table.data { width: 100%; border-collapse: collapse; }
    table.data th, table.data td { border: 1px solid #94a3b8; padding: 5px 7px; text-align: left; vertical-align: top; }
    table.data th { background: #2F5B8C; color: #fff; font-size: 10px; }
    .kanan { text-align: right; }
    .tengah { text-align: center; }
    .total td { font-weight: bold; background: #E3ECF6; }
    .kosong { text-align: center; color: #64748b; padding: 14px; }
    .footer { margin-top: 14px; font-size: 9px; color: #64748b; text-align: center; }
  </style>
</head>
<body>
  <h1>NAT Laundry</h1>
  <p class="sub">Laporan Transaksi</p>

  <table class="info">
    <tr>
      <td><span class="label">Periode</span> : {{ $periode }}</td>
      <td><span class="label">Pembayaran</span> : {{ $statusPembayaran ?? 'Semua' }}</td>
      <td><span class="label">Jumlah Pesanan</span> : {{ $pesanan->count() }}</td>
    </tr>
  </table>

  <table class="data">
    <thead>
      <tr>
        <th class="tengah" style="width:24px">No</th>
        <th>Kode Pesanan</th>
        <th>Pelanggan</th>
        <th>Tanggal</th>
        <th>Status Pesanan</th>
        <th>Pembayaran</th>
        <th class="kanan">Total</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($pesanan as $i => $p)
        <tr>
          <td class="tengah">{{ $i + 1 }}</td>
          <td>{{ $p->kode_pesanan ?? '-' }}</td>
          <td>{{ $p->pengguna->nama_pengguna ?? '-' }}</td>
          <td>{{ \Carbon\Carbon::parse($p->tgl_pesanan)->format('d/m/Y') }}</td>
          <td>{{ strtoupper($p->status_pesanan) }}</td>
          <td>{{ strtoupper($p->status_pembayaran) }}</td>
          <td class="kanan">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="7" class="kosong">Tidak ada transaksi pada periode ini.</td></tr>
      @endforelse
    </tbody>
    @if ($pesanan->count())
      <tfoot>
        <tr class="total">
          <td colspan="6" class="kanan">Total Pendapatan (Lunas)</td>
          <td class="kanan">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
        </tr>
      </tfoot>
    @endif
  </table>

  <p class="footer">Dicetak pada {{ now()->format('d/m/Y H.i') }} &middot; NAT Laundry</p>
</body>
</html>