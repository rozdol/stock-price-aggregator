<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stock extends Model
{
    use HasFactory;
    protected $fillable = ['symbol', 'name'];
    public function prices()
    {
        return $this->hasMany(StockPrice::class);
    }
}
