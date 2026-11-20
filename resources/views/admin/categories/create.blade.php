@extends('admin.layout')

@section('title', 'Add Category')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Add Category</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Create a new category for coffee beans.</p>
    </div>

    @include('admin.categories._form')
@endsection
