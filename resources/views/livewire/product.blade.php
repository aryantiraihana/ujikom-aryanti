<div>
    <div class="container">
        <div class="row my-2">
            <h4 class="mb-5">Produk</h4>
            <div class="col-12">
                @if ($isAdmin)
                <button wire:click="selectMenu('lihat')"
                    class="btn {{ $menuOptions=='lihat' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Semua Produk
                </button>
                    <button wire:click="selectMenu('tambah')"
                        class="btn {{ $menuOptions=='tambah' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Tambah
                    </button>
                @endif
                @if ($menuOptions == 'lihat')
                <button wire:click="exportProduct"
                    class="btn btn-success text-white">
                    Export Produk (.xlsx)
                </button>
                @endif
                <button wire:loading
                    class="btn btn-info text-white">
                    Loading...
                </button>
            </div>
            {{-- @endif --}}
        </div>

        <div class="row">
            <div class="col-12">
                @if ($menuOptions == 'lihat')
                    <div class="card mt-4" style="max-height: 70vh; overflow-y: auto;">
                        <div class="card-header">
                            Semua Produk
                        </div>
                        {{-- <div class="d-flex justify-content-between m-3">
                            <div class="d-flex align-items-center gap-2">
                                <label for="">Tampilkan</label>
                                <select wire:model.live.debounce.250ms="" id="perPage" class="form-select w-auto">
                                    <option value=""></option>
                                </select>
                            </div>
                            <div>
                                <input type="search" class="form-control" wire:model.live="" placeholder="Cari...">
                            </div>
                        </div> --}}
                        <div class="card-body">
                            <table class="table table-bordered table-striped text-center">
                                <thead>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Nama</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    @if ($isAdmin)
                                        <th>Aksi</th>
                                    @endif
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{-- <img src="{{ asset('storage/' . $product->image) }}" width="100" height="100" alt=""> --}}
                                                <img src="{{ asset('storage/' . ($product->image ?? 'images/default.jpeg')) }}" width="120" height="100" alt="">
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->stock }}</td>
                                            <td>Rp. {{ number_format($product->price, 2, ',', '.') }}</td>
                                            @if ($isAdmin)
                                                <td>
                                                    <div class="">
                                                        <button wire:click="toBeEdited({{ $product->id }})"
                                                            class="btn btn-warning text-white btn-sm">
                                                            Edit
                                                        </button>
                                                        <button wire:click="openStockModal({{ $product->id }})"
                                                            class="btn btn-info text-white btn-sm">
                                                            Update Stok
                                                        </button>
                                                        <button wire:click="toBeDelete({{ $product->id }})"
                                                            class="btn btn-danger text-white btn-sm">
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </td>

                                            @endif
                                        </tr>
                                        @endforeach
                                            {{-- <tr>
                                                <td colspan=7 class="text-center bg-light text-muted">Data tidak ditemukan.</td>
                                            </tr> --}}

                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-3">

                            </div>
                        </div>
                    </div>

                @elseif ($menuOptions == 'tambah')
                  {{-- TAMBAH --}}
                    <div class="card border-primary">
                        <div class="card-header">
                            Tambah Produk
                        </div>
                        <div class="card-body">
                            <form action="" wire:submit='save'>
                                <label for="">Nama Produk</label>
                                <input type="text" class="form-control" wire:model='name' />
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Stok</label>
                                <input type="number" class="form-control" wire:model='stock' />
                                @error('stock')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Harga</label>
                                <input type="number" class="form-control" wire:model='price' />
                                @error('price')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Gambar</label>
                                <input type="file" class="form-control" wire:model='image' />
                                @if ($image)
                                    <img src="" width="100" alt="">
                                @endif
                                @error('image')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                                <button wire:click="cancel" class="btn btn-secondary mt-3">Batal</button>
                            </form>
                        </div>
                    </div>

                @elseif ($menuOptions == 'edit')
                    <div class="card border-primary">
                        <div class="card-header">
                            Edit Produk
                        </div>
                        <div class="card-body">
                            <form action="" wire:submit='saveEdit'>
                                <label for="">Nama Produk</label>
                                <input type="text" class="form-control" wire:model='name' />
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Stok</label>
                                <input type="number" readonly class="form-control text-muted bg-light" wire:model='stock' />
                                @error('stock')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Harga</label>
                                <input type="number" class="form-control" wire:model='price' />
                                @error('price')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                @if ($selectedProduct->image)
                                    <img src="{{ asset('storage/' . ($selectedProduct->image ?? 'images/default.jpeg')) }}" width="100" alt="Gambar Produk">
                                @endif
                                    <input type="file" class="form-control" wire:model='image' />
                                @if ($image)
                                    <img src="{{ $image->temporaryUrl() }}" width="100" alt="Preview Gambar">
                                @endif
                                    @error('image')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                <br />
                                <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                                <button class="btn btn-secondary mt-3" wire:click='cancel'>
                                    Batal
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif ($menuOptions == 'delete')
                    {{-- HAPUS --}}
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            Hapus Produk
                        </div>
                        <div class="card-body">
                            Anda akan menghapus data produk. Lanjutkan?
                            <p>Nama : {{ $selectedProduct->name }}</p>
                            <button class="btn btn-danger text-white" wire:click='delete'>
                                Hapus
                            </button>
                            <button class="btn btn-secondary" wire:click='cancel'>
                                Batal
                            </button>
                        </div>
                    </div>

                {{-- @elseif ($menuOptions == 'import')
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        Import Produk
                    </div>
                    <div class="card-body">
                        <form action="" wire:submit="importExcel">
                            <input type="file" class="form-control" wire:model='fileExcel'>
                            <br />
                            <button class="btn btn-primary" type="submit">
                                Kirim
                            </button>
                        </form>
                    </div>
                </div> --}}
                @endif

            </div>
        </div>
    </div>

    @if ($isStockModalOpen)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Stok</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label><br>
                            <input type="text" wire:model="name" class="form-control text-muted bg-light">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" required wire:model="stock" class="form-control" placeholder="Masukkan jumlah stok baru">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="cancel">Batal</button>
                        <button class="btn btn-primary" wire:click="updateStockOnly">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
