
<div>

    <div class="container">
        <div class="row my-2">
            <h4 class="mb-5">Pengguna</h4>
            <div class="col-12">
                <button wire:click="selectMenu('lihat')"
                    class="btn {{ $menuOptions=='lihat' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Semua Pengguna
                </button>
                <button wire:click="selectMenu('tambah')"
                    class="btn {{ $menuOptions=='tambah' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Tambah
                </button>
                @if ($menuOptions == 'lihat')
                <button wire:click="exportUser"
                    class="btn btn-success text-white">
                    Export Pengguna (.xlsx)
                </button>
                @endif
                <button wire:loading
                    class="btn btn-info">
                    Loading...
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @if($menuOptions == 'lihat')
                    <div class="card border-primary" style="max-height: 70vh; overflow-y: auto;">
                        <div class="card-header">
                            Semua Pengguna
                        </div>
                        {{-- <div class="d-flex justify-content-between m-3">
                            <div class="d-flex align-items-center gap-2">
                                <label for="" class="mb-0 text-center">Tampilkan</label>
                                <select id="perPage" wire:model.live.bounce.300ms="perPage" class="form-select">
                                    <option value=""></option>
                                </select>

                            </div>
                            <div>
                                <input type="search" class="form-control" wire:model.live.bounce.300ms="" placeholder="Cari...">
                            </div>
                        </div> --}}
                        <div class="card-body">
                            <table class="table table-bordered table-striped text-center">
                                <thead>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Peran</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration}}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            
                                                @if ($user->role == 'admin')
                                                    <td>Admin</td>
                                                @else
                                                    <td>Employee</td>
                                                @endif

                                            <td class="text-center">
                                                <button wire:click="toBeEdited({{ $user->id }})"
                                                    class="btn btn-warning btn-sm text-white">
                                                    Edit
                                                </button>
                                                <button wire:click="toBeDelete({{ $user->id }})"
                                                    class="btn btn-danger btn-sm text-white">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center bg-light text-muted">Data tidak ditemukan.</td>
                                            </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center mt-3">

                            </div>
                        </div>
                    </div>
                @elseif($menuOptions == 'tambah')
                    <div class="card border-primary">
                        <div class="card-header">
                            Tambah Pengguna
                        </div>
                        <div class="card-body">
                            <form action="" wire:submit='save'>
                                <label for="">Nama</label>
                                <input type="text" class="form-control" wire:model='name' />
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Email</label>
                                <input type="email" class="form-control" wire:model='email' />
                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Password</label>
                                <input type="password" class="form-control" wire:model='password' />
                                @error('password')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Peran</label>
                                <select name="" id="" class="form-control" wire:model='role'>
                                    <option>--Pilih Peran--</option>
                                    <option value="admin">Admin</option>
                                    <option value="employee">Petugas</option>
                                </select>
                                @error('role')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                            </form>
                        </div>
                    </div>
                @elseif($menuOptions == 'edit')
                    <div class="card border-primary">
                        <div class="card-header">
                            Edit Pengguna
                        </div>
                        <div class="card-body">
                            <form action="" wire:submit='saveEdit'>
                                <label for="">Nama</label>
                                <input type="text" class="form-control" wire:model='name' />
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Email</label>
                                <input type="email" class="form-control" wire:model='email' />
                                @error('email')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Password</label>
                                <input type="password" class="form-control" wire:model='password' />
                                @error('password')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <label for="">Peran</label>
                                <select name="" id="" class="form-control" wire:model='role'>
                                    <option>--Pilih Peran--</option>
                                    <option value="admin">Admin</option>
                                    <option value="employee">Petugas</option>
                                </select>
                                @error('role')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <br />
                                <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                                <button type="button" class="btn btn-secondary mt-3" wire:click='cancel'>Batal</button>
                            </form>
                        </div>
                    </div>
                @elseif($menuOptions == 'delete')
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            Hapus Pengguna
                        </div>
                        <div class="card-body">
                            Anda akan menghapus data pengguna. Lanjutkan?
                            <p>Nama :
                                {{ $selectedUser->name }}
                            </p>
                            <button class="btn btn-danger" wire:click='delete'>
                                Hapus
                            </button>
                            <button class="btn btn-secondary" wire:click='cancel'>
                                Batal
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
