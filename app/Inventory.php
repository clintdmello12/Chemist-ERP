<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
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
        'purchase_id', 'product_id', 'price', 'quantity', 'expiry_date'
    ];

    // Relations

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function purchase()
    {
        return $this->belongsTo('App\Purchase');
    }

}
