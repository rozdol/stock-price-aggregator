<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['symbol', 'name'];
    public function prices()
    {
        return $this->hasMany(StockPrice::class);
    }
}
