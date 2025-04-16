<div>
    <div class="container">
        <div class="row my-2">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-5">
                            <a href="{{ route('purchase-invoice.print', $purchase->id) }}" target="_blank" class="btn btn-primary">Unduh</a>
                            <a href="{{ route('purchase') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                        <div class="d-flex justify-content-between my-4 mb-4">
                            @if ($purchase->member)
                            <div class="text-muted">
                                <p class="mb-1"><strong>{{ $purchase->member->phone_number }}</strong></p>
                                <p class="mb-1">MEMBER SEJAK : {{ \Carbon\Carbon::parse($purchase->member->joined_at)->format('d F Y') }}</p>
                                <p class="mb-1">MEMBER POIN : {{ (int) $purchase->member->points }}</p>
                            </div>
                            @endif
                            <div class="text-muted text-end">
                                <h6>Invoice - #{{ $purchase->id }}</h6>
                                <p>{{ \Carbon\Carbon::parse($purchase->created_at)->format('d F Y') }}</p>
                            </div>
                        </div>
                        <table class="table text-center">
                            <thead class="text-center mb-2">
                                <tr>
                                    <th class="text-secondary">Produk</th>
                                    <th class="text-secondary">Harga</th>
                                    <th class="text-secondary">Quantity</th>
                                    <th class="text-secondary">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchase->items as $item)
                                    <tr>
                                        <td class="pb-4 text-secondary">{{ $item->product->name }}</td>
                                        <td class="pb-4 text-secondary">Rp. {{ number_format($item->price) }}</td>
                                        <td class="pb-4 text-secondary">{{ $item->quantity }}</td>
                                        <td class="pb-4 text-secondary">Rp. {{ number_format($item->subtotal) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-active p-4">
                                    <td class="align-middle">
                                        <p class="mb-0 small text-secondary">POIN DIGUNAKAN</p>
                                        <h5 class="text-secondary">{{$purchase->used_point ?? 0}}</h5>
                                    </td>
                                    <td class="align-middle">
                                        <p class="mb-0 small text-secondary">KASIR</p>
                                        <h5 class="text-secondary">{{$purchase->user->name}}</h5>
                                    </td>
                                    <td class="align-middle">
                                        <p class="mb-0 small text-secondary">KEMBALIAN</p>
                                        <h5 class="text-secondary">Rp. {{ number_format(max(0, $purchase->pay_change)) }}</h5>
                                    </td>
                                    <td colspan="2" class="col text-end bg-dark text-white p-3">
                                        <p class="mb-0 text-left">TOTAL</p>

                                        @if ($purchase->used_point > 0)
                                            <h5 class="text-decoration-line-through text-white-50">
                                                Rp. {{ number_format($purchase->total_price + $purchase->used_point) }}
                                            </h5>
                                        @endif

                                        <h3>Rp. {{ number_format($purchase->total_price) }}</h3>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
