<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Models\Category;
use App\Services\CatalogWriter;
use Illuminate\Database\QueryException;

class CategoryController extends Controller
{
    public function __construct(
        private CatalogWriter $writer
    ) {}

    public function index()
    {
        $categories = Category::withCount('coffeeBeans')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->writer->save(new Category, $request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(StoreCategoryRequest $request, Category $category)
    {
        $this->writer->save($category, $request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category deleted.');
        } catch (QueryException $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category that still has beans.');
        }
    }
}
