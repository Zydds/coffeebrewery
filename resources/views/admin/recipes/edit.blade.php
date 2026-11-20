@extends('admin.layout')

@section('title', 'Edit Recipe')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Edit Recipe</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Update this brew guide.</p>
    </div>

    @include('admin.recipes._form')
@endsection
