<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

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
        'name', 'content', 'pack', 'price', 'manufacturer_id'
    ];

    // Relations

    public function manufacturer()
    {
        return $this->belongsTo('App\Manufacturer');
    }

    public function inventories()
    {
        return $this->hasMany('App\Inventory');
    }

    public function saleProducts()
    {
        return $this->hasMany('App\SaleProduct');
    }
}
