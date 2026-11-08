<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMethodRequest;
use App\Models\BrewMethod;
use App\Services\CatalogWriter;
use Illuminate\Database\QueryException;

class MethodController extends Controller
{
    public function __construct(
        private CatalogWriter $writer
    ) {}

    public function index()
    {
        $methods = BrewMethod::withCount('recipes')->get();

        return view('admin.methods.index', compact('methods'));
    }

    public function create()
    {
        return view('admin.methods.create');
    }

    public function store(StoreMethodRequest $request)
    {
        $this->writer->save(
            new BrewMethod,
            $request->validated(),
            $request->file('image'),
            'coffee-app/methods'
        );

        return redirect()->route('admin.methods.index')
            ->with('success', 'Brew method created.');
    }

    public function edit(BrewMethod $method)
    {
        return view('admin.methods.edit', compact('method'));
    }

    public function update(StoreMethodRequest $request, BrewMethod $method)
    {
        $this->writer->save(
            $method,
            $request->validated(),
            $request->file('image'),
            'coffee-app/methods'
        );

        return redirect()->route('admin.methods.index')
            ->with('success', 'Brew method updated.');
    }

    public function destroy(BrewMethod $method)
    {
        try {
            $this->writer->delete($method);

            return redirect()->route('admin.methods.index')
                ->with('success', 'Brew method deleted.');
        } catch (QueryException $e) {
            return redirect()->route('admin.methods.index')
                ->with('error', 'Cannot delete method that still has recipes.');
        }
    }
}
