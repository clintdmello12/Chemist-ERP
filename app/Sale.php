<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'address', 'phone', 'total_products', 'price',
    ];

    public function sale_products()
    {
        return $this->hasMany('App\SaleProduct');
    }
}
