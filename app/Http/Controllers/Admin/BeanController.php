<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBeanRequest;
use App\Models\Category;
use App\Models\CoffeeBean;
use App\Services\CloudinaryUploader;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class BeanController extends Controller
{
    public function __construct(
        private CloudinaryUploader $uploader
    ) {}

    public function index()
    {
        $beans = CoffeeBean::with('category')->latest()->get();

        return view('admin.beans.index', compact('beans'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.beans.create', compact('categories'));
    }

    public function store(StoreBeanRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $result = $this->uploader->upload($request->file('image'), 'coffee-app/beans');
            $data['image_url'] = $result['secure_url'];
            $data['image_public_id'] = $result['public_id'];
        }

        CoffeeBean::create($data);

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
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $result = $this->uploader->replace($request->file('image'), $bean->image_public_id, 'coffee-app/beans');
            $data['image_url'] = $result['secure_url'];
            $data['image_public_id'] = $result['public_id'];
        }

        $bean->update($data);

        return redirect()->route('admin.beans.index')
            ->with('success', 'Coffee bean updated.');
    }

    public function destroy(CoffeeBean $bean)
    {
        try {
            $this->uploader->delete($bean->image_public_id);
            $bean->delete();

            return redirect()->route('admin.beans.index')
                ->with('success', 'Coffee bean deleted.');
        } catch (QueryException $e) {
            return redirect()->route('admin.beans.index')
                ->with('error', 'Cannot delete bean that still has recipes.');
        }
    }
}
