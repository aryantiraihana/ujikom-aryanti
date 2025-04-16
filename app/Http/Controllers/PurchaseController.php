<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function print($id)
    {
        $purchase = Purchase::with(['items.product', 'user', 'member'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.invoice', compact('purchase'));
        return $pdf->download('bukti-pembelian-'.$purchase->id.'.pdf');
    }
}
