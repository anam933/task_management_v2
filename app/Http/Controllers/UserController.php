<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Models\Task;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage-employees')->only(['index', 'create', 'store', 'edit', 'update', 'destroy', 'show']);
    }

   public function index()
{
    $user = auth()->user();

    $selectedCategory = $this->currentCategoryId();
    $selectedUser = request('user_id');

    $users = User::with(['creator', 'manager', 'category'])

        // Admin
        ->when($user->hasRole('admin'), function ($query) use ($selectedCategory) {

            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory);
            }

        })

        // Manager
        ->when($user->hasRole('manager'), function ($query) use ($user) {

            $query->where(function ($q) use ($user) {

                $q->where('id', $user->id)
                  ->orWhere('reports_to', $user->id);

            });

        })

        // Employee
        ->when($user->hasRole('employee'), function ($query) use ($user) {

            $query->where('id', $user->id);

        })

        // User Filter
        ->when($selectedUser, function ($query) use ($selectedUser) {

            $query->where('id', $selectedUser);

        })

        ->latest()
        ->get();



    // User Dropdown
    $allUsers = User::query()

        ->when($user->hasRole('admin'), function ($query) use ($selectedCategory) {

            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory);
            }

        })

        ->when($user->hasRole('manager'), function ($query) use ($user) {

            $query->where(function ($q) use ($user) {

                $q->where('id', $user->id)
                  ->orWhere('reports_to', $user->id);

            });

        })

        ->when($user->hasRole('employee'), function ($query) use ($user) {

            $query->where('id', $user->id);

        })

        ->orderBy('name')
        ->get(['id', 'name']);
        $userInfo = null;
$stats = [];

if ($selectedUser) {

    $userInfo = User::with([
        'creator',
        'manager',
        'category'
    ])->find($selectedUser);

    if ($userInfo) {

        $stats = [

            'assigned' => Task::where('assigned_to', $selectedUser)->count(),

            'created' => Task::where('assigned_by', $selectedUser)->count(),

            'pending' => Task::where('assigned_to', $selectedUser)
                ->where('status', 'Pending')
                ->count(),

            'in_progress' => Task::where('assigned_to', $selectedUser)
                ->where('status', 'In Progress')
                ->count(),

            'submitted' => Task::where('assigned_to', $selectedUser)
                ->where('status', 'Submitted')
                ->count(),

            'completed' => Task::where('assigned_to', $selectedUser)
                ->where('status', 'Completed')
                ->count(),

        ];

    }

}
    return view('users.index', compact(
        'users',
        'allUsers',
        'selectedUser',
        'userInfo',
        'stats'
    ));
}

public function create()
{
    $user = auth()->user();
    $selectedCategory = $this->currentCategoryId();

    // Admin → Show all managers in the selected category
    // Manager / Employee → Only show managers from their own category
    $managers = User::where('role', 'manager')

        ->when($user->hasRole('admin') && $selectedCategory, function ($query) use ($selectedCategory) {

            $query->where('category_id', $selectedCategory);

        })

        ->when($user->hasRole('manager') || $user->hasRole('employee'), function ($query) use ($user) {

            $query->where('category_id', $user->category_id);

        })

        ->orderBy('name')
        ->get();


   $categories = ProjectCategory::orderBy('category_name')->get();

return view('users.create', compact(
    'managers',
    'categories'
));
}

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $roleRule = $currentUser->hasRole('manager')
            ? 'required|in:employee'
            : 'required|in:admin,manager,employee';

        $role = $currentUser->hasRole('manager') ? 'employee' : $request->role;
        $categoryId = $currentUser->hasRole('admin') ? $this->currentCategoryId() : $currentUser->category_id;

        if ($role !== 'admin' && !$categoryId) {
            return back()->withErrors(['role' => 'Please select a project category from the top navigation first.'])->withInput();
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => $roleRule,
            'phone' => 'nullable|string|max:20',
           
        ];

        // If Admin creates Employee, they must choose a Manager from the current category
        if ($currentUser->hasRole('admin') && $role === 'employee') {
            $rules['reports_to'] = [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($categoryId) {
                    $query->where('role', 'manager')->where('category_id', $categoryId);
                }),
            ];
        }

        $request->validate($rules);
      

        $reportsTo = null;
        if ($role === 'manager') {
            if ($currentUser->hasRole('admin')) {
                $reportsTo = $currentUser->id;
            }
        } elseif ($role === 'employee') {
            if ($currentUser->hasRole('admin')) {
                $reportsTo = $request->reports_to;
            } else {
                $reportsTo = $currentUser->id;
            }
        }

        $createPayload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $role,
            'created_by' => $currentUser->id,
            'reports_to' => $reportsTo,
            'password' => Hash::make($request->password),
            'category_id' => $role === 'admin' ? null : $categoryId,
            
        ];

        User::create($createPayload);

        return redirect('/users')->with('success', 'User Added Successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        abort_unless(auth()->user()->hasRole('admin') || $user->hasRole('employee'), 403);
        $currentUser = auth()->user();
        $selectedCategory = $this->currentCategoryId();

        $managers = User::where('role', 'manager')
            ->when(! $currentUser->hasRole('admin'), function ($query) use ($currentUser) {
                $query->where('category_id', $currentUser->category_id);
            })
            ->when($currentUser->hasRole('admin') && $selectedCategory, function ($query) use ($selectedCategory) {
                $query->where('category_id', $selectedCategory);
            })
            ->orderBy('name')
            ->get();


       $categories = ProjectCategory::orderBy('category_name')->get();

return view('users.edit', compact(
    'user',
    'managers',
    'categories'
));
    }

    public function show(User $user)
    {
        return redirect()->route('users.edit', $user->id);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        abort_unless(auth()->user()->hasRole('admin') || $user->hasRole('employee'), 403);

        $currentUser = auth()->user();
        $roleRule = $currentUser->hasRole('manager')
            ? 'required|in:employee'
            : 'required|in:admin,manager,employee';

        $role = $currentUser->hasRole('manager') ? 'employee' : $request->role;
        $categoryId = $currentUser->hasRole('admin') ? $this->currentCategoryId() : $currentUser->category_id;

        if ($role !== 'admin' && !$categoryId) {
            return back()->withErrors(['role' => 'Please select a project category from the top navigation first.'])->withInput();
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => $roleRule,
            'phone' => 'nullable|string|max:20',
            
            
        ];

        if ($currentUser->hasRole('admin') && $role === 'employee') {
            $rules['reports_to'] = [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($categoryId) {
                    $query->where('role', 'manager')->where('category_id', $categoryId);
                }),
            ];
        }

        $request->validate($rules);

        $reportsTo = $user->reports_to;
        if ($role === 'manager') {
            if ($currentUser->hasRole('admin')) {
                $reportsTo = $currentUser->id;
            } else {
                $reportsTo = null;
            }
        } elseif ($role === 'employee') {
            if ($currentUser->hasRole('admin')) {
                $reportsTo = $request->reports_to;
            } else {
                $reportsTo = $currentUser->id;
            }
        } else {
            $reportsTo = null;
        }

        $updatePayload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $role,
            'reports_to' => $reportsTo,
            'category_id' => $role === 'admin' ? null : $categoryId,
            
        ];

        $user->update($updatePayload);

        return redirect('/users')->with('success', 'User Updated');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        abort_unless(auth()->user()->hasRole('admin') || $user->hasRole('employee'), 403);

        $user->delete();

        return redirect('/users')->with('success', 'User Deleted');
    }
}
