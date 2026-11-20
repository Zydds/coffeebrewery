@extends('admin.layout')

@section('title', 'Add Recipe')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Add Recipe</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Create a new brew guide.</p>
    </div>

    @include('admin.recipes._form')
@endsection
