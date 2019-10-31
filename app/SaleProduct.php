<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'sale_id', 'quantity', 'price',
    ];

    public function sale()
    {
        return $this->belongsTo('App\Sale');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

}
