<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e3a5f; }
    h2 { margin-bottom: 4px; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border: 1px solid #94a3b8; padding: 6px 8px; text-align: left; }
    th { background: #e2e8f0; }
    .kanan { text-align: right; }
    .total { text-align: right; font-weight: bold; margin-top: 12px; }
  </style>
</head>
<body>
  <h2>NAT Laundry</h2>
  <p>Kode Pesanan: <b>{{ $pesanan->kode_pesanan }}</b></p>
  {{-- Relasi di model: pengguna, kolom nama: nama_pengguna --}}
  <p>Pelanggan: {{ $pesanan->pengguna->nama_pengguna ?? '-' }}</p>
  <p>Telepon: {{ $pesanan->pengguna->no_telpon ?? '-' }}</p>
  <p>Tanggal: {{ \Carbon\Carbon::parse($pesanan->tgl_pesanan ?? $pesanan->created_at)->format('d/m/Y H.i') }}</p>
  <p>Status: {{ $pesanan->status_pesanan }} | Pembayaran: {{ $pesanan->status_pembayaran }}</p>

  <table>
    <thead>
      <tr>
        <th>Layanan</th>
        <th>Jumlah</th>
        <th class="kanan">Harga</th>
        <th class="kanan">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($pesanan->detailPesanan as $d)
        <tr>
          <td>{{ $d->layanan->nama_layanan ?? '-' }}</td>
          <td>{{ $d->kuantitas }} {{ $d->layanan->satuan ?? '' }}</td>
          <td class="kanan">Rp {{ number_format($d->harga_satuan ?? 0, 0, ',', '.') }}</td>
          <td class="kanan">Rp {{ number_format($d->subtotal ?? 0, 0, ',', '.') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <p class="total">Total Harga: Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
</body>
</html>