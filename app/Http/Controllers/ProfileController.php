<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Task;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:view-profile');
    }

    public function index()
    {
        $user = Auth::user();

        $totalTasks = Task::where('assigned_to', $user->id)->count();

        $pendingTasks = Task::where('assigned_to', $user->id)
            ->where('status', 'Pending')
            ->count();

        $progressTasks = Task::where('assigned_to', $user->id)
            ->where('status', 'In Progress')
            ->count();

        $completedTasks = Task::where('assigned_to', $user->id)
            ->where('status', 'Completed')
            ->count();

        return view('Profile.index', compact(
            'user',
            'totalTasks',
            'pendingTasks',
            'progressTasks',
            'completedTasks'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($request->hasFile('image')) {

            $imageName = time() . '.' . $request->image->extension();

            $request->image->storeAs(
                'profile',
                $imageName,
                'public'
            );

            $user->image = $imageName;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully');
    }
}
