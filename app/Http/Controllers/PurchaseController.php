<?php

namespace App\Http\Controllers;

use App\Vendor;
use App\Product;
use App\Purchase;
use App\Inventory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PurchaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['purchases'] = Purchase::all()->load('inventories.product');
        return view('purchases.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['purchase'] = new Purchase;
        $data['vendors'] = Vendor::all()->pluck('name', 'id');
        $products = Product::all();
        $data['products'] = $products->pluck('name', 'id');
        $data['productsPrices'] = $products->pluck('price', 'id')->toJson();
        $data['form_param'] = ['route'=> 'purchases.store'];
        if (!empty(Input::old('inventory'))) {
            $data['inventories'] = collect(Input::old('inventory'))->map(function($i){ return (object) $i; });
        }

        return view('purchases.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        $inventory = $request->inventory;
        $total_products = count($inventory);
        $price = 0;

        foreach ($inventory as $key => $product) {
            $price += $product['price'] * $product['quantity'];
        }

        $purchase = new Purchase;
        $purchase->vendor_id = $request->vendor_id;
        $purchase->total_products = $total_products;
        $purchase->price = $price;
        $purchase->save();

        foreach ($inventory as $key => $product) {
            $purchase->inventories()->create($product);
        }

        return redirect()->route('purchases.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['purchase'] = Purchase::findOrFail($id);
        $data['vendors'] = Vendor::all()->pluck('name', 'id');
        $products = Product::all();
        $data['products'] = $products->pluck('name', 'id');
        $data['productsPrices'] = $products->pluck('price', 'id')->toJson();
        $data['inventories'] = $data['purchase']->inventories;
        $data['form_param'] = ['route'=> ['purchases.update', $id], 'method'=> 'PUT'];
        if (!empty(Input::old('inventory'))) {
            $data['inventories'] = collect(Input::old('inventory'))->map(function($i){ return (object) $i; });
        }

        return view('purchases.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateRequest($request);

        $purchase = Purchase::findOrFail($id);

        $inventory = $request->inventory;
        $total_products = count($inventory);
        $price = 0;

        foreach ($inventory as $key => $product) {
            $price += $product['price'] * $product['quantity'];
        }

        $purchase->vendor_id = $request->vendor_id;
        $purchase->total_products = $total_products;
        $purchase->price = $price;
        $purchase->update();

        $purchase->inventories()->forceDelete();

        foreach ($inventory as $key => $product) {
            $purchase->inventories()->create($product);
        }

        return redirect()->route('purchases.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->inventories()->delete();
        $purchase->delete();
        return redirect()->route('purchases.index');
    }

    /**
     * Common validator
     *
     * @param  object $request
     */
    private function validateRequest($request)
    {
        if ($request) {
            $this->validate(
                $request,
                [
                    'vendor_id'=> 'required|exists:vendors,id',
                    'inventory.*.product_id'=> 'required|exists:products,id',
                    'inventory.*.quantity'=> 'required|numeric|min:1',
                    'inventory.*.price'=> 'required|numeric|min:1',
                    'inventory.*.expiry_date'=> 'required|date'
                ],
                [
                    'vendor_id.required'=> 'Select vendor',
                    'vendor_id.exists'=> 'Vendor does not exists.',
                    'inventory.*.product_id.required'=> 'Select product',
                    'inventory.*.product_id.exists'=> 'Product does not exists',
                    'inventory.*.quantity.required'=> 'Quantity is required',
                    'inventory.*.quantity.min'=> 'Quantity must be greater than 0',
                    'inventory.*.price.required'=> 'Price is required',
                    'inventory.*.price.min'=> 'Price must be greater than 0',
                    'inventory.*.expiry_date.required'=> 'Expiry Date is required'
                ]
            );
        }

    }
}
