<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'description',
        'image',
        'price',
        'quantity',
        'marketplace_id',
    ];

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}
