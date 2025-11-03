<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['buyer_id', 'product_id', 'status'];

    public function buyer() {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function messages() {
        return $this->hasMany(TransactionMessage::class);
    }

    public function rating() {
        return $this->hasOne(Rating::class);
    }
}
