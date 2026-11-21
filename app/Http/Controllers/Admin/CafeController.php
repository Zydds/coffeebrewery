<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCafeRequest;
use App\Models\Cafe;
use App\Models\Wilayah;

class CafeController extends Controller
{
    public function index()
    {
        $cafes = Cafe::where('source', 'manual')->latest()->get();

        return view('admin.cafes.index', compact('cafes'));
    }

    public function create()
    {
        $regions = Wilayah::kabupatenKota()->orderBy('nama')->get();

        return view('admin.cafes.create', compact('regions'));
    }

    public function store(StoreCafeRequest $request)
    {
        Cafe::create(array_merge($request->validated(), [
            'source' => 'manual',
            'is_missing' => false,
        ]));

        return redirect()->route('admin.cafes.index')->with('success', 'Cafe created.');
    }

    public function edit(Cafe $cafe)
    {
        abort_unless($cafe->source === 'manual', 404);
        $regions = Wilayah::kabupatenKota()->orderBy('nama')->get();

        return view('admin.cafes.edit', compact('cafe', 'regions'));
    }

    public function update(StoreCafeRequest $request, Cafe $cafe)
    {
        abort_unless($cafe->source === 'manual', 404);
        $cafe->update(array_merge($request->validated(), ['source' => 'manual', 'is_missing' => false]));

        return redirect()->route('admin.cafes.index')->with('success', 'Cafe updated.');
    }

    public function destroy(Cafe $cafe)
    {
        abort_unless($cafe->source === 'manual', 404);
        $cafe->delete();

        return redirect()->route('admin.cafes.index')->with('success', 'Cafe deleted.');
    }
}
