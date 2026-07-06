<li class="nav-item dropdown">
    <form action="{{ route('category.select') }}" method="GET" class="form-inline mb-0">
        <div class="input-group input-group-sm">
            <select name="category_id" class="form-control form-control-sm" onchange="this.form.submit()">
                <option value="" {{ $currentCategoryId === null ? 'selected' : '' }}>All Categories</option>
                @foreach($navCategories as $category)
                    <option value="{{ $category->id }}" {{ $currentCategoryId === (int) $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</li>
