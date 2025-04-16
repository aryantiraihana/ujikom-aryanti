<?php

namespace App\Livewire;

use App\Exports\ExportPurchase;
use App\Models\Member;
use App\Models\Product;
use App\Models\Purchase as ModelsPurchase;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Purchase extends Component
{
    use WithPagination;
    public $menuOptions = 'lihat';
    public $products;
    public $cart = [];
    public $member_status = 'non-member';
    public $phone_number;
    public $payment_amount;
    public $payChange;
    public $payment_warning = null;
    public $purchase;
    public $used_point = 0;

    public $is_member = false;
    public $member;
    public $name;
    public $points = 0;
    public $totalPoints;

    public $can_use_point = false;
    public $point_warning = '';

    public $perPage = 10;
    public $perPageOptions = [1, 2, 5, 10, 25, 50, 100];

    public $filterByDate;

    public $isEmployee;
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function cancel(){
        $this->reset(['menuOptions']);
    }

    // public function exportPurchase(){
    //     return Excel::download(new ExportPurchase, 'laporan-pembelian.xlsx');
    // }

    public function exportPurchase()
    {
        $data = $this->getFilteredPurchases()->get();

        return Excel::download(new ExportPurchase($data), 'laporan-pembelian.xlsx');
    }

    public function stepForMember(){

        if ($this->payment_amount < $this->getTotalPrice()) {
            $this->payment_warning = 'Jumlah pembayaran kurang dari total belanja.';
            return;
        }

        $member = Member::where('phone_number', $this->phone_number)->first();

        if($member){
            $this->is_member = true;
            $this->name = $member->name;
            $this->member = $member;
            $earnedPoints = floor(0.01 * $this->getTotalPrice());
            $this->points = (int)$member->points + $earnedPoints;

            $hasPreviousPurchase = ModelsPurchase::where('member_id', $member->id)->exists();
            $this->can_use_point = $hasPreviousPurchase;

            $this->point_warning = $hasPreviousPurchase ? '' : 'Poin tidak dapat digunakan pada pembelanjaan pertama.';

        } else {
            $this->is_member = false;
            $this->points = floor(0.01 * $this->getTotalPrice());
            $this->can_use_point = false;
            $this->point_warning = "Poin tidak dapat digunakan pada pembelanjaan pertama.";
        }

        $this->menuOptions = 'member';
    }

    public function getMemberIdFromPhone($phone){
        $member = Member::where('phone_number',$phone)->first();
        return $member ? $member->id : null;
    }

    public function confirmOrder()
    {

        $totalPrice = $this->getTotalPrice();


        $usedPoint = ($this->used_point && $this->can_use_point)
            ? min($this->points, $totalPrice)
            : 0;

        $finalTotal = $totalPrice - $usedPoint;

        if ($this->payment_amount < $finalTotal) {
            $this->payment_warning = "Nominal pembayaran kurang dari total belanja";
            return;
        }

        $max = 'Rp. 999999999999999999.99';
        if ($this->payment_amount > $max) {
            $this->payment_warning = "Nominal pembayaran tidak boleh lebih dari {$max}.";
            return;
        }

        $memberId = null;
        if ($this->member_status === 'member') {
            $member = $this->member ?? Member::where('phone_number', $this->phone_number)->first();
            $earned = floor(0.01 * $totalPrice);
            if ($member) {
                $memberId = $member->id;

                $totalPoints = $member->points + $earned;
                $member->points = max(0, $totalPoints - $usedPoint);
                $member->save();
            } else {

                $member = new Member();
                $member->name = $this->name;
                $member->phone_number = $this->phone_number;
                $member->points = $earned;
                $member->save();

                $memberId = $member->id;
            }
        }


        $purchase = new ModelsPurchase();
        $purchase->user_id = Auth::id();
        $purchase->member_id = $memberId;
        $purchase->total_price = $finalTotal;
        $purchase->payment_amount = $this->payment_amount;
        $purchase->used_point = $usedPoint;
        $purchase->purchase_date = now();
        $purchase->save();

        foreach ($this->cart as $productId => $qty) {
            $product = Product::find($productId);

            if ($product && $qty > 0) {
                $product->stock -= $qty;
                $product->save();

                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'price' => $product->price,
                    'subtotal' => $product->price * $qty,
                ]);
            }
        }

        return redirect()->route('purchase-detail', ['id' => $purchase->id]);
    }

    public function getTotalPrice()
    {
        $total = 0;
        foreach ($this->cart as $productId => $qty) {
            $product = $this->products->firstWhere('id', $productId);
            if ($product) {
                $total += $product->price * $qty;
            }
        }
        return $total;
    }


    public function proceedToPayment(){
        if(empty(array_filter($this->cart))){
            $this->menuOptions = 'add';
            return;
        }

        $this->menuOptions = 'payment';
    }

    // public function getSubtotal($productId){
    //    if(!isset($this->cart[$productId])) return 0;
    //    $product = $this->products->find($productId);
    //    return $product ? $product->price * $this->cart[$productId] : 0;
    // }

    public function getSubtotal($productId)
    {
        if (!isset($this->cart[$productId])) return 0;
        // Menggunakan koleksi produk yang sudah dimuat
        $product = $this->products->firstWhere('id', $productId);
        return $product ? $product->price * $this->cart[$productId] : 0;
    }

    public function increaseQty($productId)
    {
        $product = Product::find($productId);

        if (!$product) return;

        $currentQty = $this->cart[$productId] ?? 0;

        if ($currentQty + 1 > $product->stock) {
            $this->dispatch('stock-alert', [
                'message' => "Stok produk '{$product->name}' tidak mencukupi."
            ]);
            return;
        }

        $this->cart[$productId] = $currentQty + 1;
    }

    public function decreaseQty($productId)
    {

        // if(isset($this->cart[$productId]) && $this->cart[$productId] > 0){
        //     $this->cart[$productId]--;
        // }

        $currentQty = $this->cart[$productId] ?? 0;
        if($currentQty - 1 < 0){
            $this->dispatch('stock-alert', [
                'message' => "Jumlah produk tidak bisa kurang dari 0."
            ]);
            return;
        }
        $this->cart[$productId] = $currentQty - 1;
    }

    public function selectMenu($menu){
        $this->resetExcept(['products', 'cart']);
        $this->menuOptions = $menu;
    }

    public function mount()
    {
        $user = Auth::user();
        $this->isEmployee = $user && $user->role === 'employee';

        $this->reset(['cart']);
        $this->products = Product::all();
    }

    protected function getFilteredPurchases()
    {
        return ModelsPurchase::with(['user', 'member'])
            ->when($this->filterByDate, function($query) {
                $query->whereDate('purchase_date', $this->filterByDate);
            })
            // ->when($this->search, function($query) {
            //     $query->whereHas('member', function ($q) {
            //         $q->where('name', 'like', '%' . $this->search. '%');
            //     })
            //     ->orWhereHas('user', function ($q) {
            //         $q->where('name', 'like', '%' . $this->search . '%');
            //     });
            // })
            ->latest();
    }

        public function render()
        {
            $purchases = $this->getFilteredPurchases()->paginate(10);

            return view('livewire.purchase', [
                'purchases' => $purchases,
            ]);

            // return view('livewire.purchase')->with([
            //     'purchases' => ModelsPurchase::with([
            //         'user',
            //         'member'
            //     ])->latest()->get(),
            // ]);
        }
    }


