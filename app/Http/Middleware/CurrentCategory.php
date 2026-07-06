<?php

namespace App\Http\Middleware;

use App\Models\ProjectCategory;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrentCategory
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $categoryId = $request->session()->get('current_category_id');

        if ($request->has('category_id')) {
            $requestedCategoryId = $request->input('category_id');

            if ($requestedCategoryId === '' || $requestedCategoryId === null) {
                if ($user->hasRole('admin')) {
                    $categoryId = null;
                    $request->session()->forget('current_category_id');
                }
            } else {
                $requestedCategoryId = (int) $requestedCategoryId;
                $categoryExists = ProjectCategory::whereKey($requestedCategoryId)->exists();

                if ($categoryExists && ($user->hasRole('admin') || $requestedCategoryId === $user->category_id)) {
                    $categoryId = $requestedCategoryId;
                    $request->session()->put('current_category_id', $categoryId);
                }
            }
        }

        if (! $categoryId && ! $user->hasRole('admin')) {
            $categoryId = $user->category_id;
            if ($categoryId && ProjectCategory::whereKey($categoryId)->exists()) {
                $request->session()->put('current_category_id', $categoryId);
            }
        }

        if ($categoryId) {
            $request->attributes->set('current_category_id', $categoryId);
        } else {
            $request->attributes->remove('current_category_id');
        }

        return $next($request);
    }
}
