<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;
use App\Manufacturer;

class ProductController extends Controller
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
        $data['products'] = Product::all();
        return view('products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['product'] = new Product;
        $data['manufacturers'] = Manufacturer::all()->pluck('name', 'id');
        $data['form_param'] = ['route'=> 'products.store'];
        return view('products.create', $data);
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

        $product = Product::create($request->all());
        return redirect()->route('products.index');
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
        $data['product'] = Product::findOrFail($id);
        $data['manufacturers'] = Manufacturer::all()->pluck('name', 'id');
        $data['form_param'] = ['route'=> ['products.update', $id], 'method'=> 'PUT'];
        return view('products.edit', $data);
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

        $product = Product::findOrFail($id);
        $product->update($request->all());
        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return redirect()->route('products.index');
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
                    'name'=> 'required|alpha_num',
                    'price'=> 'required|numeric|min:1',
                    'manufacturer_id'=> 'required|numeric|exists:manufacturers,id',
                ],
                [
                    'manufacturer_id.required' => 'Select Manufacturer',
                    'manufacturer_id.exists' => 'Manufacturer does not exists'
                ]
            );
        }

    }
}
