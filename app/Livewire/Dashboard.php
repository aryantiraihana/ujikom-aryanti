<?php

namespace App\Livewire;

use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $role;
    public $username;

    // admin
    protected $columnChartModel;
    protected $pieChartModel;

    // employee
    public $totalSalesToday;
    public $lastUpdatedAt;

    public function mount()
    {
        $this->username = Auth::user()->name;
        $this->role = Auth::user()->role;

        if ($this->role === 'admin') {
            $this->loadAdminData();
        } elseif ($this->role === 'employee') {
            $this->loadEmployeeData();
        }
    }

    private function loadAdminData()
    {
        $penjualanPerTanggal = DB::table('purchases')
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as total_penjualan'))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $chart = (new ColumnChartModel())
            ->withoutLegend()
            ->withGrid(true);

        foreach ($penjualanPerTanggal as $data) {
            $chart->addColumn(
                \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d M Y'),
                $data->total_penjualan,
                '#60a5fa'
            );
        }

        $this->columnChartModel = $chart;

        $produkTerjual = DB::table('purchase_details')
            ->join('products', 'purchase_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(purchase_details.quantity) as total'))
            ->groupBy('products.name')
            ->get();

        $pieChart = (new PieChartModel())
            ->setTitle('Persentase Penjualan Produk');

        foreach ($produkTerjual as $produk) {
            $totalPenjualan = (int)$produk->total;

            $pieChart->addSlice($produk->name, $totalPenjualan, $this->randomColor());
        }

        $this->pieChartModel = $pieChart;
    }

    private function loadEmployeeData()
    {
        $this->totalSalesToday = Purchase::whereDate('purchase_date', today())->count();
        $this->lastUpdatedAt = optional(Purchase::latest()->first())->updated_at
        ? Carbon::parse(Purchase::latest()->first()->updated_at)->translatedFormat('d F Y H:i')
        : now()->translatedFormat('d F Y H:i');
    }

    private function randomColor()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    public function render()
    {
        return view('livewire.dashboard')->with([
            'role' => $this->role,
            'username' => $this->username,
            'columnChartModel' => $this->columnChartModel,
            'pieChartModel' => $this->pieChartModel,
            'totalSalesToday' => $this->totalSalesToday,
            'lastUpdatedAt' => $this->lastUpdatedAt,
        ]);
    }
}
