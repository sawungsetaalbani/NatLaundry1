<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class PenggunaController extends Controller
{
    public function index()
    {
        return response()->json(
            Pengguna::all()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pengguna' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:pengguna,username',
            'password' => 'required|string|min:8',
            'no_telpon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'role' => 'required|in:admin,pelanggan',
        ]);

        $validated['password'] = Hash::make(
            $validated['password']
        );

        $pengguna = Pengguna::create($validated);

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => $pengguna,
        ], 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Pengguna::findOrFail($id)
        );
    }

    public function update(Request $request, string $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $validated = $request->validate([
            'nama_pengguna' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:pengguna,username,' . $id . ',id_pengguna',
            'password' => 'sometimes|required|string|min:8',
            'no_telpon' => 'sometimes|required|string|max:20',
            'alamat' => 'sometimes|required|string',
            'role' => 'sometimes|required|in:admin,pelanggan',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make(
                $validated['password']
            );
        }

        $pengguna->update($validated);

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui',
            'data' => $pengguna,
        ]);
    }

    public function destroy(string $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        $pengguna->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus',
        ]);
    }
    private function riwayatPelanggan($kode)
    {
        $pesanan = Pesanan::where('kode_pesanan', strtoupper($kode))->first();
        if (!$pesanan) return null;

        $pelanggan = $pesanan->pelanggan;

        $data = Pesanan::with(['pelanggan', 'detailPesanan.layanan.jenisLayanan'])
            ->where('id_pelanggan', $pesanan->id_pelanggan)
            ->where('status_pesanan', 'Selesai')
            ->orderByDesc('tgl_pesanan')
            ->get();

        return compact('pelanggan', 'data');
    }

    public function riwayatByKode($kode)
    {
        $hasil = $this->riwayatPelanggan($kode);

        if (!$hasil) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'pelanggan' => $hasil['pelanggan'],
            'data'      => $hasil['data'],
        ]);
    }

    public function exportRiwayatPdfByKode(Request $request, $kode)
    {
        $hasil = $this->riwayatPelanggan($kode);

        if (!$hasil) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        $data = $hasil['data']->filter(function ($p) use ($request) {
            $t = \Carbon\Carbon::parse($p->tgl_pesanan);
            return $t->month == (int) $request->bulan && $t->year == (int) $request->tahun;
        });

        $pdf = Pdf::loadView('pdf.riwayat', [
            'pelanggan' => $hasil['pelanggan'],
            'data'      => $data,
            'bulan'     => $request->bulan,
            'tahun'     => $request->tahun,
        ]);

        return $pdf->download("riwayat-transaksi-{$request->tahun}-{$request->bulan}.pdf");
    }
}