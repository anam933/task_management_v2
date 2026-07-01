<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage-tags');
    }

    public function index()
    {
        $tags = Tag::withCount('tasks')->latest()->get();

        return view('tags.index', compact('tags'));
    }

    public function create()
    {
        return view('tags.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        Tag::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'color' => $data['color'] ?? '#0d6efd',
            'description' => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag Created Successfully');
    }

    public function edit(Tag $tag)
    {
        return view('tags.edit', compact('tag'));
    }

    public function show(Tag $tag)
    {
        return redirect()->route('tags.index');
    }

    public function update(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $tag->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'color' => $data['color'] ?? $tag->color ?? '#0d6efd',
            'description' => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag Updated Successfully');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Tag Deleted Successfully',
            ]);
        }

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag Deleted Successfully');
    }
}
