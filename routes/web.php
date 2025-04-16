<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PurchaseController;
use App\Livewire\Dashboard;
use App\Livewire\Product;
use App\Livewire\Purchase;
use App\Livewire\PurchaseDetail;
use App\Livewire\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

Auth::routes(['login', false]);
Route::middleware(['auth', 'IsLogin'])->group(function(){
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/product', Product::class)->name('product');
    Route::get('/purchase', Purchase::class)->name('purchase');
    // Route::get('/purchase-detail', PurchaseDetail::class)->name('purchase-detail');
    Route::get('/purchase-detail/{id}', PurchaseDetail::class)->name('purchase-detail');
    Route::get('/purchase-invoice/{id}/print', [PurchaseController::class, 'print'])->name('purchase-invoice.print');
});

Route::middleware(['auth','IsLogin', 'IsAdmin'])->group(function(){
    Route::get('/user', User::class)->name('user');
});
