<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountCategory;


class AccountCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage-account-categories');
    }

    public function index()
    {
        $category = AccountCategory::latest()->get();

        return view(
            'Account_category.index',
            compact('category')
        );
    }

    public function create()
    {
        return view('Account_category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:account_categories,category_name',
            'category_type' => 'required|in:Asset,Income,Expense,Liability',
            'description'   => 'nullable|string',
            'status'        => 'required|in:Active,Inactive',
        ]);

        AccountCategory::create($request->only([
            'category_name',
            'category_type',
            'description',
            'status',
        ]));

        return redirect()
            ->route('Account_category.index')
            ->with('success', 'Category Created Successfully');
    }

    public function edit(AccountCategory $Account_category)
    {
        return view(
            'Account_category.edit',
            compact('Account_category')
        );
    }

    public function show(AccountCategory $Account_category)
    {
        return redirect()->route('Account_category.index');
    }

    public function update(
        Request $request,
        AccountCategory $Account_category
    )
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:account_categories,category_name,' . $Account_category->id,
            'category_type' => 'required|in:Asset,Income,Expense,Liability',
            'description'   => 'nullable|string',
            'status'        => 'required|in:Active,Inactive',
        ]);

        $Account_category->update($request->only([
            'category_name',
            'category_type',
            'description',
            'status',
        ]));

        return redirect()
            ->route('Account_category.index')
            ->with('success', 'Category Updated Successfully');
    }

    public function destroy(AccountCategory $Account_category)
    {
        $Account_category->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Category Deleted Successfully',
            ]);
        }

        return redirect()
            ->route('Account_category.index')
            ->with('success', 'Category Deleted Successfully');
    }
}
