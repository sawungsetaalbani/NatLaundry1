<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Pengguna;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) $request->query('year', now()->year);
        $bulan = (int) $request->query('month', now()->month);

        if (
            $tahun < 2000 ||
            $tahun > 2100 ||
            $bulan < 1 ||
            $bulan > 12
        ) {
            return response()->json([
                'message' => 'Bulan atau tahun tidak valid.'
            ], 422);
        }

        $pesananLunas = Pesanan::where(
            'status_pembayaran',
            'Lunas'
        );

        $totalPendapatan = (clone $pesananLunas)
            ->sum('total_harga');

        $pendapatanBulanan = (clone $pesananLunas)
            ->whereYear('tgl_pesanan', $tahun)
            ->whereMonth('tgl_pesanan', $bulan)
            ->sum('total_harga');

        return response()->json([
            'pesanan_diproses' => Pesanan::where(
                'status_pesanan',
                'Diproses'
            )->count(),

            'pelanggan_aktif' => Pengguna::where(
                'role',
                'pelanggan'
            )->count(),

            'total_pendapatan' => (float) $totalPendapatan,

            'pendapatan_bulanan' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'total' => (float) $pendapatanBulanan,
            ],
        ]);
    }

    /**
     * Grafik penjualan (pesanan Lunas).
     * mode=tahunan  -> from, to
     * mode=bulanan  -> year            (default)
     * mode=mingguan -> year, month
     */
    public function grafikPenjualan(Request $request)
    {
        $mode = $request->query('mode', 'bulanan');

        $dasar = fn () => Pesanan::query()->where('status_pembayaran', 'Lunas');

        if ($mode === 'tahunan') {
            $from = (int) $request->query('from', now()->year);
            $to   = (int) $request->query('to', $from);
            if ($from > $to) {
                [$from, $to] = [$to, $from];
            }

            if ($from < 2000 || $to > 2100) {
                return response()->json(['message' => 'Rentang tahun tidak valid.'], 422);
            }

            $rows = $dasar()
                ->whereYear('tgl_pesanan', '>=', $from)
                ->whereYear('tgl_pesanan', '<=', $to)
                ->selectRaw('YEAR(tgl_pesanan) as k, SUM(total_harga) as total')
                ->groupBy('k')
                ->pluck('total', 'k');

            $data = [];
            for ($t = $from; $t <= $to; $t++) {
                $data[] = ['label' => (string) $t, 'total' => (float) ($rows[$t] ?? 0)];
            }
        } elseif ($mode === 'mingguan') {
            $year  = (int) $request->query('year', now()->year);
            $month = (int) $request->query('month', now()->month);

            if ($year < 2000 || $year > 2100 || $month < 1 || $month > 12) {
                return response()->json(['message' => 'Bulan atau tahun tidak valid.'], 422);
            }

            $jumlahMinggu = (int) ceil(Carbon::create($year, $month, 1)->daysInMonth / 7);

            $rows = $dasar()
                ->whereYear('tgl_pesanan', $year)
                ->whereMonth('tgl_pesanan', $month)
                ->selectRaw('CEIL(DAY(tgl_pesanan) / 7) as k, SUM(total_harga) as total')
                ->groupBy('k')
                ->pluck('total', 'k');

            $data = [];
            for ($m = 1; $m <= $jumlahMinggu; $m++) {
                $data[] = ['label' => 'Mg ' . $m, 'total' => (float) ($rows[$m] ?? 0)];
            }
        } else { // bulanan
            $year = (int) $request->query('year', now()->year);

            if ($year < 2000 || $year > 2100) {
                return response()->json(['message' => 'Tahun tidak valid.'], 422);
            }

            $nama = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agust', 'Sep', 'Okt', 'Nov', 'Des'];

            $rows = $dasar()
                ->whereYear('tgl_pesanan', $year)
                ->selectRaw('MONTH(tgl_pesanan) as k, SUM(total_harga) as total')
                ->groupBy('k')
                ->pluck('total', 'k');

            $data = [];
            for ($b = 1; $b <= 12; $b++) {
                $data[] = ['label' => $nama[$b - 1], 'total' => (float) ($rows[$b] ?? 0)];
            }
        }

        return response()->json([
            'success' => true,
            'mode'    => $mode,
            'data'    => $data,
        ]);
    }

    public function grafikPelanggan(Request $request)
    {
        $year = (int) $request->query('year', now()->year);

        $perBulan = DB::table('pengguna')
            ->where('role', 'pelanggan')
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $data = [];
        for ($b = 1; $b <= 12; $b++) {
            $data[] = ['bulan' => $b, 'total' => (int) ($perBulan[$b] ?? 0)];
        }

        return response()->json(['success' => true, 'year' => $year, 'data' => $data]);
    }

    public function pesananDiproses()
    {
        $pesanan = Pesanan::with('pengguna:id_pengguna,nama_pengguna')
            ->select([
                'id_pesanan',
                'id_pengguna',
                'tgl_pesanan',
                'status_pesanan',
                'total_harga',
                'status_pembayaran',
            ])
            ->where('status_pesanan', 'Diproses')
            ->orderByDesc('tgl_pesanan')
            ->orderByDesc('id_pesanan')
            ->paginate(10);

        return response()->json($pesanan);
    }
}