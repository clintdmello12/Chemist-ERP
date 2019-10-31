<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Manufacturer;

class ManufacturerController extends Controller
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
        $data['manufacturers'] = Manufacturer::all();
        return view('manufacturers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['manufacturer'] = new Manufacturer;
        $data['form_param'] = ['route'=> 'manufacturers.store'];
        return view('manufacturers.create', $data);
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

        $manufacturer = Manufacturer::create($request->all());
        return redirect()->route('manufacturers.index');
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
        $data['manufacturer'] = Manufacturer::findOrFail($id);
        $data['form_param'] = ['route'=> ['manufacturers.update', $id], 'method'=> 'PUT'];
        return view('manufacturers.edit', $data);
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
        $this->validateRequest($request, ['name'=> 'required']);

        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->update($request->all());
        return redirect()->route('manufacturers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);
        $manufacturer->products()->delete();
        $manufacturer->delete();
        return redirect()->route('manufacturers.index');
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
                        'name'=> 'required|unique:manufacturers',
                    ],
                    $rules
                ]),
                array_collapse([
                    [
                        'name.unique'=> 'The manufacturer :attribute already exists.',
                    ],
                    $messages
                ])
            );
        }

    }
}
