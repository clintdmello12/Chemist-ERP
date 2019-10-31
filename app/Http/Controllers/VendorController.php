<?php

namespace App\Http\Controllers;

use App\Vendor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Gate;

class VendorController extends Controller
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
        if (Gate::denies('admin'))
            return redirect()->route('inventories.index')->with('flashMessage', 'Access denied!!|warning');

        $data['vendors'] = Vendor::all();
        return view('vendors.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Gate::denies('admin'))
            return redirect()->route('inventories.index')->with('flashMessage', 'Access denied!!|warning');

        $data['vendor'] = new Vendor;
        $data['form_param'] = ['route'=> 'vendors.store', 'files' => true];
        return view('vendors.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('admin'))
            return redirect()->route('inventories.index')->with('flashMessage', 'Access denied!!|warning');

        $this->validateRequest($request);

        $data = $request->all();

        $vendor = Vendor::create($data);
        return redirect()->route('vendors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Gate::denies('admin'))
            return redirect()->route('inventories.index')->with('flashMessage', 'Access denied!!|warning');

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
        if (Gate::denies('admin'))
            return redirect()->route('inventories.index')->with('flashMessage', 'Access denied!!|warning');

        $data['vendor'] = Vendor::findOrFail($id);
        $data['form_param'] = ['route'=> ['vendors.update', $id], 'method'=> 'PUT', 'files' => true];
        return view('vendors.edit', $data);
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
        if (Gate::denies('admin'))
            return redirect()->route('inventories.index')->with('flashMessage', 'Access denied!!|warning');

        $this->validateRequest($request, ['email'=> 'required|email']);

        $vendor = Vendor::findOrFail($id);

        $data = $request->all();

        $vendor->update($data);
        return redirect()->route('vendors.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('admin'))
            return redirect()->route('inventories.index')->with('flashMessage', 'Access denied!!|warning');

        //
    }

    /**
     * Common validator
     *
     * @param  object $request
     */
    private function validateRequest($request, $rules= [], $messages= [])
    {
        if ($request) {
            $this->validate(
                $request,
                array_collapse([
                    [
                        'name'=> 'required|alpha',
                        'email'=> 'required|email|unique:vendors',
                    ],
                    $rules
                ]),
                array_collapse([
                    [],
                    $messages
                ])
            );
        }

    }
}
