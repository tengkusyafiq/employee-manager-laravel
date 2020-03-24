<?php

namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('boss.users.index')->with('users', User::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // user user click on edit on their own id, redirect back to index page.
        if (Auth::user()->id == $id) {
            return redirect()->route('boss.users.index')->with('warning', 'You cannot edit yourself.');
        } // user can't edit themselves

        // if not, go to the edit page.
        return view('boss.users.edit')->with(['user' => User::find($id), 'roles' => Role::all()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // user user click on edit on their own id, redirect back to index page.
        if (Auth::user()->id == $id) {
            return redirect()->route('boss.users.index')->with('warning', 'You cannot edit yourself.');
        } // user can't edit themselves

        $user = User::find($id); // find users id
        // since in view we take an array (roles[]), we can use sync()
        $user->roles()->sync($request->roles);

        return redirect()->route('boss.users.index')->with('success', 'Employee data has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
