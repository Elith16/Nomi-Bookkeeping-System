<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     */
    protected $fillable = [
        'title',
        'name',
        'email',
        'phone',
        'address',
        'prescription',
        'remarks',
    ];
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
