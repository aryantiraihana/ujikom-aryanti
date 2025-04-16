<div>
    <div class="container my-5">
        @if (Session::get('loginSuccess'))
            <div class="alert alert-success">{{ Session::get('loginSuccess') }}</div>
        @endif
        <h4 class="mb-5">Dashboard</h4>
        @if ($role == 'admin')
            <div class="card p-4">
                <h5 class="mb-4">Selamat Datang, {{ $username }}!</h5>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div style="min-height: 300px; height: 100%; max-height: 500px;">
                            <livewire:livewire-column-chart :column-chart-model="$columnChartModel" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="d-flex justify-content-center align-items-center" style="width: 100%; height: 300px;">
                            <livewire:livewire-pie-chart :pie-chart-model="$pieChartModel" key="{{ $pieChartModel->reactiveKey() }}"
                                style="max-width: 300px; max-height: 300px; width: 10%; height: 10%;" />
                        </div>
                    </div>
                </div>
            </div>

        @else
            <div class="card p-4">
                <h5 class="mb-4">Selamat Datang, {{ $username }}!</h5>
                <div class="card">
                    <div class="card-header">
                        <p class="card-title text-center text-muted"><small>Total Penjualan Hari Ini</small></p>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-center text-center m-2">
                            <strong>{{ $totalSalesToday }}</strong>
                        </p>
                        <p class="text-muted text-center"><small>Jumlah total penjualan yang terjadi hari ini.</small></p>
                    </div>
                    <div class="card-footer">
                        <p class="text-muted text-center"><small>Terakhir diperbarui: {{ $lastUpdatedAt }}</small></p>
                    </div>
                </div>
            </div>
        @endif

    </div>

</div>
@livewireChartsScripts
