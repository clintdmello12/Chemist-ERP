<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Gate;

class UserController extends Controller
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

        $data['current_user'] = auth()->user();
        $data['users'] = User::all();
        return view('users.index', $data);
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

        $data['usr'] = new User;
        $data['roles'] = Role::all()->pluck('name', 'id');
        $data['form_param'] = ['route'=> 'users.store'];
        return view('users.create', $data);
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
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        return redirect()->route('users.index');
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

        $data['usr'] = User::findOrFail($id);
        $data['roles'] = Role::all()->pluck('name', 'id');
        $data['form_param'] = ['route'=> ['users.update', $id], 'method'=> 'PUT'];

        return view('users.edit', $data);
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

         $this->validateRequest(
            $request,
            ['email'=> 'required|email', 'password'=> 'required_with:password|min:5']
        );

        $user = User::findOrFail($id);
        $data = $request->all();
        $data['password'] = (empty($data['password'])) ? $user->password : bcrypt($data['password']);
        $user->update($data);
        return redirect()->route('users.index');
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

        User::findOrFail($id)->delete();
        return redirect()->route('users.index');
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
                        'first_name'=> 'required|alpha',
                        'last_name'=> 'required|alpha',
                        'email'=> 'required|email|unique:users',
                        'password'=> 'required|min:5',
                        'role_id'=> 'required|numeric|exists:roles,id',
                    ],
                    $rules
                ]),
                array_collapse([
                    [
                        'email.unique'=> 'The users :attribute already exists.',
                        'role_id.required'=> 'Select role',
                    ],
                    $messages
                ])
            );
        }

    }
}
