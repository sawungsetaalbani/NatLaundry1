<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Throwable;


class PesananController extends Controller
{
    public function index()
    {
        try {
            $pesanan = Pesanan::with([
                'pengguna',
                'detailPesanan.layanan'
            ])->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pesanan berhasil ditemukan.',
                'data' => $pesanan,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    public function store(Request $request)
    {
        try {
            // Kode dari halaman admin selalu huruf besar
            if ($request->filled('kode_pesanan')) {
                $request->merge(['kode_pesanan' => strtoupper(trim($request->input('kode_pesanan')))]);
            }

            $validated = $request->validate([
                // Opsional: bila dikirim (halaman Tambah Pesanan), kode ini yang dipakai.
                // Bila tidak dikirim, model Pesanan membuat kode otomatis.
                'kode_pesanan' => 'sometimes|nullable|string|size:8|unique:pesanan,kode_pesanan',
                'id_pengguna' => 'required|exists:pengguna,id_pengguna',
                'tgl_pesanan' => 'required|date',
                'status_pesanan' => 'sometimes|required|in:Diproses,Selesai',
                'total_harga' => 'sometimes|required|numeric|min:0',
                'status_pembayaran' => 'sometimes|required|in:Belum Lunas,Lunas',
            ], [
                'kode_pesanan.size' => 'Kode pesanan harus 8 karakter.',
                'kode_pesanan.unique' => 'Kode pesanan sudah dipakai.',
                'id_pengguna.required' => 'Pelanggan wajib dipilih.',
                'id_pengguna.exists' => 'Pelanggan tidak ditemukan.',
                'tgl_pesanan.required' => 'Tanggal pesanan wajib diisi.',
                'tgl_pesanan.date' => 'Format tanggal pesanan tidak valid.',
                'status_pesanan.required' => 'Status pesanan harus Diproses atau Selesai.',
                'total_harga.numeric' => 'Total harga harus berupa angka.',
                'total_harga.min' => 'Total harga tidak boleh kurang dari nol.',
                'status_pembayaran.in' => 'Status pembayaran harus Belum Lunas atau Lunas.',
            ]);

            // Kode dari halaman admin dipakai; jika tidak dikirim, dibuat di sini.
            // Diisi langsung (tidak lewat $fillable) supaya pasti tersimpan di database.
            $kode = $validated['kode_pesanan'] ?? null;
            unset($validated['kode_pesanan']);
            if (!$kode) {
                $kode = $this->buatKodeUnik();
            }

            $pesanan = new Pesanan($validated);
            $pesanan->kode_pesanan = $kode;
            $pesanan->save();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil ditambahkan.',
                'data' => $pesanan->load([
                    'pengguna',
                    'detailPesanan.layanan'
                ]),
            ], 201);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Pesanan gagal disimpan karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    public function show(string $id)
    {
        try {
            $pesanan = Pesanan::with([
                'pengguna',
                'detailPesanan.layanan'
            ])->find($id);

            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail pesanan berhasil ditemukan.',
                'data' => $pesanan,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $pesanan = Pesanan::find($id);

            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            $validated = $request->validate([
                'id_pengguna' => 'sometimes|required|exists:pengguna,id_pengguna',
                'tgl_pesanan' => 'sometimes|required|date',
                'status_pesanan' => 'sometimes|required|string|max:255',
                'total_harga' => 'sometimes|required|numeric|min:0',
                'status_pembayaran' => 'sometimes|required|in:Belum Lunas,Lunas',
            ], [
                'id_pengguna.exists' => 'Pelanggan tidak ditemukan.',
                'tgl_pesanan.date' => 'Format tanggal pesanan tidak valid.',
                'total_harga.numeric' => 'Total harga harus berupa angka.',
                'total_harga.min' => 'Total harga tidak boleh kurang dari nol.',
                'status_pembayaran.in' => 'Status pembayaran harus Belum Lunas atau Lunas.',
            ]);

            $pesanan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil diperbarui.',
                'data' => $pesanan->fresh()->load([
                    'pengguna',
                    'detailPesanan.layanan'
                ]),
            ], 200);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Pesanan gagal diperbarui karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    public function destroy(string $id)
    {
        try {
            $pesanan = Pesanan::find($id);

            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            $pesanan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dihapus.',
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat dihapus karena masih memiliki detail atau data terkait.',
            ], 409);
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIK: cek pesanan lewat kode (tanpa login)
    |--------------------------------------------------------------------------
    */

    private function cariByKode(string $kode)
    {
        return Pesanan::with(['pengguna', 'detailPesanan.layanan.jenisLayanan'])
            ->where('kode_pesanan', strtoupper($kode))
            ->first();
    }

    public function byKode(string $kode)
    {
        try {
            $pesanan = $this->cariByKode($kode);

            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json(['success' => true, 'data' => $pesanan], 200);
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    public function exportPdfByKode(string $kode)
    {
        try {
            $pesanan = $this->cariByKode($kode);

            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            // File: resources/views/pdf/pesanan-pdf.blade.php
            $pdf = Pdf::loadView('pdf.pesanan-pdf', ['pesanan' => $pesanan]);

            return $pdf->download('pesanan-' . $pesanan->kode_pesanan . '.pdf');
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PUBLIK: riwayat pesanan (semua pesanan Selesai milik pemilik kode)
    |--------------------------------------------------------------------------
    */

    private function riwayatPelanggan(string $kode): ?array
    {
        $pesanan = Pesanan::with('pengguna')
            ->where('kode_pesanan', strtoupper($kode))
            ->first();

        if (!$pesanan) {
            return null;
        }

        $data = Pesanan::with(['pengguna', 'detailPesanan.layanan.jenisLayanan'])
            ->where('id_pengguna', $pesanan->id_pengguna)
            ->where('status_pesanan', 'Selesai')
            ->orderByDesc('tgl_pesanan')
            ->get();

        return [
            'pelanggan' => $pesanan->pengguna,
            'data' => $data,
        ];
    }

    public function riwayatByKode(string $kode)
    {
        try {
            $hasil = $this->riwayatPelanggan($kode);

            if (!$hasil) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'pelanggan' => $hasil['pelanggan'],
                'data' => $hasil['data'],
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    public function exportRiwayatPdfByKode(Request $request, string $kode)
    {
        try {
            $hasil = $this->riwayatPelanggan($kode);

            if (!$hasil) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            $bulan = (int) $request->query('bulan');
            $tahun = (int) $request->query('tahun');

            $data = $hasil['data']->filter(function ($p) use ($bulan, $tahun) {
                $t = Carbon::parse($p->tgl_pesanan);
                return $t->month === $bulan && $t->year === $tahun;
            })->values();

            // File: resources/views/pdf/riwayat-pesanan-pdf.blade.php
            $pdf = Pdf::loadView('pdf.riwayat-pesanan-pdf', [
                'pelanggan' => $hasil['pelanggan'],
                'data' => $data,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);

            return $pdf->download("riwayat-transaksi-{$tahun}-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.pdf');
        } catch (Throwable $e) {
            return $this->serverError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helper respon error
    |--------------------------------------------------------------------------
    */

    // Kode acak 8 karakter (tanpa 0/O/1/I), dijamin belum dipakai pesanan lain
    private function buatKodeUnik(): string
    {
        $huruf = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $kode = '';
            for ($i = 0; $i < 8; $i++) {
                $kode .= $huruf[random_int(0, strlen($huruf) - 1)];
            }
        } while (Pesanan::where('kode_pesanan', $kode)->exists());

        return $kode;
    }

    private function validationError(ValidationException $e)
    {
        $errors = $e->errors();

        $message = collect($errors)->flatten()->first()
            ?? 'Data yang dikirim tidak valid.';

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    private function serverError(?Throwable $e = null)
    {
        if ($e) {
            // Catat error asli ke storage/logs/laravel.log
            report($e);
        }

        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan pada server.',
        ], 500);
    }
}