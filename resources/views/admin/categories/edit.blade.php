@extends('admin.layout')

@section('title', 'Edit Category')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Edit Category</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Update the details of this category.</p>
    </div>

    @include('admin.categories._form')
@endsection
