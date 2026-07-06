<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability): ?bool {
            return $user->hasRole('admin') ? true : null;
        });

        $permissions = collect(config('rbac.roles'))
            ->flatten()
            ->filter(fn ($permission) => $permission !== '*')
            ->unique()
            ->values();

        foreach ($permissions as $permission) {
            Gate::define($permission, function (User $user, mixed $context = null) use ($permission): bool {
                return $user->canAccess($permission, $context);
            });
        }

        View::composer('*', function ($view): void {
            if (Auth::check()) {
                $user = Auth::user();
                $categories = $user->hasRole('admin')
                    ? ProjectCategory::orderBy('category_name')->get()
                    : ProjectCategory::whereKey($user->category_id)->orderBy('category_name')->get();

                $currentCategoryId = session('current_category_id');
                $currentCategoryId = $currentCategoryId !== null ? (int) $currentCategoryId : null;

                $view->with('navCategories', $categories);
                $view->with('currentCategoryId', $currentCategoryId);
            }
        });
    }
}
