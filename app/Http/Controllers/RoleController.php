<?php

namespace App\Http\Controllers;

use App\Role;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
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

        $data['roles'] = Role::all();
        return view('roles.index', $data);
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

        $data['role'] = new Role;
        $data['form_param'] = ['route'=> 'roles.store'];
        return view('roles.create', $data);
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

        $role = Role::create($request->all());
        return redirect()->route('roles.index');
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

        $data['role'] = Role::findOrFail($id);
        $data['form_param'] = ['route'=> ['roles.update', $id], 'method'=> 'PUT'];
        return view('roles.edit', $data);
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

        $this->validateRequest($request);

        $role = Role::findOrFail($id);
        $role->update($request->all());
        return redirect()->route('roles.index');
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
    private function validateRequest($request)
    {
        if ($request) {
            $this->validate(
                $request,
                [
                    'name'=> 'required|alpha|unique:roles',
                ],
                [
                    'name.unique'=> 'The role :attribute already exists.',
                ]
            );
        }

    }
}
