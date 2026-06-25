<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Task;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalTasks = Task::where('assigned_to', $user->id)->count();

        $pendingTasks = Task::where('assigned_to', $user->id)
            ->where('status', 'pending')
            ->count();

        $progressTasks = Task::where('assigned_to', $user->id)
            ->where('status', 'progress')
            ->count();

        $completedTasks = Task::where('assigned_to', $user->id)
            ->where('status', 'completed')
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
}