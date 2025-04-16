<div>
    <div class="container">
        <div class="row my-2">
            <h4 class="mb-5">Pembelian</h4>
            <div class="col-12">
                @if($isEmployee)
                <button wire:click="selectMenu('lihat')" class="btn {{ $menuOptions == 'lihat' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Semua Pembelian
                </button>
                <button wire:click="selectMenu('add')" class="btn  {{ $menuOptions == 'add' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Tambah Pembelian
                </button>
                @endif
                @if ($menuOptions == 'lihat')
                <button wire:click="exportPurchase"
                    class="btn btn-info text-white">
                    Export Penjualan (.xlsx)
                </button>
                @endif
                {{-- <button wire:loading
                    class="btn btn-info">
                    Loading...
                </button> --}}
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @if($menuOptions=='lihat')
                <div class="card bordered-primary" style="max-height: 70vh; overflow-y: auto;">
                    <div class="card-header">
                        Semua Pembelian
                    </div>
                    <div class="d-flex justify-content-between m-3 flex-wrap gap-2">
                        {{-- <div class="d-flex align-items-center gap-2">
                            <label for="perPage">Tampilkan</label>
                            <select wire:model.live.debounce.250ms="perPage" id="perPage" class="form-select w-auto">
                                @foreach($perPageOptions as $perPageOption)
                                    <option value="{{$perPageOption}}">{{$perPageOption}}</option>

                                @endforeach
                            </select>
                        </div> --}}

                        <div>
                            <label class="mb-1" for="filterByDate">Filter Tanggal</label>
                            <input type="date" wire:model.live="filterByDate" id="filterByDate" class="form-control">
                        </div>

                        {{-- <div>
                            <input type="search" class="form-control" wire:model.live="search" placeholder="Cari...">
                        </div> --}}
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <th>No</th>
                                <th>Nama Pelanggan</th>
                                <th>Tanggal Penjualan</th>
                                <th>Total Harga</th>
                                <th>Dibuat Oleh</th>
                                <th>Aksi</th>
                            </thead>
                            <tbody>
                                @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$purchase->member?->name ?? 'Non Member'}}</td>
                                    <td>{{\Carbon\Carbon::parse($purchase->purchase_date)->format('d-m-Y')}}</td>
                                    <td>Rp. {{ number_format($purchase->total_price)}}</td>
                                    <td>{{$purchase->user->name ?? 'Petugas'}}</td>
                                    <td>
                                        <a href="{{ route('purchase-detail', ['id' => $purchase->id]) }}" class="btn btn-warning text-white btn-sm">
                                            Lihat
                                        </a>
                                        <a href="{{ route('purchase-invoice.print', $purchase->id) }}" target="_blank" class="btn btn-success text-white btn-sm">
                                            Unduh Bukti
                                        </a>
                                    </td>
                                </tr>
                                {{-- @empty
                                    <tr>
                                        <td colspan="7" class="text-muted bg-light text-center">Data tidak ditemukan.</td>
                                    </tr> --}}
                                @endforeach
                            </tbody>
                        </table>
                        {{-- <div class="d-flex justify-content-center mt-3">
                            {{ $purchases->links('vendor.livewire.bootstrap') }}
                        </div> --}}
                    </div>
                </div>
                @elseif($menuOptions=='add')
                <button class="btn btn-primary mb-2" wire:click="cancel">Kembali</button>
                <div class="card p-5 mb-3" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row">
                        @foreach ($products as $product)
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-header text-center">
                                    <img src="{{ asset('storage/' . ($product->image ?? 'images/default.jpeg')) }}" width="120" height="100" alt="{{$product->name}}">
                                </div>
                                <div class="card-body text-center">
                                    <strong>{{$product->name}}</strong>
                                    <div>
                                        <span class="badge {{$product->stock <= 1 ? 'bg-danger' : 'bg-success'}} text-white fs-7">Stok: <span id="stock">{{$product->stock}}</span></span>

                                        <h6 class="mt-3 mb-3"><span id="harga">Rp. {{ number_format($product->price, 2, ',', '.') }}</span></h6>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <button wire:click="decreaseQty({{ $product->id }})" class="btn btn-light border-0 rounded-0 shadow-none"
                                                    style="box-shadow: none; outline: none; width: 32px;">
                                                <strong>-</strong>
                                            </button>

                                            <input type="number" value="{{ $cart[$product->id] ?? 0 }}" min="0"
                                            max="{{ $product->stock }}"
                                            wire:model.lazy="cart.{{ $product->id }}"
                                            {{-- wire:change="updateQty({{ $product->id }})" --}}
                                                   class="form-control text-center border-0 rounded-0"
                                                   style="width: 50%; box-shadow: none;">

                                            <button wire:click="increaseQty({{ $product->id }})" class="btn btn-light border-0 rounded-0 shadow-none"
                                                    style="box-shadow: none; outline: none; width: 32px;">
                                                <strong>+</strong>
                                            </button>
                                        </div>
                                        <h6 class="mt-3">Subtotal: <span id="subtotal">Rp. {{ number_format($this->getSubtotal($product->id, 2, ',', '.'))}}</span></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div style="position: fixed; bottom: 0; left: 0; width: 100%; background: white; padding: 15px 10px; text-align: center; z-index: 1000;">
                    <button class="btn btn-primary" wire:click='proceedToPayment'>
                        Lanjutkan Pesanan
                    </button>
                </div>
                @elseif($menuOptions == 'payment')
                <div class="row g-4">
                    <div class="col-md-6 col-12">
                        <div class="card border-primary shadow-sm rounded">
                            <div class="card-body">
                                <h4 class="fw-semibold mb-4">Produk yang dipilih</h4>

                                @foreach($cart as $productId => $qty)
                                    @php
                                        $product = $products->find($productId);
                                    @endphp
                                    @if ($product)
                                        <div class="mb-3">
                                            <h6 class="mb-1 text-capitalize">{{ $product->name }}</h6>
                                            <div class="d-flex justify-content-between text-muted">
                                                <small>Rp. {{ number_format($product->price, 0, ',', '.') }} x {{ $qty }}</small>
                                                <small><strong>Rp. {{ number_format($this->getSubtotal($product->id), 0, ',', '.') }}</strong></small>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach

                                <hr />
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold">Total</h5>
                                    <h5 class="mb-0">Rp. {{ number_format($this->getTotalPrice(), 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="card border-primary shadow-sm rounded">
                            <div class="card-body">
                                <label class="fw-medium mb-1">Member Status <span class="text-danger"><small>Dapat juga membuat member</small></span></label>
                                <select class="form-control mb-3" wire:model.lazy="member_status">
                                    <option value="non-member">Bukan Member</option>
                                    <option value="member">Member</option>
                                </select>
                                @error('member_status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                @if ($member_status == 'member')
                                    <label class="fw-medium mt-2 mb-1">No. Telepon <span class="text-danger"><small>(daftar/gunakan member)</small></span></label>
                                    <input type="text" class="form-control mb-3" wire:model="phone_number" />
                                    @error('phone_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                @endif

                                <label class="fw-medium mt-2 mb-1">Total Bayar</label>
                                <input type="number" class="form-control mb-3" wire:model.lazy="payment_amount">
                                @if ($payment_warning)
                                    <span class="text-danger">{{ $payment_warning }}</span>
                                @endif

                                @error('payment_amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <div class="d-flex justify-content-end">
                                    @if ($member_status == 'member')
                                    <button wire:click="stepForMember" class="btn btn-primary">Pesan</button>
                                    @else
                                        <button wire:click="confirmOrder" class="btn btn-primary">Pesan</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @elseif ($menuOptions == 'member')
                <div class="row g-4">
                    <div class="col-md-6 col-12">
                        <div class="card border-primary shadow-sm rounded">
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <th>Nama Produk</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </thead>
                                    <tbody>
                                        @foreach($cart as $productId => $qty)
                                        @php
                                            $product = $products->find($productId);
                                        @endphp
                                        @if ($product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td>{{ $qty }}</td>
                                                <td>Rp. {{ number_format($product->price, 0, ',', '.') }}</td>
                                                <td>Rp. {{ number_format($this->getSubtotal($product->id), 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td class="fw-bold text-start">Total Harga</td>
                                            <td class="fw-bold">Rp. {{ number_format($this->getTotalPrice(), 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td class="fw-bold text-start">Total Bayar</td>
                                            <td class="fw-bold">Rp. {{ number_format((float)$payment_amount, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-12">
                        <div class="card border-primary shadow-sm rounded">
                            <div class="card-body">
                                <label class="fw-medium mt-2 mb-1">Nama Member <span><small>(identitas)</small></span></label>
                                <input type="text" class="form-control mb-3" wire:model="name"/>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <label class="fw-medium mt-2 mb-1">Poin</label>
                                <input type="number" class="form-control mb-3" readonly wire:model="points"/>

                                @error('points')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                <div class="form-check">
                                    <input type="checkbox" id="usedPoints" wire:model="used_point" value="{{ $points }}"
                                    @disabled(!$can_use_point)>
                                    <label for="usePoints">Gunakan Poin</label>

                                    @if(!$can_use_point && $point_warning)
                                        <p class="text-danger text-sm mt-1"><small>{{ $point_warning }}</small></p>
                                    @endif
                                  </div>

                                <div class="d-flex justify-content-end">
                                    <button wire:click='confirmOrder' class="btn btn-primary">Selanjutnya</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>

    window.addEventListener('stock-alert', event => {
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: event.detail[0].message,
            confirmButtonText: 'OK'
        });
    });
</script>


@endpush


