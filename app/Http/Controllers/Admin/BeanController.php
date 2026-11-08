<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBeanRequest;
use App\Models\Category;
use App\Models\CoffeeBean;
use App\Services\CatalogWriter;
use Illuminate\Database\QueryException;

class BeanController extends Controller
{
    public function __construct(
        private CatalogWriter $writer
    ) {}

    public function index()
    {
        $query = CoffeeBean::with('category');

        if (request('filter') === 'cheap') {
            $query->where('is_cheap', true);
        } elseif (request('filter') === 'regular') {
            $query->where('is_cheap', false);
        }

        $beans = $query->latest()->get();

        return view('admin.beans.index', compact('beans'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.beans.create', compact('categories'));
    }

    public function store(StoreBeanRequest $request)
    {
        $this->writer->save(
            new CoffeeBean,
            $request->validated(),
            $request->file('image'),
            'coffee-app/beans'
        );

        return redirect()->route('admin.beans.index')
            ->with('success', 'Coffee bean created.');
    }

    public function edit(CoffeeBean $bean)
    {
        $categories = Category::all();

        return view('admin.beans.edit', compact('bean', 'categories'));
    }

    public function update(StoreBeanRequest $request, CoffeeBean $bean)
    {
        $this->writer->save(
            $bean,
            $request->validated(),
            $request->file('image'),
            'coffee-app/beans'
        );

        return redirect()->route('admin.beans.index')
            ->with('success', 'Coffee bean updated.');
    }

    public function destroy(CoffeeBean $bean)
    {
        try {
            $this->writer->delete($bean);

            return redirect()->route('admin.beans.index')
                ->with('success', 'Coffee bean deleted.');
        } catch (QueryException $e) {
            return redirect()->route('admin.beans.index')
                ->with('error', 'Cannot delete bean that still has recipes.');
        }
    }
}
