<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Sale;
use App\Product;
use App\SaleProduct;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class SaleController extends Controller
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
        $data['sale_date_frm'] = "";
        $data['sale_date_to'] = "";
        $data['dis_frm_date'] = "";
        $data['dis_to_date'] = "";
        $data['sales'] = Sale::all();
        return view('sales.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['sale'] = new Sale;
        $products = Product::all()
                            ->load('inventories')
                            ->filter(function($p){
                                $res = false;
                                $i = $p->inventories;
                                if (!$i->isEmpty()){
                                    $res = $i->filter(function($in){
                                        $date = Carbon::parse($in->expiry_date);
                                        return ($in->quantity > 0) && ($date->isFuture() || $date->isToday());
                                    })
                                    ->count();
                                }

                                return $res;
                            });

        $data['products'] = $products->pluck('name', 'id');
        $data['productsPrices'] = $products->pluck('price', 'id')->toJson();
        $data['form_param'] = ['route'=> 'sales.store'];

        if (!empty(Input::old('sale_product'))) {
            $data['sale_products'] = collect(Input::old('sale_product'))->map(function($i){ return (object) $i; });
        }

        return view('sales.create', $data);
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

        $sale_product = $request->sale_product;
        $total_products = count($sale_product);

        $price = 0;

        foreach ($sale_product as $key => $product) {
            $price += $product['price'] * $product['quantity'];
        }

        $sale = new Sale;
        $sale->name = $request->name;
        $sale->address = $request->address;
        $sale->phone = $request->phone;
        $sale->total_products = $total_products;
        $sale->price = $price;
        $sale->save();

        foreach ($sale_product as $key => $product) {

            $buyProducts = $sale->sale_products()->create($product);

            if ($buyProducts) {
                $inventory = $buyProducts->product->inventories()->orderBy('expiry_date', 'asc')->first();
                $inventory->quantity -= $product['quantity'];
                $inventory->update();
            }
        }

       return redirect()->route('invoice', $sale);
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
        $data['sale'] = Sale::findOrFail($id);
        $data['sale_products'] = $data['sale']->sale_products;
        $data['products'] = Product::all()->pluck('name', 'id');
        $data['form_param'] = ['route'=> ['sales.update', $id], 'method'=> 'PUT'];
        return view('sales.edit', $data);
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

        $sale = Sale::findOrFail($id);
        $sale->update($request->all());
        return redirect()->route('sales.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function sales_report(Request $request)
    {
        $this->validate($request,
                [
                'sale_date_frm' => 'required_with:sale_date_to',
                'sale_date_to'=> 'required_with:sale_date_frm',
                'sale_date_to' => 'after:sale_date_frm'
                ],
                [
                    'sale_date_frm.required_with'=> 'Enter From Date',
                    'sale_date_to.required_with'=> 'Enter To Date',
                    'sale_date_to.after'=> 'To date should be greater than From date',
                ]
             );
        $data['sale_date_frm'] = "";
        $data['sale_date_to'] = "";
        $data['dis_frm_date'] = "";
        $data['dis_to_date'] = "";
        $data['sales'] = Sale::all();
        if(isset($_GET['sale_date_frm']) && isset($_GET['sale_date_to'])){

            if(!empty($_GET['sale_date_frm']) && !empty($_GET['sale_date_to'])){
                $data['sale_date_frm'] = $_GET['sale_date_frm']." 00:00:00";
                $data['sale_date_to']  = $_GET['sale_date_to']." 23:59:59";
                $data['dis_frm_date'] = $_GET['sale_date_frm'];
                $data['dis_to_date']  = $_GET['sale_date_to'];

                $data['sales'] = Sale::where('created_at', '>=', $data['sale_date_frm'])
                                       ->where('created_at', '<=', $data['sale_date_to'])
                                       ->get();

            }
        }
        return view('sales.index', $data);
    }

    public function sales_invoice()
    {
        $data['sales'] = Sale::all();
        return view('sales.sales_invoice', $data);
    }

    public function invoice(Request $request, $id)
    {
        $data['sale'] = Sale::findOrFail($id)->load('sale_products.product');
        return view('sales.invoice', $data);
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
                    'name'=> 'required|alpha',
                    'address'=> 'required',
                    'phone'=> 'required|numeric|digits_between:10,13',
                    'sale_product.*.product_id'=> 'required|numeric|exists:products,id',
                    'sale_product.*.price'=> 'required|numeric|min:1',
                    'sale_product.*.quantity'=> 'required|numeric|min:1',
                ],
                [
                    'sale_product.*.product_id.required'=> 'Select product',
                    'sale_product.*.product_id.exists'=> 'Product does not exists',
                    'sale_product.*.price.required'=> 'Price is required',
                    'sale_product.*.price.min'=> 'Price must be greater than 0',
                    'sale_product.*.quantity.required'=> 'Quantity is required',
                    'sale_product.*.quantity.min'=> 'Quantity must be greater than 0',
                ]
            );
        }

    }
}
