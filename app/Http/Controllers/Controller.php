<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    protected function currentCategoryId(): ?int
    {
        $categoryId = request()->attributes->get('current_category_id') ?: session('current_category_id');

        return $categoryId ? (int) $categoryId : null;
    }

    protected function authorizeCategory(mixed $model): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $categoryId = null;
        if (isset($model->category_id)) {
            $categoryId = $model->category_id;
        } elseif ($model instanceof \App\Models\Task && $model->project) {
            $categoryId = $model->project->category_id;
        }

        if ($categoryId !== null) {
            $selectedCategory = $user->hasRole('admin') ? $this->currentCategoryId() : $user->category_id;
            if ($selectedCategory && (int) $categoryId !== (int) $selectedCategory) {
                abort(403, 'Unauthorized category access.');
            }
            if (!$user->hasRole('admin') && (int) $categoryId !== (int) $user->category_id) {
                abort(403, 'Unauthorized category access.');
            }
        }
    }
}
