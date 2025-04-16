<?php

namespace App\Livewire;

use App\Exports\ExportProduct;
use App\Models\Product as ModelsProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Product extends Component
{

    use WithFileUploads;
    public $menuOptions = 'lihat';

    public $name;
    public $stock;
    public $price;
    public $image;

    public $selectedProduct;

    public $productId;
    public $isStockModalOpen = false;

    public $page = 1;
    public $searchProduct = '';
    protected $queryString = ['page', 'searchProduct'];
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [5, 10, 25, 50, 100];

    public $isAdmin;

    public function updatingsearchProduct(){
        $this->resetPage();
    }

    public function updatingPerPage(){
        $this->resetPage();
    }

    public function mount(){
        $user = Auth::user();
        $this->isAdmin = $user && $user->role === 'admin';
    }

    public function exportProduct(){
        return Excel::download(new ExportProduct, 'data_produk.xlsx');
    }

    public function openStockModal($id){
        $product = ModelsProduct::findOrFail($id);
        $this->productId = $product->id;
        $this->stock = $product->stock;
        $this->name = $product->name;
        $this->isStockModalOpen = true;
    }

    public function updateStockOnly()
    {

        $this->validate([
            'stock' => 'required|numeric|min:0',
        ]);

        $product = ModelsProduct::findOrFail($this->productId);
        $product->stock = $this->stock;
        $product->save();

        $this->reset(['productId', 'stock', 'name', 'isStockModalOpen']);
    }

    public function cancel(){
        $this->reset(['menuOptions', 'selectedProduct', 'productId', 'name', 'stock', 'price', 'image', 'isStockModalOpen']);
    }

    public function toBeDelete($id){
        $this->selectedProduct = ModelsProduct::findOrFail($id);
        $this->menuOptions = 'delete';
    }

    public function delete(){

        if($this->selectedProduct->image){
            Storage::delete('public/'. $this->selectedProduct->image);
        }

        $this->selectedProduct->delete();
        $this->reset();
    }

    public function toBeEdited($id){
        $this->selectedProduct = ModelsProduct::findOrFail($id);

        $this->name = $this->selectedProduct->name;
        $this->stock = $this->selectedProduct->stock;
        $this->price = $this->selectedProduct->price;

        $this->menuOptions = 'edit';
    }

    public function saveEdit()
    {
        $this->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0| max:999999999999999999',
            'image' => 'nullable|image|max: 2048'
        ], [
            'name.required' => 'Nama harus diisi.',
            'price.required' => 'Harga harus diisi.',
            'price.max' => 'Harga tidak bisa lebih dari Rp. 999.999.999.999.999.999.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Gambar tidak boleh lebih dari 2MB.'
        ]);

        $save = $this->selectedProduct;
        $save->name = $this->name;
        $save->stock = $this->stock;
        $save->price = $this->price;
        if($this->image){
            if($save->image){
                Storage::delete('/public' . $save->image);
            }
            $imagePath = $this->image->store('products', 'public');
            $save->image = $imagePath;
        }

        $save->save();

        $this->reset(['name', 'stock', 'price', 'image', 'selectedProduct']);
        $this->menuOptions = 'lihat';
    }

    public function save(){
        $this->validate([
            'name' => 'required',
            // 'stock' => 'required',
            'price' => 'required|min: 0',
            'image' => 'nullable|image|max: 2048'
        ], [
            'name.required' => 'Nama harus diisi.',
            // 'stock.required' => 'Stok harus diisi',
            'price.required' => 'Harga harus diisi.',
            'image.required' => 'File harus berupa gambar.',
            'image.max' => 'Gambar tidak boleh lebih dari 2MB.'
        ]);

        $imagePath = null;
        if($this->image){
            $imagePath = $this->image->store('products', 'public');
        }
        // dd($imagePath);

        $save = new ModelsProduct();
        $save->name = $this->name;
        $save->stock = $this->stock;
        $save->price = $this->price;
        $save->image = $imagePath;

        $save->save();

        $this->reset(['name', 'stock', 'price', 'image']);
        $this->menuOptions = 'lihat';
    }

    public function selectMenu($menu){
        // $this->resetPage();
        $this->menuOptions = $menu;
    }

    public function render()
    {
        return view('livewire.product')->with([
            'products' => ModelsProduct::all()
        ]);
    }
}
