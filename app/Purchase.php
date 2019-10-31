<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
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
        'vendor_id', 'total_products', 'price', 'date_received'
    ];

    // Relations

    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }

    public function inventories()
    {
        return $this->hasMany('App\Inventory');
    }
}
