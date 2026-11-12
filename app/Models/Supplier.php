<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact', 'address', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}   
