@extends('admin.layout')

@section('title', 'Add Brew Method')

@section('content')
    <div>
        <h1 class="text-2xl font-bold">Add Brew Method</h1>
        <p class="mt-1 text-sm text-coffee-mocha">Create a new brewing method.</p>
    </div>

    @include('admin.methods._form')
@endsection
