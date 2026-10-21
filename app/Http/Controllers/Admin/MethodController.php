<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMethodRequest;
use App\Models\BrewMethod;
use App\Services\CloudinaryUploader;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class MethodController extends Controller
{
    public function __construct(
        private CloudinaryUploader $uploader
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
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $result = $this->uploader->upload($request->file('image'), 'coffee-app/methods');
            $data['image_url'] = $result['secure_url'];
            $data['image_public_id'] = $result['public_id'];
        }

        BrewMethod::create($data);

        return redirect()->route('admin.methods.index')
            ->with('success', 'Brew method created.');
    }

    public function edit(BrewMethod $method)
    {
        return view('admin.methods.edit', compact('method'));
    }

    public function update(StoreMethodRequest $request, BrewMethod $method)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $result = $this->uploader->replace($request->file('image'), $method->image_public_id, 'coffee-app/methods');
            $data['image_url'] = $result['secure_url'];
            $data['image_public_id'] = $result['public_id'];
        }

        $method->update($data);

        return redirect()->route('admin.methods.index')
            ->with('success', 'Brew method updated.');
    }

    public function destroy(BrewMethod $method)
    {
        try {
            $this->uploader->delete($method->image_public_id);
            $method->delete();

            return redirect()->route('admin.methods.index')
                ->with('success', 'Brew method deleted.');
        } catch (QueryException $e) {
            return redirect()->route('admin.methods.index')
                ->with('error', 'Cannot delete method that still has recipes.');
        }
    }
}
