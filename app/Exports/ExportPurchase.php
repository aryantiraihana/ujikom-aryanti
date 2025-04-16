<?php

namespace App\Exports;

use App\Models\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class ExportPurchase implements FromCollection, WithHeadings, WithMapping
{

    /**
     * @return \Illuminate\Support\Collection
     */

     protected $purchases;

     public function __construct($purchases)
     {
         $this->purchases = $purchases;
     }

     public function collection()
     {
         return $this->purchases;
     }

    public function map($purchase): array
    {
        $listProduct = $purchase->items->map(function ($item) {
            return $item->product->name . ' ( ' . $item->quantity . ' : Rp. ' . number_format($item->subtotal, 0, ',', '.') . ' )';
        })->implode(' , ');

        return [
            $purchase->member->name ?? 'Non Member',
            $purchase->member->phone_number ?? '-',
            $purchase->member->points ?? 0,
            $listProduct,
            'Rp. ' . number_format($purchase->total_price, 0, ',', '.'),
            'Rp. ' . number_format($purchase->payment_amount, 0, ',', '.'),
            $purchase->used_point ?? 0,
            'Rp. ' . number_format(($purchase->payment_amount - $purchase->total_price), 0, ',', '.'),
            $purchase->purchase_date ?? $purchase->created_at,
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Pelanggan',
            'No HP Pelanggan',
            'Poin',
            'Produk',
            'Total Harga',
            'Total Bayar',
            'Total Diskon',
            'Total Kembalian',
            'Tanggal Pembelian'
        ];
    }
}

