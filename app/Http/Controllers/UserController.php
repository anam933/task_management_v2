<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage-employees')->only(['index', 'create', 'store', 'edit', 'update', 'destroy', 'show']);
    }

    public function index()
    {
        $users = auth()->user()->hasRole('manager')
            ? User::with('creator')->employees()->latest()->get()
            : User::with('creator')->latest()->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $roleRule = $currentUser->hasRole('manager')
            ? 'required|in:employee'
            : 'required|in:admin,manager,employee';

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
            'role' => $roleRule,
        ]);

        $role = $currentUser->hasRole('manager') ? 'employee' : $request->role;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $role,
            'created_by' => $currentUser->id,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/users')->with('success','User Added Successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        abort_unless(auth()->user()->hasRole('admin') || $user->hasRole('employee'), 403);

        return view('users.edit', compact('user'));
    }

    public function show(User $user)
    {
        return redirect()->route('users.edit', $user->id);
    }

    public function update(Request $request,$id)
    {
        $user = User::findOrFail($id);
        abort_unless(auth()->user()->hasRole('admin') || $user->hasRole('employee'), 403);

        $currentUser = auth()->user();
        $roleRule = $currentUser->hasRole('manager')
            ? 'required|in:employee'
            : 'required|in:admin,manager,employee';

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'role' => $roleRule,
        ]);

        $role = $currentUser->hasRole('manager') ? 'employee' : $request->role;

        $user->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'role'=>$role,
        ]);

        return redirect('/users')->with('success','User Updated');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        abort_unless(auth()->user()->hasRole('admin') || $user->hasRole('employee'), 403);

        $user->delete();

        return redirect('/users')->with('success','User Deleted');
    }
}
