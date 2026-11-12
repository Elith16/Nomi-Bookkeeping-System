<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quantity',
        'category',
        'price',
        'supplier_id'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isLowStock()
    {
        return $this->quantity < 100;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
