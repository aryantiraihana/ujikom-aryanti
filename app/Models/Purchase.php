<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'user_id',
        'member_id',
        'total_price',
        'purchase_date',];

    public function items(){
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }

    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
